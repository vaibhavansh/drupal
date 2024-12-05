<?php

// namespace Drupal\scholarship_registration\Form;

// use Drupal\Core\Form\FormBase;
// use Drupal\Core\Form\FormStateInterface;
// use Drupal\user\Entity\User;

// class VolunteerAlumniForm extends FormBase
// {
//     /**
//      * {@inheritdoc}
//      */
//     public function getFormId()
//     {
//         return 'volunteer_alumni_registration_form';
//     }

//     /**
//      * {@inheritdoc}
//      */
//     public function buildForm(array $form, FormStateInterface $form_state)
//     {
//         // Checkbox to indicate if the user is an Alumni
//         $form['is_volunteer'] = [
//             '#type' => 'checkbox',
//             '#title' => 'I am an Alumni',
//             '#default_value' => 0,
//             '#ajax' => [
//                 'callback' => '::updateEmailField',
//                 'wrapper' => 'email-field-wrapper', // AJAX wrapper
//             ],
//         ];

//         // Determine the checkbox state
//         $is_volunteer = $form_state->getValue('is_volunteer', 0); // Default to unchecked

//         // Email field with dynamic title and default value
//         $form['email'] = [
//             '#type' => 'email',
//             '#default_value' => '',
//             '#title' => $is_volunteer ? 'Email ID' : 'Cybage Email ID',
//             '#required' => TRUE,
//             '#attributes' => [
//                 'placeholder' => $is_volunteer ? 'Enter your Email ID' : 'Enter your Cybage Email ID',
//             ],
//             '#prefix' => '<div id="email-field-wrapper">', // AJAX wrapper
//             '#suffix' => '</div>',
//         ];

//         // Unique ID field
//         $form['field_unique_id'] = [
//             '#type' => 'textfield',
//             '#title' => 'Unique ID',
//             '#required' => $is_volunteer,
//             '#attributes' => [
//                 'placeholder' => 'Enter your Unique ID',
//             ],
//             '#states' => [
//                 'visible' => [
//                     ':input[name="is_volunteer"]' => ['checked' => TRUE],
//                 ],
//             ],
//         ];

//         // Password field
//         $form['password'] = [
//             '#type' => 'password',
//             '#title' => 'Password',
//             '#required' => TRUE,
//             '#attributes' => [
//                 'placeholder' => 'Enter your password',
//             ],
//         ];

//         // CAPTCHA field
//         $form['captcha'] = [
//             '#type' => 'captcha',
//             '#captcha_type' => 'default',
//         ];

//         // Submit button
//         $form['submit'] = [
//             '#type' => 'submit',
//             '#value' => 'Register',
//         ];

//         return $form;
//     }

//     /**
//      * AJAX callback to update the email field dynamically.
//      */
//     public function updateEmailField(array &$form, FormStateInterface $form_state)
//     {
//         // Rebuild the email field
//         return $form['email'];
//     }

//     /**
//      * {@inheritdoc}
//      */
//     public function submitForm(array &$form, FormStateInterface $form_state)
//     {
//         // Logic to save the Beneficiary registration data.
//         $is_volunteer = $form_state->getValue('is_volunteer');
//         $email = $form_state->getValue('email');
//         $unique_id = $form_state->getValue('field_unique_id');
//         $pass = $form_state->getValue('password');

//         $this->register_user($is_volunteer, $email, $pass, $unique_id = null);

//         // Simulate saving data and sending an email.
//         \Drupal::messenger()->addMessage("Beneficiary with email $email successfully registered!");

//         // Redirect to the success page.
//         $form_state->setRedirect('scholarship_registration.beneficiary_success');
//     }

//     function register_user($is_volunteer, $email, $pass, $unique_id)
//     {
//         try {
//             $role = $unique_id ? 'alumani' : 'volunteer';
//             // Create the user account.
//             $user = User::create([
//                 'name' => $email,
//                 'field_unique_id' => $unique_id,
//                 'mail' => $email,
//                 'pass' => $pass,
//                 'status' => 1, // 1 for active, 0 for blocked.
//                 'roles' => $role, // Include default 'authenticated' role.
//             ]);

