<?php

namespace Drupal\usfca_migrate\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\Component\Utility\Html;
use Drupal\migrate\MigrateSkipRowException;

/**
 * Provides a ParseIntro migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "parse_intro"
 * )
 */
class ParseIntro extends ParseBody {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Removing not Intro text.
    $intros = [];
    $dom = Html::load($value);
    $paragraphs = $dom->getElementsByTagName('p');
    $dom_intro = new \DOMDocument();
    $dom_intro->loadHTML('<html></html>');
    for ($i = $paragraphs->length; --$i >= 0; ) {
      $paragraph = $paragraphs->item($i);
      $class = $paragraph->getAttribute('class');
      if (!empty($class) && strpos($class, 'text_intro') !== false) {
        $node = $dom_intro->importNode($paragraph, TRUE);
        $dom_intro->documentElement->appendChild($node);
      }
    }
    $value = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<html>', '</html>', '<body>', '</body>'], ['', '', '', ''], $dom_intro->saveHTML()));
    $value = str_replace(["\r", "\n"], '', $value);
    if (empty($value)) {
      throw new MigrateSkipRowException('Intro text is empty', TRUE);
    }

    $value = ' ' . $value . ' ';
    $value = preg_replace_callback('/\[\[.*?\]\]/s', [
      &$this,
      'replaceToken',
    ], $value);
    return $value;
  }

}
