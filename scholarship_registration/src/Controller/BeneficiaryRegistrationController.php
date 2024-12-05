<?php

namespace Drupal\scholarship_registration\Controller;

use Drupal\Core\Controller\ControllerBase;

class BeneficiaryRegistrationController extends ControllerBase {

  /**
   * Displays the success page for Beneficiary registration.
   */
  public function successPage() {
    return [
      '#type' => 'markup',
      '#markup' => '<h1>Thank you for registering as a Beneficiary!</h1><p>You will receive a confirmation email shortly.</p>',
    ];
  }
}
