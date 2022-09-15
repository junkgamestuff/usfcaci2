<?php

namespace Drupal\usfca_migrate\Plugin\migrate\process;

use Drupal\Component\Utility\Html;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Database\Database;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Template\Attribute;
use Drupal\file\Entity\File;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'ParseBody' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "parse_body"
 * )
 */
class ParseBody extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The subfolder to move file.
   *
   * @var string
   */
  private $type;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface $file_system
   */
  protected FileSystemInterface $fileSystem;

  /**
   * Creates a FileUri instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \InvalidArgumentException
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    FileSystemInterface $file_system
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->fileSystem = $file_system;
    $this->type = isset($this->configuration['type']) ? $this->configuration['type'] : 'article';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!empty($value)) {
      $value = ' ' . $value . ' ';
      $value = preg_replace_callback('/\[\[.*?\]\]/s', [
        &$this,
        'replaceToken',
      ], $value);
    }
    // Removing Intro text.
    $dom = Html::load($value);
    $paragraphs = $dom->getElementsByTagName('p');
    for ($i = $paragraphs->length; --$i >= 0; ) {
      $paragraph = $paragraphs->item($i);
      $class = $paragraph->getAttribute('class');
      if (!empty($class) && strpos($class, 'text_intro') !== false) {
        $paragraph->parentNode->removeChild($paragraph);
      }
    }
    $value = Html::serialize($dom);
    return $value;
  }

  /**
   * Copy source file into migration subfolder and returns new uri.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   *
   * @return string
   *   The uri of the migrated file.
   */
  protected function copyMigrationFile($path, $source) {
    $destination = "public://migrated/" . $this->type . "/" . $path;

    // Check if a writable directory exists, and if not try to create it.
    $dir = $this->fileSystem->dirname($destination);
    if (substr($dir, -3) == '://') {
      $dir = $this->fileSystem->realpath($dir);
    }

    // If the directory exists and is writable, avoid
    // \Drupal\Core\File\FileSystemInterface::prepareDirectory() call and write
    // the file to destination.
    if (!is_dir($dir) || !is_writable($dir)) {
      if (!$this->fileSystem->prepareDirectory($dir, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
        throw new MigrateException("Could not create or write to directory '$dir'");
      }
    }

    return $this->fileSystem->copy($source, $destination);
  }

  /**
   * Replace callback to convert a media file tag into HTML markup.
   *
   * Partially copied from 7.x media module media.filter.inc (media_filter).
   */
  protected function replaceToken($match) {
    $settings = [];
    $match = str_replace("[[", "", $match);
    $match = str_replace("]]", "", $match);
    $tag = $match[0];

    if (!is_string($tag)) {
      throw new MigrateSkipRowException('No File Tag', TRUE);
    }

    // Make it into a fancy array.
    $tag_info = Json::decode($tag);

    if (!isset($tag_info['fid'])) {
      throw new MigrateSkipRowException('No FID', TRUE);
    }

    $result = Database::getConnection('default', 'migrate')
      ->select('file_managed', 'fm')
      ->fields('fm')
      ->condition('fm.fid', $tag_info['fid'], '=')
      ->execute()
      ->fetchAssoc();

    if (!isset($result['uri'])) {
      throw new MigrateSkipRowException('Couldn\'t Load File', TRUE);
    }

    if (isset($result['type']) && $result['type'] == 'video') {
      preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $result['uri'], $youtube_match);
      $youtube_id = $youtube_match[1];
      if (!empty($youtube_id)) {
        $youtube_url = 'https://www.youtube.com/embed/' . $youtube_id;
        $youtube_embed = '<br/><iframe width="560" height="315" src="https://www.youtube.com/embed/' . trim($youtube_id) . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br/>';
        $output = $youtube_embed;
      }
      echo "output:$output\n";
    }
    else {
      // Getting the correct source file from migration folder.
      $path = str_replace('public://', '', $result['uri']);
      $source = str_replace('public://', 'public://migration_files/files_live/', $result['uri']);
      $uri = $this->copyMigrationFile($path, $source);

      // Create File entity based on the migrated file.
      $image = $this->entityTypeManager->getStorage('file')->create();
      $image->setFileUri($uri);
      $image->setOwnerId(1);
      $image->setMimeType('image/' . pathinfo($uri, PATHINFO_EXTENSION));
      $image->setFileName($this->fileSystem->basename($uri));
      $image->setPermanent();
      $image->save();

      // Create Media entity for created File.
      $media = $this->entityTypeManager->getStorage('media')->create([
        'bundle' => 'image',
        'uid' => 1,
        'thumbnail' => [
          'target_id' => $image->id(),
          'alt' => !empty($tag_info['attributes']['alt']) ? $tag_info['attributes']['alt'] : NULL,
        ],
        'field_media_image' => [
          'target_id' => $image->id(),
          'alt' => !empty($tag_info['attributes']['alt']) ? $tag_info['attributes']['alt'] : NULL,
        ],
      ]);
      $media->setPublished(TRUE)->save();

      // Creating ckeditor media placeholder.
      // <drupal-media data-align="right" data-caption="caption goes here" data-entity-type="media" data-entity-uuid="52cf41ab-cc52-4d50-aec1-28fb8c650045" data-view-mode=""></drupal-media>
      $attributes = new Attribute();
      $attributes['data-entity-type'] = 'media';
      $attributes['data-entity-uuid'] = $media->uuid();
      $attributes['data-view-mode'] = '';
      if (!empty($tag_info['attributes']['title'])) {
        $attributes['data-caption'] = $tag_info['attributes']['title'];
      }
      $attributes['data-align'] = 'center';
      if (!empty($tag_info['attributes']['class'])) {
        if (strpos($tag_info['attributes']['class'], 'block_left') !== false) {
          $attributes['data-align'] = 'left';
        }
        if (strpos($tag_info['attributes']['class'], 'block_right') !== false) {
          $attributes['data-align'] = 'right';
        }
      }
      $output = '<drupal-media ' . $attributes . '></drupal-media>';
    }

    return $output;
  }

}
