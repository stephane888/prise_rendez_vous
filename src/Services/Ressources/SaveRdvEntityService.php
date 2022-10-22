<?php

namespace Drupal\prise_rendez_vous\Services\Ressources;

use Drupal\Core\Controller\ControllerBase;
use Drupal\prise_rendez_vous\Entity\SubmitRdvEntity;

/**
 *
 * @author stephane
 *
 */
class SaveRdvEntityService extends ControllerBase {
  /**
   *
   * @var string
   */
  protected const entitySubmitRdv = 'equipes_entity';

  /**
   *
   * @param array $values
   * @return \Drupal\prise_rendez_vous\Entity\SubmitRdvEntity
   */
  function saveRdv(array $values) {
    $EntitySubmitRdv = SubmitRdvEntity::create($values);
    // add addtionnal info.
    $this->addDomain($EntitySubmitRdv);
    $EntitySubmitRdv->save();
    return $EntitySubmitRdv;
  }

  /**
   * --
   */
  function addDomain(&$Entity) {
    if (\Drupal::moduleHandler()->moduleExists('domain')) {
      $field_access = \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD;
      $field_source = \Drupal\domain_source\DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD;
      $domainId = \Drupal\creation_site_virtuel\CreationSiteVirtuel::getActiveDomain();
      $Entity->set($field_access, $domainId);
      $Entity->set($field_source, $domainId);
    }
  }

}