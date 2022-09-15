<?php

/**
 * @file
 * Preprocess html functions.
 */

use Drupal\node\NodeInterface;

/**
 * Implements hook_preprocess_html().
 */
function usfca_preprocess_html(&$variables) {
  $node = \Drupal::routeMatch()->getParameter('node');
  $view_id = \Drupal::routeMatch()->getParameter('view_id');

  if ($node instanceof NodeInterface) {
    $bundle = $node->bundle();
    $pl_class = usfca_get_node_template_patterns($bundle);

    if (!empty($pl_class)) {
      $variables['attributes']['class'][] = $pl_class;
    }

    if ($bundle == 'chapter_page') {
      if ($node->hasField('field_image') && !$node->field_image->isEmpty()) {
        $variables['attributes']['class'][] = 'transparent-header';
      }
      $variables['attributes']['class'][] = 'jump-links';
    }

    if (in_array($bundle, ['article', 'basic_page', 'home', 'landing', 'landing_subpage', 'minisite_home', 'minisite_subpage', 'notification', 'program', 'program_home', 'program_subpage', 'form_page'])) {
      if ($node->hasField('field_image') && !$node->field_image->isEmpty()) {
        $variables['attributes']['class'][] = 'transparent-header';
      }
      elseif ($node->hasField('field_hero') && !$node->field_hero->isEmpty()) {
        $variables['attributes']['class'][] = 'transparent-header';
      }
    }
  }
  elseif (!empty($view_id)) {
    $pl_class = usfca_get_views_template_patterns($view_id);
    if (!empty($pl_class)) {
      $variables['attributes']['class'][] = $pl_class;
    }
  }
}
