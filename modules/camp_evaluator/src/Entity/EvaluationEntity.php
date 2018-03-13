<?php

namespace Drupal\camp_evaluator\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\field_permissions\Plugin\FieldPermissionType\Base;
use Drupal\user\UserInterface;

/**
 * Defines the Evaluation entity.
 *
 * @ingroup camp_evaluator
 *
 * @ContentEntityType(
 *   id = "evaluation",
 *   label = @Translation("Evaluation"),
 *   bundle_label = @Translation("Evaluation type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\camp_evaluator\EvaluationEntityListBuilder",
 *     "views_data" = "Drupal\camp_evaluator\Entity\EvaluationEntityViewsData",
 *     "translation" = "Drupal\camp_evaluator\EvaluationEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\camp_evaluator\Form\EvaluationEntityForm",
 *       "add" = "Drupal\camp_evaluator\Form\EvaluationEntityForm",
 *       "edit" = "Drupal\camp_evaluator\Form\EvaluationEntityForm",
 *       "delete" = "Drupal\camp_evaluator\Form\EvaluationEntityDeleteForm",
 *     },
 *     "access" = "Drupal\camp_evaluator\EvaluationEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\camp_evaluator\EvaluationEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "evaluation",
 *   data_table = "evaluation_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer evaluation entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/evaluation/{evaluation}",
 *     "edit-form" = "/admin/content/evaluation/{evaluation}/edit",
 *     "delete-form" = "/admin/content/evaluation/{evaluation}/delete",
 *     "collection" = "/admin/content/evaluation",
 *   },
 *   bundle_entity_type = "evaluation_type",
 *   field_ui_base_route = "entity.evaluation_type.edit_form"
 * )
 */
class EvaluationEntity extends ContentEntityBase implements EvaluationEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'name' => \Drupal::currentUser()->getAccountName(),
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', TRUE);
    return $this;
  }

  /**
   * @return mixed
   */
  public function getEvaluatingNode() {
    return $this->get('entity_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Evaluated by'))
      ->setDescription(t('The user ID of the Evaluator'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
      ])
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['entity_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Related Entity'))
      ->setDescription(t('The related entity this payment belongs to. 
      e.g:Training or donation node / entity.'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default')
      ->setSetting('default_value',null)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', array(
        'type' => 'hidden',
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
