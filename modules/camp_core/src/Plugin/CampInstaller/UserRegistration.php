<?php

namespace Drupal\camp_core\Plugin\CampInstaller;

use Drupal\camp\CampInstallerBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\camp\Annotation\CampInstaller;

/**
 * Class UserRegistration
 *
 * @package Drupal\camp_core\Plugin\CampInstaller
 * @CampInstaller (
 *   id = "user_registration",
 *   title = @Translation("User Registration"),
 *   description = @Translation("Information about user registration.")
 * )
 */
class UserRegistration extends CampInstallerBase {

  public function buildForm(array $form, FormStateInterface $form_state) {
    // TODO: Implement buildForm() method.

    $form['allow_registration'] = [
      '#title' => $this->t('Allow user registration for the site?'),
      '#description' => $this->t('Should users be able to register for accounts within the site? This can be set at a later point.'),
      '#type' => 'checkbox',
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