<?php

namespace Drupal\prise_rendez_vous\Services\Ressources;

use Drupal\Core\Controller\ControllerBase;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;

/**
 *
 * @author stephane
 *
 */
class EquipesService extends ControllerBase {
  use RessourcesTrait;
  /**
   *
   * @var string
   */
  protected const entityEquipes = 'equipes_entity';

  /**
   * On aurra un contenu equipe pour un type de reservation.
   *
   * @param RdvConfigEntity $entity
   */
  public function getEntityEquipes(RdvConfigEntity $entity) {
    $equipes = $this->entityTypeManager()->getStorage(self::entityEquipes)->loadByProperties([
      'rdv_config_entity' => $entity->id()
    ]);
    //
    if (!empty($equipes)) {
      return reset($equipes);
    }
    else {
      $values = [
        'rdv_config_entity' => $entity->id()
      ];
      $Entity = $this->entityTypeManager()->getStorage(self::entityEquipes)->create($values);
      $this->addDomain($Entity);
      return $Entity;
    }
  }

  /**
   * --
   *
   * @param RdvConfigEntity $entity
   */
  public function clone(RdvConfigEntity $entity, $domainId = null) {
    $equipe = $this->getEntityEquipes($entity);
    $cloneEquipe = $equipe->createDuplicate();
    $this->addDomain($cloneEquipe, $domainId);
    $cloneEquipe->save();
  }

}