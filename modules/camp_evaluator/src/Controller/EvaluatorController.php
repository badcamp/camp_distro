<?php

namespace Drupal\camp_evaluator\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Class NodeEvaluatorController.
 */
class EvaluatorController extends ControllerBase {

  /** @var \Drupal\Core\Entity\EntityManagerInterface $entityManager */
  protected $entityManager;

  /** @var \Drupal\Core\Form\FormBuilderInterface $formBuilder */
  protected $formBuilder;

  /** @var \Drupal\Core\Session\AccountProxyInterface $currentUser */
  protected $currentUser;

  public function __construct(EntityManagerInterface $entityManager, FormBuilderInterface $formBuilder, AccountInterface $currentUser) {
    $this->entityManager = $entityManager;
    $this->formBuilder = $formBuilder;
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'camp_evaluator';
  }

  /**
   * @param \Drupal\node\NodeInterface $node
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  public function getNodeTitle(NodeInterface $node) {
    return t('Evaluations for %node', ['%node' => $node->getTitle()]);
  }

  /**
   * @param \Drupal\node\NodeInterface $node
   *
   * @return array
   */
  public function getNodeEvaluation(NodeInterface $node) {
    $type = $this
      ->entityManager
      ->getStorage('node_type')
      ->load($node->getType());

    $bundle = $type->getThirdPartySetting('camp_evaluator', 'form', 'default');

    $evals = $this->entityManager->getStorage('evaluation')->loadByProperties([
      'entity_id' => $node->id(),
      'user_id' => $this->currentUser->id()
    ]);

    if(count($evals) > 0) {
      $evaluation = array_shift($evals);
    }else {
      $evaluation = $this->entityManager->getStorage('evaluation')->create([
        'type' => $bundle,
        'entity_id' => $node->id()
      ]);
    }
    $form = $this->entityManager
      ->getFormObject('evaluation', 'default')
      ->setEntity($evaluation);
    $build = $this->formBuilder->getForm($form);


    $view = views_embed_view('node_evaluations', 'default', $node->id());

    return [
      '#markup' => '<p>' . $this->t('Simple page: The quick brown fox jumps over the lazy dog.') . '</p>',
      'form' => $build,
      'view' => $view,
    ];
  }

  public function getNodeAccess(NodeInterface $node, AccountInterface $account) {
    $type = $this
      ->entityManager
      ->getStorage('node_type')
      ->load($node->getType());
    $enabledForm = $type->getThirdPartySetting('camp_evaluator', 'enable', 0);
    return AccessResult::allowedIf(
      ( $account->hasPermission('evaluate everything') || $account->hasPermission('evaluate ' . $node->getType())) &&
      $enabledForm == 1
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('form_builder'),
      $container->get('current_user')
    );
  }
}
