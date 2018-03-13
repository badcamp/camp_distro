<?php

namespace Drupal\camp_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Class PageCodeOfConductController
 *
 * @package Drupal\camp_core\Controller
 */
class CampContentController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'camp_core';
  }

  /**
   * Return the code of conduct that was saved.
   */
  public function page() {
    $message = $this->config('camp_core.code_of_conduct')->get('message');
    return [
      '#markup' => check_markup($message['value'], $message['format']),
    ];
  }
}