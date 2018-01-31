<?php

namespace Drupal\camp_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    $value = $this->config('camp_core.code_of_conduct')->get('value');
    $format = $this->config('camp_core.code_of_conduct')->get('format');
    return [
      '#markup' => check_markup($value, $format),
    ];
  }

}