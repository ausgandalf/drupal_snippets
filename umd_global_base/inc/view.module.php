<?php

/**
 * @file
 * umd_global_base module file for view related functions.
 */

 use Drupal\Core\Cache\Cache;
 use Drupal\Core\Cache\CacheBackendInterface;
 use Drupal\Core\Datetime\DrupalDateTime;
 use Drupal\Core\Entity\EntityInterface;
 use Drupal\Core\Form\FormStateInterface;
 use Drupal\Core\StringTranslation\TranslatableMarkup;
 use Drupal\node\NodeInterface;

/**
 * Implements template_preprocess_views_view().
 */
function umd_global_base_preprocess_views_view(&$variables) {
  return;

  $view = \Drupal\views\Views::getView('umd_global_courses');
  $view->setDisplay('block_active');
  $view->execute();
  dump($view->result);
  exit;
}

/**
 * Find parent paragraph of the view if ever have.
 */
function umd_global_base_get_view_field_parent_paragraph($view, $field = 'field_widgets', $candidats = [['ip_views_embed', 'field_ip_views_embed_view']]) {
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

/**
 * Implements hook_views_pre_execute().
 *
 * Modify query to sort courses so that recent upcoming courses will come first.
 */
function umd_global_base_views_query_alter(\Drupal\views\ViewExecutable $view, \Drupal\views\Plugin\views\query\QueryPluginBase $query) {
  if ($view->id() == 'umd_global_courses') {

    array_unshift($query->orderby, [
      "field" => "_is_upcoming",
      "direction" => "DESC",
    ], [
      "field" => "_upcoming_date",
      "direction" => "ASC",
    ]);
  }
}

/**
 * Implements hook_views_pre_execute().
 *
 * Modify query to add expression to check if its upcoming courses or not.
 */
function umd_global_base_views_pre_execute($view) {
  $viewId = $view->storage->id();

  if ($viewId == 'umd_global_courses') {
    $today = date('Y-m-d');
    $query = &$view->build_info['query'];
    $query->leftJoin('node__field_course_end_date', 'ed', '(node_field_data.nid = ed.entity_id) AND (node_field_data.type = ed.bundle)');
    $query->addExpression('IF("ed"."field_course_end_date_value" >= \'' . $today . '\' , 1, 0)', '_is_upcoming');
    $query->addExpression('IF("ed"."field_course_end_date_value" >= \'' . $today . '\' , "ed"."field_course_end_date_value", \'0000-00-00\')', '_upcoming_date');
  }

  return $view;
}

/**
 * Implements hook_form_views_exposed_form_alter().
 *
 * For specific views (news_releases_full, jhu_apl_projects_full), we are adding Year filters.
 * Better approach for possible future update: If Authored_on filter is given, then render Year filters.
 */
function umd_global_base_form_views_exposed_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  if (!isset($form['#id'])) {
    return;
  }
  $formId = $form['#id'];

  if ($formId == 'views-exposed-form-umd-global-courses-block-active') {
    if (isset($form['term'])) {
      // $form['term']['#options'] = umd_global_base_only_active_terms ('umd_global_classroom_course', $form['term']['#options']);
      $form['term']['#options'] = umd_global_base_offered_terms_filters();
    }
  }
}

/**
 * Build terms offered selector markups.
 */
function umd_global_base_offered_terms_filters () {

  $options = [];
  $options['All'] = new TranslatableMarkup('- Any -');

  $view = \Drupal\views\Views::getView('terms_offered');
  $view->setDisplay('default');
  $view->execute();
  foreach ($view->result as $id => $result) {
    $term = $result->_entity;
    $options[$result->tid] = $term->get('name')->value;
  }
  return $options;
}

/**
 * Build Year selector markups per Drupal Form protocol for given node type.
 */
function umd_global_base_only_active_terms ($node_type) {

  $options = &drupal_static(__FUNCTION__);

  if (true || is_null($options)) {
    $date = new DrupalDateTime('today', new DateTimeZone('UTC'));
    $today = $date->format('Y-m-d');

    $cid = 'umd_global_base:' . $node_type . ':active_terms:' . $today;
    $data = \Drupal::cache()->get($cid);
    if (true || !$data) {
      $options = [];
      // $options['All'] = new TranslatableMarkup('All');
      if (isset($default['All'])) $options['All'] = $default['All'];

      // $thisYear = date("Y");
      // $options[$thisYear] = $thisYear;
      $query = \Drupal::database()->select('node_field_data', 'n');
      $query->leftJoin('node__field_course_end_date', 'ned', '(ned.bundle = n.type) AND (ned.entity_id = n.nid)');
      $query->leftJoin('node__field_terms_offered', 'nt', '(nt.bundle = n.type) AND (nt.entity_id = n.nid)');
      $query->leftJoin('taxonomy_term_field_data', 't', '(t.tid = nt.field_terms_offered_target_id) AND (t.status = 1)');
      $query->condition('n.type', $node_type);
      $query->condition('ned.field_course_end_date_value', $today, '>=');
      $query->fields('t', ['tid', 'name']);
      $query->groupBy('tid');
      $query->orderBy('t.weight', 'ASC');
      $results = $query->execute()->fetchAll();

      if ($results) {
        foreach ($results as $record) {
          if ($record->tid && $record->name) $options[$record->tid] = new TranslatableMarkup($record->name);
        }
      }

      $cache_tags = ['node:' . $node_type . ':active_terms_' . $today];
      \Drupal::cache()->set($cid, $options, CacheBackendInterface::CACHE_PERMANENT, $cache_tags);
    }
    else {
      $options = $data->data;
    }
  }

  return $options;
}