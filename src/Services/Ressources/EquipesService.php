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
      // dd($values);
      return $this->entityTypeManager()->getStorage(self::entityEquipes)->create($values);
    }
  }

}