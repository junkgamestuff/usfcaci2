<?php

/**
 * @file
 * Theme suggestions alter form functions.
 */

/**
 * Implements hook_theme_suggestions_form_alter().
 */
function usfca_theme_suggestions_form_alter(array &$suggestions, array $variables) {
  $suggestions[] = 'form__' . $variables['element']['#form_id'];
  foreach ($variables['element']['#theme'] as $theme) {
    $suggestions[] = "form__$theme";
  }
}

/**
 * Implements hook_form_theme_suggestions_input_alter().
 */
function usfca_theme_suggestions_input_alter(&$suggestions, array $variables) {
  $element = $variables['element'];

  if (!empty($element['#array_parents'])) {
    $suggestions[] = 'input__' . $element['#type'] . '__' . implode('__', $element['#array_parents']);
  }
}

/**
 * Implements hook_form_theme_suggestions_fieldset_alter().
 */
function usfca_theme_suggestions_fieldset_alter(&$suggestions, array $variables, $hook) {
  $element = $variables['element'];
  if ($element['#type'] === 'checkboxes') {
    _usfca_add_suggestions_by_view_context($suggestions, $element, $hook);
  }
}

/**
 * Helper function to add some more context to form elements and wrappers.
 *
 * @param array $suggestions
 *   The suggestions array.
 * @param array $element
 *   The element.
 * @param string $hook
 *   The theme hook.
 */
function _usfca_add_suggestions_by_view_context(array &$suggestions, array $element, $hook) {
  if (!empty($element['#context']['#view_id']) && !empty($element['#context']['#display_id'])) {
    $suggestions[] = $hook . '__' . $element['#type'] . '__' . $element['#context']['#view_id'];
    $suggestions[] = $hook . '__' . $element['#type'] . '__' . $element['#context']['#view_id'] . '__' . $element['#context']['#display_id'];
    $suggestions[] = $hook . '__' . $element['#type'] . '__' . $element['#context']['#display_id'];
  }
}

/**
 * Implements hook_form_theme_suggestions_fieldset_alter().
 */
function usfca_theme_suggestions_form_element_alter(&$suggestions, array $variables, $hook) {
  $view_id = \Drupal::routeMatch()->getParameter('view_id');
  if (isset($variables['element']['#type'])) {
    $suggestions[] = 'form_element__' . $variables['element']['#type'];
  }
  if (!empty($view_id)) {
    $suggestions[] = 'form_element__' . $variables['element']['#type'] . '__' . $view_id;
  }
}
