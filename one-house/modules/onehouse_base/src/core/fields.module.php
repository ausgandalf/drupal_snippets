<?php

/**
 * @file
 * Onehouse_base module file for field related functions.
 */

/**
 * Implements template_preprocess_textarea().
 */
function onehouse_base_preprocess_field(&$variables) {
  $current_user = \Drupal::currentUser();
  $current_user_name = '';
  if ($current_user->isAnonymous()) {
    $current_user_name = 'guest';
  }
  else {
    $current_user_name = $current_user->getDisplayName();
  }

  $replace_patterns = [
    'search' => [
      '{{@@user_name@@}}',
    ],
    'replace' => [
      $current_user_name,
    ],
  ];

  if ($variables['field_name'] == 'field_ip_text_block_text') {
    for ($i = 0; $i < count($variables['items']); $i++) {
      if (isset($variables['items'][$i]['content']['#text'])) {
        $variables['items'][$i]['content']['#text'] = str_replace(
          $replace_patterns['search'],
          $replace_patterns['replace'],
          $variables['items'][$i]['content']['#text']
        );
      }
    }
  }
}
