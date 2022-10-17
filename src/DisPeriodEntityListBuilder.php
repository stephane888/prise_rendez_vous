<?php

namespace Drupal\prise_rendez_vous;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Disable periode entity entities.
 *
 * @ingroup prise_rendez_vous
 */
class DisPeriodEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Disable periode entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\prise_rendez_vous\Entity\DisPeriodEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.dis_period_entity.edit_form',
      ['dis_period_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
