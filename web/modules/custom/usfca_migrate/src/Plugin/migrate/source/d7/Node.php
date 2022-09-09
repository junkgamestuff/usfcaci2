<?php

namespace Drupal\usfca_migrate\Plugin\migrate\source\d7;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d7\Node as D7Node;

/**
 * Custom node source including url aliases.
 *
 * @MigrateSource(
 *   id = "usfca_migrate_node"
 * )
 */
class Node extends D7Node {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();
    // Only migrate published records.
    //$query->condition('n.status', 1);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return ['alias' => $this->t('Path alias')] + parent::fields();
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Include path alias.
    $nid = $row->getSourceProperty('nid');

    $query = $this->select('url_alias', 'ua')
      ->fields('ua', ['alias']);
    $query->condition('ua.source', 'node/' . $nid);
    $alias = $query->execute()->fetchField();

    if (!empty($alias)) {
      $row->setSourceProperty('alias', '/' . $alias);
    }

    return parent::prepareRow($row);
  }

}
