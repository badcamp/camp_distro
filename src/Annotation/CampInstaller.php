<?php

namespace Drupal\camp\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Class CampInstaller
 *
 * @package Drupal\camp\Annotation
 *
 * @Annotation
 */
class CampInstaller extends Plugin {

  /**
   * @var \Drupal\Core\Annotation\Translation;
   */
  public $title;

  /**
   * @var \Drupal\Core\Annotation\Translation;
   */
  public $description;

}