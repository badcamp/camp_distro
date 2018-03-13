<?php

namespace Drupal\camp_evaluator\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Evaluation entities.
 *
 * @ingroup camp_evaluator
 */
interface EvaluationEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Evaluation creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Evaluation.
   */
  public function getCreatedTime();

  /**
   * Sets the Evaluation creation timestamp.
   *
   * @param int $timestamp
   *   The Evaluation creation timestamp.
   *
   * @return \Drupal\camp_evaluator\Entity\EvaluationEntityInterface
   *   The called Evaluation entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Evaluation published status indicator.
   *
   * Unpublished Evaluation are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Evaluation is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Evaluation.
   *
   * @param bool $published
   *   TRUE to set this Evaluation to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\camp_evaluator\Entity\EvaluationEntityInterface
   *   The called Evaluation entity.
   */
  public function setPublished($published);

}
