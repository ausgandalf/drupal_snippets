<?php

/**
 * @file
 * umd_global_base module file for paragraph related functions.
 */

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Paragraphs form alter hooks.
 */

/**
 * Implements hook_field_widget_single_element_WIDGET_TYPE_form_alter().
 */
function umd_global_base_field_widget_single_element_paragraphs_form_alter(array &$element, FormStateInterface &$form_state, array $context) {
  umd_global_base_handle_toggling($element, $form_state, $context);
}

/**
 * Implements hook_field_widget_complete_WIDGET_TYPE_form_alter().
 */
function umd_global_base_field_widget_complete_paragraphs_form_alter(array &$element, FormStateInterface &$form_state, array $context) {
  umd_global_base_handle_toggling($element, $form_state, $context);
}

/**
 * Implements hook_preprocess_paragraph().
 *
 * For every paragraph, attach parent field name and parent entity type name.
 */
function umd_global_base_preprocess_paragraph(&$variables) {

  $paragraph = $variables['paragraph'];
  $parentFieldName = $paragraph->get('parent_field_name')->value;
  $variables['_parent_field'] = $parentFieldName;

  $node = \Drupal::routeMatch()->getParameter('node');
  if ($node) {
    $variables['_node'] = $node;
  }

  $pageType = umd_global_base_get_node_type_id();
  $variables['_page_type'] = $pageType;

  $parentEntity = $paragraph->getParentEntity();
  if ($parentEntity) {
    if ($parentEntity instanceof Paragraph) {

      $parentType = $parentEntity->getType();
      $variables['_parent_type'] = $parentType;
    }
  }
}

/**
 * Get parent field name of the paragraph element.
 */
function umd_global_base_get_form_parent_field_name($element) {
  if (isset($element['subform']['#parents'])) {
    $len = count($element['subform']['#parents']);
    $pos = $len - 3;

    return ($pos < 0) ? '' : $element['subform']['#parents'][$pos];
  }
  return '';
}

/**
 * Get paragraph element selector, which can be used for dynamic form altering logic.
 */
function umd_global_base_get_form_element_selector($element, $dependee_field_name, $type = '') {

  $parentNamePart = '';

  if (isset($element['subform']['#parents'])) {
    $parentsArray = $element['subform']['#parents'];
    $parentNamePart = array_shift($parentsArray) . '[' . implode('][', $parentsArray) . ']';
  }

  $selector = $parentNamePart . '[' . $dependee_field_name . ']';

  if ($type == 'checkbox') {
    $selector .= '[value]';
  }

  $selector = '[name="' . $selector . '"]';

  return $selector;
}

/**
 * Field visibility toggling in paragraph for form alter.
 */
function umd_global_base_form_toggling_visibility(&$element, $dependee_field_name, $mapper, $is_visible = TRUE) {
  $key = $is_visible ? 'visible' : 'invisible';
  $selector = umd_global_base_get_form_element_selector($element, $dependee_field_name);

  foreach ($mapper as $k => $v) {
    if (!isset($element['subform'][$k])) {
      continue;
    }
    $element['subform'][$k]['#states'] = [
      $key => [
        $selector => ['value' => $v],
      ],
    ];
  }
}

/**
 * Add toggle logic.
 */
function umd_global_base_handle_toggling(&$element, &$form_state, $context) {
  if (!isset($element['#paragraph_type'])) {
    return;
  }

  $type = $element['#paragraph_type'];
}