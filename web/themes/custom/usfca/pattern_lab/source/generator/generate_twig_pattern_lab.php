<?php

function write_twig_field_include($fh, $content_definition, $pattern_definition, $level = 1) {
  $component_name = $content_definition['name'];

  if (!empty($content_definition['condition'])) {
    write_line($fh, '{% if ' . $content_definition['condition'] . ' is not empty %}', $level);

    $level += 1;
  }

  if (!empty($content_definition['with'])) {
    $line = "{% include 'fields-" . $component_name . "' with { ";

    if (is_array($content_definition['with'])) {
      $numVars = count($content_definition['with']);

      $i = 0;

      foreach ($content_definition['with'] as $key => $value) {
        if (is_bool($value)) {
          $line .= $key . ': ' . ($value ? 'true' : 'false');
        }
        else {
          $line .= $key . ': ' . $value;
        }

        $i += 1;

        $line .= $i === $numVars ? '' : ', ';
      }
    }
    else {
      $line .= $content_definition['with'] . ': ' . $content_definition['with'];
    }

    $line .= " } %}";

    write_line($fh, $line, $level);
  }
  else {
    write_line($fh, "{% include 'fields-" . $component_name . "' %}", $level);
  }

  if (!empty($content_definition['condition'])) {
    $level -= 1;

    write_line($fh, '{% endif %}', $level);
  }
}

function write_twig_component_include($fh, $content_definition, $pattern_definition, $level = 1) {
  $component_name = $content_definition['name'];

  if (!empty($content_definition['condition'])) {
    write_line($fh, '{% if ' . $content_definition['condition'] . ' is not empty %}', $level);

    $level += 1;
  }

  if (!empty($content_definition['with'])) {
    $line = "{% include 'components-" . $component_name . "' with { ";

    $numVars = count($content_definition['with']);

    $i = 0;

    foreach ($content_definition['with'] as $key => $value) {
      $line .= $key . ': ' . $value;

      $i += 1;

      $line .= $i === $numVars ? '' : ', ';
    }

    $line .= " } %}";

    write_line($fh, $line, $level);
  }
  else {
    write_line($fh, "{% include 'components-" . $component_name . "' %}", $level);
  }

  if (!empty($content_definition['condition'])) {
    $level -= 1;

    write_line($fh, '{% endif %}', $level);
  }
}

function write_twig_container($fh, $content_definition, $pattern_definition, $level = 1) {
  $element = !empty($content_definition['element']) ? $content_definition['element'] : 'div';

  if (!empty($content_definition['condition'])) {
    write_line($fh, '{% if ' . $content_definition['condition'] . ' is not empty %}', $level);

    $level += 1;
  }

  if (!empty($content_definition['class'])) {
    $class_name = $content_definition['class'];

    write_line($fh, "<" . $element . " class=\"" . $class_name . "\">", $level);
  }
  else {
    write_line($fh, '<' . $element . '>', $level);
  }

  $level += 1;

  write_twig_content($fh, $content_definition, $pattern_definition, $level);

  $level -= 1;

  write_line($fh, "</" . $element . ">", $level);

  if (!empty($content_definition['condition'])) {
    $level -= 1;

    write_line($fh, '{% endif %}', $level);
  }
}


