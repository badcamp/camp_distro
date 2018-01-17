<?php

namespace Drupal\camp_venue\Plugin\CampInstaller;

use Drupal\camp\CampInstallerBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\camp\Annotation\CampInstaller;

/**
 *
 * @package Drupal\camp_venue\Plugin\CampInstaller
 * @CampInstaller (
 *   id = "camp_venue",
 *   title = @Translation("Venue Configuration"),
 *   description = @Translation("Venue Configuration settings")
 * )
 */
class CampVenueInstaller extends CampInstallerBase {

  public function buildForm(array $form, FormStateInterface $form_state) {
    // TODO: Implement buildForm() method.
    $form['test'] = [
      '#markup' => 'test 2'
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement validateForm() method.
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}