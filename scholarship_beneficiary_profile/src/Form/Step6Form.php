<?php
namespace Drupal\scholarship_beneficiary_profile\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Step 6 of the multi-step form.
 */
class Step6Form extends MultiStepFormBase {
  public function getFormId() {
    return 'multistep_form_step6';
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'scholarship_beneficiary_profile/progress_bar';
    $form['progress_bar'] = $this->buildProgressBar(6, 7);
    $form['field_6'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field 6'),
      '#default_value' => $this->getData('field_6'),
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
    $this->saveData([ 'field_6' => $form_state->getValue('field_6') ]);
    $form_state->setRedirect('scholarship_beneficiary_profile.step7');
  }
}
