<?php
namespace Drupal\scholarship_beneficiary_profile\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Step 2 of the multi-step form.
 */
class Step2Form extends MultiStepFormBase {
  public function getFormId() {
    return 'multistep_form_step2';
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'scholarship_beneficiary_profile/progress_bar';
    $form['progress_bar'] = $this->buildProgressBar(2, 7);
    $form['field_2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field 2'),
      '#default_value' => $this->getData('field_2'),
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
    $this->saveData([ 'field_2' => $form_state->getValue('field_2') ]);
    $form_state->setRedirect('scholarship_beneficiary_profile.step3');
  }
}
