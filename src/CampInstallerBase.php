<?php

namespace Drupal\camp;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class CampInstallerBase extends PluginBase implements CampInstallerInterface, ContainerFactoryPluginInterface {

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

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }
}