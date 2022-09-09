<?php

namespace Drupal\usfca_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin to provide the list of files attached to file reference field.
 *
 * @MigrateSource(
 *   id = "usfca_image_reference_field"
 * )
 */
class ImageReferenceField extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {

    $field_name = $this->configuration['field_name'];

    if (!empty($field_name)) {

      $query = $this->select('field_data_' . $field_name, 'h');
      $query->innerJoin('file_managed', 'f', 'h.' . $field_name . '_fid  = f.fid');
      $query->leftJoin('field_data_field_file_image_title_text', 't', 'h.' . $field_name . '_fid  = t.entity_id');
      $query->leftJoin('field_data_field_file_image_alt_text', 'a', 'h.' . $field_name . '_fid  = a.entity_id');
      $query->addField('h', $field_name . '_fid', 'fid');
      $query->addField('h', $field_name . '_alt', 'alt');
      $query->addField('h', $field_name . '_title', 'title');
      $query->addField('t', 'field_file_image_title_text_value', 'media_title');
      $query->addField('a', 'field_file_image_alt_text_value', 'media_alt');
      $query->addField('f', 'uri');
      $query->distinct();

      $bundles = $this->configuration['bundles'];
      if (!empty($bundles)) {
        $query->condition('h.bundle', $bundles, 'IN');
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
      'alt' => $this->t('Alt Text'),
      'title' => $this->t('Title Text'),
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