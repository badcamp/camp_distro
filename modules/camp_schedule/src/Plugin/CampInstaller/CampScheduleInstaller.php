<?php

namespace Drupal\camp_schedule\Plugin\CampInstaller;

use Drupal\camp\CampInstallerBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\camp\Annotation\CampInstaller;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class CampCore
 *
 * @package Drupal\camp_schedule\Plugin\CampInstaller
 * @CampInstaller (
 *   id = "camp_schedule",
 *   title = @Translation("Schedule"),
 *   description = @Translation("Core Configuration settings")
 * )
 */
class CampScheduleInstaller extends CampInstallerBase {

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['single_page'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Single Page Schedule'),
      '#description' => $this->t('Make the schedule a single page instead of broken out'),
    ];

    $form['dates'] = [
      '#type' => 'fieldset',
      '#tree' => true,
      '#title' => $this->t('Dates')
    ];

    $form['dates']['start'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Start Date'),
      '#default_value' => new DrupalDateTime('today'),
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
    ];

    $form['dates']['end'] = [
      '#type' => 'datetime',
      '#title' => $this->t('End Date'),
      '#default_value' => new DrupalDateTime('today'),
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
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