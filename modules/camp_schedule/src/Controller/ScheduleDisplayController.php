<?php

namespace Drupal\camp_schedule\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ScheduleDisplayController.
 */
class ScheduleDisplayController extends ControllerBase {

  /** @var \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter */
  protected $dateFormatter;

  /**
   * ScheduleDisplayController constructor.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   */
  public function __construct(DateFormatterInterface $dateFormatter) {
    $this->dateFormatter = $dateFormatter;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'camp_schedule';
  }

  /**
   *
   */
  public function scheduleDisplay($date = NULL) {
    $config = $this->config('camp_schedule.schedule_settings');
    if($config->get('single_page') === true){
      $view = views_embed_view('schedule', 'full_schedule');
    }else {
      $date = $this->dateFormatter->format(strtotime($date), 'custom', 'Y-m-d');
      $view = views_embed_view('schedule', 'day', $date);
    }

    return $view;
  }

}
