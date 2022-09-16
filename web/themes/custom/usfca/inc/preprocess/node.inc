<?php

/**
 * @file
 * Preprocess node functions.
 */

use Drupal\node\NodeInterface;

/**
 * Implements hook_preprocess_node().
 */
function usfca_preprocess_node(&$variables) {
  $node = $variables['elements']['#node'];

  if (!($node instanceof NodeInterface)) {
    return;
  }

  switch ($node->bundle()) {
    case 'chapter_page':
      $jump_links = [];

      foreach ($node->field_components->referencedEntities() as $paragraph) {
        if ($paragraph->hasField('field_title') && !$paragraph->field_title->isEmpty()) {
          $jump_links[] = [
            'title' => $paragraph->field_title->value,
            'linkCta' => $paragraph->field_title->value,
            'id' => usfca_get_chapter_id($paragraph),
          ];
        }
      }

      $variables['jump_link_data'] = $jump_links;

      break;

    case 'program_home':
    case 'program_subpage':
      $variables['logged_in'] = \Drupal::currentUser()->isAuthenticated();

      break;

    case 'program':
      _usfca_add_taxonomy_interests($variables);
      _usfca_add_taxonomy_careers($variables);

      break;

    case 'search_results':
      $variables['inputValue'] = \Drupal::request()->query->get('q');
      $active_refinement = \Drupal::request()->query->get('gps_refinement');
      $variables['activeRefinement'] = !empty($active_refinement) ? $active_refinement : 'school';

      $variables['#cache']['contexts'][] = 'url.query_args:q';
      $variables['#cache']['contexts'][] = 'url.query_args:gps_refinement';

      break;
  }

  if ($variables['view_mode'] == 'full') {
    _usfca_add_content_extra_to_content($variables);
  }
}

function _usfca_sort_taxonomy_by_namecmp($a, $b) {
  return strcmp($a->name, $b->name);
}

/**
 * Helper function: get Interests.
 */
function _usfca_add_taxonomy_interests(&$variables) {
  $interests = [
    0 => [
      'value' => 'All',
      'label' => "I'm interested in",
    ],
  ];

  $query = \Drupal::entityQuery('taxonomy_term');
  $query->condition('vid', 'interests');
  $query->sort('name', 'ASC');

  $tids = $query->execute();
  $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);

  foreach ($terms as $term) {
    $interests[] = [
      'value' => $term->id(),
      'label' => $term->name->value,
    ];
  }

  $variables['interests'] = $interests;
}

/**
 * Helper function: get Careers.
 */
function _usfca_add_taxonomy_careers(&$variables) {
  $careers = [
    0 => [
      'value' => 'All',
      'label' => "I want to be a",
    ],
  ];

  $query = \Drupal::entityQuery('taxonomy_term');
  $query->condition('vid', 'career_goals');
  $query->sort('name', 'ASC');

  $tids = $query->execute();
  $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);

  foreach ($terms as $term) {
    $careers[] = [
      'value' => $term->id(),
      'label' => $term->name->value,
    ];
  }

  $variables['careers'] = $careers;
}

/**
 * Implements hook_preprocess_node().
 */
function usfca_preprocess_node__profile__full(&$variables) {
  $node = $variables['elements']['#node'];
  $chaptered_fields = [
    'field_bio',
    'field_expertise',
    'field_research_areas',
    'field_appointments',
    'field_education',
    'field_prior_experience',
    'field_awards_distinctions',
    'field_books',
    'field_selected_publications',
    'field_additional_information',
  ];

  $jump_links = [];
  foreach ($chaptered_fields as $field) {
    if ($node->hasField($field) && !$node->{$field}->isEmpty()) {
      $value = $node->{$field}->value;
      if (!empty(strip_tags($value))) {
        $jump_link = [
          'title' => $node->{$field}->getFieldDefinition()->getLabel(),
          'id' => $field,
        ];
        if ($field == 'field_additional_information' && !empty($node->field__information_label->value)) {
          $jump_link['title'] = $node->field__information_label->value;
        }
        $jump_links[] = $jump_link;
      }
    }
  }
  $variables['jump_link_data'] = $jump_links;

  $roles = [];
  if ($node->hasField('field_school_program_roles') && !$node->field_school_program_roles->isEmpty()) {
    foreach ($node->field_school_program_roles->referencedEntities() as $paragraph) {
      // Ignoring empty paragraph.
      if ($paragraph->field_role->isEmpty() && $paragraph->field_school->isEmpty() && $paragraph->field_program->isEmpty()) {
        continue;
      }

      $role = '';
      if (!$paragraph->field_role->isEmpty()) {
        $role = $paragraph->field_role->entity->getName();
      }

      if (empty($roles[$role])) {
        $roles[$role] = [
          'title' => $role,
          'places' => [],
        ];
      }

      if (!$paragraph->field_school->isEmpty()) {
        $school = $paragraph->field_school->entity->getName();
        $roles[$role]['places'][$school]['name'] = $school;
      }

      // Per client request, remove school from output.
      $roles[$role]['places'][$school]['name'] = NULL;
    }
  }
  $variables['roles'] = $roles;
}
