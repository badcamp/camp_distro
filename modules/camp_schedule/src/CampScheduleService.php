<?php

/**
 * @file
 * Contains \Drupal\camp_schedule\CampScheduleService.
 */

namespace Drupal\camp_schedule;

class CampScheduleService {

  /** @var \Drupal\Core\Config\ConfigFactoryInterface */
  protected $config_factory;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->configFactory = $config_factory;
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
