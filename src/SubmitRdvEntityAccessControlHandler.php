<?php

namespace Drupal\prise_rendez_vous;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Submit rdv entity entity.
 *
 * @see \Drupal\prise_rendez_vous\Entity\SubmitRdvEntity.
 */
class SubmitRdvEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished submit rdv entity entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published submit rdv entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit submit rdv entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete submit rdv entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add submit rdv entity entities');
  }


}
