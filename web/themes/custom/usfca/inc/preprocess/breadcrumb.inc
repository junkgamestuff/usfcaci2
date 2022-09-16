<?php

/**
 * @file
 * Preprocess breadcrumbe functions.
 */

use Drupal\node\NodeInterface;

/**
 * Implements hook_preprocess_breadcrumb().
 */
function usfca_preprocess_breadcrumb(&$variables) {
  $node = \Drupal::routeMatch()->getParameter('node');

  if ($node instanceof NodeInterface) {
    // Providing correct breadcrumb to school if it is missing.
    if (!empty($node->field_school[0])) {
      if (!empty($node->field_school[0]->entity->field_school_home[0])) {
        $school_name = $node->field_school[0]->entity->field_school_home[0]->entity->label();
        $school_nid = $node->field_school[0]->entity->field_school_home[0]->entity->id();
        $school_url = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $school_nid);

        $school_is_present = FALSE;
        foreach ($variables['breadcrumb'] as $breadcrumb) {
          if (!empty($breadcrumb['url']) && $breadcrumb['url'] == $school_url) {
            $school_is_present = TRUE;
          }
        }

        if (!$school_is_present) {
          $school_link = [
            'url' => $school_url,
            'text' => $school_name,
          ];
          array_splice($variables['breadcrumb'], 1, 0, [$school_link]);
        }
      }
    }
  }
}
