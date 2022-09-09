<?php

define('HTL_LINE_SPACING', 1);
define('HTL_TAB_SPACING', 4);
define('HTL_PROPERTIES_VARIABLE', 'cprops');
define('HTL_PROPERTIES_FILE', 'cprops.js');
define('HTL_CONTENT_NAMESPACE', '/apps/myapp/components/content');
define('HTL_CORE_BUNDLE_NAMESPACE', 'com.digitalpulp.myapp.core');
define('HTL_FIELD_TEMPLATE_PREFIX', '_');

function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
{

    $str = str_replace('-', '', ucwords($string, '-'));

    if (!$capitalizeFirstCharacter) {
        $str = lcfirst($str);
    }

    return $str;
}

function component_htl_useapi_variable_name($component_name) {
  return dashesToCamelCase($component_name);
}

function component_htl_filename($component_definition) {
  $component_htl_filename = NULL;

  $component_name = $component_definition['name'];

  $component_directory = component_directory($component_definition);

  $component_htl_filename = $component_directory . '/' . $component_name . '.html';

  return $component_htl_filename;
}

function write_htl_container($fh, $content_definition, $pattern_definition, $level = 1) {
  $element = !empty($content_definition['element']) ? $content_definition['element'] : 'div';

  $line = '<' . $element;

  if (!empty($content_definition['condition'])) {
    $line .= ' data-sly-test="${' . $content_definition['condition'] . '}"';
  }

  if (!empty($content_definition['class'])) {
    $class_name = $content_definition['class'];

    $line .= ' class="' . $class_name . '"';
  }

  $line .= '>';

  write_line($fh, $line, $level);

  $level += 1;

  write_htl_content($fh, $content_definition, $pattern_definition, $level);

  $level -= 1;

  write_line($fh, "</" . $element . ">", $level, HTL_LINE_SPACING);
}

function write_htl_field_include($fh, $field_definition, $component_definition, $level = 1) {
  $component_name = $component_definition['name'];

  $component_htl_variable_name = component_htl_useapi_variable_name($component_name);

  $field_name = $field_definition['name'];

  $field_htl_variable_name = component_htl_useapi_variable_name($field_name);

  $field_template_name = HTL_FIELD_TEMPLATE_PREFIX . $field_htl_variable_name;

  $call_line = '<sly';

  if (!empty($field_definition['condition'])) {
    $call_line .= ' data-sly-test="${' . $field_definition['condition'] . '}"';
  }

  $call_line .= ' data-sly-use.' . $field_template_name . '="../fields/' . $field_name . '.html"';

  $call_line .= ' data-sly-call="${' . $field_template_name . '.template';

  if (!empty($field_definition['with'])) {
    $call_line .= ' @';

    $loop = 0;

    foreach ($field_definition['with'] as $key => $value) {
      if (is_array($value)) {
        $value = $key;
      }

      $value_set = null;

      if ($value === 'null') {
        $value_set = '\'\'';
      }
      elseif (is_bool($value)) {
        $value_set = $value ? 'true' : 'false';
      }
      else {
        $value_set = $value;
      }

      $call_line .= ($loop == 0 ? ' ' : ', ') . $key . '=' . $value_set;

      $loop += 1;
    }

    $call_line .= '}"></sly>';

    write_line($fh, $call_line, $level, HTL_LINE_SPACING + 1);
  }
  else {
    write_line($fh, $call_line . '}"></sly>', $level, HTL_LINE_SPACING);
  }
}

