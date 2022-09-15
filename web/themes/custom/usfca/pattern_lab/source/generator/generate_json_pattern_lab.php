<?php

function pattern_json_filename($pattern_definition) {
  $pattern_json_filename = NULL;

  $pattern_name = $pattern_definition['name'];

  $pattern_directory = pattern_directory($pattern_definition);

  $pattern_json_filename = $pattern_directory . '/' . $pattern_name . '.json';

  return $pattern_json_filename;
}

function generate_pattern_json_file($pattern_definition) {
  if (!empty($pattern_definition['data'])) {
    $pattern_name = $pattern_definition['name'];

    $pattern_json_filename = pattern_json_filename($pattern_definition);
  
    $fh = fopen_create_or_truncate($pattern_json_filename, 'wa+');
  
    $data_encoded = json_encode($pattern_definition['data'], JSON_PRETTY_PRINT);

    $data_encoded = str_replace('    ', '  ', $data_encoded);

    fwrite($fh, $data_encoded);
  
    fclose($fh);
  }
}
