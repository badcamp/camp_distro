<?php

namespace Drupal\camp_evaluator\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Evaluation entities.
 */
class EvaluationEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
