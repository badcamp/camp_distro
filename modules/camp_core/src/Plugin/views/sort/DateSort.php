<?php

namespace Drupal\camp_core\Plugin\views\sort;

use Drupal\views\Plugin\views\sort\Date;

/**
 * Basic sort handler for Events.
 *
 * @ViewsSort("date_sort")
 */
class DateSort extends Date {

  /**
   * Called to add the sort to a query.
   */
  public function query() {
    $this->ensureMyTable();

    $options = $this->options;

    $format = 'Y-m-d';
    switch($options['granularity']){
      case 'year':
        $format = 'Y';
        break;
      case 'month':
        $format = 'Y-m';
        break;
      case 'day':
        $format = 'Y-m-d';
        break;
      case 'hour':
        $format = 'Y-m-d H';
        break;
      case 'minute':
        $format = 'Y-m-d H:i';
        break;
      case 'second':
        $format = 'Y-m-d H:i:s';
        break;
    }

    $this->query->addOrderBy(
      NULL,
      $this->getDateFormat($format),
      $this->options['order'],
      $options['table'] . '__' . $options['granularity']
    );
  }
}