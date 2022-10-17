<?php

namespace Drupal\prise_rendez_vous;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface;

/**
 * Defines the storage handler class for Submit rdv entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Submit rdv entity entities.
 *
 * @ingroup prise_rendez_vous
 */
interface SubmitRdvEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Submit rdv entity revision IDs for a specific Submit rdv entity.
   *
   * @param \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface $entity
   *   The Submit rdv entity entity.
   *
   * @return int[]
   *   Submit rdv entity revision IDs (in ascending order).
   */
  public function revisionIds(SubmitRdvEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Submit rdv entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Submit rdv entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface $entity
   *   The Submit rdv entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(SubmitRdvEntityInterface $entity);

  /**
   * Unsets the language for all Submit rdv entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
