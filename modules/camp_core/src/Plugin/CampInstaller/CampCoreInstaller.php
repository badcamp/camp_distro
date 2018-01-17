<?php

namespace Drupal\camp_core\Plugin\CampInstaller;

use Drupal\camp\CampInstallerBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\camp\Annotation\CampInstaller;

/**
 * Class CampCore
 *
 * @package Drupal\camp_core\Plugin\CampInstaller
 * @CampInstaller (
 *   id = "camp_core",
 *   title = @Translation("Core"),
 *   description = @Translation("Core Configuration settings")
 * )
 */
class CampCoreInstaller extends CampInstallerBase {

  public function buildForm(array $form, FormStateInterface $form_state) {
    // TODO: Implement buildForm() method.

    $form['test'] = [
      '#markup' => 'test'
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