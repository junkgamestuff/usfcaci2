<?php

/**
 * @file
 * Theme suggestions alter menu functions.
 */

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function usfca_theme_suggestions_menu_alter(array &$suggestions, array $variables) {
  if (isset($variables['attributes']['block'])) {
    $block_hint = str_replace(['block-', '-'], ['', '_'], $variables['attributes']['block']);

    // Add a theme suggestion for just the block without the derivative.
    $suggestions[] = 'menu__' . $block_hint;

    // Add a theme suggestion for menus, based on the containing block.
    $suggestions[] = $variables['theme_hook_original'] . '__' . $block_hint;
  }
}
