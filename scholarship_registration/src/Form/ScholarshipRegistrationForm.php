<?php

namespace Drupal\scholarship_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

class ScholarshipRegistrationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'scholarship_registration_form';
  }



  public function access(AccountInterface $account) {
    return $account->hasPermission('access scholarship registration form');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Personal Details Section
    $form['personal_details'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Personal Details'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['personal_details']['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#required' => TRUE,
    ];

    $form['personal_details']['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#required' => TRUE,
    ];

    // Academic Details Section
    $form['academic_details'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Academic Details'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['academic_details']['institution'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Institution Name'),
      '#required' => TRUE,
    ];

    $form['academic_details']['degree'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Degree'),
      '#required' => TRUE,
    ];

    // Family Details Section
    $form['family_details'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Family Details'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['family_details']['father_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Father\'s Name'),
      '#required' => TRUE,
    ];

    $form['family_details']['mother_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mother\'s Name'),
      '#required' => TRUE,
    ];

    // Income Details Section
    $form['income_details'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Income Details'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['income_details']['annual_income'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Annual Family Income'),
      '#required' => TRUE,
    ];

    // Health Details Section
    $form['health_details'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Health Details'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['health_details']['medical_conditions'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Medical Conditions'),
      '#required' => FALSE,
    ];

    // Document Details Section
    $form['document_details'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Document Details'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['document_details']['id_proof'] = [
      '#type' => 'file',
      '#title' => $this->t('ID Proof'),
      '#required' => TRUE,
    ];

    $form['document_details']['academic_certificate'] = [
      '#type' => 'file',
      '#title' => $this->t('Academic Certificate'),
      '#required' => TRUE,
    ];

    // Submit button
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Registration'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage($this->t('Scholarship Registration form submitted.'));
  }
}
