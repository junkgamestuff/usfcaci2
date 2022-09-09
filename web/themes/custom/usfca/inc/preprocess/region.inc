<?php

/**
 * @file
 * Preprocess region functions.
 */

use Drupal\node\NodeInterface;

/**
 * Implements usfca_preprocess_region__header().
 */
function usfca_preprocess_region__header(&$variables) {
  $node = \Drupal::routeMatch()->getParameter('node');

  if ($node instanceof NodeInterface && $node->bundle() == 'notification') {
    $variables['emergency'] = TRUE;
  }

  if ($node instanceof NodeInterface) {
    $search_url = '/search';

    if (!empty($node->field_school[0])) {
      if (!empty($node->field_school[0]->entity->field_school_search[0])) {
        $search_nid = $node->field_school[0]->entity->field_school_search[0]->entity->id();

        $search_url = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $search_nid);
      }
    }

    $variables['search_url'] = $search_url;
  }
}
