<?php
namespace Drupal\scholarship_registration\Controller;

use Drupal\Core\Controller\ControllerBase;

class VolunteerRegistrationController extends ControllerBase {

  /**
   * Displays the success page for Volunteer registration.
   */
  public function successPage() {
    return [
      '#type' => 'markup',
      '#markup' => '<h1>Thank you for registering as a Volunteer!</h1><p>You will receive a confirmation email shortly.</p>',
    ];
  }
}
