<?php

/**
 * @file
 * Contains evaluation.page.inc.
 *
 * Page callback for Evaluation entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Evaluation templates.
 *
 * Default template: evaluation.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_evaluation(array &$variables) {
  // Fetch EvaluationEntity Entity Object.
  $evaluation = $variables['elements']['#evaluation'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
