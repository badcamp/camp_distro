<?php

namespace Drupal\camp_schedule\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with the current day's schedule.
 *
 * @Block(
 *   id = "camp_schedule_current_day",
 *   admin_label = @Translation("Current Day's Schedule")
 * )
 */
class CurrentDaySchedule extends BlockBase implements ContainerFactoryPluginInterface {

  /** @var \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter */
  protected $dateFormatter;

  /**
   * CurrentDaySchedule constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, DateFormatterInterface $dateFormatter) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->dateFormatter = $dateFormatter;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $date = $this->dateFormatter->format(time(), 'custom', 'Y-m-d')
    $view = views_embed_view('schedule', 'current_day', $date);
    return $view;
  }

}
