<?php

namespace Drupal\prise_rendez_vous\Services\Ressources;

use Drupal\Core\Controller\ControllerBase;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;

class DisPeriodService extends ControllerBase {
  protected const entityDisPeriod = 'dis_period_entity';

  /**
   * On aurra un contenu equipe pour un type de reservation.
   */
  public function getEntityDisPeriod(RdvConfigEntity $entity) {
    $equipes = $this->entityTypeManager()->getStorage(self::entityDisPeriod)->loadByProperties([
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
      return $this->entityTypeManager()->getStorage(self::entityDisPeriod)->create($values);
    }
  }

}