<?php

namespace Drupal\camp_evaluator\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Evaluation edit forms.
 *
 * @ingroup camp_evaluator
 */
class EvaluationEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\camp_evaluator\Entity\EvaluationEntity */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Evaluation.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Evaluation.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.evaluation.canonical', ['evaluation' => $entity->id()]);
  }

}
