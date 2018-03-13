<?php

namespace Drupal\camp_evaluator;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Evaluation entity.
 *
 * @see \Drupal\camp_evaluator\Entity\EvaluationEntity.
 */
class EvaluationEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\camp_evaluator\Entity\EvaluationEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished evaluation entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published evaluation entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit evaluation entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete evaluation entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add evaluation entities');
  }

}
