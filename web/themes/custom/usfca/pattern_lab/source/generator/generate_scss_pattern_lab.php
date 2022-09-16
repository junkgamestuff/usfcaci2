<?php

function pattern_scss_filename($pattern_definition) {
  $pattern_scss_filename = NULL;

  $pattern_name = $pattern_definition['name'];

  $pattern_directory = pattern_directory($pattern_definition);

  $pattern_scss_filename = $pattern_directory . '/' . $pattern_name . '.scss';

  return $pattern_scss_filename;
}

function generate_pattern_scss_file($pattern_definition, $noStyles = FALSE) {
  $pattern_name = $pattern_definition['name'];

  $pattern_type = $pattern_definition['type'];

  $pattern_scss_filename = pattern_scss_filename($pattern_definition);

  $fh = fopen_create_or_truncate($pattern_scss_filename, 'wa+');

  $level = 0;

  switch ($pattern_type) {
    case 'component':
      write_line($fh, '.cc--' . $pattern_name . ' {', $level, 0);

      $level += 1;

      if (!$noStyles) {
        if (!empty($pattern_definition['style'])) {
          write_line($fh, '// BEGIN auto-generated styles BEGIN', $level, 2);

          write_line($fh, '@media only screen and (min-width: 1024px) {', $level, 1);

          $level += 1;

          $style = $pattern_definition['style'];

          foreach ($style as $style_definition) {
            $selector = !empty($style_definition['selector']) ? $style_definition['selector'] : null;

            if (!empty($selector)) {
              write_line($fh, $selector . ' {', $level, 1);

              $level += 1;
            }

            if (!empty($style_definition['grid'])) {
              $grid = $style_definition['grid'];

              write_line($fh, 'display: flex;', $level, 1);
              write_line($fh, 'flex-wrap: wrap;', $level, 1);

              $across = !empty($grid['across']) ? $grid['across'] : 3;

              $item_flex_basis = (100 / $across) . '%';

              write_line($fh, '> * {', $level, 2);

              $level += 1;

              write_line($fh, 'flex-basis: ' . $item_flex_basis . ';', $level, 1);

              $level -= 1;

              write_line($fh, '}', $level, 1);

            }
            elseif (!empty($style_definition['rules'])) {
              if (!empty($style_definition['rules'])) {
                foreach ($style_definition['rules'] as $rule) {
                  write_line($fh, $rule, $level, 1);
                }
              }
            }

            if (!empty($selector)) {
              $level -= 1;

              write_line($fh, '}', $level, 1);
            }
          }

          $level -= 1;

          write_line($fh, '}', $level, 1);

          write_line($fh, '// END auto-generated styles END', $level, 1);
        }
      }

      $level -= 1;

      write_line($fh, '}', $level, 2);

      write_line($fh, '', $level, 1);

      break;

    case 'field':
      write_line($fh, '.f--' . $pattern_name . ' {', $level, 0);

      write_line($fh, '}', $level, 2);

      write_line($fh, '', $level, 1);

      break;
  }

  fclose($fh);
}
