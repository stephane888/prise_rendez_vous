<?php

/**
 * @file
 * Contains dis_period_entity.page.inc.
 *
 * Page callback for Disable periode entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Disable periode entity templates.
 *
 * Default template: dis_period_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dis_period_entity(array &$variables) {
  // Fetch DisPeriodEntity Entity Object.
  $dis_period_entity = $variables['elements']['#dis_period_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
