<?php

/**
 * @file
 * umd_global_base module file for template related functions.
 */

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 *
 * Add tpl used to render file link in email.
 */
function umd_global_base_theme_suggestions_webform_element_document_file_alter(array &$suggestions, array &$variables) {
  $fragments = [];
  if (isset($variables['element']['#webform'])) {
    $fragments[] = $variables['element']['#webform'];
  }
  if (isset($variables['element']['#webform_key'])) {
    $fragments[] = $variables['element']['#webform_key'];
  }

  for ($i = 0; $i < count($fragments); $i++) {
    $output = array_slice($fragments, 0, $i + 1);
    $ntg = implode('__', $output);
    $suggestions[] = 'webform_element_document_file__' . $ntg;
  }
}

/**
 * Implements hook_theme_suggestions_alter().
 */
function umd_global_base_theme_suggestions_alter(array &$suggestions, array &$variables, $hook) {
  $exclude = [
    'field', 'node', 'html', 'username', 'paragraph', 'webform_submission_form', 'form_element', 'table', 'webform_composite_name',
    'input', 'form_element_label', 'container', 'select',
  ];
  if (in_array($hook, $exclude)) {
    return;
  }

  // dump($hook);
  // dump($variables);
}

/**
 * Implements hook_theme().
 *
 * Lets us define our paragraphs templates in this module.
 */
function umd_global_base_theme($existing, $type, $theme, $path) {
  return [
    'webform_element_document_file__bid' => [
      'base hook' => 'webform_element_document_file',
    ],
    'paragraph__ut_raw_html' => [
      'base hook' => 'paragraph',
    ],
  ];
}

/**
 * Remove N/A option for select list.
 */
function umd_global_base_options_list_alter(array &$options, array $context) {

}
