<?php

/**
 * @file
 * Onehouse_base module file for sign in/sign up/forgot password related functions.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\Core\Url;

/**
 * Implements hook_user_presave().
 */
function onehouse_base_user_presave(EntityInterface $entity) {
  if ($entity->isNew()) {
    $entity->addRole('bidder');
  }
  else {
    $old_status = $entity->original->field_user_bidder_status->value;
    $new_status = $entity->field_user_bidder_status->value;
    if (is_null($old_status) || ($old_status != $new_status)) {
      $old_status_date = $entity->original->field_user_bdr_stts_changed_on->value;
      $new_status_date = $entity->field_user_bdr_stts_changed_on->value;
      // Only if user does not set it up, then automatically change the value.
      if (is_null($old_status_date) || ($old_status_date == $new_status_date)) {
        $now = new DrupalDateTime();
        $now->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
        $now_str = $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
        $entity->set('field_user_bdr_stts_changed_on', $now_str);
      }
    }
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function onehouse_base_form_user_login_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  $form['name']['#weight'] = 0;
  $form['pass']['#weight'] = 0;
  $form['actions']['submit']['#value'] = t('Sign In');
  $form['actions']['#weight'] = 20;

  $form['reset_link'] = [
    '#type' => 'fieldset',
    '#title' => '',
    '#weight' => 0,
    "#attributes" => [
      "class" => [
        'fieldset_user_link',
        'fieldset_reset_link',
      ],
    ],
    '#markup' => t('<a href="/user/password">Forgot Password</a>'),
  ];

  $form['signup_link'] = [
    '#type' => 'fieldset',
    '#title' => '',
    '#weight' => 25,
    "#attributes" => [
      "class" => [
        'fieldset_user_link',
        'fieldset_signup_link',
      ],
    ],
    '#markup' => t('Don’t have an account? <a href="/user/register">Sign Up</a>'),
  ];
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function onehouse_base_form_user_register_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  if (isset($form['field_user_accept'])) {
    if (function_exists('one_house_theme_get_setting')) {
      $form['field_user_accept']['widget']['value']['#title'] = one_house_theme_get_setting('one_house_accept_terms_of_service_description');
    }
    else {
      $form['field_user_accept']['#access'] = FALSE;
    }
  }

  $form['actions']['submit']['#value'] = t('Sign Up');
  $form['actions']['#weight'] = 20;

  $form['login_link'] = [
    '#type' => 'fieldset',
    '#title' => '',
    '#weight' => 25,
    "#attributes" => [
      "class" => [
        'fieldset_user_link',
        'fieldset_login_link',
      ],
    ],
    '#markup' => t('Already have an account? <a href="/user/login">Sign In</a>'),
  ];
  $form['actions']['submit']['#submit'][] = 'onehouse_base_after_user_register';
}

/**
 * Implements hook_after_user_register().
 */
function onehouse_base_after_user_register($form, &$form_state) {
  $url = Url::fromUri('internal:/user');
  $form_state->setRedirectUrl($url);
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function onehouse_base_form_user_form_alter(&$form, $form_state, $form_id) {
  if (($form_id == 'user_form') || ($form_id == 'user_bidder_options_form')) {
    $is_landing_selector = '[name="field_user_participate"]';
    $visible_mapper = [
    ['field_user_zip_codes', [$is_landing_selector => ['value' => 1]]],
    ];

    foreach ($visible_mapper as $vm) {
      $form[$vm[0]]['#states'] = [
        'visible' => $vm[1],
      ];
    }
  }

  if ($form_id == 'user_bidder_options_form') {
    if (isset($form['actions']['delete'])) {
      unset($form['actions']['delete']);
    }
    $form['actions']['cancel'] = [
      '#type' => 'button',
      '#value' => t('cancel'),
      '#button_type' => 'primary',
      '#weight' => 5,
      '#attributes' => [
        'class' => [
          'button',
          'btn_cancel--bidder_options',
        ],
        'onclick' => 'cancel_bidder_option(this); return false;',
      ],
      '#executes_submit_callback' => TRUE,
    ];
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function onehouse_base_form_change_pwd_form_alter(&$form, $form_state, $form_id) {
  $form['actions']['submit']['#value'] = t('Save Changes');
}

/**
 * Implements hook_entity_type_build().
 */
function onehouse_base_entity_type_build(array &$entity_types) {
  $entity_types['user']->setFormClass('bidder_options', 'Drupal\user\ProfileForm');
}

/**
 * Implements hook_element_info_alter().
 */
function onehouse_base_element_info_alter(array &$types) {
  if (isset($types['password'])) {
    $types['password']['#process'][] = 'onehouse_base_change_title';
  }
}

/**
 * Change the title of the form element.
 */
function onehouse_base_change_title($element, $form_state, $form) {
  if (isset($form['#form_id']) && isset($element['#name'])) {
    if (($form['#form_id'] == 'change_pwd_form') && ($element['#name'] == 'pass[pass1]')) {
      $element['#title'] = t('New Password');
    }
  }
  return $element;
}
