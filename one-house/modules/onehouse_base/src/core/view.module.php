<?php

/**
 * @file
 * Onehouse_base module file for view related functions.
 */

/**
 * Implements template_preprocess_views_view().
 */
function onehouse_base_preprocess_views_view(&$variables) {

}

/**
 * Find parent paragraph of the view if ever have.
 */
function onehouse_base_get_view_field_parent_paragraph($view, $field = 'field_widgets', $candidats = [['ip_views_embed', 'field_ip_views_embed_view']]) {
  // Get current node.
  $node = \Drupal::routeMatch()->getParameter('node');
  if (!$node) {
    return NULL;
  }
  $uuid = $view->storage->uuid();
  $vpar = NULL;
  foreach ($node->{$field} as $p) {
    $pe = $p->entity;
    $p_type = $pe->getType();

    foreach ($candidats as $candidate) {

      if ($p_type == $candidate[0]) {
        $pv = $pe->{$candidate[1]};
        if ($pv && $pv->first()) {
          if ($uuid == $pv->first()->entity->uuid()) {
            $vpar = $pe;
            break;
          }
        }
      }
    }

    if (!is_null($vpar)) {
      break;
    }
  }

  return $vpar;
}
