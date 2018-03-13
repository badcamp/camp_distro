<?php

namespace Drupal\camp_evaluator;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Evaluation entities.
 *
 * @ingroup camp_evaluator
 */
class EvaluationEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['type'] = $this->t('Type');
    $header['name'] = $this->t('Evaluator');
    $header['node'] = $this->t('Title');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['type'] = $entity->bundle();
    $row['name'] = Link::createFromRoute(
      $entity->getOwner()->getDisplayName(),
      'entity.user.canonical',
      ['user' => $entity->getOwnerId()]
    );

    $row['node'] = Link::createFromRoute(
      $entity->getEvaluatingNode()->getTitle(),
      'entity.node.canonical',
      ['node' => $entity->getEvaluatingNode()->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
