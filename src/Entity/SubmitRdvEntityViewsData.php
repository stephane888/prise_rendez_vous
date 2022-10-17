<?php

namespace Drupal\prise_rendez_vous\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Submit rdv entity entities.
 */
class SubmitRdvEntityViewsData extends EntityViewsData {

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
