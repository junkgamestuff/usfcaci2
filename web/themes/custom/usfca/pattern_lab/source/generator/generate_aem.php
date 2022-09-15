<?php

require_once __DIR__ . '/generate_htl.php';

define('COMPONENTS_REL_PATH', './aem/components/content');

function component_directory($component_definition) {
  $component_directory = NULL;

  $component_type = $component_definition['type'];

  $component_name = $component_definition['name'];

  switch ($component_type) {
    case 'component':
      $component_directory = __DIR__ . '/' . COMPONENTS_REL_PATH . '/components';

      break;

    case 'field':
      $component_directory = __DIR__ . '/' . COMPONENTS_REL_PATH . '/fields';

      break;

    default:
      throw new Exception("Error: no such component type: " . $component_type, 1);

      break;
  }

  return $component_directory;
}

function generate_aem_component($component_definition, $force = FALSE) {
  global $log;

  if (!is_array($component_definition)) {
    $component_definition = [
      'type' => 'component',
      'name' => $component_definition,
    ];
  }

  if (empty($component_definition['type'])) {
    $component_definition['type'] = 'component';
  }

  $component_htl_filename = component_htl_filename($component_definition);

  if ($force || !file_exists($component_htl_filename)) {
    $component_directory = component_directory($component_definition);

    if (!file_exists($component_directory)) {
      mkdir($component_directory, 0777, true);
    }

    generate_htl_file($component_definition);
  }
  else {
    $log->debug("File exists already, skipping.\n", [ $component_htl_filename ]);
  }
}
