<?php

namespace Drupal\prise_rendez_vous\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Equipes entity entities.
 *
 * @ingroup prise_rendez_vous
 */
interface EquipesEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Equipes entity name.
   *
   * @return string
   *   Name of the Equipes entity.
   */
  public function getName();

  /**
   * Sets the Equipes entity name.
   *
   * @param string $name
   *   The Equipes entity name.
   *
   * @return \Drupal\prise_rendez_vous\Entity\EquipesEntityInterface
   *   The called Equipes entity entity.
   */
  public function setName($name);

  /**
   * Gets the Equipes entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Equipes entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Equipes entity creation timestamp.
   *
   * @param int $timestamp
   *   The Equipes entity creation timestamp.
   *
   * @return \Drupal\prise_rendez_vous\Entity\EquipesEntityInterface
   *   The called Equipes entity entity.
   */
  public function setCreatedTime($timestamp);

}
