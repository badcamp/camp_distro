<?php

namespace Drupal\camp_core;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\NodeListBuilder;

/**
 * Class NodeBundleListBuilder
 *
 * @package Drupal\camp_core
 */
class NodeBundleListBuilder extends NodeListBuilder {

  /** @var string $bundle */
  protected $bundle;

  /**
   * @param $bundle
   */
  public function setBundle($bundle) {
    $this->bundle = $bundle;
  }

  /**
   * Loads entity IDs using a pager sorted by the entity id.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this
      ->getStorage()
      ->getQuery()
      ->sort($this->entityType->getKey('id'));

    if(isset($this->bundle)){
      $query->condition('type', $this->bundle);
    }

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }

    return $query->execute();
  }

  /**
   * @return array
   */
  public function buildHeader() {
    $header = parent::buildHeader();
    unset($header['type']);
    $entity_type = $this->entityType;
    $bundle = $this->bundle;
    $this->moduleHandler()->alter('node_build_header', $header, $entity_type, $bundle);
    return $header;
  }

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return array
   */
  public function buildRow(EntityInterface $entity) {
    $row = parent::buildRow($entity);
    unset($row['type']);
    $this->moduleHandler()->alter('node_build_row',$row, $entity);
    return $row;
  }

  /**
   * @return array|mixed
   */
  public function render() {
    $nodeType = \Drupal::entityTypeManager()->getStorage('node_type')->load($this->bundle);
    $build = parent::render();
    $build['table']['#empty'] = $this->t('There is no @label content yet.', ['@label' => $nodeType->label()]);
    $this->moduleHandler()->alter('node_list_render', $build);
    return $build;
  }
}