function write_htl_component_include($fh, $content_definition, $component_definition, $level = 1) {
  $component_name = $component_definition['name'];

  $field_name = $content_definition['name'];

  $call_line = '<sly';

  if (!empty($content_definition['condition'])) {
    $call_line .= ' data-sly-test="${' . $content_definition['condition'] . '}"';
  }

  $component_htl_variable_name = component_htl_useapi_variable_name($field_name);

  $component_template_name = HTL_FIELD_TEMPLATE_PREFIX . $component_htl_variable_name;

  $call_line .= ' data-sly-use.' . $component_template_name . '="../components/' . $field_name . '.html"';

  $call_line .= ' data-sly-call="${' . $component_template_name . '.template';

  if (!empty($content_definition['with'])) {
    $call_line .= ' @';

    $loop = 0;

    foreach ($content_definition['with'] as $key => $value) {
      if (is_array($value)) {
        $value = $key;
      }

      $value_set = null;

      if ($value === 'null') {
        $value_set = '\'\'';
      }
      elseif (is_bool($value)) {
        $value_set = $value ? 'true' : 'false';
      }
      else {
        $value_set = $value;
      }

      $call_line .= ($loop == 0 ? ' ' : ', ') . $key . '=' . $value_set;

      $loop += 1;
    }

    $call_line .= '}"></sly>';

    write_line($fh, $call_line, $level, HTL_LINE_SPACING + 1);
  }
  else {
    write_line($fh, $call_line . '}"></sly>', $level, HTL_LINE_SPACING + 1);
  }
}

function write_htl_repeater($fh, $repeater_definition, $component_definition, $level = 1) {
  $component_name = $component_definition['name'];

  $component_htl_variable_name = component_htl_useapi_variable_name($component_name);

  $repeater_field_name = !empty($repeater_definition['name']) ? $repeater_definition['name'] : (!empty($repeater_definition['in']) ? $repeater_definition['in'] : 'repeater');

  if (empty($repeater_definition['swiper'])) {
    write_line($fh, '<div class="f--field f--' . $repeater_field_name . '">', $level, HTL_LINE_SPACING + 1);
  }
  else {
    write_line($fh, '<div class="f--field f--' . $repeater_field_name . ' swiper-container">', $level, HTL_LINE_SPACING + 1);
  }

  $level += 1;

  $for_variable = !empty($repeater_definition['for']) ? $repeater_definition['for'] : 'item';

  $in_variable = !empty($repeater_definition['in']) ? $repeater_definition['in'] : 'items';

  $line = '<sly data-sly-list.' . $for_variable . '="${' . $in_variable . '}"';

  if (!empty($repeater_definition['swiper'])) {
    $line .= ' class="swiper-wrapper"';
  }

  $line .= '>';

  write_line($fh, $line, $level, HTL_LINE_SPACING + 1);

  $level += 1;

  $repeat = $repeater_definition['repeat'];

  if (!empty($repeat)) {
    $item_type = !empty($repeat['type']) ? $repeat['type'] : 'component';

    switch ($item_type) {
      case 'component':
        $with = !empty($repeat['with']) ? $repeat['with'] : [];

        if (!empty($repeater_definition['swiper'])) {
          $with['variant'] = !empty($with['variant']) ? $with['variant'] : [];

          $with['variant'][] = '\'swiper-slide\'';
        }

        $component_htl_variable_name = component_htl_useapi_variable_name($repeat['name']);

        $line = '<sly data-sly-use._' . $component_htl_variable_name . '="../components/' . $repeat['name'] . '.html" data-sly-call="${_' . $component_htl_variable_name . '.template';

        if (empty($with)) {
          $line .= '}"></sly>';

          write_line($fh, $line, $level);

          $level -= 1;
        }
        else {
          $line .= ' @ ';

          $numVars = count($with);

          $i = 0;

          foreach ($with as $key => $value) {
            if (is_array($value)) {
              $value = $key;
            }

            $line .= $key . '=' . $for_variable . '.' . $value;

            $i += 1;

            $line .= $i === $numVars ? '' : ', ';
          }

          $line .= '}"></sly>';

          write_line($fh, $line, $level);
        }

        break;

      case 'field':
        $with = !empty($repeat['with']) ? $repeat['with'] : [];

        $field_name = $repeat['name'];

        $field_htl_variable_name = component_htl_useapi_variable_name($field_name);

        $field_template_name = HTL_FIELD_TEMPLATE_PREFIX . $field_htl_variable_name;

        $call_line = '<sly data-sly-use.' . $field_template_name . '="../fields/' . $repeat['name'] . '.html" data-sly-call="${' . $field_template_name . '.template';

        if (empty($with)) {
          $call_line .= '\'}"></sly>';

          write_line($fh, $call_line, $level);

          $level -= 1;
        }
        else {
          $call_line .= ' @ ';

          $numVars = count($with);

          $i = 0;

          foreach ($with as $key => $value) {
            if (is_array($value)) {
              $value = $key;
            }

            $call_line .= $key . '=' . $for_variable . '.' . $value;

            $i += 1;

            $call_line .= $i === $numVars ? '' : ', ';
          }

          $call_line .= '}"></sly>';

          write_line($fh, $call_line, $level);
        }

        break;
    }
  }

  $level -= 1;

  write_line($fh, '</sly>', $level);

  if (!empty($repeater_definition['swiper'])) {
    write_line($fh, '<div class="swiper-button-prev"></div>', $level, HTL_LINE_SPACING + 1);
    write_line($fh, '<div class="swiper-button-next"></div>', $level, 1);
  }

  $level -= 1;

  write_line($fh, "</div>", $level);
}

