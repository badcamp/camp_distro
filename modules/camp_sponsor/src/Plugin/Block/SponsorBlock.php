<?php

namespace Drupal\camp_sponsor\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a generic Menu block.
 *
 * @Block(
 *   id = "sponsor_block",
 *   admin_label = @Translation("Sponsor"),
 *   category = @Translation("Sponsors"),
 *   deriver = "Drupal\camp_sponsor\Plugin\Derivative\SponsorBlock"
 * )
 */
class SponsorBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a new SponsorBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

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

  /**
   * {@inheritdoc}
   */
  public function build() {
    $sponsor_level = $this->getDerivativeId();
    return views_embed_view('sponsors', 'sponsor_level', $sponsor_level);
  }

}
