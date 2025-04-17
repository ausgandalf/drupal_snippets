<?php

/**
 * @file
 * Onehouse_base module file for paragraph related functions.
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
function onehouse_base_field_widget_single_element_paragraphs_form_alter(array &$element, FormStateInterface &$form_state, array $context) {
  onehouse_base_show_paragraph_id($element, $form_state, $context);
  onehouse_base_handle_toggling($element, $form_state, $context);
}

/**
 * Implements hook_field_widget_complete_WIDGET_TYPE_form_alter().
 */
function onehouse_base_field_widget_complete_paragraphs_form_alter(array &$element, FormStateInterface &$form_state, array $context) {
  onehouse_base_show_paragraph_id($element, $form_state, $context);
  onehouse_base_handle_toggling($element, $form_state, $context);
}

/**
 * Implements hook_preprocess_paragraph().
 *
 * For every paragraph, attach parent field name and parent entity type name.
 */
function onehouse_base_preprocess_paragraph(&$variables) {

  $paragraph = $variables['paragraph'];
  $parentFieldName = $paragraph->get('parent_field_name')->value;
  $variables['_parent_field'] = $parentFieldName;

  $node = \Drupal::routeMatch()->getParameter('node');
  if ($node) {
    $variables['_node'] = $node;
  }

  $pageType = onehouse_base_get_node_type_id();
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
function onehouse_base_get_form_parent_field_name($element) {
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
function onehouse_base_get_form_element_selector($element, $dependee_field_name, $type = '') {

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
 * Showing HTML Element Selector of each paragraph in admin panel.
 */
function onehouse_base_show_paragraph_id(&$element, &$form_state, $context) {
  onehouse_base_display_paragraph_id($element, $form_state, $context);
}

/**
 * Core function to show HTML Element Selector of each paragraph in admin panel.
 */
function onehouse_base_display_paragraph_id(&$element, &$form_state, $context) {
  $paraId = '';
  $fieldType = $element['#paragraph_type'] ?? NULL;

  $allowedParagraphs = [
    'ip_accordion',
    'ip_bidder_status',
    'ip_blockquote',
    'ip_button_set',
    'ip_card_group',
    'ip_carousel',
    'ip_cta',
    'ip_date_board',
    'ip_featured_block',
    'ip_featured_text',
    'ip_icon',
    'ip_large_image',
    'ip_link_block',
    'ip_match_suggestion',
    'ip_multi_column',
    'ip_profile_form',
    'ip_property_finder',
    'ip_property_sold_record',
    'ip_steps_block',
    'ip_table',
    'ip_text_block',
    'ip_video',
    'ip_views_embed',
    'ip_webform_embed',
  ];

  $currentTheme = \Drupal::config('system.theme')->get('default');
  if ($currentTheme == 'one_house') {
    $allowedParagraphs[] = 'ip_table';
  }

  if (is_null($fieldType) || !in_array($fieldType, $allowedParagraphs)) {
    return;
  }

  $itemInfo = $context['items'][$element["#delta"]]->getValue();
  if (isset($itemInfo['target_id'])) {
    $paraId = $itemInfo['target_id'];
  }

  if ($paraId == '') {
    $paraId = '<strong><i> N/A (please save your changes first.)</i></strong>';
  }
  else {
    $paraId = '<strong>#ip_paragraph_adv-' . $paraId . '</strong>';
  }

  $markup = <<<Markup
  <i>Selector:</i> {$paraId}
Markup;

  if (isset($element[$fieldType . '_pro_tips'])) {
    $element[$fieldType . 'pro_tips']['#markup'] = $markup . '<br/>' . $element[$fieldType . '_tips']['#markup'];
  }
  else {
    $element[$fieldType . '_pro_tips'] = [
      '#type' => 'fieldset',
      '#title' => '',
      '#weight' => -1100,
      "#attributes" => [
        "class" => [
          'fieldset_ip_pro_tips',
        ],
      ],
      '#markup' => $markup,
    ];
  }
}

/**
 * Field visibility toggling in paragraph for form alter.
 */
function onehouse_base_form_toggling_visibility(&$element, $dependee_field_name, $mapper, $is_visible = TRUE) {
  $key = $is_visible ? 'visible' : 'invisible';
  $selector = onehouse_base_get_form_element_selector($element, $dependee_field_name);

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
function onehouse_base_handle_toggling(&$element, &$form_state, $context) {
  if (!isset($element['#paragraph_type'])) {
    return;
  }

  $type = $element['#paragraph_type'];

  if ($type == 'ip_link_block') {
    // Hide background image option as we use default one set from theme settings page.
    $element['subform']['field_ip_link_block_link_style']['#access'] = FALSE;
    $element['subform']['field_ip_link_block_align']['#access'] = FALSE;
  }
  elseif ($type == 'ip_cta') {

    $element['subform']['field_ip_cta_image']['#access'] = FALSE;
    $element['subform']['field_ip_cta_hide_bg']['#access'] = FALSE;
    $element['subform']['field_ip_cta_size']['#access'] = FALSE;

  }
  elseif ($type == 'ip_video') {

    onehouse_base_form_toggling_visibility($element, 'field_ip_video_type', [
      'field_ip_video_own' => '0',
      'field_ip_video_youtube_code' => '1',
      'field_ip_video_vimeo_code' => '2',
    ]);

  }
  elseif ($type == 'ip_text_block') {

    // Hide link type.
    $element['subform']['field_ip_text_block_link_type']['#access'] = FALSE;

    $hideTitle = TRUE;
    $hideLinks = TRUE;

    $parentField = onehouse_base_get_form_parent_field_name($element);

    if ($parentField == 'field_ip_sub_item_content') {
      if (in_array('field_ip_tabs_items', $element['#field_parents'])) {
        $hideTitle = FALSE;
        $hideLinks = FALSE;
      }
    }
    elseif ($parentField == 'field_ip_featured_block_content') {
      $hideTitle = FALSE;
      $hideLinks = FALSE;
    }

    if ($hideTitle) {
      $element['subform']['field_ip_text_block_title']['#access'] = FALSE;
    }
    if ($hideLinks) {
      $element['subform']['field_ip_text_block_link_type']['#access'] = FALSE;
      $element['subform']['field_ip_text_block_links']['#access'] = FALSE;
    }

  }
  elseif ($type == 'ip_table') {
    // Hide caption for table
    // $element['subform']['field_ip_table_table']['widget'][0]['caption']['#access'] = false;.
  }
  elseif ($type == 'ip_icon') {

    onehouse_base_form_toggling_visibility($element, 'field_ip_icon_type', [
      'field_ip_icon_image' => '0',
      'field_ip_icon_svg' => '1',
    ]);

  }
  elseif ($type == 'ip_featured_block') {

    $element['subform']['field_ip_featured_block_type']['#access'] = FALSE;
    $element['subform']['field_ip_featured_block_f_label']['#access'] = FALSE;
    $element['subform']['field_ip_featured_block_f_value']['#access'] = FALSE;

  }
  elseif ($type == 'ip_card_item') {

    $parentField = onehouse_base_get_form_parent_field_name($element);
    if ($parentField == 'field_ip_carousel_items') {
      // $element['subform']['field_ip_card_item_title']['#access'] = false;
      // $element['subform']['field_ip_card_item_link']['#access'] = false;
    }

  }
  elseif ($type == 'ip_col2_text') {
    $parentField = onehouse_base_get_form_parent_field_name($element);
    if ($parentField == 'field_programs_about') {
      $element['subform']['field_ip_col2_text_title']['#access'] = FALSE;
    }
  }
  elseif ($type == 'ip_carousel') {
    $element['subform']['field_ip_carousel_items']['widget']['add_more']['add_more_button_ip_card_item']['#value'] = 'Add carousel item';
  }
  elseif ($type == 'ip_accordion') {
    $element['subform']['field_ip_accordion_items']['widget']['add_more']['add_more_button_ip_sub_item']['#value'] = 'Add accordion item';
  }
}

/**
 * Implements hook_preprocess_paragraph().
 *
 * The paragrash preprocess for ip_tabs.
 *
 * 3|Bidder Form
 * 0|Bidder Options
 * 1|Change Password
 * 2|Full
 */
function onehouse_base_preprocess_paragraph__ip_profile_form(&$variables) {
  $p = $variables['paragraph'];
  //
  // Set current user
  // .
  $current_user = \Drupal::currentUser();
  $user = \Drupal::entityTypeManager()
    ->getStorage('user')
    ->load($current_user->id());

  $variables['_user'] = $user;
  $variables['_bidder_status'] = $user->field_user_bidder_status->value;

  //
  // Set form specs
  // .
  $form_type_id = $p->field_ip_profile_form_type->value;
  $type_mapper = [
    3 => 'bidder_form',
    0 => 'bidder_options',
    1 => 'password',
    2 => 'default',
  ];

  $form_type = $type_mapper[$form_type_id] ?? 'bidder_options';
  $variables['_form_type'] = $form_type;
}

/**
 * Implements hook_preprocess_paragraph().
 *
 * The paragrash preprocess for ip_match_suggestion.
 */
function onehouse_base_preprocess_paragraph__ip_match_suggestion(&$variables) {
  $zipcodes = [];

  $current_user = \Drupal::currentUser();
  $user = \Drupal::entityTypeManager()
    ->getStorage('user')
    ->load($current_user->id());

  if ($user->field_user_participate->value == '1') {
    foreach ($user->field_user_zip_codes as $zc) {
      $zipcodes[] = $zc->target_id;
    }
  }

  $variables['_user'] = $user;
  $variables['_zipcodes'] = $zipcodes;
}

/**
 * Implements hook_preprocess_paragraph().
 *
 * The paragrash preprocess for ip_card_item.
 */
function onehouse_base_preprocess_paragraph__ip_card_item(&$variables) {
  $p = $variables['paragraph'];
  $parentEntity = $p->getParentEntity();
  $cardType = 'card';

  if ($parentEntity) {
    $parentType = $parentEntity->getType();
    if ($parentType == 'ip_card_group') {
      if ($parentEntity->field_ip_card_group_type->value == 1) {
        $cardType = 'list';
      }
    }
    elseif ($parentType == 'ip_carousel') {
      $cardType = 'carousel';
    }
  }

  $variables['_card_type'] = $cardType;
}

/**
 * Implements hook_preprocess_paragraph().
 *
 * The paragrash preprocess for ip_card_item.
 */
function onehouse_base_preprocess_paragraph__ip_bidder_status(&$variables) {

  $current_user = \Drupal::currentUser();
  $user = \Drupal::entityTypeManager()
    ->getStorage('user')
    ->load($current_user->id());

  $variables['_user'] = $user;
  $variables['_bidder_status'] = $user->field_user_bidder_status->value ? $user->field_user_bidder_status->value : 0;
  $variables['_bidder_status_note'] = $user->field_user_bidder_status_notes->value ? $user->field_user_bidder_status_notes->value : '';

}

/**
 * Implements hook_preprocess_paragraph().
 *
 * The paragrash preprocess for ip_ne_feeds.
 */
function onehouse_base_preprocess_paragraph__ip_ne_feeds(&$variables) {

  $p = $variables['paragraph'];

  //
  // Load news categories set in paragraph.
  //
  $tids = [];
  foreach ($p->field_ip_ne_feeds_news_tags as $term) {
    $tids[] = $term->target_id;
  }

  // Load recent news in given categories.
  $conditions = [];
  $conditions[] = ['type', 'article', '='];
  if (count($tids) > 0) {
    $conditions[] = ['field_tags', $tids, 'in'];
  }
  $sorts = [['field_news_date', 'DESC']];
  $news = onehouse_base_load_entities($conditions, $sorts, [0, 5], TRUE, ['node', ['card', 'smallcard']]);

  // Load instagram links if ever set.
  $instas = [];
  foreach ($p->field_ip_ne_feeds_news_insta as $inst) {
    $instas[] = $inst;
  }

  // Mix news and instagram links.
  // 0: news, 1: instagram.
  $posInfo = [0, 1, 0, 0, 1];
  $newsFeed = [];
  $newsInd = 0;
  $instaInd = 0;
  for ($i = 0; $i < 5; $i++) {
    $itemFilled = FALSE;

    if ($posInfo[$i] == 0) {
      if (isset($news[$newsInd])) {
        $newsFeed[] = $news[$newsInd];
        $newsInd++;
        $itemFilled = TRUE;
      }
    }

    if (!$itemFilled) {
      if (isset($instas[$instaInd])) {
        $newsFeed[] = $variables['content']['field_ip_ne_feeds_news_insta'][$instaInd];
        $instaInd++;
      }
    }

    if (count($newsFeed) > 4) {
      break;
    }
  }

  // Set news feed.
  $variables['_news_feed'] = $newsFeed;

  //
  // Load events categories set in paragraph.
  //
  $tids = [];
  foreach ($p->field_ip_ne_feeds_event_tags as $term) {
    $tids[] = $term->target_id;
  }

  $variables['_events_categories'] = $tids;

}

/**
 * Implements hook_preprocess_paragraph().
 *
 * The paragrash preprocess for ip_ne_feeds.
 */
function onehouse_base_preprocess_paragraph__ip_events_feed(&$variables) {

  $p = $variables['paragraph'];

  //
  // Load news categories set in paragraph.
  //
  $tids = [];
  foreach ($p->field_ip_events_feed_category as $term) {
    $tids[] = $term->target_id;
  }

  $length = $p->field_ip_events_feed_length->value;
  if (!$length) {
    $length = 2;
  }

  // Load recent news in given categories.
  $conditions = [];
  $conditions[] = ['type', 'event', '='];
  if (count($tids) > 0) {
    $conditions[] = ['field_tags', $tids, 'in'];
  }

  $date = new DrupalDateTime('today');
  $date->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
  $formatted = $date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
  $conditions[] = ['field_event_date', $formatted, '>='];

  $sorts = [['field_event_date', 'ASC']];
  $events = onehouse_base_load_entities($conditions, $sorts, [0, $length], TRUE, ['node', ['card']]);

  // Set news feed.
  $variables['_events_feed'] = $events;
  // Set column count.
  $cnt = count($events);
  $cols = 2;
  if ($cnt > 2) {
    if (($cnt % 3) == 0) {
      $cols = 3;
    }
    elseif (($cnt % 4) == 0) {
      $cols = 4;
    }
  }
  $variables['_feed_cols'] = $cols;
}

/**
 * Implements hook_preprocess_paragraph().
 *
 * The paragrash preprocess for ip_text_block.
 */
function onehouse_base_preprocess_paragraph__ip_text_block(&$variables) {

  $p = $variables['paragraph']->getParentEntity();
  if ($p->bundle() == 'ip_sub_item') {
    $p = $p->getParentEntity();
    $variables['_root_type'] = $p->bundle();
  }

}
