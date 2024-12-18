<?php
namespace Drupal\scholarship_beneficiary_profile\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Step 1 of the multi-step form.
 */
class Step1Form extends MultiStepFormBase {
  public function getFormId() {
    return 'multistep_form_step1';
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'scholarship_beneficiary_profile/progress_bar';
    $form['progress_bar'] = $this->buildProgressBar(1, 7);
    $form['field_1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field 1'),
      '#default_value' => $this->getData('field_1'),
      '#required' => TRUE,
    ];
    $form['actions']['#type'] = 'actions';
    $form['actions']['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next'),
    ];
    return $form;
  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->saveData([ 'field_1' => $form_state->getValue('field_1') ]);
    $form_state->setRedirect('scholarship_beneficiary_profile.step2');
  }
}
