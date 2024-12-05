<?php

namespace Drupal\scholarship_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

class VolunteerAlumniForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'volunteer_alumni_registration_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Add Bootstrap classes to the form
        $form['#attributes']['class'][] = 'needs-validation';
        $form['#attributes']['novalidate'] = 'novalidate'; // Disable browser's default validation.

        // Checkbox to indicate if the user is an Alumni
        $form['is_volunteer'] = [
            '#type' => 'checkbox',
            '#title' => 'I am an Alumni',
            '#default_value' => 0,
            '#ajax' => [
                'callback' => '::updateEmailField',
                'wrapper' => 'email-field-wrapper', // AJAX wrapper
            ],
            '#attributes' => [
                'class' => ['form-check-input'], // Bootstrap class for checkboxes
            ],
        ];

        // Determine the checkbox state
        $is_volunteer = $form_state->getValue('is_volunteer', 0);

        // Email field with dynamic title and placeholder
        $form['email'] = [
            '#type' => 'email',
            '#title' => $is_volunteer ? 'Email ID' : 'Cybage Email ID',
            '#default_value' => '',
            '#required' => TRUE,
            '#attributes' => [
                'placeholder' => $is_volunteer ? 'Enter your Email ID' : 'Enter your Cybage Email ID',
                'class' => ['form-control'], // Bootstrap class
            ],
            '#prefix' => '<div id="email-field-wrapper">', // AJAX wrapper
            '#suffix' => '</div>',
        ];

        // Unique ID field
        $form['field_unique_id'] = [
            '#type' => 'textfield',
            '#title' => 'Unique ID',
            '#required' => $is_volunteer,
            '#attributes' => [
                'placeholder' => 'Enter your Unique ID',
                'class' => ['form-control'], // Bootstrap class
            ],
            '#states' => [
                'visible' => [
                    ':input[name="is_volunteer"]' => ['checked' => TRUE],
                ],
            ],
        ];

        // Password field
        $form['password'] = [
            '#type' => 'password',
            '#title' => 'Password',
            '#required' => TRUE,
            '#attributes' => [
                'placeholder' => 'Enter your password',
                'class' => ['form-control'], // Bootstrap class
            ],
        ];

        // CAPTCHA field
        $form['captcha'] = [
            '#type' => 'captcha',
            '#captcha_type' => 'default',
        ];

        // Submit button
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => 'Register',
            '#attributes' => [
                'class' => ['btn', 'btn-primary'], // Bootstrap button class
            ],
        ];

        return $form;
    }

    /**
     * AJAX callback to update the email field dynamically.
     */
    public function updateEmailField(array &$form, FormStateInterface $form_state)
    {
        return $form['email'];
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $is_volunteer = $form_state->getValue('is_volunteer');
        $email = $form_state->getValue('email');
        $unique_id = $form_state->getValue('field_unique_id');

        // Validation: If not a volunteer, email must contain @cybage.com
        if (!$is_volunteer && strpos($email, '@cybage.com') === FALSE) {
            $form_state->setErrorByName('email', $this->t('Email must contain @cybage.com if you are not an Alumni.'));
        }

        // Validation: If a volunteer (Alumni), Unique ID must be provided
        if ($is_volunteer && empty($unique_id)) {
            $form_state->setErrorByName('field_unique_id', $this->t('Unique ID is required if you are an Alumni.'));
        }

        // Check if email already exists
        $existing_user_by_email = user_load_by_mail($email);
        if ($existing_user_by_email) {
            $form_state->setErrorByName('email', $this->t('The email address %email is already registered.', ['%email' => $email]));
        }

        // Check if unique_id already exists
        if ($unique_id) {
            $existing_user_by_unique_id = \Drupal::entityTypeManager()
                ->getStorage('user')
                ->loadByProperties(['field_unique_id' => $unique_id]);

            if (!empty($existing_user_by_unique_id)) {
                $form_state->setErrorByName('field_unique_id', $this->t('The unique ID %unique_id is already registered.', ['%unique_id' => $unique_id]));
            }
        }
    }



    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Logic to save the Beneficiary registration data.
        $is_volunteer = $form_state->getValue('is_volunteer');
        $email = $form_state->getValue('email');
        $unique_id = $form_state->getValue('field_unique_id');
        $pass = $form_state->getValue('password');

        $this->register_user($is_volunteer, $email, $pass, $unique_id);

        \Drupal::messenger()->addMessage("Beneficiary with email $email successfully registered!");

        $form_state->setRedirect('scholarship_registration.beneficiary_success');
    }

    function register_user($is_volunteer, $email, $pass, $unique_id)
    {
        try {
            $role = $is_volunteer ? 'csr' : 'volunteer';
            $user = User::create([
                'name' => $email,
                'field_unique_id' => $unique_id,
                'mail' => $email,
                'pass' => $pass,
                'status' => 1,
                'roles' => $role,
            ]);

            $user->save();

            \Drupal::messenger()->addMessage("User $email has been registered successfully.");
        } catch (\Exception $e) {
            \Drupal::logger('custom_user_registration')->error($e->getMessage());
            \Drupal::messenger()->addError('An error occurred while creating the user account.');
        }
    }
}
