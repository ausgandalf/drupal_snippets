<?php

/**
 * @file
 * umd_global_base module file for node related functions.
 */

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Implements hook_form_alter().
 */
function umd_global_base_form_alter(&$form, FormStateInterface $form_state, $form_id) {
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
}


/**
 * Implements hook_entity_presave().
 */
function umd_global_base_entity_presave(EntityInterface $entity) {

  if ($entity instanceof NodeInterface) {
    $type = $entity->getType();

    if ($type == 'umd_global_classroom_course') {
      //
    }
  }
}