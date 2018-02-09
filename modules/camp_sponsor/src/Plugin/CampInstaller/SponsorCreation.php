<?php

namespace Drupal\camp_sponsor\Plugin\CampInstaller;

use Drupal\camp\CampInstallerBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\camp\Annotation\CampInstaller;

/**
 * Class SponsorCreation
 *
 * @package Drupal\camp_sponsor\Plugin\CampInstaller
 * @CampInstaller (
 *   id = "sponsor_create",
 *   title = @Translation("Camp Sponsors"),
 *   description = @Translation("Set up sponsor levels quick and easy.")
 * )
 */
class SponsorCreation extends CampInstallerBase {

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['levels'] = [
      '#type' => 'textarea',
      '#title' => $this->t("Sponsor Levels"),
      '#description' => $this->t('Provide a list of all the sponsor levels you would like to create. Sponsor levels should go on a new one.')
    ]

    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement validateForm() method.
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}