<?php

/**
 * @file
 * Contains \Drupal\camp_schedule\CampScheduleService.
 */

namespace Drupal\camp_schedule;

use Drupal\Core\Config\ConfigFactoryInterface;

class CampScheduleService {

  /** @var \Drupal\Core\Config\ConfigFactoryInterface */
  protected $configFactory;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  public function addBundleToFlag($bundle) {
    $config = $this->configFactory->getEditable('flag.flag.add_to_schedule');
    $bundles = $config->get('bundles', []);
    if(array_search($bundle, $bundles) === FALSE){
      $bundles[] = $bundle;
      $config->set('bundles', $bundles);
      $config->save(TRUE);
    }
  }

  public function removeBundleFromFlag($bundle) {
    $config = $this->configFactory->getEditable('flag.flag.add_to_schedule');
    $bundles = $config->get('bundles', []);
    if(array_search($bundle, $bundles) !== FALSE){
      $index = array_search($bundle, $bundles);
      unset($bundles[$index]);
      $config->set('bundles', $bundles);
      $config->save(TRUE);
    }
  }
}
