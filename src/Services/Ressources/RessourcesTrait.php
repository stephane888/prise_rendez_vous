<?php

namespace Drupal\prise_rendez_vous\Services\Ressources;

trait RessourcesTrait {

  /**
   * --
   */
  public function addDomain(&$Entity, $domainId = null) {
    if (\Drupal::moduleHandler()->moduleExists('domain')) {
      $field_access = \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD;
      $field_source = \Drupal\domain_source\DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD;
      if (!$domainId) {
        $domainId = \Drupal\creation_site_virtuel\CreationSiteVirtuel::getActiveDomain();
      }
      //
      $Entity->set($field_access, $domainId);
      $Entity->set($field_source, $domainId);
    }
  }

}