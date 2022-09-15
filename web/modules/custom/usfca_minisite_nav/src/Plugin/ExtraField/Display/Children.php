<?php

namespace Drupal\usfca_minisite_nav\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\MenuActiveTrail;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayFormattedBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extra field with a menu to children of the current page.
 *
 * @ExtraFieldDisplay(
 *   id = "usfca_minisite_nav_children",
 *   label = @Translation("Minisite Nav Menu Children"),
 *   bundles = {
 *     "node.minisite_home",
 *     "node.minisite_subpage"
 *   }
 * )
 */
class Children extends ExtraFieldDisplayFormattedBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Injected service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuTreeService;

  /**
   * Injected service.
   *
   * @var \Drupal\Core\Menu\MenuActiveTrail
   */
  protected $menuActiveTrail;

  /**
   * Injected service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $menuLinkStorage;

  /**
   * Injected service
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $manager, MenuLinkTreeInterface $menuLinkTree, MenuActiveTrail $menuActiveTrail, RouteMatchInterface $routeMatch) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->menuTreeService = $menuLinkTree;
    $this->menuLinkStorage = $manager->getStorage('menu_link_content');
    $this->menuActiveTrail = $menuActiveTrail;
    $this->routeMatch = $routeMatch;
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
      $container->get('menu.link_tree'),
      $container->get('menu.active_trail'),
      $container->get('current_route_match'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(ContentEntityInterface $entity) {
    $build = [];
    if ($entity->isNew()) {
      return $build;
    }
    if (!$entity instanceof NodeInterface) {
      $entity = $this->routeMatch->getParameter('node');
    }
    if (!$entity instanceof NodeInterface) {
      return $build;
    }
    $menu_id = $this->getMenuId($entity);
    if (empty($menu_id)) {
      return $build;
    }

    $menu_link = $this->menuLinkStorage->load($menu_id);
    $menu_name = $menu_link->menu_name->value;
    $active_trail = $this->menuActiveTrail->getActiveTrailIds($menu_name);

    $parent = $this->findTreeRoot($menu_link, $active_trail);

    $parameters = $parameters = new MenuTreeParameters();

    $parameters
      ->setActiveTrail($active_trail)
      ->setRoot($parent)
      ->excludeRoot()
      ->onlyEnabledLinks();
    $tree = $this->menuTreeService->load($menu_name, $parameters);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];

    $tree = $this->menuTreeService->transform($tree, $manipulators);

    $menu_array = [
      'label' => 'usfca-minisite-nav-children',
      'name' => 'main',
      'items' => $this->prepareTree($tree),
    ];

    $buttons = [];

    if (!empty($entity->field_minisite)) {
      if (!empty($entity->field_minisite[0]->entity->field_cta_links)) {
        foreach ($entity->field_minisite[0]->entity->field_cta_links as $link_item) {
          $buttons[] = [
            'url' => $link_item->getUrl(),
            'title' => $link_item->title,
          ];
        }
      }
    }

    $build = [
      '#cache' => [
        'tags' => [
          'config:system.menu.main',
          'config:system.menu.college-of-arts-and-sciences',
          'config:system.menu.school-of-education',
          'config:system.menu.school-of-law',
          'config:system.menu.school-of-management',
          'config:system.menu.school-of-nursing',
        ],
      ],
      '#type' => 'inline_template',
      '#template' => '{% include "components-minisite-home-hero-nav" with { menu: menu, buttons: buttons } only %}',
      '#context' => [
        'menu' => $menu_array,
        'buttons' => $buttons,
      ],
    ];

    return $build;
  }

  /**
   * Prepare the tree for pattern lab pattern.
   *
   * @param array $tree
   *   The tree array.
   *
   * @return array
   *   The render array.
   */
  private function prepareTree(array $tree) {
    $tree_array = [];
    /** @var \Drupal\Core\Menu\MenuLinkTreeElement $tree_item */
    foreach ($tree as &$tree_item) {
      $active = $this->menuActiveTrail->getActiveLink()
        ? ($this->menuActiveTrail->getActiveLink()->getPluginId() === $tree_item->link->getPluginId())
        : FALSE;
      $url = $tree_item->link->getUrlObject();
      $tree_array[] = [
        'href' => $url->toString(),
        'offsite' => $url->isExternal(),
        'title' => $tree_item->link->getTitle(),
        'activeTrail' => $tree_item->inActiveTrail,
        'class' => $tree_item->inActiveTrail ? 'is-open': NULL,
        'isActive' => $active,
        'submenu' => $this->prepareTree($tree_item->subtree),
      ];
    }

    return $tree_array;
  }

  /**
   * Helper function adapted from menu_ui_get_menu_link_defaults()
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to check for a menu item.
   *
   * @return int
   *   The menu link id.
   */
  protected function getMenuId(NodeInterface $node) {
    $id = NULL;
    /** @var \Drupal\node\NodeTypeInterface $node_type */
    $node_type = $node->type->entity;
    $menu_name = strtok($node_type->getThirdPartySetting('menu_ui', 'parent', 'main:'), ':');
    if ($node->id()) {
      // Give priority to the default menu.
      $type_menus = [
        'main',
        'college-of-arts-and-sciences',
        'school-of-education',
        'school-of-law',
        'school-of-management',
        'school-of-nursing',
      ];

      if (in_array($menu_name, $type_menus)) {
        $query = $this->menuLinkStorage->getQuery();
        $query->condition('link.uri', 'entity:node/' . $node->id())
          ->condition('menu_name', $menu_name)
          ->sort('id', 'ASC')
          ->range(0, 1);
        $result = $query->execute();

        $id = (!empty($result)) ? reset($result) : FALSE;
      }
      // Check all allowed menus if a link does not exist in the default menu.
      if (empty($id) && !empty($type_menus)) {
        $query = $this->menuLinkStorage->getQuery();
        $query->condition('link.uri', 'entity:node/' . $node->id())
          ->condition('menu_name', array_values($type_menus), 'IN')
          ->sort('id', 'ASC')
          ->range(0, 1);
        $result = $query->execute();

        $id = (!empty($result)) ? reset($result) : FALSE;
      }
    }
    return $id;
  }

  /**
   * Find the tree root link.
   *
   * @param \Drupal\menu_link_content\Entity\MenuLinkContent $menu_link
   *   The menu content link item.
   * @param array $active_trail
   *   The active_trail.
   *
   * @return string
   *   The ID of the parent link in form: [menu_item_bundle]:[menu_link_uuid].
   */
  protected function findTreeRoot(MenuLinkContent $menu_link, array $active_trail): string {
    $active_trail = array_filter($active_trail);
    $parent = end($active_trail);
    $active_trail_uuids = str_replace($menu_link->bundle() . ':', '', array_filter($active_trail));

    if ($active_trail_uuids) {
      $tree_root = $this->menuLinkStorage->loadByProperties([
        'uuid' => $active_trail_uuids,
        'is_root' => TRUE,
      ]);
      if ($tree_root) {
        $tree_root_item = reset($tree_root);
        $parent = "{$menu_link->bundle()}:{$tree_root_item->uuid()}";
      }
    }

    return $parent;
  }

}
