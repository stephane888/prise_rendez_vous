<?php

namespace Drupal\prise_rendez_vous;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Disable periode entity entity.
 *
 * @see \Drupal\prise_rendez_vous\Entity\DisPeriodEntity.
 */
class DisPeriodEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\prise_rendez_vous\Entity\DisPeriodEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished disable periode entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published disable periode entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit disable periode entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete disable periode entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add disable periode entity entities');
  }


}
