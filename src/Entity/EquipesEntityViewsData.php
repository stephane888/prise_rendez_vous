<?php

namespace Drupal\prise_rendez_vous\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Equipes entity entities.
 */
class EquipesEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
