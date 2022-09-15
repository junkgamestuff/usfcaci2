<?php

namespace Drupal\usfca_school_context\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the 'School context' condition.
 *
 * @Condition(
 *   id = "usfca_schoool_context_condition",
 *   label = @Translation("School"),
 *   context_definitions = {
 *     "school" = @ContextDefinition("entity:taxonomy_term", required = FALSE, label = @Translation("School taxonomy Term"))
 *   }
 * )
 */
class SchoolCondition extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['schools' => []] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $schools = $this->entityTypeManager
      ->getStorage("taxonomy_term")
      ->loadTree('school', $parent = 0, $max_depth = NULL, $load_entities = FALSE);

    $options = [];
    foreach ($schools as $school) {
      $options[$school->name] = $school->name;
    }
    $options['no_school_context'] = $this->t('Without school context');

    $form['schools'] = [
      '#title' => $this->t('Schools'),
      '#type' => 'checkboxes',
      '#options' => $options,
      '#default_value' => $this->configuration['schools'],
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['schools'] = array_filter($form_state->getValue('schools'));
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * Provides a human readable summary of the condition's configuration.
   */
  public function summary() {
    if (empty($this->configuration['schools'])) {
      return $this->t('Not restricted');
    }

    $schools = implode(', ', $this->configuration['schools']);
    if ($this->isNegated()) {
      return $this->t('Doesn\'t display in schools: @schools', ['@schools' => $schools]);
    }

    return $this->t('Display in following schools: @schools', ['@schools' => $schools]);
  }

  /**
   * Evaluates the condition and returns TRUE or FALSE accordingly.
   *
   * @return bool
   *   TRUE if the condition has been met, FALSE otherwise.
   */
  public function evaluate() {
    if (empty($this->configuration['schools']) && !$this->isNegated()) {
      return TRUE;
    }

    $school = $this->getContextValue('school');
    // If school context is empty then check the specific condition.
    if (empty($school)) {
      return !empty($this->configuration['schools']['no_school_context']);
    }

    return !empty($this->configuration['schools'][$school->getName()]);
  }

}