function write_htl_content($fh, $component_markup, $component_definition, $level = 1) {
  global $recurse;

  if (is_array($component_markup)) {
    if (!empty($component_markup['content'])) {
      if (is_array($component_markup['content'])) {
        foreach ($component_markup['content'] as $content_definition) {
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
              write_htl_component_include($fh, $content_definition, $component_definition, $level);

              if ($recurse) {
                $component_component_definition = $content_definition;

                $own = !empty($component_component_definition['own']) ? TRUE : FALSE;

                generate_aem_component($component_component_definition, $own);
              }

              break;

            case 'repeater':
              write_htl_repeater($fh, $content_definition, $component_definition, $level);

              if (!empty($content_definition['repeat']) && $recurse) {
                $repeat = $content_definition['repeat'];

                $own = !empty($repeat['own']) ? TRUE : FALSE;

                generate_aem_component($repeat, $own);
              }

              break;

            case 'field':
              write_htl_field_include($fh, $content_definition, $component_definition, $level);

              if ($recurse) {
                $field_component_definition = $content_definition;

                $own = !empty($field_component_definition['own']) ? TRUE : FALSE;

                generate_aem_component($field_component_definition, $own);
              }

              break;

            case 'container':
              write_htl_container($fh, $content_definition, $component_definition, $level);

              break;

            case 'markup':
              write_line($fh, $content_definition['markup'], $level);

              break;
          }
        }
      }
    }
  }
  elseif (!empty($component_markup)) {
    write_line($fh, $component_markup, $level);
  }
}

function generate_field_htl_file($component_definition) {
  $component_name = $component_definition['name'];

  $component_htl_filename = component_htl_filename($component_definition);

  $fh = fopen_create_or_truncate($component_htl_filename, 'wa+');

  $level = 0;

  if (!empty($component_definition['with'])) {
    $with = $component_definition['with'];

    $context = ' @ ';

    $loop = 0;

    foreach ($with as $key => $value) {
      $context .= ($loop != 0 ? ', ' : '' ). $key;

      $loop += 1;
    }

    write_line($fh, '<template data-sly-template.template="${' . $context . ' }">', $level, 0);
  }
  else {
    write_line($fh, '<template data-sly-template.template="${}">', $level, 0);
  }

  $level += 1;

  write_line($fh, '<div class="f--field f--' . $component_name . '">', $level, 1, HTL_TAB_SPACING);

  $level += 1;

  if (!empty($component_definition['markup'])) {
    $component_markup = $component_definition['markup'];

    if (!empty($component_markup)) {
      write_htl_content($fh, $component_markup, $component_definition, $level);
    }
  }
  elseif (!empty($component_definition['with'])) {
    $with = $component_definition['with'];

    foreach ($with as $key => $value) {
      write_line($fh, '${' . $key . ' @ context=\'html\'}', $level, 1, HTL_TAB_SPACING);
    }
  }

  $level -= 1;

  write_line($fh, '</div>', $level, HTL_LINE_SPACING, HTL_TAB_SPACING);

  $level -= 1;

  write_line($fh, '</template>', $level, HTL_LINE_SPACING, HTL_TAB_SPACING);

  write_line($fh, "", 0, 1);

  fclose($fh);
}

