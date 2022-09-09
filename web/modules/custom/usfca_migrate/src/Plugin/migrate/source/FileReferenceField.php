<?php

namespace Drupal\usfca_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin to provide the list of files attached to file reference field.
 *
 * @MigrateSource(
 *   id = "usfca_file_reference_field"
 * )
 */
class FileReferenceField extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {

    $field_name = $this->configuration['field_name'];

    if (!empty($field_name)) {

      $query = $this->select('field_data_' . $field_name, 'h');
      $query->innerJoin('file_managed', 'f', 'h.' . $field_name . '_fid  = f.fid');
      $query->addField('h', $field_name . '_fid', 'fid');
      $query->addField('f', 'uri');
      $query->distinct();

      $bundles = $this->configuration['bundles'];
      if (!empty($bundles)) {
        $query->condition('bundle', $bundles, 'IN');
      }

      $query->condition('f.uri', 'public://%', 'LIKE');

      return $query;

    }
    else {
      return NULL;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $filename = substr($row->getSourceProperty('uri'), 9);
    $row->setSourceProperty('filename', $filename);
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'fid' => $this->t('File Id'),
      'filename' => $this->t('Filename'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['fid'] = [
      'type' => 'integer',
    ];

    return $ids;
  }

}