//             // Save the user account.
//             $user->save();

//             // Add a message for successful registration.
//             \Drupal::messenger()->addMessage("User $email has been registered successfully.");
//         } catch (\Exception $e) {
//             \Drupal::logger('custom_user_registration')->error($e->getMessage());
//             \Drupal::messenger()->addError('An error occurred while creating the user account.');
//         }
//     }
// }



<?php

/**
 * Implements hook_menu() to define custom pages.
 */
function scholarship_registration_menu() {
  $items = array();

  // Add a page for the scholarship registration form.
  $items['scholarship-registration'] = array(
    'title' => 'Scholarship Registration',
    'page callback' => 'scholarship_registration_form_page',
    'page arguments' => array(),
    'access callback' => 'user_access',
    'access arguments' => array('administer site configuration'),
    'type' => MENU_CALLBACK,
  );

  return $items;
}

/**
 * Callback function to render the form page.
 */
function scholarship_registration_form_page() {
  return drupal_render(scholarship_registration_form());
}

/**
 * Define the form structure with tabs and fields.
 */
function scholarship_registration_form() {
  $form = array();

  // Define the tabs for different sections of the form.
  $form['tabs'] = array(
    '#type' => 'tabs',
    '#theme' => 'tabs',
    '#links' => array(
      'personal' => array('title' => t('Personal Details'), 'url' => '#personal'),
      'academic' => array('title' => t('Academic Details'), 'url' => '#academic'),
      'family' => array('title' => t('Family Details'), 'url' => '#family'),
      'income' => array('title' => t('Income Details'), 'url' => '#income'),
      'health' => array('title' => t('Health Details'), 'url' => '#health'),
      'documents' => array('title' => t('Document Details'), 'url' => '#documents'),
    ),
  );

  // Personal Details
  $form['personal'] = array(
    '#type' => 'fieldset',
    '#title' => t('Personal Details'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    'first_name' => array(
      '#type' => 'textfield',
      '#title' => t('First Name'),
      '#required' => TRUE,
    ),
    'last_name' => array(
      '#type' => 'textfield',
      '#title' => t('Last Name'),
      '#required' => TRUE,
    ),
    'dob' => array(
      '#type' => 'date',
      '#title' => t('Date of Birth'),
      '#required' => TRUE,
    ),
  );

  // Academic Details
  $form['academic'] = array(
    '#type' => 'fieldset',
    '#title' => t('Academic Details'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    'school' => array(
      '#type' => 'textfield',
      '#title' => t('School Name'),
      '#required' => TRUE,
    ),
    'grade' => array(
      '#type' => 'textfield',
      '#title' => t('Grade'),
      '#required' => TRUE,
    ),
  );

  // Family Details
  $form['family'] = array(
    '#type' => 'fieldset',
    '#title' => t('Family Details'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    'father_name' => array(
      '#type' => 'textfield',
      '#title' => t('Father\'s Name'),
    ),
    'mother_name' => array(
      '#type' => 'textfield',
      '#title' => t('Mother\'s Name'),
    ),
  );

  // Income Details
  $form['income'] = array(
    '#type' => 'fieldset',
    '#title' => t('Income Details'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    'income' => array(
      '#type' => 'textfield',
      '#title' => t('Annual Income'),
      '#required' => TRUE,
    ),
  );

  // Health Details
  $form['health'] = array(
    '#type' => 'fieldset',
    '#title' => t('Health Details'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    'health_conditions' => array(
      '#type' => 'textarea',
      '#title' => t('Health Conditions'),
    ),
  );

  // Document Details
  $form['documents'] = array(
    '#type' => 'fieldset',
    '#title' => t('Document Details'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    'documents' => array(
      '#type' => 'file',
      '#title' => t('Upload Documents'),
    ),
  );

  // Submit button
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

/**
 * Form submission handler.
 */
function scholarship_registration_form_submit($form, &$form_state) {
  // Process form submission logic here
  drupal_set_message(t('Scholarship registration successful.'));
}

