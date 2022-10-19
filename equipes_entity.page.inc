<?php

/**
 * @file
 * Contains equipes_entity.page.inc.
 *
 * Page callback for Equipes entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Equipes entity templates.
 *
 * Default template: equipes_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_equipes_entity(array &$variables) {
  // Fetch EquipesEntity Entity Object.
  $equipes_entity = $variables['elements']['#equipes_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