function write_twig_repeater($fh, $content_definition, $pattern_definition, $level = 1) {
  $repeater_field_name = !empty($content_definition['name']) ? $content_definition['name'] : NULL;

  if (!empty($repeater_field_name)) {
    if (empty($content_definition['swiper'])) {
      write_line($fh, "{% embed 'base-field' with { name: '" . $repeater_field_name . "' } %}", $level);
    }
    else {
      write_line($fh, "{% embed 'base-field' with { name: '" . $repeater_field_name . "', field_variant: [ 'swiper-container' ] } %}", $level);
    }

    $level += 1;

    write_line($fh, "{% block content %}", $level);

    $level += 1;
  }
  elseif (!empty($content_definition['swiper'])) {
    write_line($fh, '<div class="swiper-container">', $level);

    $level += 1;
  }

  if (!empty($content_definition['swiper'])) {
    write_line($fh, '<div class="swiper-wrapper">', $level);

    $level += 1;
  }

  $for_variable = !empty($content_definition['for']) ? $content_definition['for'] : 'item';

  $in_variable = !empty($content_definition['in']) ? $content_definition['in'] : 'items';

  write_line($fh, "{% for " . $for_variable . " in " . $in_variable . " %}", $level);

  $level += 1;

  $repeat = $content_definition['repeat'];

  if (!empty($repeat)) {
    $item_type = !empty($repeat['type']) ? $repeat['type'] : 'component';

    switch ($item_type) {
      case 'component':
        $with = !empty($repeat['with']) ? $repeat['with'] : [];

        if (!empty($with) && !is_array($with)) {
          $with = [$with];
        }

        if (!empty($content_definition['swiper'])) {
          $with['variant'] = !empty($with['variant']) ? $with['variant'] : [];

          $with['variant'][] = '\'swiper-slide\'';
        }

        if (empty($with)) {
          write_line($fh, "{% include 'components-" . $repeat['name'] . "' %}", $level);
        }
        else {
          $line = "{% include 'components-" . $repeat['name'] . "' with { ";

          if (is_array($with)) {
            $numVars = count($with);

            $i = 0;

            foreach ($with as $key => $value) {
              if (is_array($value)) {
                $line .= $key . ': [ ';

                $numVarsNested = count($value);

                $j = 0;

                foreach ($value as $value_nested) {
                  $line .= $value_nested;

                  $j += 1;

                  $line .= $j === $numVarsNested ? '' : ', ';
                }

                $line .= ' ]';
              }
              else {
                if (is_numeric($key)) {
                  $line .= $value . ': ' . $value;
                }
                else {
                  $line .= $key . ': ' . $for_variable . '.' . $value;
                }
              }

              $i += 1;

              $line .= $i === $numVars ? '' : ', ';
            }
          }
          else {
            $line .= $with . ': ' . $with;
          }

          $line .= " } only %}";

          write_line($fh, $line, $level);
        }

        break;

      case 'field':
        $with = !empty($repeat['with']) ? $repeat['with'] : [];

        if (empty($with)) {
          write_line($fh, "{% include 'fields-" . $repeat['name'] . "' %}", $level);

          $level -= 1;
        }
        else {
          $line = "{% include 'fields-" . $repeat['name'] . "' with { ";

          if (is_array($with)) {
            $numVars = count($with);

            $i = 0;

            foreach ($with as $key => $value) {
              if (is_array($value)) {
                $line .= $key . ': [ ';

                $numVarsNested = count($value);

                $j = 0;

                foreach ($value as $value_nested) {
                  $line .= $value_nested;

                  $j += 1;

                  $line .= $j === $numVarsNested ? '' : ', ';
                }

                $line .= ' ]';
              }
              else {
                $line .= $key . ': ' . $for_variable . '.' . $value;
              }

              $i += 1;

              $line .= $i === $numVars ? '' : ', ';
            }
          }
          else {
            $line .= $with . ': ' . $with;
          }

          $line .= " } only %}";

          write_line($fh, $line, $level);
        }

        break;
    }
  }

  $level -= 1;

  write_line($fh, '{% endfor %}', $level);

  if (!empty($content_definition['swiper'])) {
    $level -= 1;

    write_line($fh, '</div>', $level);

    write_line($fh, '<div class="swiper-button-prev"></div>', $level);

    write_line($fh, '<div class="swiper-button-next"></div>', $level);

  }

  if (!empty($repeater_field_name)) {
    $level -= 1;

    write_line($fh, "{% endblock content %}", $level);

    $level -= 1;

    write_line($fh, "{% endembed %}", $level);
  }
  elseif (!empty($content_definition['swiper'])) {
    $level -= 1;

    write_line($fh, '</div>', $level);
  }
}

