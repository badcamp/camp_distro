<?php

namespace Drupal\camp_core\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class that provides the menu links for the Products.
 */
class CampContentActionLink extends DeriverBase implements ContainerDeriverInterface {

  /**
   * @var EntityTypeManagerInterface $entityTypeManager.
   */
  protected $entityTypeManager;

  /** @var \Drupal\Core\Path\CurrentPathStack $currentPathStack */
  protected $currentPathStack;

  /**
   * Creates a ProductMenuLink instance.
   *
   * @param $base_plugin_id
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Core\Path\CurrentPathStack $currentPathStack
   */
  public function __construct($base_plugin_id, EntityTypeManagerInterface $entity_type_manager, CurrentPathStack $currentPathStack) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentPathStack = $currentPathStack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('entity_type.manager'),
      $container->get('path.current')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $links = [];

    $nodeTypes = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    foreach ($nodeTypes as $id => $nodeType) {
      $links[$nodeType->id()] = [
          'id' => 'camp_content.admin.' . $nodeType->id(),
          'title' => t('Add @node_type', ['@node_type' => $nodeType->label()]),
          'route_parameters' => [
            'node_type' => $id
          ],
          'route_name' => 'node.add',
          'options' => [
            'query' => [
              'destination' => \Drupal::url('<current>', [], ['absolute' => FALSE])
            ]
          ],
          'appears_on' => [
            'camp_core.content.' . $nodeType->id()
          ]
        ] + $base_plugin_definition;
    }

    return $links;
  }
}