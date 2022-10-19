<?php

namespace Drupal\prise_rendez_vous;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Equipes entity entities.
 *
 * @ingroup prise_rendez_vous
 */
class EquipesEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Equipes entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\prise_rendez_vous\Entity\EquipesEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.equipes_entity.edit_form',
      ['equipes_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
