<?php

/**
 * Contains \Drupal\payment\Plugin\Payment\MethodConfiguration\Basic.
 */

namespace Drupal\camp_finances\Plugin\Payment\MethodConfiguration;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorManagerInterface;
use Drupal\plugin\PluginType\PluginTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\payment\Plugin\Payment\MethodConfiguration\PaymentMethodConfigurationBase;

/**
 * Provides the configuration for the payment_basic payment method plugin.
 *
 * Plugins extending this class should provide a configuration schema that
 * extends
 * plugin.plugin_configuration.payment_method_configuration.payment_basic.
 *
 * @PaymentMethodConfiguration(
 *   description = @Translation("A payment method type that always successfully executes payments, but never actually transfers money."),
 *   id = "payment_omnipay",
 *   label = @Translation("OmniPay")
 * )
 */
class OmniPay extends PaymentMethodConfigurationBase implements ContainerFactoryPluginInterface {

  /**
   * The payment status plugin type.
   *
   * @var \Drupal\plugin\PluginType\PluginTypeInterface
   */
  protected $paymentStatusType;

  /**
   * The plugin selector manager.
   *
   * @var \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorManagerInterface
   */
  protected $pluginSelectorManager;

  /**
   * Constructs a new instance.
   *
   * @param mixed[] $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed[] $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translator.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorManagerInterface
   *   The plugin selector manager.
   * @param \Drupal\plugin\PluginType\PluginTypeInterface $payment_status_type
   *   The payment status plugin type.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, TranslationInterface $string_translation, ModuleHandlerInterface $module_handler, PluginSelectorManagerInterface $plugin_selector_manager, PluginTypeInterface $payment_status_type) {
    $configuration += $this->defaultConfiguration();
    parent::__construct($configuration, $plugin_id, $plugin_definition, $string_translation, $module_handler);
    $this->paymentStatusType = $payment_status_type;
    $this->pluginSelectorManager = $plugin_selector_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var \Drupal\plugin\PluginType\PluginTypeManagerInterface $plugin_type_manager */
    $plugin_type_manager = $container->get('plugin.plugin_type_manager');

