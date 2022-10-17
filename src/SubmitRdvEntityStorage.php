<?php

namespace Drupal\prise_rendez_vous;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class SubmitRdvEntityStorage extends SqlContentEntityStorage implements SubmitRdvEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(SubmitRdvEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {submit_rdv_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {submit_rdv_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(SubmitRdvEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {submit_rdv_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('submit_rdv_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
