<?php

namespace Drupal\prise_rendez_vous\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for prise rendez vous routes.
 */
class PriseRendezVousController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
