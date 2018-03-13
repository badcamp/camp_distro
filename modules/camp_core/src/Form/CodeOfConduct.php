<?php

namespace Drupal\camp_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @see \Drupal\Core\Form\FormBase
 */
class CodeOfConduct extends ConfigFormBase {

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

    $config = $this->config('camp_core.code_of_conduct');

    $form['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I want to have a Code of Conduct'),
      '#default_value' => $config->get('enable')
    ];

    $form['code_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Code of Conduct'),
      '#states' => [
        'visible' => [
          ':input[name="enable"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Text format.
    $form['code_wrapper']['code_of_conduct'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Code of Conduct'),
      '#title_display' => 'invisible',
      '#format' => $config->get('message')['format'],
      '#default_value' => $config->get('message')['value'],
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
    return 'form_camp_core_code_of_conduct';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'camp_core.code_of_conduct',
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
    $this->configFactory->getEditable('camp_core.code_of_conduct')
      ->set('enable', $form_state->getValue('enable'))
      ->set('message', $form_state->getValue('code_of_conduct'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}