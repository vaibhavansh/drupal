<?php

/**
 * @file
 * Contains \Drupal\user_assignment_manager\Controller\UserAssignmentController.
 *
 * Assigns beneficiary users to volunteer users via round-robin logic
 * and updates the corresponding content nodes.
 *
 * @author Vaibhav Bargal
 * @date 2025-06-12
 */

namespace Drupal\user_assignment_manager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Class UserAssignmentController
 *
 * Handles assignment of beneficiaries to volunteers and updates nodes accordingly.
 */
class UserAssignmentController extends ControllerBase {

  /**
   * Assigns beneficiaries to volunteers in a round-robin manner and updates nodes.
   *
   * @return array
   *   A renderable array indicating assignment result.
   */
  public function assignVolunteer() {


    if (
  $this->currentUser()->hasRole('administrator') || 
  $this->currentUser()->hasRole('csr')
) {
  // User is either admin or CSR — continue script
} else {
  // Access denied or exit
  throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Access denied.');
}
   $sql = "
  SELECT 
    u.uid AS uid,
    ufqid.field_unique_id_value AS UniqueID,
    u.name AS username,
    u.mail AS email,
    'ss' AS benificaiaryname,
    n.nid,
    n.title,
    s.field_form_status_value,
    bu.field_benificiary_uid_value
  FROM 
    node_field_data AS n
  INNER JOIN 
    node__field_form_status AS s ON n.nid = s.entity_id
  INNER JOIN 
    node__field_benificiary_uid AS bu ON n.nid = bu.entity_id
  INNER JOIN 
    users_field_data AS u ON u.uid = bu.field_benificiary_uid_value
  LEFT JOIN 
    user__roles AS r ON r.entity_id = u.uid
  LEFT JOIN 
    user__field_unique_id AS ufqid ON ufqid.entity_id = u.uid
  WHERE 
    n.type = 'beneficiary_application_status'
    AND s.field_form_status_value = 1
";

$results = \Drupal::database()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);


    $results = \Drupal::database()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

    $beneficiaries = [];
    foreach ($results as $result) {
      $beneficiaries[$result['uid']] = $result['nid'];
    }

    $volunteers = $this->getUsersByRole('volunteer');
    $volunteers = array_values(array_filter($volunteers));

    if (empty($volunteers)) {
      return ['#markup' => 'No volunteers found.'];
    }

    $assigned = $this->assignBeneficiariesToVolunteers($beneficiaries, $volunteers);
    $assigner_uid = $this->currentUser()->id();

    foreach ($assigned as $volunteer_uid => $nids_uids) {
      foreach ($nids_uids as $nid_uid) {
        [$nid, $uid] = explode('_', $nid_uid);
        $this->updateCustomNode((int) $nid, $volunteer_uid, $assigner_uid);
      }
    }

    \Drupal::messenger()->addMessage(count($beneficiaries) . ' beneficiaries assigned successfully.');
    return new RedirectResponse('/admin/dashboard'); // Change to your desired path
    return ['#markup' => 'Assignment complete. ' . count($beneficiaries) . ' beneficiaries assigned.'];
  }

  /**
   * Loads all active users with a specific role.
   *
   * @param string $role_id
   *   The machine name of the role (e.g., 'beneficiary', 'volunteer').
   *
   * @return \Drupal\user\Entity\User[]
   *   An array of loaded user entities.
   */
  private function getUsersByRole(string $role_id): array {
    $user_ids = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->condition('roles', $role_id)
      ->accessCheck(FALSE)
      ->execute();

    return User::loadMultiple($user_ids);
  }

  /**
   * Distributes beneficiaries to volunteers in a round-robin manner.
   *
   * @param array $beneficiaries
   *   Array of [uid => nid] mapping.
   * @param \Drupal\user\Entity\User[] $volunteers
   *   Array of volunteer user entities.
   *
   * @return array
   *   Associative array of assignments: [volunteer_uid => [nid_uid_string, ...]].
   */
  private function assignBeneficiariesToVolunteers(array $beneficiaries, array $volunteers): array {
    $assignments = [];
    $volunteerCount = count($volunteers);
    $i = 0;

    foreach ($beneficiaries as $uid => $nid) {
      $volunteer = $volunteers[$i % $volunteerCount];
      $assignments[$volunteer->id()][] = "{$nid}_{$uid}";
      $i++;
    }

    return $assignments;
  }

  /**
   * Updates a node with assigned assignee and assigner.
   *
   * @param int $nid
   *   The node ID to update.
   * @param int|null $assignee_uid
   *   UID of the volunteer (assignee).
   * @param int|null $assigner_uid
   *   UID of the assigner (current user).
   *
   * @return bool
   *   TRUE if node was successfully updated, FALSE otherwise.
   */
  private function updateCustomNode(int $nid, $assignee_uid = NULL, $assigner_uid = NULL): bool {
    $node = Node::load($nid);

    if (!$node || $node->bundle() !== 'beneficiary_application_status') {
      return FALSE;
    }

    if ($assignee_uid !== NULL) {
      $node->set('field_assignee_uid', $assignee_uid);
    }

    if ($assigner_uid !== NULL) {
      $node->set('field_assigner_uid', $assigner_uid);
    }

    $node->save();
    return TRUE;
  }

}
