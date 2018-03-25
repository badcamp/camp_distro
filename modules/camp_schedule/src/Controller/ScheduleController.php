<?php

namespace Drupal\camp_schedule\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ScheduleController.
 */
class ScheduleController extends ControllerBase {

  /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager */
  protected $entityTypeManager;

  /**
   * ScheduleController constructor.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
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
  public function scheduleBuilder() {
    $build = [];

    $events = $this->getCampEvents(FALSE);
    $build['content'] = [
      '#theme' => 'camp_scheduler',
      '#events' => $events
    ];

    $build['content']['#attached']['library'][] = 'camp_schedule/camp_schedule.scheduler';

    $venues = $this->getTree('venues');
    $build['content']['#attached']['drupalSettings']['camp_schedule']['venues'] = $venues;

    $scheduled_events = $this->getCampEvents(TRUE);
    $build['content']['#attached']['drupalSettings']['camp_schedule']['events'] = $scheduled_events;

    return $build;
  }

  public function getCampEvents($scheduled = TRUE) {
    $bundles = $this->config('flag.flag.add_to_schedule')->get('bundles');
    $query = $this->entityTypeManager->getStorage('node')->getQuery('AND');
    $query->condition('type', $bundles, 'IN');
    $query->condition('status', 1);

    if(!$scheduled) {
      $query->notExists('field_venue');
      $query->notExists('field_date');
    }else{
      $query->exists('field_date');
      $query->exists('field_venue');
    }
    $query->sort('title');
    $entity_ids = $query->execute();

    $events = Node::loadMultiple($entity_ids);

    if($scheduled) {
      $eventList = [];
      foreach ($events AS $event) {
        $dates = $event->get('field_date')->getValue();
        $venues = $event->get('field_venue')->getValue();
        $date = array_shift($dates);
        $venue = array_shift($venues);
        $eventList[] = [
          'id' => $event->id(),
          'title' => $event->getTitle(),
          'start' => $date['value'],
          'end' => $date['end_value'],
          'resourceId' => $venue['target_id'],
          'type' => $event->bundle()
        ];
      }
      return $eventList;
    }

    return $events;
  }

  /**
   * Loads the tree of a vocabulary.
   *
   * @param string $vocabulary
   *   Machine name
   *
   * @return array
   */
  public function getTree($vocabulary) {
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vocabulary);
    $tree = [];
    foreach ($terms as $tree_object) {
      $this->buildTree($tree, $tree_object, $vocabulary);
    }

    return $tree;
  }

  /**
   * Populates a tree array given a taxonomy term tree object.
   *
   * @param $tree
   * @param $object
   * @param $vocabulary
   */
  protected function buildTree(&$tree, $object, $vocabulary) {
    if ($object->depth != 0) {
      return;
    }
    $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($object->tid);
    $tmpObject = [];
    $tmpObject['id'] = $object->tid;
    $tmpObject['title'] = $object->name;
    $tmpObject['children'] = [];
    $capacity = $term->get('field_capacity')->getValue();
    $tmpObject['capacity'] = array_shift($capacity)['value'];
    $tree[] = &$tmpObject;

    $object_children = &$tmpObject['children'];

    $children = $this->entityTypeManager->getStorage('taxonomy_term')->loadChildren($object->tid);
    if (!$children) {
      return;
    }

    $child_tree_objects = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vocabulary, $object->tid);

    foreach ($children as $child) {
      foreach ($child_tree_objects as $child_tree_object) {
        if ($child_tree_object->tid == $child->id()) {
          $this->buildTree($object_children, $child_tree_object, $vocabulary);
        }
      }
    }

  }
}
