<?php

/**
 * @file
 * Preprocess block functions.
 */

use Drupal\node\NodeInterface;

/**
 * Implements hook_preprocess_block().
 */
function usfca_preprocess_block(&$variables) {
  $block_id = !empty($variables['elements']['#id']) ? $variables['elements']['#id'] : NULL;

  if ($block_id == 'usfca_mainpagecontent') {
    $node = \Drupal::routeMatch()->getParameter('node');
    $view_id = \Drupal::routeMatch()->getParameter('view_id');
    if ($node instanceof NodeInterface) {
      $pl_class = usfca_get_node_template_patterns($node->bundle());
      if (!empty($pl_class)) {
        $variables['template_pattern'] = $pl_class;
      }
    }
    elseif (!empty($view_id)) {
      $pl_class = usfca_get_views_template_patterns($view_id);
      if (!empty($pl_class)) {
        $variables['template_pattern'] = $pl_class;
      }
    }
  }
}

/**
 * Implements hook_preprocess_block().
 */
function usfca_preprocess_block__footer_copyright(&$variables) {
  $copyright = theme_get_setting('copyright');
  $variables['footer_copyright'] = [
    '#type' => 'processed_text',
    '#text' => $copyright['value'],
    '#format' => $copyright['format'],
  ];
}

/**
 * Implements hook_preprocess_block().
 */
function usfca_preprocess_block__footer_description(&$variables) {
  $description = theme_get_setting('description');
  $variables['footer_description'] = [
    '#type' => 'processed_text',
    '#text' => $description['value'],
    '#format' => $description['format'],
  ];
}
