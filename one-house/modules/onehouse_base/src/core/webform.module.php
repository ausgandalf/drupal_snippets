<?php

/**
 * @file
 * Onehouse_base module file for web-form related functions.
 */

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Implements hook_form_BASE_FORM_ID_alter().
 */
function onehouse_base_form_webform_submission_bid_form_alter(&$form, &$form_state, $form_id) {

}

/**
 * Implements hook_webform_submission_presave().
 */
function onehouse_base_webform_submission_presave($submission) {

  if ($submission->isNew()) {
    $emails = $submission->getElementData('email_address');
    $emails = $submission->setElementData('email_multiple', implode(",", $emails));
  }

  $current_user = \Drupal::currentUser();
  $user = \Drupal::entityTypeManager()
    ->getStorage('user')
    ->load($current_user->id());
  //
  // Change bidder status
  // .
  // Set bidder status to Pending.
  $user->set('field_user_bidder_status', 1);
  $user->set('field_user_accept', 1);
  //
  // Set last form submission date and bidder status changed date
  // .
  $now = new DrupalDateTime();
  $now->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
  $now_str = $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
  $user->set('field_user_last_form_on', $now_str);
  $user->set('field_user_bdr_stts_changed_on', $now_str);

  $violations = $user->validate();

  if (count($violations) === 0) {
    $user->save();
  }
}
