<?php

namespace Drupal\camp_sponsor\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides block plugin definitions for custom menus.
 *
 * @see \Drupal\camp_sponsor\Plugin\Block\SponsorBlock
 */
class SponsorBlock extends DeriverBase implements ContainerDeriverInterface {

  /** @var \Drupal\Core\Entity\EntityStorageInterface $term_storage */
  protected $term_storage;

  /**
   * Constructs new SponsorBlock.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $term_storage
   */
  public function __construct(EntityStorageInterface $term_storage) {
    $this->term_storage = $term_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity.manager')->getStorage('taxonomy_term')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $vid = 'sponsor_level';
    foreach ($this->term_storage->loadTree($vid) as $term) {
      $this->derivatives[$term->tid] = $base_plugin_definition;
      $this->derivatives[$term->tid]['admin_label'] = $term->name;
    }
    return $this->derivatives;
  }

}
