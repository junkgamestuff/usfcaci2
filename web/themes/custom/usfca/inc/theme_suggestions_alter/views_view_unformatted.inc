<?php

/**
 * @file
 * Theme suggestions alter views_view_unformatted functions.
 */

/**
 * Implements hook_preprocess_views_view_unformatted().
 */
function usfca_theme_suggestions_views_view_unformatted_alter(&$suggestions, $variables) {
  $view    = $variables['view'];
  $display = $view->current_display;

  $suggestions[] = 'views_view_unformatted__' . $view->id();

  switch ($view->id()) {
    case 'latest_news':
      $suggestions[] = 'views_view_unformatted__' . $view->id() . '__' . $display;

      break;
  }
}
