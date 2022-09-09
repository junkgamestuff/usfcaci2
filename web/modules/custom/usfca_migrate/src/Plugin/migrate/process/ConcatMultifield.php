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
 *   id = "concat_multifield",
 *   handle_multiples = TRUE
 * )
 */
class ConcatMultifield extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $string = '';
    if (!empty($value) && is_array($value) && !empty($this->configuration['source_key'])) {

      if (!empty($this->configuration['prefix'])) {
        $string .= $this->configuration['prefix'];
      }

      $values = [];
      foreach ($value as $key => $item) {
        if (!empty($item[$this->configuration['source_key']])) {
          $values[] = $item[$this->configuration['source_key']];
        }
      }

      $string .= implode(!empty($this->configuration['separator']) ? $this->configuration['separator'] : '', $values);

      if (!empty($this->configuration['suffix'])) {
        $string .= $this->configuration['suffix'];
      }
    }
    return $string;
  }

}
