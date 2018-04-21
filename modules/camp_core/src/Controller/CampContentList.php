<?php

namespace Drupal\camp_core\Controller;

use Drupal\camp_core\EntityBundleListBuilder;
use Drupal\camp_core\NodeBundleListBuilder;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CampContentList extends ControllerBase {

  /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
  protected $container;

  /** @var EntityTypeManagerInterface $entityTypeManager */
  protected $entityTypeManager;

  /** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
  protected $moduleHandler;

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container,
      $container->get('entity_type.manager')
    );
  }

  /**
   * CampContentList constructor.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(ContainerInterface $container, EntityTypeManagerInterface $entityTypeManager) {
    $this->container = $container;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Return the page of content for the specific $node_type.
   */
  public function page($node_type) {
    $nodeType = $this->getNodeType($node_type);
    $settings = $nodeType->getThirdPartySettings('camp_core');
    if (isset($settings['view']) && $settings['view'] != '') {
      list($name, $display_id) = explode('.', $settings['view']);
      return views_embed_view($name, $display_id);
    }
    else {
      $entity_type = $this->entityTypeManager->getDefinition('node');
      $bundleListBuilder = NodeBundleListBuilder::createInstance($this->container, $entity_type);
      $bundleListBuilder->setBundle($node_type);
      return $bundleListBuilder->render();
    }
  }

  /**
   * Returns the title for the page.
   *
   * @param $node_type
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function title($node_type) {
    $nodeType = $this->getNodeType($node_type);
    return $this->t('@type', ['@type' => $nodeType->get('name')]);
  }

  /**
   * @param $node_type
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function getNodeType($node_type) {
    return $this->entityTypeManager->getStorage('node_type')->load($node_type);
  }
}
