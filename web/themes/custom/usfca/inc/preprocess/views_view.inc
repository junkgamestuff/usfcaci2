<?php

/**
 * @file
 * Theme suggestions alter views_view functions.
 */

use Drupal\node\NodeInterface;

/**
 * Implements hook_preprocess_views_view_unformatted().
 */
function usfca_preprocess_views_view_unformatted__program_faculty_listing(&$variables) {
  $view = $variables['view'];
  $arguments = $view->argument;
  $program_ids = $role_ids = [];
  if (!empty($arguments['field_program_target_id'])) {
    $programs = $arguments['field_program_target_id']->getValue();
    if ($programs) {
      $program_ids = explode(',', $programs);
    }
  }
  if (!empty($arguments['field_role_target_id'])) {
    $roles = $arguments['field_role_target_id']->getValue();
    if ($roles) {
      $role_ids = explode(',', $roles);
    }
  }
  $variables['program_ids'] = $program_ids;
  $variables['role_ids'] = $role_ids;
}
