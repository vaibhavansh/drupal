<?php

namespace Drupal\user_assignment_manager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class UserAssignmentController extends ControllerBase {

  public function assignVolunteer() {
    $beneficiaries = $this->getUsersByRole('beneficiary');
    $volunteers = $this->getUsersByRole('volunteer');

    $volunteers = array_filter($volunteers);
    $volunteers = array_values($volunteers);

    if (empty($volunteers)) {
      return ['#markup' => 'No volunteers found.'];
    }

    $assigned = $this->assignBeneficiariesToVolunteers($beneficiaries, $volunteers);
    $assigner_uid = $this->currentUser()->id();

    foreach ($assigned as $volunteer_uid => $beneficiary_ids) {
      foreach ($beneficiary_ids as $beneficiary_uid) {
        $this->createCustomNode($volunteer_uid, $assigner_uid, $beneficiary_uid);
      }
    }

    return ['#markup' => 'Assignment complete. ' . count($beneficiaries) . ' beneficiaries assigned.'];
  }

  private function getUsersByRole(string $role_id): array {
    $user_ids = \Drupal::entityQuery('user')
      ->condition('status', 1)
      ->condition('roles', $role_id)
      ->accessCheck(FALSE)
      ->execute();

    $users = User::loadMultiple($user_ids);
    $filtered_users = [];

    foreach ($users as $user) {
      if (count($user->getRoles()) === 1 && in_array($role_id, $user->getRoles())) {
        $filtered_users[] = $user;
      }
    }

    return $filtered_users;
  }

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

  private function createCustomNode($assignee_uid, $assigner_uid, $beneficiary_uid): void {
    $node = Node::create([
      'type' => 'beneficiary_application_status',
      'title' => 'Assignment for User ' . $beneficiary_uid,
      'field_assignee_uid' => ['target_id' => $assignee_uid],
      'field_assigner_uid' => ['target_id' => $assigner_uid],
      'field_beneficiary_uid' => ['target_id' => $beneficiary_uid],
      'status' => 1,
    ]);

    $node->save();
  }

}
