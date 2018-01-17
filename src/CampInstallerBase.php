<?php

namespace Drupal\camp;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;

abstract class CampInstallerBase extends PluginBase implements CampInstallerInterface {

  /**
   * @return string
   */
  public function title() {
    return $this->pluginDefinition['title'];
  }

  /**
   * @return string
   */
  public function description() {
    return $this->pluginDefinition['description'];
  }

  /**
   * {@inheritdoc}
   */
  abstract public function buildForm(array $form, FormStateInterface $form_state);

  /**
   * {@inheritdoc}
   */
  abstract public function validateForm(array &$form, FormStateInterface $form_state);

  /**
   * {@inheritdoc}
   */
  abstract public function submitForm(array &$form, FormStateInterface $form_state);
}