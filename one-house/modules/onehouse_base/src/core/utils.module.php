<?php

/**
 * @file
 * Onehouse_base module file for utility functions.
 */

use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\block\Entity\Block;

/**
 * Return node type name of current page.
 *
 * Identical with one_house_get_page_id() function defined in one_house.theme.
 */
function onehouse_base_get_node_type_id() {

  $namedId = '';

  $routeMatched = \Drupal::routeMatch();

  $node = $routeMatched->getParameter('node');
  if ($node instanceof Node) {
    $type = $node->getType();
    $namedId = $type;
  }

  if (!$namedId) {
    $nodeType = $routeMatched->getParameter('node_type');
    if ($nodeType) {
      $namedId = $nodeType->get('type');
    }
  }

  if (!$namedId) {
    $routeName = $routeMatched->getRouteName();
    $routeNameArray = explode('.', $routeName);
    $namedId = array_pop($routeNameArray);
  }

  return $namedId;

}

/**
 * Sort menu items by weight.
 */
function onehouse_base_cmp($a, $b) {
  return intval($a['weight']) - intval($b['weight']);
}

/**
 * Get block content.
 */
function onehouse_base_load_block($block_id, $get_rendered_result = FALSE) {
  if (empty($block_id)) {
    return FALSE;
  }
  $block_content = '';
  $block = Block::load($block_id);
  if ($block) {
    $block_content = \Drupal::entityTypeManager()->getViewBuilder('block')->view($block);
    if ($get_rendered_result) {
      $block_content = \Drupal::service('renderer')->render($block_content);
    }
    return $block_content;
  }
  return FALSE;
}

/**
 * Render node field element.
 */
function onehouse_base_render_field($field, $bundle = 'node') {
  $builder = \Drupal::entityTypeManager()->getViewBuilder($bundle);
  $field_builder = $builder->viewField($field);
  return \Drupal::service('renderer')->render($field_builder);
}

/**
 * Get term label.
 */
function onehouse_base_get_term_label($tid) {
  $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
  return $term ? $term->label() : '';
}

/**
 * Load entities by condition with builder info attached if needed.
 */
function onehouse_base_load_entities($conditions = [], $sorts = [], $range = FALSE, $get_builder = FALSE, $builder_opts = ['node', ['default']]) {
  $data = [];

  $query = \Drupal::entityQuery('node');
  foreach ($conditions as $cond) {
    $query = $query->condition(...$cond);
  }

  foreach ($sorts as $sort) {
    $query = $query->sort(...$sort);
  }

  if (is_array($range) && count($range) == 2) {
    $query = $query->range($range[0], $range[1]);
  }

  $result = $query->execute();
  $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($result);

  $entity_type = 'node';
  $view_mode = 'default';
  if (isset($builder_opts[0])) {
    $entity_type = $builder_opts[0];
  }
  $node_ind = 0;
  foreach ($nodes as $nodeId => $node) {
    if ($get_builder) {
      if (isset($builder_opts[1][$node_ind])) {
        $view_mode = $builder_opts[1][$node_ind];
      }
      $builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
      $build = $builder->view($node, $view_mode);
      $data[] = $build;
    }
    else {
      $data[$nodeId] = $node;
    }

    $node_ind++;
  }

  return $data;
}

/**
 * Get menu items.
 */
function onehouse_base_get_menu_links($menu_name = 'main') {
  $menu = \Drupal::service('menu.tree_storage')->loadByProperties([
    'menu_name' => $menu_name,
    'enabled' => 1,
  ]) ?: [];

  usort($menu, "onehouse_base_cmp");
  return $menu;
}

/**
 * Get first menu item's URL.
 */
function onehouse_base_get_first_menu_url($menu_name = 'main') {
  // Redirect to first Bidder menu page.
  $newPath = '';
  $menuItems = onehouse_base_get_menu_links($menu_name);
  if (count($menuItems) > 0) {
    $mkey = array_keys($menuItems)[0];

    if (isset($menuItems[$mkey]['url']) && ($menuItems[$mkey]['url'] != '')) {
      $newPath = $menuItems[$mkey]['url'];
    }
    else {
      try {
        $newPath = Url::fromRoute($menuItems[$mkey]['route_name'], $menuItems[$mkey]['route_parameters'])->toString();
      }
      catch (\Exception $e) {
      }
    }
  }

  return $newPath;
}
