<?php

/**
 *
 * @file
 * Provides Drupal\icecream\FlavorInterface
 */
namespace Drupal\prise_rendez_vous;

/**
 * Defines an interface for ice cream flavor plugins.
 */
class PriseRendezVous implements PriseRendezVousInterface {

  /**
   * --
   */
  public static function getJoursOptions() {
    $options = [];
    foreach (self::jours as $k => $v) {
      $options[$k] = $v['label'];
    }
    return $options;
  }

}