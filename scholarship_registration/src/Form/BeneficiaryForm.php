<?php

namespace Drupal\scholarship_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

class BeneficiaryForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'beneficiary_registration_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Add Bootstrap classes to the form
        $form['#attributes']['class'][] = 'needs-validation';
        $form['#attributes']['novalidate'] = 'novalidate'; // Disable browser's default validation.

        // Email field with placeholder
        $form['email'] = [
            '#type' => 'email',
            '#title' => 'Email ID',
            '#required' => TRUE,
            '#attributes' => [
                'placeholder' => 'Enter your email ID',
                'class' => ['form-control'], // Bootstrap class
            ],
        ];

        // Date of Birth field with placeholder
        $form['field_date_of_birth'] = [
            '#type' => 'date',
            '#title' => 'Date of Birth',
            '#required' => TRUE,
            '#attributes' => [
                'placeholder' => 'Select your date of birth',
                'class' => ['form-control'], // Bootstrap class
            ],
        ];

        // Password field with placeholder
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

        // Submit button with Bootstrap classes
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => 'Register',
            '#attributes' => [
                'class' => ['btn', 'btn-success'], // Bootstrap button class
            ],
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $email = $form_state->getValue('email');

        // Check if email already exists
        $existing_user = user_load_by_mail($email);
        if ($existing_user) {
            $form_state->setErrorByName('email', $this->t('The email address %email is already registered.', ['%email' => $email]));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Logic to save the Beneficiary registration data.
        $email = $form_state->getValue('email');
        $dob = $form_state->getValue('field_date_of_birth');
        $pass = $form_state->getValue('password');

        $this->register_user($email, $dob, $pass);

        // Simulate saving data and sending an email.
        \Drupal::messenger()->addMessage("Beneficiary with email $email successfully registered!");

        // Redirect to the success page.
        $form_state->setRedirect('scholarship_registration.beneficiary_success');
    }

    function register_user($email, $dob, $password, array $roles = [])
    {
        try {
            // Create the user account.
            $user = User::create([
                'name' => $email,
                'field_date_of_birth' => $dob,
                'mail' => $email,
                'pass' => $password,
                'status' => 1, // 1 for active, 0 for blocked.
                'roles' => array_merge(['beneficiary'], $roles), // Include default 'authenticated' role.
            ]);

            // Save the user account.
            $user->save();

            // Add a message for successful registration.
            \Drupal::messenger()->addMessage("User $email has been registered successfully.");

            // Optionally, send a welcome email or perform additional actions.
        } catch (\Exception $e) {
            \Drupal::logger('custom_user_registration')->error($e->getMessage());
            \Drupal::messenger()->addError('An error occurred while creating the user account.');
        }
    }
}
