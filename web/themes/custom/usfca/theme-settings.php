<?php

/**
 * @file
 * Theme settings file.
 */

/**
 * {@inheritdoc}
 */
function usfca_form_system_theme_settings_alter(&$form, &$form_state) {
  $form['advanced_settings'] = [
    '#type' => 'details',
    '#title' => t('Advanced Settings'),
    '#open' => TRUE,
  ];
  $description = theme_get_setting('description');
  $form['advanced_settings']['description'] = [
    '#type' => 'text_format',
    '#title' => t('Footer description'),
    '#default_value' => $description ? $description['value'] : NULL,
    '#format' => $description ? $description['format'] : 'restricted_html',
    '#base_type' => 'textarea',
  ];
  $copyright = theme_get_setting('copyright');
  $form['advanced_settings']['copyright'] = [
    '#type' => 'text_format',
    '#title' => t('Footer copyright'),
    '#default_value' => $copyright ? $copyright['value'] : NULL,
    '#format' => $copyright ? $copyright['format'] : 'restricted_html',
    '#base_type' => 'textarea',
  ];
}
