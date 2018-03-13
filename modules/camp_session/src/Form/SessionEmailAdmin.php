<?php

namespace Drupal\camp_session\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the SessionEmailAdmin form controller.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class SessionEmailAdmin extends ConfigFormBase {

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

    $config = $this->config('camp_session.emails');

    $form['session_proposed'] = [
      '#type' => 'details',
      '#title' => $this->t('Session Proposed'),
      '#tree' => true
    ];

    $form['session_proposed']['description'] = [
      '#markup' => '<p>'.$this->t('Message sent after Session node type created.').'</p>'
    ];

    $form['session_proposed']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#default_value' => $config->get('session_proposed')['enabled']
    ];

    $form['session_proposed']['subject'] = [
      '#title' => $this->t('Subject'),
      '#type' => 'textfield',
      '#default_value' =>  $config->get('session_proposed')['subject'],
    ];

    $form['session_proposed']['message'] = [
      '#title' => $this->t('Message'),
      '#type' => 'text_format',
      '#format' =>  $config->get('session_proposed')['message']['format'],
      '#default_value' =>  $config->get('session_proposed')['message']['value'],
    ];

    $form['session_accepted'] = [
      '#type' => 'details',
      '#title' => $this->t('Session Accepted'),
      '#tree' => true
    ];

    $form['session_accepted']['description'] = [
      '#markup' => '<p>'.$this->t('Message Sent After Session Accepted').'</p>'
    ];

    $form['session_accepted']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#default_value' => $config->get('session_accepted')['enabled']
    ];

    $form['session_accepted']['subject'] = [
      '#title' => $this->t('Subject'),
      '#type' => 'textfield',
      '#default_value' =>  $config->get('session_accepted')['subject'],
    ];
    $form['session_accepted']['message'] = [
      '#title' => $this->t('Message'),
      '#type' => 'text_format',
      '#format' =>  $config->get('session_accepted')['message']['format'],
      '#default_value' =>  $config->get('session_accepted')['message']['value'],
    ];

    $form['session_rejected'] = [
      '#type' => 'details',
      '#title' => $this->t('Session Rejected'),
      '#tree' => true
    ];

    $form['session_rejected']['description'] = [
      '#markup' => '<p>'.$this->t('Message Sent After Session Rejected').'</p>'
    ];

    $form['session_rejected']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#default_value' => $config->get('session_rejected')['enabled']
    ];

    $form['session_rejected']['subject'] = [
      '#title' => $this->t('Subject'),
      '#type' => 'textfield',
      '#default_value' =>  $config->get('session_rejected')['subject'],
    ];

    $form['session_rejected']['message'] = [
      '#title' => $this->t('Message'),
      '#type' => 'text_format',
      '#format' =>  $config->get('session_rejected')['message']['format'],
      '#default_value' =>  $config->get('session_rejected')['message']['value'],
    ];

    $form['token_tree'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => array('node', 'user')
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Getter method for Form ID.
   *
   * The form ID is used in implementations of hook_form_alter() to allow other
   * modules to alter the render array built by this form controller. It must be
   * unique site wide. It normally starts with the providing module's name.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'session_email_admin';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'camp_session.emails',
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
    $this->configFactory->getEditable('camp_session.emails')
      ->set('session_proposed', $form_state->getValue('session_proposed'))
      ->set('session_accepted', $form_state->getValue('session_accepted'))
      ->set('session_rejected', $form_state->getValue('session_rejected'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}