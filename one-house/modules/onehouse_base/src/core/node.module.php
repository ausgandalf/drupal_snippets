<?php

/**
 * @file
 * Onehouse_base module file for node related functions.
 */

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Implements hook_preprocess_node_add_list().
 */
function onehouse_base_preprocess_node_add_list(&$variables) {
  // Remove Bidder Page option form Add Content page to avoid confusion for admins.
  unset($variables['content']['bidder_page']);
}

/**
 * Implements hook_form_alter().
 */
function onehouse_base_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  // Remove preview from all.
  $form['actions']['preview']['#access'] = FALSE;

  if (preg_match('/node_/', $form_id)) {
    if (isset($form['promote'])) {
      unset($form['promote']);
    }
    if (isset($form['sticky'])) {
      unset($form['sticky']);
    }
  }

  if (preg_match('/node_homepage_/', $form_id)) {
  }
  elseif (preg_match('/node_page_/', $form_id)) {
  }
  elseif (preg_match('/node_property_/', $form_id)) {

    $form['title']['#access'] = FALSE;

    $auction_status_selector = '[name="field_property_auction_status"]';
    $visible_mapper = [
      ['field_property_auction_date', [$auction_status_selector => ['value' => 0]]],
      ['field_year_sold', [$auction_status_selector => ['value' => 2]]],
      ['field_property_offer_link', [$auction_status_selector => ['value' => 1]]],
      ['field_property_sold_price', [$auction_status_selector => ['value' => 2]]],
      ['field_property_contract_due', [$auction_status_selector => ['value' => 3]]],
    ];

    foreach ($visible_mapper as $vm) {
      $form[$vm[0]]['#states'] = [
        'visible' => $vm[1],
      ];
    }
  }
  elseif (preg_match('/node_bidder_page_/', $form_id)) {
  }
  else {
  }
}

/**
 * Implements hook_entity_presave().
 */
function onehouse_base_entity_presave(EntityInterface $entity) {

  if ($entity instanceof NodeInterface) {
    $type = $entity->getType();

    if ($type == 'property') {
      // Get full address for search purpose.
      $zipcode = onehouse_base_get_term_label($entity->field_zip_code[0]->target_id);
      $full = implode(', ', [
        $entity->field_property_address[0]->address_line1,
        $entity->field_property_address[0]->locality,
        $entity->field_property_address[0]->administrative_area . ' ' . $zipcode,
      ]);

      // Set title with full address.
      $entity->set('title', $full);

      $neighborhood = onehouse_base_get_term_label($entity->field_neighborhood[0]->target_id);
      $full_search = implode(' | ', [
        $full,
        $neighborhood,
      ]);

      // Set full address with neighborhood.
      $entity->set('field_property_full_address', $full_search);

    }
  }
}

/**
 * Implements hook_node_view_alter().
 */
function onehouse_base_node_view_alter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {
  if ($entity->bundle() == 'bidder_page') {
    $build['#cache']['max-age'] = 0;
    \Drupal::service('page_cache_kill_switch')->trigger();
  }
}

/**
 * Implements hook_preprocess_hook().
 */
function onehouse_base_preprocess_node(&$variables) {
  $node = $variables['node'];
  $node_type = $node->getType();

  if (in_array($node_type, ['property', 'page', 'homepage'])) {
    // Load pre_content block.
    $variables['_local_actions_block'] = onehouse_base_load_block('one_house_local_actions');
    $variables['_primary_local_tasks_block'] = onehouse_base_load_block('one_house_primary_local_tasks');
  }

  if ($node_type == 'property') {
    // Prepare previously sold list.
    $property_record = [];
    foreach ($node->field_field_property_record as $record) {
      $re = $record->entity;
      $property_record[] = [
        'year' => onehouse_base_render_field($re->field_ip_property_sold_year, 'paragraph'),
        'price' => onehouse_base_render_field($re->field_ip_property_sold_price, 'paragraph'),
      ];
    }

    $variables['_previous_record'] = $property_record;
  }
}
