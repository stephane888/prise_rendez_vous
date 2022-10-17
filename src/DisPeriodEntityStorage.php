<?php

namespace Drupal\prise_rendez_vous;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class DisPeriodEntityStorage extends SqlContentEntityStorage implements DisPeriodEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(DisPeriodEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {dis_period_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {dis_period_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(DisPeriodEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {dis_period_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('dis_period_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
