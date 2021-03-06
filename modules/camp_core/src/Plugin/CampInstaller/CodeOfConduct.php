<?php

namespace Drupal\camp_core\Plugin\CampInstaller;

use Drupal\camp\CampInstallerBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\camp\Annotation\CampInstaller;
use Drupal\user\RoleInterface;

/**
 * Class CampCore
 *
 * @package Drupal\camp_core\Plugin\CampInstaller
 * @CampInstaller (
 *   id = "code_of_conduct",
 *   title = @Translation("Code of Conduct"),
 *   description = @Translation("Code of Conduct Settings")
 * )
 */
class CodeOfConduct extends CampInstallerBase {

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Temp give access to Anonymous Users to Use Full HTML because then can't
    // Use the WYSIWYG to change the text and have it make sense.
    user_role_grant_permissions(RoleInterface::ANONYMOUS_ID, ['use text format full_html']);

    $config = \Drupal::config('camp_core.code_of_conduct');
    $enabled = $config->get('enabled');
    $text = $config->get('message')['value'];
    $format_id = $config->get('message')['format'];

    $form['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("I want to have a code of conduct")
    ];

    $form['text'] = [
      '#markup' => check_markup($text, $format_id),
      '#states' => [
        'visible' => [
          ':input[name="code_of_conduct[enable]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['change'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("I want to change the current code of conduct"),
      '#states' => [
        'visible' => [
          ':input[name="code_of_conduct[enable]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['change_text_wrapper'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Code of Conduct'),
      '#states' => [
        'visible' => [
          ':input[name="code_of_conduct[change]"]' => ['checked' => TRUE],
          ':input[name="code_of_conduct[enable]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['change_text_wrapper']['change_text'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Code of Conduct'),
      '#title_display' => 'invisible',
      '#format' => $format_id,
      '#default_value' => $text,
    ];

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
    // TODO: Implement submitForm() method.
    $enable = $form_state->getValue('code_of_conduct')['enable'];
    $config = \Drupal::configFactory()->getEditable('camp_core.code_of_conduct');

    if($enable) {
      $config->set('enable', $enable);
      $change = $form_state->getValue('code_of_conduct')['change'];

      if ($change) {
        $text = $form_state->getValue('code_of_conduct')['change_text_wrapper']['change_text'];
        $config->set('message', $text);
      }

      $config->save();

      /**
       * Remove access for Anon user's to access WYSIWYG
       */
      user_role_revoke_permissions(RoleInterface::ANONYMOUS_ID, ['use text format full_html']);
    }
  }

}