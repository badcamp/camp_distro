<?php

namespace Drupal\camp_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Class PageCodeOfConductController
 *
 * @package Drupal\camp_core\Controller
 */
class PageCodeOfConductController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'camp_core';
  }

  /**
   *
   */
  public function page() {
    $message = $this->config('camp_core.code_of_conduct')->get('message');
    return [
      '#markup' => check_markup($message['value'], $message['format']),
    ];
  }

  /**
   * @return \Drupal\Core\Access\AccessResult
   */
  public function getAccess() {
    $enable = $this->config('camp_core.code_of_conduct')->get('enable');
    if($enable)
      return AccessResult::allowed();
    else
      return AccessResult::forbidden();
  }
}