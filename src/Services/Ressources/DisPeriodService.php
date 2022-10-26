<?php

namespace Drupal\prise_rendez_vous\Services\Ressources;

use Drupal\Core\Controller\ControllerBase;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;

class DisPeriodService extends ControllerBase {
  use RessourcesTrait;
  protected const entityDisPeriod = 'dis_period_entity';

  /**
   * On aurra un contenu equipe pour un type de reservation.
   *
   * @deprecated
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
      $Entity = $this->entityTypeManager()->getStorage(self::entityDisPeriod)->create($values);
      $this->addDomain($Entity);
      return $Entity;
    }
  }

  /**
   * --
   *
   * @param RdvConfigEntity $entity
   */
  public function clone(RdvConfigEntity $entity, $domainId = null, $id_rdv_config_entity) {
    $field_access = \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD;
    $domaineId = \Drupal\creation_site_virtuel\CreationSiteVirtuel::getActiveDomain();
    $equipes = $this->entityTypeManager()->getStorage(self::entityDisPeriod)->loadByProperties([
      'rdv_config_entity' => $entity->id(),
      $field_access => $domaineId
    ]);
    foreach ($equipes as $equipe) {
      $cloneEquipe = $equipe->createDuplicate();
      $cloneEquipe->set('rdv_config_entity', $id_rdv_config_entity);
      $this->addDomain($cloneEquipe, $domainId);
      $cloneEquipe->save();
    }
  }

}