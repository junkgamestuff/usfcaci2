<?php

namespace Drupal\usfca_alert\Normalizer;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\Core\Render\Renderer;
use Drupal\node\NodeInterface;
use Drupal\serialization\Normalizer\ContentEntityNormalizer;
use Drupal\serialization\Normalizer\FieldableEntityNormalizerTrait;

/**
 * {@inheritdoc}
 */
class RenderedNormalizer extends ContentEntityNormalizer {
  use FieldableEntityNormalizerTrait;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityTypeRepositoryInterface $entity_type_repository, EntityFieldManagerInterface $entity_field_manager, Renderer $renderer) {
    parent::__construct($entity_type_manager, $entity_type_repository, $entity_field_manager);
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    $data = parent::normalize($object, $format, $context);
    if ($object instanceof NodeInterface) {
      if ($object->bundle() === 'alert') {
        $view_builder = $this->entityTypeManager->getViewBuilder('node');
        $build = $view_builder->view($object);
        $data['rendered'] = $this->renderer->renderPlain($build);
      }
    }
    return $data;
  }

}
