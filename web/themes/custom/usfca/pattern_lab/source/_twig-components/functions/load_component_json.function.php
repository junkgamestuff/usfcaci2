<?php

use Symfony\Component\Yaml\Yaml;

$function = new Twig_SimpleFunction('load_component_json', function ($component_name, $component_variation = null) {
  $json = [];

  // TODO: abstract dir lookup
  $pattern_directory = __DIR__ . '/../../_patterns/05-components/';

  // TODO: abstract component lookup
  $component_json_filename = $pattern_directory . $component_name . '/' . $component_name . (!empty($component_variation) ? '~' . $component_variation : '') . '.json';

  if (file_exists($component_json_filename)) {
    $json_string = file_get_contents($component_json_filename);

    if (!empty($json_string)) {
      $json_decoded = json_decode($json_string, TRUE);

      if (is_array($json_decoded)) {
        $json = $json_decoded;
      }
    }
  }

  return $json;
});