    return new static($configuration, $plugin_id, $plugin_definition, $container->get('string_translation'), $container->get('module_handler'), $container->get('plugin.manager.plugin.plugin_selector'), $plugin_type_manager->getPluginType('payment_status'));
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + array(
        'brand_label' => '',
        'execute_status_id' => 'payment_pending',
        'capture' => FALSE,
        'capture_status_id' => 'payment_success',
        'refund' => FALSE,
        'refund_status_id' => 'payment_refunded',
      );
  }

  /**
   * Sets the status to set on payment execution.
   *
   * @param string $status
   *   The plugin ID of the payment status to set.
   *
   * @return $this
   */
  public function setExecuteStatusId($status) {
    $this->configuration['execute_status_id'] = $status;

    return $this;
  }

  /**
   * Gets the status to set on payment execution.
   *
   * @return string
   *   The plugin ID of the payment status to set.
   */
  public function getExecuteStatusId() {
    return $this->configuration['execute_status_id'];
  }

  /**
   * Sets the status to set on payment capture.
   *
   * @param string $status
   *   The plugin ID of the payment status to set.
   *
   * @return $this
   */
  public function setCaptureStatusId($status) {
    $this->configuration['capture_status_id'] = $status;

    return $this;
  }

  /**
   * Gets the status to set on payment capture.
   *
   * @return string
   *   The plugin ID of the payment status to set.
   */
  public function getCaptureStatusId() {
    return $this->configuration['capture_status_id'];
  }

  /**
   * Sets whether or not capture is supported.
   *
   * @param bool $capture
   *   Whether or not to support capture.
   *
   * @return $this
   */
  public function setCapture($capture) {
    $this->configuration['capture'] = $capture;

    return $this;
  }

  /**
   * Gets whether or not capture is supported.
   *
   * @param bool
   *   Whether or not to support capture.
   */
  public function getCapture() {
    return $this->configuration['capture'];
  }

  /**
   * Sets the status to set on payment refund.
   *
   * @param string $status
   *   The plugin ID of the payment status to set.
   *
   * @return $this
   */
  public function setRefundStatusId($status) {
    $this->configuration['refund_status_id'] = $status;

    return $this;
  }

  /**
   * Gets the status to set on payment refund.
   *
   * @return string
   *   The plugin ID of the payment status to set.
   */
  public function getRefundStatusId() {
    return $this->configuration['refund_status_id'];
  }

  /**
   * Sets whether or not refunds are supported.
   *
   * @param bool $refund
   *   Whether or not to support refunds.
   *
   * @return $this
   */
  public function setRefund($refund) {
    $this->configuration['refund'] = $refund;

    return $this;
  }

  /**
   * Gets whether or not refunds are supported.
   *
   * @param bool
   *   Whether or not to support refunds.
   */
  public function getRefund() {
    return $this->configuration['refund'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['plugin_form'] = array(
      '#process' => array(array($this, 'processBuildConfigurationForm')),
      '#type' => 'container',
    );

    return $form;
  }

  /**
   * Implements a form API #process callback.
   */
  public function processBuildConfigurationForm(array &$element, FormStateInterface $form_state, array &$form) {
    $element['brand_label'] = array(
      '#default_value' => $this->getBrandLabel(),
      '#description' => $this->t('The label that payers will see when choosing a payment method. Defaults to the payment method label.'),
      '#title' => $this->t('Brand label'),
      '#type' => 'textfield',
    );
    $workflow_group = implode('][', array_merge($element['#parents'], array('workflow')));
    $element['workflow'] = array(
      '#type' => 'vertical_tabs',
    );
    $element['execute'] = array(
      '#group' => $workflow_group,
      '#open' => TRUE,
      '#type' => 'details',
      '#title' => $this->t('Execution'),
    );
    $element['execute']['execute_status'] = $this->getExecutePaymentStatusSelector($form_state)->buildSelectorForm([], $form_state);
    $element['capture'] = array(
      '#group' => $workflow_group,
      '#open' => TRUE,
      '#type' => 'details',
      '#title' => $this->t('Capture'),
    );
    $capture_id = Html::getUniqueId('capture');
    $element['capture']['capture'] = array(
      '#id' => $capture_id,
      '#type' => 'checkbox',
      '#title' => $this->t('Add an additional capture step after payments have been executed.'),
      '#default_value' => $this->getCapture(),
    );
    $element['capture']['plugin_form'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => array(
          '#' . $capture_id => array(
            'checked' => TRUE,
          ),
        ),
      ],
    ];
    $element['capture']['plugin_form']['capture_status'] = $this->getCapturePaymentStatusSelector($form_state)->buildSelectorForm([], $form_state);
    $refund_id = Html::getUniqueId('refund');
    $element['refund'] = array(
      '#group' => $workflow_group,
      '#open' => TRUE,
      '#type' => 'details',
      '#title' => $this->t('Refund'),
    );
    $element['refund']['refund'] = array(
      '#id' => $refund_id,
      '#type' => 'checkbox',
      '#title' => $this->t('Add an additional refund step after payments have been executed.'),
      '#default_value' => $this->getRefund(),
    );
    $element['refund']['plugin_form'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => array(
          '#' . $refund_id => array(
            'checked' => TRUE,
          ),
        ),
      ],
    ];
    $element['refund']['plugin_form']['refund_status'] = $this->getRefundPaymentStatusSelector($form_state)->buildSelectorForm([], $form_state);

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->getExecutePaymentStatusSelector($form_state)->validateSelectorForm($form['plugin_form']['execute']['execute_status'], $form_state);
    $this->getCapturePaymentStatusSelector($form_state)->validateSelectorForm($form['plugin_form']['capture']['plugin_form']['capture_status'], $form_state);
    $this->getRefundPaymentStatusSelector($form_state)->validateSelectorForm($form['plugin_form']['refund']['plugin_form']['refund_status'], $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->getExecutePaymentStatusSelector($form_state)->submitSelectorForm($form['plugin_form']['execute']['execute_status'], $form_state);
    $this->getCapturePaymentStatusSelector($form_state)->submitSelectorForm($form['plugin_form']['capture']['plugin_form']['capture_status'], $form_state);
    $this->getRefundPaymentStatusSelector($form_state)->submitSelectorForm($form['plugin_form']['refund']['plugin_form']['refund_status'], $form_state);

    $parents = $form['plugin_form']['brand_label']['#parents'];
    array_pop($parents);
    $values = $form_state->getValues();
    $values = NestedArray::getValue($values, $parents);
    $this->setExecuteStatusId($this->getExecutePaymentStatusSelector($form_state)->getSelectedPlugin()->getPluginId());
    $this->setCapture($values['capture']['capture']);
    $this->setCaptureStatusId($this->getCapturePaymentStatusSelector($form_state)->getSelectedPlugin()->getPluginId());
    $this->setRefund($values['refund']['refund']);
    $this->setRefundStatusId($this->getRefundPaymentStatusSelector($form_state)->getSelectedPlugin()->getPluginId());
    $this->setBrandLabel($values['brand_label']);
  }

  /**
   * Gets the brand label.
   *
   * @return string
   */
  public function getBrandLabel() {
    return$this->configuration['brand_label'];
  }

  /**
   * Sets the brand label.
   *
   * @param string $label
   *
   * @return static
   */
  public function setBrandLabel($label) {
    $this->configuration['brand_label'] = $label;

    return $this;
  }

  /**
   * Gets the payment status selector for the execute phase.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorInterface
   */
  protected function getExecutePaymentStatusSelector(FormStateInterface $form_state) {
    $plugin_selector = $this->getPaymentStatusSelector($form_state, 'execute', $this->getExecuteStatusId());
    $plugin_selector->setLabel($this->t('Payment execution status'));
    $plugin_selector->setDescription($this->t('The status to set payments to after being executed by this payment method.'));

    return $plugin_selector;
  }

  /**
   * Gets the payment status selector for the capture phase.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorInterface
   */
  protected function getCapturePaymentStatusSelector(FormStateInterface $form_state) {
    $plugin_selector = $this->getPaymentStatusSelector($form_state, 'capture', $this->getExecuteStatusId());
    $plugin_selector->setLabel($this->t('Payment capture status'));
    $plugin_selector->setDescription($this->t('The status to set payments to after being captured by this payment method.'));

    return $plugin_selector;
  }

  /**
   * Gets the payment status selector for the refund phase.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorInterface
   */
  protected function getRefundPaymentStatusSelector(FormStateInterface $form_state) {
    $plugin_selector = $this->getPaymentStatusSelector($form_state, 'refund', $this->getExecuteStatusId());
    $plugin_selector->setLabel($this->t('Payment refund status'));
    $plugin_selector->setDescription($this->t('The status to set payments to after being refunded by this payment method.'));

    return $plugin_selector;
  }

  /**
   * Gets the payment status selector.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param string $type
   * @param string $default_plugin_id
   *
   * @return \Drupal\plugin\Plugin\Plugin\PluginSelector\PluginSelectorInterface
   */
  protected function getPaymentStatusSelector(FormStateInterface $form_state, $type, $default_plugin_id) {
    $key = 'payment_status_selector_' . $type;
    if ($form_state->has($key)) {
      $plugin_selector = $form_state->get($key);
    }
    else {
      $plugin_selector = $this->pluginSelectorManager->createInstance('payment_select_list');
      $plugin_selector->setSelectablePluginType($this->paymentStatusType);
      $plugin_selector->setRequired(TRUE);
      $plugin_selector->setCollectPluginConfiguration(FALSE);
      $plugin_selector->setSelectedPlugin($this->paymentStatusType->getPluginManager()->createInstance($default_plugin_id));

      $form_state->set($key, $plugin_selector);
    }

    return $plugin_selector;
  }

}
