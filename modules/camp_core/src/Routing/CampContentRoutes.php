<?php

/**
 * @file
 * Contains \Drupal\camp_core\Routing\CampContentRoutes.
 */

namespace Drupal\camp_core\Routing;

use Symfony\Component\Routing\Route;

/**
 * Defines dynamic routes.
 */
class CampContentRoutes {

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $routes = array();
    $nodeTypes = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    foreach($nodeTypes AS $nodeType) {
      $routes['camp_core.content.' . $nodeType->id()] = new Route(
        '/admin/content/camp/' . $nodeType->id(),
        [
          '_controller' => '\Drupal\camp_core\Controller\CampContentList::page',
          '_title_callback' => '\Drupal\camp_core\Controller\CampContentList::title',
          'node_type' => $nodeType->id()
        ],
        [
          '_permission' => 'edit any ' . $nodeType->id() . ' content',
        ]
      );
    }
    return $routes;
  }

}