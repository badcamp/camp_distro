<?php

/**
 * Contains \Drupal\payment\Plugin\Payment\LineItem\Basic.
 */

namespace Drupal\payment\Plugin\Payment\LineItem;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A basic line item.
 *
 * Plugins extending this class should provide a configuration schema that
 * extends plugin.plugin_configuration.line_item.payment_basic.
 *
 * @PaymentLineItem(
 *   id = "payment_donation",
 *   label = @Translation("Donation")
 * )
 */
class Donation extends PaymentLineItemBase implements ContainerFactoryPluginInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new instance.
   *
   * @param mixed[] $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed[] $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\StringTranslation\TranslationInterface  $string_translation
   *   The translation manager.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, TranslationInterface $string_translation, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('string_translation'), $container->get('database'));
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + array(
        'amount' => 0,
        'currency_code' => NULL,
        'description' => NULL,
      );
  }

  /**
   * {@inheritdoc}
   */
  public function setAmount($amount) {
    $this->configuration['amount'] = $amount;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAmount() {
    return $this->configuration['amount'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrencyCode() {
    return $this->configuration['currency_code'];
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrencyCode($currency_code) {
    $this->configuration['currency_code'] = $currency_code;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  function getDescription() {
    return $this->configuration['description'];
  }

  /**
   * Sets the line item description.
   *
   * @param string $description
   *
   * @return \Drupal\payment\Plugin\Payment\LineItem\Basic
   */
  function setDescription($description) {
    $this->configuration['description'] = $description;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $elements = array(
      '#input' => TRUE,
      '#tree' => TRUE,
      '#type' => 'container',
    );
    $elements['name'] = array(
      '#type' => 'value',
      '#value' => $this->getName(),
    );
    $elements['amount'] = array(
      '#type' => 'currency_amount',
      '#title' => $this->t('Amount'),
      '#default_value' => array(
        'amount' => $this->getAmount(),
        'currency_code' => $this->getCurrencyCode(),
      ),
      '#required' => TRUE,
    );
    $elements['quantity'] = array(
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
      '#default_value' => $this->getQuantity(),
      '#min' => 1,
      '#size' => 3,
      '#required' => TRUE,
    );
    $elements['description'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $this->getDescription(),
      '#required' => TRUE,
      '#maxlength' => 255,
    );
    $elements['clear'] = array(
      '#type' => 'markup',
      '#markup' => '<div class="clear"></div>',
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $values = NestedArray::getValue($values, $form['#parents']);

    $this->setAmount($values['amount']['amount']);
    $this->setCurrencyCode($values['amount']['currency_code']);
    $this->setDescription($values['description']);
    $this->setName($values['name']);
    $this->setQuantity($values['quantity']);
  }

}
