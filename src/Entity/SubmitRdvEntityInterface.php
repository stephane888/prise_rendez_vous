<?php

namespace Drupal\prise_rendez_vous\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Submit rdv entity entities.
 *
 * @ingroup prise_rendez_vous
 */
interface SubmitRdvEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Submit rdv entity name.
   *
   * @return string
   *   Name of the Submit rdv entity.
   */
  public function getName();

  /**
   * Sets the Submit rdv entity name.
   *
   * @param string $name
   *   The Submit rdv entity name.
   *
   * @return \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface
   *   The called Submit rdv entity entity.
   */
  public function setName($name);

  /**
   * Gets the Submit rdv entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Submit rdv entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Submit rdv entity creation timestamp.
   *
   * @param int $timestamp
   *   The Submit rdv entity creation timestamp.
   *
   * @return \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface
   *   The called Submit rdv entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Submit rdv entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Submit rdv entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface
   *   The called Submit rdv entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Submit rdv entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Submit rdv entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface
   *   The called Submit rdv entity entity.
   */
  public function setRevisionUserId($uid);

}
