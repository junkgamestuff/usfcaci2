<?php
/**
 * @file
 * Hooks provided by the DP Twig Helper module.
 */

use Drupal\Core\Entity\Entity\EntityViewDisplay;

/**
 * Perform alterations before an field values array is applied to the PL template.
 *
 * @param array $value
 *   Array of PL fields values.
 * @param array $ref_entity
 *   Referenced paragraph.
 * @param string $field_name
 *   Field name (referencing the paragraph).
 * @param \Drupal\Core\Entity\Entity\EntityViewDisplay $display.
 *   Entity view display.
 * @param int $level
 *   Nesting level.
 */
function hook_pl_component_fields_PARAGRAPH_TYPE_alter(
  &$value,
  $ref_entity,
  $field_name = NULL,
  $display = NULL,
  $level = 0
) {
  $parent = $ref_entity->getParentEntity();
}


/**
 * Alters the squashed field value for PL.
 *
 * @param array $value
 *   Squashed values array.
 * @param $variables
 *   Theme variables.
 * @param \Drupal\Core\Entity\Entity\EntityViewDisplay $display
 *   The display object.
 * @param string $field_name
 *   Field name.
 * @param \Drupal\Core\Field\FieldItemList $field_item
 *   The field item.
 * @param $twig_config
 *   Twig config.
 * @param $level
 *   Nesting level.
 */
function hook_field_item_squash_entity_reference_value_alter(
  &$value,
  $variables,
  EntityViewDisplay $display,
  $field_name,
  $field_item,
  $twig_config,
  $level
) {
  $value = (string) $field_item->getValue();
}

/**
 * Alters the squashed field value for PL.
 *
 * @param array $value
 *   Squashed values array.
 * @param $variables
 *   Theme variables.
 * @param \Drupal\Core\Entity\Entity\EntityViewDisplay $display
 *   The display object.
 * @param string $field_name
 *   Field name.
 * @param \Drupal\Core\Field\FieldItemList $field_item
 *   The field item.
 * @param $twig_config
 *   Twig config.
 * @param $level
 *   Nesting level.
 */
function hook_field_item_squash_entity_reference_value(
  &$variables,
  EntityViewDisplay $display,
  $field_name,
  $field_item,
  $twig_config,
  $level
) {
  return $field_item->value;
}