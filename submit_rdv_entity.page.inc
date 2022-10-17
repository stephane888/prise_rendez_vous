<?php

/**
 * @file
 * Contains submit_rdv_entity.page.inc.
 *
 * Page callback for Submit rdv entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Submit rdv entity templates.
 *
 * Default template: submit_rdv_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_submit_rdv_entity(array &$variables) {
  // Fetch SubmitRdvEntity Entity Object.
  $submit_rdv_entity = $variables['elements']['#submit_rdv_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