function write_twig_content($fh, $pattern_markup, $pattern_definition, $level = 1) {
  global $recurse;

  if (is_array($pattern_markup)) {
    if (!empty($pattern_markup['content'])) {
      if (is_array($pattern_markup['content'])) {
        foreach ($pattern_markup['content'] as $content_definition) {
          if (!is_array($content_definition)) {
            $content_definition = [
              'type' => 'field',
              'name' => $content_definition,
            ];
          }

          if (empty($content_definition['type'])) {
            if (count($content_definition) == 1 && !empty($content_definition['markup'])) {
              $content_definition = [
                'type' => 'markup',
                'markup' => $content_definition['markup'],
              ];
            }
            else {
              $content_definition['type'] = 'field';
            }
          }

          switch ($content_definition['type']) {
            case 'component':
              write_twig_component_include($fh, $content_definition, $pattern_definition, $level);

              if ($recurse) {
                $component_pattern_definition = $content_definition;

                $own = !empty($component_pattern_definition['own']) ? TRUE : FALSE;

                generate_pattern_lab($component_pattern_definition, $own);
              }

              break;

            case 'repeater':
              write_twig_repeater($fh, $content_definition, $pattern_definition, $level);

              if (!empty($content_definition['repeat']) && $recurse) {
                $repeat = $content_definition['repeat'];

                $own = !empty($repeat['own']) ? TRUE : FALSE;

                generate_pattern_lab($repeat, $own);
              }

              break;

            case 'field':
              write_twig_field_include($fh, $content_definition, $pattern_definition, $level);

              if ($recurse) {
                $field_pattern_definition = $content_definition;

                $own = !empty($field_pattern_definition['own']) ? TRUE : FALSE;

                generate_pattern_lab($field_pattern_definition, $own);
              }

              break;

            case 'container':
              write_twig_container($fh, $content_definition, $pattern_definition, $level);

              break;

            case 'markup':
              write_line($fh, $content_definition['markup'], $level);

              break;
          }
        }
      }
    }
  }
  elseif (!empty($pattern_markup)) {
    write_line($fh, $pattern_markup, $level);
  }
}

function pattern_twig_filename($pattern_definition) {
  $pattern_twig_filename = NULL;

  $pattern_name = $pattern_definition['name'];

  $pattern_directory = pattern_directory($pattern_definition);

  $pattern_twig_filename = $pattern_directory . '/' . $pattern_name . '.twig';

  return $pattern_twig_filename;
}

function generate_field_twig_file($pattern_definition) {
  $pattern_name = $pattern_definition['name'];

  $pattern_twig_filename = pattern_twig_filename($pattern_definition);

  $fh = fopen_create_or_truncate($pattern_twig_filename, 'wa+');

  $level = 0;

  write_line($fh, "{% extends 'base-field' %}", $level, 0);

  write_line($fh, "{% set name = '" . $pattern_name . "' %}");

  write_line($fh, "{% block content %}");

  if (!empty($pattern_definition['markup'])) {
    $pattern_markup = $pattern_definition['markup'];

    if (!empty($pattern_markup)) {
      write_twig_content($fh, $pattern_markup, $pattern_definition);
    }
  }

  write_line($fh, "{% endblock content %}", 0, 2);

  write_line($fh, "", 0, 1);

  fclose($fh);
}

function generate_component_twig_file($pattern_definition) {
  $pattern_name = $pattern_definition['name'];

  $pattern_twig_filename = pattern_twig_filename($pattern_definition);

  $fh = fopen_create_or_truncate($pattern_twig_filename, 'wa+');

  $level = 0;

  write_line($fh, "{% extends 'base-component' %}", $level, 0);

  write_line($fh, "{% set name = '" . $pattern_name . "' %}", $level);

  if (!empty($pattern_definition['element'])) {
    write_line($fh, "{% set container_element = '" . $pattern_definition['element'] . "' %}");
  }

  if (!empty($pattern_definition['aria'])) {
    write_line($fh, "{% set container_aria_label = '" . $pattern_definition['aria'] . "' %}");
  }

  write_line($fh, "{% block content %}");

  if (!empty($pattern_definition['markup'])) {
    $pattern_markup = $pattern_definition['markup'];

    write_twig_content($fh, $pattern_markup, $pattern_definition);
  }

  write_line($fh, "{% endblock content %}", 0, 2);

  write_line($fh, "", 0, 1);

  fclose($fh);
}

function generate_pattern_twig_file($pattern_definition) {
  $pattern_type = $pattern_definition['type'];

  switch ($pattern_type) {
    case 'field':
      generate_field_twig_file($pattern_definition);

      break;

    default:
      generate_component_twig_file($pattern_definition);

      break;
  }
}
