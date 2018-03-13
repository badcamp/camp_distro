<?php

namespace Drupal\camp_evaluator\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Evaluation type entity.
 *
 * @ConfigEntityType(
 *   id = "evaluation_type",
 *   label = @Translation("Evaluation type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\camp_evaluator\EvaluationEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\camp_evaluator\Form\EvaluationEntityTypeForm",
 *       "edit" = "Drupal\camp_evaluator\Form\EvaluationEntityTypeForm",
 *       "delete" = "Drupal\camp_evaluator\Form\EvaluationEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\camp_evaluator\EvaluationEntityTypeHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\camp_evaluator\EvaluationTypeEntityAccessControlHandler"
 *   },
 *   config_prefix = "evaluation_type",
 *   admin_permission = "administer evaluation types configuration",
 *   bundle_of = "evaluation",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/evaluation/type/{evaluation_type}",
 *     "add-form" = "/admin/structure/evaluation/add",
 *     "edit-form" = "/admin/structure/evaluation/{evaluation_type}/edit",
 *     "delete-form" = "/admin/structure/evaluation/{evaluation_type}/delete",
 *     "collection" = "/admin/structure/evaluation"
 *   }
 * )
 */
class EvaluationEntityType extends ConfigEntityBundleBase implements EvaluationEntityTypeInterface {

  /**
   * The Evaluation type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Evaluation type label.
   *
   * @var string
   */
  protected $label;

}
