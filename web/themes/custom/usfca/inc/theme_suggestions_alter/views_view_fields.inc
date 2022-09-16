<?php

/**
 * @file
 * Theme suggestions alter views_view_fields functions.
 */

/**
 * Implements hook_preprocess_views_view_fields().
 */
function usfca_theme_suggestions_views_view_fields_alter(&$suggestions, $variables) {
  $view    = $variables['view'];

  $suggestions[] = 'views_view_fields__' . $view->id();
}
