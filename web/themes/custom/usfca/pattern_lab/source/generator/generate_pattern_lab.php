<?php

require_once __DIR__ . '/generate_twig_pattern_lab.php';
require_once __DIR__ . '/generate_json_pattern_lab.php';
require_once __DIR__ . '/generate_scss_pattern_lab.php';

define('PATTERN_COMPONENTS_REL_PATH', '../_patterns/05-components');
define('PATTERN_FIELDS_REL_PATH', '../_patterns/03-fields');

function repeater_field_name($pattern_name) {
  $component_name = '';

  $pattern_name_exploded = explode('-', $pattern_name);

  foreach ($pattern_name_exploded as $part) {
    $component_name .= substr($part, 0, 1);
  }

  $component_name .= '-items';

  return $component_name;
}

function pattern_directory($pattern_definition) {
  $pattern_directory = NULL;

  $pattern_type = $pattern_definition['type'];

  $pattern_name = $pattern_definition['name'];

  switch ($pattern_type) {
    case 'component':
      $pattern_directory = __DIR__ . '/' . PATTERN_COMPONENTS_REL_PATH . '/' . $pattern_name;

      break;

    case 'field':
      $pattern_directory = __DIR__ . '/' . PATTERN_FIELDS_REL_PATH . '/' . $pattern_name;

      break;

    default:
      throw new Exception("Error: no such component type: " . $pattern_type, 1);

      break;
  }

  return $pattern_directory;
}

function generate_pattern_lab($pattern_definition, $force = FALSE, $noStyles = FALSE) {
  global $log;

  if (!is_array($pattern_definition)) {
    $pattern_definition = [
      'type' => 'component',
      'name' => $pattern_definition,
    ];
  }

  if (empty($pattern_definition['type'])) {
    $pattern_definition['type'] = 'component';
  }

  $pattern_twig_filename = pattern_twig_filename($pattern_definition);

  if ($force || !file_exists($pattern_twig_filename)) {
    $pattern_directory = pattern_directory($pattern_definition);

    if (!file_exists($pattern_directory)) {
      mkdir($pattern_directory, 0777, true);
    }

    generate_pattern_twig_file($pattern_definition);
    generate_pattern_json_file($pattern_definition);
    generate_pattern_scss_file($pattern_definition, $noStyles);
  }
  else {
    $log->debug("File exists already, skipping.\n", [$pattern_twig_filename]);
  }
}
