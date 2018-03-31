<?php

namespace Drupal\camp_schedule\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @see \Drupal\Core\Form\FormBase
 */
class ScheduleSettings extends ConfigFormBase {

  /**
   * Build the simple form.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('camp_schedule.schedule_settings');

    $form['single_page'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Single Page Schedule'),
      '#description' => $this->t('Make the schedule a single page instead of broken out'),
      '#default_value' => $config->get('single_page')
    ];

    $form['dates'] = [
      '#type' => 'fieldset',
      '#tree' => true,
      '#title' => $this->t('Dates')
    ];

    $form['dates']['start'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Start Date'),
      '#default_value' => new DrupalDateTime($config->get('dates')['start']),
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
    ];

    $form['dates']['end'] = [
      '#type' => 'datetime',
      '#title' => $this->t('End Date'),
      '#default_value' => new DrupalDateTime($config->get('dates')['end']),
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Getter method for Form ID.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'camp_schedule_schedule_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'camp_schedule.schedule_settings',
    ];
  }

  /**
   * Implements form validation.
   *
   * The validateForm method is the default method called to validate input on
   * a form.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * Implements a form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $dates = [
      'start' => $form_state->getValue('dates')['start']->format('Y-m-d'),
      'end' => $form_state->getValue('dates')['end']->format('Y-m-d')
    ];
    $this->configFactory->getEditable('camp_schedule.schedule_settings')
      ->set('single_page', $form_state->getValue('single_page'))
      ->set('dates', $dates)
      ->save();

    parent::submitForm($form, $form_state);
  }

}