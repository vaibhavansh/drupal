<?php
namespace Drupal\scholarship_beneficiary_profile\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Step 4 of the multi-step form.
 */
class Step4Form extends MultiStepFormBase {
  public function getFormId() {
    return 'multistep_form_step4';
  }
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'scholarship_beneficiary_profile/progress_bar';
    $form['progress_bar'] = $this->buildProgressBar(4, 7);
    $form['field_4'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field 4'),
      '#default_value' => $this->getData('field_4'),
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
    $this->saveData([ 'field_4' => $form_state->getValue('field_4') ]);
    $form_state->setRedirect('scholarship_beneficiary_profile.step5');
  }
}
