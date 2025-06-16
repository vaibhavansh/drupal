<?php

/**
 * @file
 * Contains \Drupal\user_assignment_manager\Controller\UserAssignmentController.
 *
 * Assigns beneficiary users to volunteer users via round-robin logic
 * and updates a content node to record the assignment.
 *
 * @author Vaibahv Bargal
 * @date 2025-06-12
 */

namespace Drupal\user_assignment_manager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Class UserAssignmentController
 *
 * Handles the logic for assigning beneficiaries to volunteers.
 */
class UserAssignmentController extends ControllerBase {

  /**
   * Assigns beneficiaries to volunteers in a round-robin manner.
   *
   * @return array
   *   A renderable array indicating assignment result.
   */
  public function assignVolunteer() {
    $beneficiaries = $this->getUsersByRole('beneficiary');
    $volunteers = $this->getUsersByRole('volunteer');

    $volunteers = array_filter($volunteers);
    $volunteers = array_values($volunteers);

    if (empty($volunteers)) {
      return ['#markup' => 'No volunteers found.'];
    }

    $assigned = $this->assignBeneficiariesToVolunteers($beneficiaries, $volunteers);

    // Debug output for checking assignment
    dd($assigned);

    $assigner_uid = $this->currentUser()->id();

    foreach ($assigned as $volunteer_uid => $beneficiary_ids) {
      foreach ($beneficiary_ids as $beneficiary_uid) {
        // Uncomment the below line to update nodes when ready
        // $this->updateCustomNode($nid, $volunteer_uid, $assigner_uid, $beneficiary_uid);
      }
    }

    return ['#markup' => 'Assignment complete. ' . count($beneficiaries) . ' beneficiaries assigned.'];
  }

  /**
   * Get all active users by a given role.
   *
   * @param string $role_id
   *   The machine name of the role (e.g., 'beneficiary').
   *
   * @return \Drupal\user\Entity\User[]
   *   An array of user entities.
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
   * Assign beneficiaries to volunteers using round-robin logic.
   *
   * @param array $beneficiaries
   *   List of beneficiary user entities.
   * @param array $volunteers
   *   List of volunteer user entities.
   *
   * @return array
   *   Associative array [volunteer_id => array of beneficiary_ids].
   */
  private function assignBeneficiariesToVolunteers(array $beneficiaries, array $volunteers): array {
    $assignments = [];
    $volunteerCount = count($volunteers);
    $i = 0;

    foreach ($beneficiaries as $beneficiary) {
      $volunteer = $volunteers[$i % $volunteerCount];
      $assignments[$volunteer->id()][] = $beneficiary->id();
      $i++;
    }

    return $assignments;
  }

  /**
   * Updates a node's assignee, assigner, and beneficiary fields.
   *
   * @param int $nid
   *   The node ID to update.
   * @param int|null $assignee_uid
   *   The user ID of the assignee (optional).
   * @param int|null $assigner_uid
   *   The user ID of the assigner (optional).
   * @param int|null $beneficiary_uid
   *   The user ID of the beneficiary (optional).
   *
   * @return bool
   *   TRUE if the node was updated, FALSE otherwise.
   */
  private function updateCustomNode(int $nid, $assignee_uid = NULL, $assigner_uid = NULL, $beneficiary_uid = NULL): bool {
    $node = Node::load($nid);

    if (!$node || $node->bundle() !== 'beneficiary_application_status') {
      return FALSE;
    }

    if ($assignee_uid !== NULL) {
      $node->set('field_assignee_uid', ['target_id' => $assignee_uid]);
    }
    if ($assigner_uid !== NULL) {
      $node->set('field_assigner_uid', ['target_id' => $assigner_uid]);
    }
    if ($beneficiary_uid !== NULL) {
      $node->set('field_beneficiary_uid', ['target_id' => $beneficiary_uid]);
    }

    $node->save();
    return TRUE;
  }

}