function _recurse_with_variable_arguments($content) {
  $arguments = [];

  foreach ($content as $item) {
    $type = !empty($item['type']) ? $item['type'] : 'field';

    switch ($type) {
      case 'field':
        if (!empty($item['with'])) {
          foreach ($item['with'] as $key => $value) {
            if ($value != 'null' && !is_bool($value)) {
              $arguments[] = $value;
            }
          }
        }

        break;

      case 'container':
        if (!empty($item['content'])) {
          $sub_arguments = _recurse_with_variable_arguments($item['content']);

          $arguments = array_merge($arguments, $sub_arguments);
        }

        if (!empty($item['condition'])) {
          $arguments[] = $item['condition'];
        }

        break;

      case 'repeater':
        if (!empty($item['in'])) {
          $arguments[] = $item['in'];
        }

        break;
    }
  }

  $arguments = array_unique($arguments);

  return $arguments;
}

function write_htl_component_template_header($fh, $component_definition, $level) {
  if (!empty($component_definition['markup']) && is_array($component_definition['markup'])) {
    $content = $component_definition['markup']['content'];

    $arguments = _recurse_with_variable_arguments($content);

    write_line($fh, '<template data-sly-template.template="${ @ ' . implode(', ', $arguments) . ' }">', $level, 0);
  }
  else {
    write_line($fh, '<template data-sly-template.template="${}">', $level, 0);
  }

  return;

  if (!empty($component_definition['data'])) {
    $arguments = [];

    $loop = 0;

    foreach ($component_definition['data'] as $key => $value) {
      $arguments[] = $key;

      $loop += 1;
    }

    write_line($fh, '<template data-sly-template.template="${ @ ' . implode(', ', $arguments) . ' }">', $level, 0);
  }
  elseif (!empty($component_definition['with'])) {
    $arguments = [];

    $loop = 0;

    foreach ($component_definition['with'] as $key => $value) {
      if ($value != 'null' && !is_bool($value)) {
        $arguments[] = $key;
      }

      $loop += 1;
    }

    write_line($fh, '<template data-sly-template.template="${ @ ' . implode(', ', $arguments) . ' }">', $level, 0);
  }
}

function generate_component_htl_file($component_definition) {
  $component_name = $component_definition['name'];

  $component_htl_filename = component_htl_filename($component_definition);

  $fh = fopen_create_or_truncate($component_htl_filename, 'wa+');

  $element = !empty($component_definition['element']) ? $component_definition['element'] : 'div';

  $level = 0;

  $component_htl_variable_name = component_htl_useapi_variable_name($component_name);

  //write_line($fh, '<sly data-sly-use._data="' . $component_htl_variable_name . '.js"></sly>', $level, 0);

  write_htl_component_template_header($fh, $component_definition, $level);

  $level += 1;

  write_line($fh, '<' . $element . ' class="cc--component-container cc--' . $component_name . '">', $level, HTL_LINE_SPACING + 1);

  $level += 1;

  write_line($fh, '<div class="c--component c--' . $component_name . '">', $level, HTL_LINE_SPACING);

  $level += 1;

  //write_line($fh, '', $level, HTL_LINE_SPACING);

  if (!empty($component_definition['aria'])) {
    write_line($fh, "{% set container_aria_label = '" . $component_definition['aria'] . "' %}", $level);
  }

  if (!empty($component_definition['markup'])) {
    $component_markup = $component_definition['markup'];

    write_htl_content($fh, $component_markup, $component_definition, $level);
  }

  $level -= 1;

  write_line($fh, '</div>', $level, HTL_LINE_SPACING + 1);

  $level -= 1;

  write_line($fh, '</' . $element . '>', $level, HTL_LINE_SPACING);

  $level -= 1;

  write_line($fh, '</template>', $level, HTL_LINE_SPACING + 1);

  write_line($fh, '', $level, 1);

  fclose($fh);
}

function generate_htl_file($component_definition) {
  $component_type = $component_definition['type'];

  switch ($component_type) {
    case 'field':
      generate_field_htl_file($component_definition);

      break;

    default:
      generate_component_htl_file($component_definition);

      break;
  }
}
