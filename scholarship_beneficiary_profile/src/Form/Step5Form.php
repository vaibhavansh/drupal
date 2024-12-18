<?php
namespace Drupal\scholarship_beneficiary_profile\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Step 5 of the multi-step form.
 */
class Step5Form extends MultiStepFormBase {
  public function getFormId() {
    return 'multistep_form_step5';
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'scholarship_beneficiary_profile/progress_bar';
    $form['progress_bar'] = $this->buildProgressBar(5, 7);
    $form['field_5'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field 5'),
      '#default_value' => $this->getData('field_5'),
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
    $this->saveData([ 'field_5' => $form_state->getValue('field_5') ]);
    $form_state->setRedirect('scholarship_beneficiary_profile.step6');
  }
}
