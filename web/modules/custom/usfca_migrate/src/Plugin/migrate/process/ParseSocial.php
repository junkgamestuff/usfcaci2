<?php

namespace Drupal\usfca_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\Component\Utility\Html;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\MigrateException;
use Drupal\Core\File\FileSystemInterface;

/**
 * Provides a 'Concat Multifield' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "parse_social",
 *   handle_multiples = TRUE
 * )
 */
class ParseSocial extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if (!empty($value) && is_array($value) && !empty($this->configuration['social_type'])) {
      foreach ($value as $key => $item) {
        // Fix links if needed by adding scheme.
        if (!empty($item['url'])) {
          $item['url'] = $this->addMissingScheme($item['url']);
        }

        // Searching for Linkedin.
        if ($this->configuration['social_type'] == 'linkedin') {
          if (!empty($item['url']) && strpos($item['url'], 'linkedin') !== false) {
            return [$item];
          }
        }

        // Searching for twitter.
        if ($this->configuration['social_type'] == 'twitter') {
          if (!empty($item['url']) && strpos($item['url'], 'twitter') !== false) {
            return [$item];
          }
        }

        // Excluding twitter and linkedin.
        if ($this->configuration['social_type'] == 'extra') {
          if (!empty($item['url']) && strpos($item['url'], 'twitter') !== false) {
            unset($value[$key]);
          }
          if (!empty($item['url']) && strpos($item['url'], 'linkedin') !== false) {
            unset($value[$key]);
          }
          $value[$key]['url'] = $item['url'];
        }
      }
    }

    if ($this->configuration['social_type'] != 'extra') {
      return [];
    }

    return $value;
  }

  /**
   * Helper function to provide scheme when it is missing.
   *
   * @param string $url
   *   A configuration array containing information about the plugin instance.
   * @param string $scheme
   *   The plugin_id for the plugin instance.
   *
   * @return string
   *   An URL with scheme.
   */
  protected function addMissingScheme($url, $scheme = 'https://') {
    return parse_url($url, PHP_URL_SCHEME) === null ?
      $scheme . $url : $url;
  }
  

}
