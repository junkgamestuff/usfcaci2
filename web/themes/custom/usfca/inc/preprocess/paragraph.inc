<?php

/**
 * @file
 * Preprocess paragraph functions.
 */

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Implements hook_preprocess_HOOK() for paragraph.
 */
function usfca_preprocess_paragraph(&$variables) {
  $paragraph = $variables['elements']['#paragraph'] ?? NULL;
  if (!($paragraph instanceof ParagraphInterface)) {
    return;
  }
  switch ($paragraph->bundle()) {
    case 'chapter':
      $variables['id'] = usfca_get_chapter_id($paragraph);

      break;
    case 'contact_box':
      _usfca_preprocess_paragraph_add_program_name_to_contact_box($variables);

      break;
  }
}

/**
 * Extract campus values from the current page node and inject to the paragraph.
 */
function _usfca_preprocess_paragraph_add_program_name_to_contact_box(&$variables) {
  $node = \Drupal::routeMatch()->getParameter('node');

  if ($node instanceof NodeInterface) {
    if (!empty($node->field_program)) {
      $variables['program_name'] = $node->field_program[0]->entity->label();
    }
  }
}
