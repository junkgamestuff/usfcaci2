<?php

namespace Drupal\usfca_minisite_nav;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuLinkManagerInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\menu_link_content\Plugin\Menu\MenuLinkContent as MenuLinkContentPlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Menu form preprocess class.
 *
 * @package Drupal\usfca_minisite_nav
 */
class MinisiteNavRootField implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The menu link manager.
   *
   * @var \Drupal\Core\Menu\MenuLinkManagerInterface
   */
  protected $menuLinkManager;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * Constructs a MenuForm object.
   *
   * @param \Drupal\Core\Menu\MenuLinkManagerInterface $menu_link_manager
   *   The menu link manager.
   *   The menu link content storage handler.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   */
  public function __construct(MenuLinkManagerInterface $menu_link_manager, EntityRepositoryInterface $entity_repository = NULL) {
    $this->menuLinkManager = $menu_link_manager;
    $this->entityRepository = $entity_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.menu.link'),
      $container->get('entity.repository')
    );
  }

  /**
   * @param $form
   * @param $form_state
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function alter(&$form, $form_state) {

    // Adding header item.
    foreach ($form['links']['links']['#header'] as $header_item) {
      if (is_array($header_item)
        && $header_item['data'] instanceof TranslatableMarkup
        && $header_item['data']->getUntranslatedString() == 'Enabled') {
        $header_item = $this->t('Is menu tree root');
        $form['links']['links']['#header']['is_root'] = $header_item;
        break;
      }
    }

    $links = $form['links']['links'];

    foreach (Element::children($links) as $id) {
      if (isset($links[$id]['#item']->link)) {
        /** @var \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent $menu_link */
        $menu_link = $links[$id]['#item']->link;
        if (!$menu_link instanceof MenuLinkContentPlugin) {
          $form['links']['links'][$id]['is_root'] = [];
          continue;
        }

        $menu_link_content = $this->entityRepository
          ->loadEntityByUuid('menu_link_content', $menu_link->getDerivativeId());

        $form['links']['links'][$id]['is_root'] = $form['links']['links'][$id]['enabled'];
        $form['links']['links'][$id]['is_root']['#default_value'] = $menu_link_content->get('is_root')->value;

      }
    }
  }

  /**
   * The submit method.
   *
   * @param $form_state
   *   The form state object.
   * @param $menu
   *   The menu tree.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function submit(array $form, FormStateInterface $form_state) {
    $menu = $form['links']['links'];
    $values = $form_state->getValues();
    foreach ($values['links'] as $id => $menu_item_values) {
      if (!isset($menu[$id]['#item']->link)) {
        continue;
      }

      /** @var \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent $menu_link */
      $menu_link = $menu[$id]['#item']->link;

      if (!$menu_link instanceof MenuLinkContentPlugin) {
        continue;
      }

      $menu_link_content = $this->entityRepository
        ->loadEntityByUuid('menu_link_content', $menu_link->getDerivativeId());
      $menu_link_content->set('is_root', $menu_item_values['is_root']);
      $menu_link_content->save();
    }
  }
}
