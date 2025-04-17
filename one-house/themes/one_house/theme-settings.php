<?php

/**
 * @file
 * One House (one_house), add custom theme settings options here.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function one_house_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {

  // Close all settings first.
  foreach ($form as $key => $value) {
    if (isset($form[$key]['#open'])) {
      $form[$key]['#open'] = FALSE;
    }
  }

  //
  // Bidder status message
  // .
  $form['one_house_message_settings'] = [
    '#type' => 'details',
    '#title' => t('Message Settings'),
    '#description' => t('Settings for messages appeared in the system.'),
    "#open" => TRUE,
    "#weight" => -8,
  ];

  $mapper = one_house_load_get_bidder_status_message_mapper();

  foreach ($mapper as $key => $label) {
    $form['one_house_message_settings']['one_house_bidder_status_' . $key . '_message'] = [
      '#type' => 'textarea',
      '#title' => t('Message for bidder status - @status', ['@status' => $label]),
      '#default_value' => one_house_theme_get_setting('one_house_bidder_status_' . $key . '_message'),
      '#description' => t('Add message for bidder status - @status', ['@status' => $label]),
    ];
  }

  //
  // Social Settings
  // .
  $form['one_house_social_settings'] = [
    '#type' => 'details',
    '#title' => t('Social Settings'),
    '#description' => t('Settings for social links and privacy consent description.'),
    "#open" => TRUE,
    "#weight" => -5,
  ];

  $mapper = one_house_load_get_social_mapper();

  foreach ($mapper as $key => $label) {
    $form['one_house_social_settings']['one_house_' . $key . '_link'] = [
      '#type' => 'url',
      '#title' => t('@label Link', ['@label' => $label]),
      '#default_value' => one_house_theme_get_setting('one_house_' . $key . '_link'),
      '#description' => t('Add @label url.', ['@label' => $label]),
    ];
  }

  //
  // Legal Settings
  // .
  $form['one_house_legal_settings'] = [
    '#type' => 'details',
    '#title' => t('Privacy/Legal Settings'),
    '#description' => t('Settings for privacy and legal information including full address, contact number, copyright description, etc.'),
    "#open" => TRUE,
    "#weight" => -6,
  ];

  $form['one_house_legal_settings']['one_house_full_address'] = [
    '#type' => 'textfield',
    '#title' => t('Full Address'),
    '#default_value' => one_house_theme_get_setting('one_house_full_address'),
    '#description' => t('Add full address line.'),
  ];

  $form['one_house_legal_settings']['one_house_phone_numbers'] = [
    '#type' => 'textarea',
    '#title' => t('Phone Numbers'),
    '#default_value' => one_house_theme_get_setting('one_house_phone_numbers'),
    '#description' => t('Add phone numbers separated by new line.'),
    '#rows' => 3,
    '#cols' => 40,
  ];

  $form['one_house_legal_settings']['one_house_fax_numbers'] = [
    '#type' => 'textarea',
    '#title' => t('Fax Numbers'),
    '#default_value' => one_house_theme_get_setting('one_house_fax_numbers'),
    '#description' => t('Add fax numbers separated by new line.'),
    '#rows' => 3,
    '#cols' => 40,
  ];

  $form['one_house_legal_settings']['one_house_copyright_text'] = [
    '#type' => 'textfield',
    '#title' => t('Copyright Text'),
    '#default_value' => one_house_theme_get_setting('one_house_copyright_text'),
    '#description' => t('Add full address line.'),
  ];

  $form['one_house_legal_settings']['one_house_privacy_consent_description'] = [
    '#type' => 'text_format',
    '#title' => t('Privacy Consent description'),
    '#default_value' => one_house_theme_get_setting('one_house_privacy_consent_description'),
    '#description' => t('Add the description shown in Privacy Consent popup.'),
  ];

  //
  // Login Settings
  // .
  $form['one_house_login_settings'] = [
    '#type' => 'details',
    '#title' => t('Login / Sign up Settings'),
    '#description' => t('Settings for login/signup form.'),
    "#open" => TRUE,
    "#weight" => -4,
  ];

  $form['one_house_login_settings']['one_house_accept_terms_of_service_description'] = [
    '#type' => 'text_format',
    '#title' => t('Label for <em>accept terms of service</em> checkbox'),
    '#default_value' => one_house_theme_get_setting('one_house_accept_terms_of_service_description'),
    '#description' => '',
  ];

  $form['one_house_login_settings']['one_house_login_guide'] = [
    '#type' => 'text_format',
    '#title' => t('Guide shown in <em>Log in</em> page'),
    '#default_value' => one_house_theme_get_setting('one_house_login_guide'),
    '#description' => '',
  ];

  $form['one_house_login_settings']['one_house_signup_guide'] = [
    '#type' => 'text_format',
    '#title' => t('Guide shown in <em>Sign up</em> page'),
    '#default_value' => one_house_theme_get_setting('one_house_signup_guide'),
    '#description' => '',
  ];

  $form['one_house_login_settings']['one_house_reset_password_guide'] = [
    '#type' => 'text_format',
    '#title' => t('Guide shown in <em>Reset Password</em> page'),
    '#default_value' => one_house_theme_get_setting('one_house_reset_password_guide'),
    '#description' => '',
  ];

  //
  // Miscellaneous Settings
  // .
  $form['one_house_misc_settings'] = [
    '#type' => 'details',
    '#title' => t('Miscellaneous Settings'),
    '#description' => '',
    "#open" => TRUE,
    "#weight" => -4,
  ];

  //
  // Miscellaneous/Thumbnails Settings
  // .
  $form['one_house_misc_settings']['one_house_thumbnail_settings'] = [
    '#type' => 'details',
    '#title' => t('Default Thumbnail Settings'),
    '#description' => '',
    "#open" => TRUE,
    "#weight" => 0,
  ];
  foreach (['share', 'default'] as $thmbkey) {
    $form['one_house_misc_settings']['one_house_thumbnail_settings']['one_house_default_thumbnail_path_' . $thmbkey] = [
      '#type' => 'textfield',
      '#title' => t("Path to default thumbnail for [@thmbkey] case", ['@thmbkey' => $thmbkey]),
      '#description' => t('Examples: default_thumbnail.png (for a file in the public filesystem), public://favicon.ico, or themes/custom/one_house/default_thumbnail.png.'),
      '#default_value' => one_house_theme_get_setting('one_house_default_thumbnail_path_' . $thmbkey),
    ];
  }

  //
  // Miscellaneous/Paths Settings
  // .
  /*
  $form['one_house_misc_settings']['one_house_path_settings'] = [
  '#type' => 'details',
  '#title' => t('Path Information'),
  '#description' => '',
  "#open" => TRUE,
  "#weight" => 0,
  ];
  $form['one_house_misc_settings']['one_house_path_settings']['one_house_path_bidder_match'] = [
  '#type' => 'textfield',
  '#title' => t("One House Match page"),
  '#description' => t('Type the path to One House Match page where bidder can set participation option. Examples: /something'),
  '#default_value' => one_house_theme_get_setting('one_house_path_bidder_match'),
  ];
   */

}
