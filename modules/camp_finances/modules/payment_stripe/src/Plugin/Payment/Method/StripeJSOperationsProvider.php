<?php

/**
 * Contains \Drupal\payment\Plugin\Payment\Method\BasicOperationsProvider.
 */

namespace Drupal\payment_stripe\Plugin\Payment\Method;

use Drupal\payment\Plugin\Payment\Method\PaymentMethodConfigurationOperationsProvider;

/**
 * Provides payment_basic operations based on config entities.
 */
class StripeJSOperationsProvider extends PaymentMethodConfigurationOperationsProvider {

  /**
   * {@inheritdoc}
   */
  protected function getPaymentMethodConfiguration($plugin_id) {
    $entity_id = substr($plugin_id, strlen('payment_stripejs:'));

    return $this->paymentMethodConfigurationStorage->load($entity_id);
  }

}
