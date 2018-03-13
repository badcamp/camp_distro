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

    $bundles = $this->config('flag.flag.add_to_schedule')->get('bundles');

    $query = $this->entityTypeManager->getStorage('node')->getQuery('AND');
    $query->condition('type', $bundles, 'IN');
    $query->condition('status', 1);
    $query->notExists('field_venue');
    $query->sort('title');
    $entity_ids = $query->execute();

    $events = Node::loadMultiple($entity_ids);
    $build['content'] = [
      '#theme' => 'camp_scheduler',
      '#events' => $events
    ];

    $build['content']['#attached']['library'][] = 'camp_schedule/camp_schedule.scheduler';

    $tree = $this->getTree('venues');
    $venues = $this->calendarRoomSettings($tree);

    $build['content']['#attached']['drupalSettings']['camp_schedule']['venues'] = $venues;

    $scheduled_events = [];
    $build['content']['#attached']['drupalSettings']['camp_schedule']['events'] = $scheduled_events;


    return $build;
  }

  public function calendarRoomSettings($tree) {
    $tmp = [];
    foreach($tree AS $k => $v){
      $item = [
        'id' => $v->tid,
        'title' => $v->name
      ];

      if(isset($v->children)){
        $item['children'] = [];

        foreach($v->children AS $kc => $vc){
          $item['children'][] = [
            'id' => $vc->tid,
            'title' => $vc->name
          ];
        }
      }

      $tmp[] = $item;
    }

    return $tmp;
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
    $tree[$object->tid] = $object;
    $tree[$object->tid]->children = [];
    $object_children = &$tree[$object->tid]->children;

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
