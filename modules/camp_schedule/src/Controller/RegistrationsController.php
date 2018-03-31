<?php

namespace Drupal\camp_schedule\Controller;

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
class RegistrationsController extends ControllerBase {

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
    return 'camp_schedule';
  }

  /**
   * @param \Drupal\node\NodeInterface $node
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  public function getNodeTitle(NodeInterface $node) {
    return t('Registrations for %node', ['%node' => $node->getTitle()]);
  }

  /**
   * @param \Drupal\node\NodeInterface $node
   *
   * @return array
   */
  public function getRegistrationList(NodeInterface $node) {
    /** @var \Drupal\camp_schedule\Form\ContactForm $form */
    $form = $this->formBuilder->getForm('\Drupal\camp_schedule\Form\ContactForm', $node);
    $view = views_embed_view('camp_schedule_registrations', 'default', $node->id());

    return [
      'form' => $form,
      'view' => $view
    ];
  }

  /**
   * @param \Drupal\node\NodeInterface $node
   * @param \Drupal\Core\Session\AccountInterface $account
   *
   * @return \Drupal\Core\Access\AccessResultForbidden
   */
  public function getNodeAccess(NodeInterface $node, AccountInterface $account) {
    $bundles = $this->config('flag.flag.add_to_schedule')->get('bundles');
    return AccessResult::allowedIf(array_search($node->bundle(), $bundles) !== FALSE);
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
