<?php

namespace Drupal\prise_rendez_vous;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\prise_rendez_vous\Entity\DisPeriodEntityInterface;

/**
 * Defines the storage handler class for Disable periode entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Disable periode entity entities.
 *
 * @ingroup prise_rendez_vous
 */
interface DisPeriodEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Disable periode entity revision IDs for a specific Disable periode entity.
   *
   * @param \Drupal\prise_rendez_vous\Entity\DisPeriodEntityInterface $entity
   *   The Disable periode entity entity.
   *
   * @return int[]
   *   Disable periode entity revision IDs (in ascending order).
   */
  public function revisionIds(DisPeriodEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Disable periode entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Disable periode entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\prise_rendez_vous\Entity\DisPeriodEntityInterface $entity
   *   The Disable periode entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DisPeriodEntityInterface $entity);

  /**
   * Unsets the language for all Disable periode entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
