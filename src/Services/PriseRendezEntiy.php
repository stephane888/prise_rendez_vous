<?php

namespace Drupal\prise_rendez_vous\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\prise_rendez_vous\Services\Ressources\EquipesService;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;
use Drupal\prise_rendez_vous\Services\Ressources\DisPeriodService;
use Drupal\prise_rendez_vous\Services\Ressources\PriseRdv;
use Drupal\prise_rendez_vous\Services\Ressources\SaveRdvEntityService;

/**
 * Permet de gerer les RDV des entites.
 *
 * @author stephane
 *        
 */
class PriseRendezEntiy extends ControllerBase {
  protected const entityRdvConfig = 'rdv_config_entity';
  
  /**
   *
   * @var DisPeriodService
   */
  protected $DisPeriodService;
  
  /**
   *
   * @var EquipesService
   */
  public $EquipesService;
  
  /**
   *
   * @var PriseRdv
   */
  protected $PriseRdv;
  
  /**
   *
   * @var SaveRdvEntityService
   */
  public $SaveRdvEntityService;
  
  function __construct(EquipesService $EquipesService, DisPeriodService $DisPeriodService, PriseRdv $PriseRdv, SaveRdvEntityService $SaveRdvEntityService) {
    $this->EquipesService = $EquipesService;
    $this->DisPeriodService = $DisPeriodService;
    $this->PriseRdv = $PriseRdv;
    $this->SaveRdvEntityService = $SaveRdvEntityService;
  }
  
  /**
   * Permet de recuperer le formulaire de rendu de la configuration.
   */
  public function getConfigForm(ContentEntityBase $entity) {
    return $this->entityFormBuilder()->getForm($this->getConfigEntity($entity));
  }
  
  /**
   * --
   */
  public function saveConfigForm(array $values) {
    $typeRdv = $this->entityTypeManager()->getStorage(self::entityRdvConfig);
    // On verifie si l'entité existe deja.
    $rdvConfig = $typeRdv->load($values['id']);
    if ($rdvConfig) {
      foreach ($values as $k => $value) {
        $rdvConfig->set($k, $value);
      }
    }
    else
      $rdvConfig = $typeRdv->create($values);
    
    //
    $rdvConfig->save();
    return $rdvConfig;
  }
  
  /**
   *
   * @param ContentEntityBase $entity
   * @return \Drupal\Core\Entity\EntityInterface|NULL|\Drupal\Core\Entity\EntityInterface
   */
  public function getConfigEntity(ContentEntityBase $entity) {
    $key = $this->getKeyId($entity);
    $typeRdv = $this->entityTypeManager()->getStorage(self::entityRdvConfig);
    $entityRdv = $typeRdv->load($key);
    if ($entityRdv)
      return $entityRdv;
    //
    $entityConf = $typeRdv->create([
      'id' => $key,
      'label' => $this->getLabelRdv($entity)
    ]);
    $entityConf->save();
    return $entityConf;
  }
  
  /**
   */
  public function getEntityEquipes(RdvConfigEntity $entity) {
    return $this->EquipesService->getEntityEquipes($entity);
  }
  
  /**
   *
   * @param RdvConfigEntity $entity
   * @return mixed|\Drupal\Core\Entity\EntityInterface
   */
  public function getEntityDisPeriod(RdvConfigEntity $entity) {
    return $this->DisPeriodService->getEntityDisPeriod($entity);
  }
  
  /**
   */
  public function getCreneaux(ContentEntityBase $entity) {
    $rdvConfig = $this->getConfigEntity($entity);
    if ($rdvConfig) {
      return $this->PriseRdv->getDatasRdv($rdvConfig);
    }
    throw new \Exception("Le contenu n'est pas definit");
  }
  
  /**
   * Permet de dupliquer la configuration d'un entité et de mettre sur un autre.
   */
  public function CloneFromAnotherEntity(ContentEntityBase $cloneNode, ContentEntityBase $node, ContentEntityBase $entityData) {
    // Duplication de la configuration.
    $cloneConfigRdv = $this->getConfigEntity($node)->createDuplicate();
    $cloneConfigRdv->set('id', $this->getKeyId($cloneNode));
    $cloneConfigRdv->set('label', $this->getLabelRdv($cloneNode));
    $cloneConfigRdv->save();
    //
    $domainId = null;
    if (\Drupal::moduleHandler()->moduleExists('domain')) {
      $field_access = \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD;
      $domainId = $entityData->get($field_access)->target_id;
    }
    $this->EquipesService->clone($this->getConfigEntity($node), $domainId, $cloneConfigRdv->id());
    $this->DisPeriodService->clone($this->getConfigEntity($node), $domainId, $cloneConfigRdv->id());
  }
  
  /**
   *
   * @param ContentEntityBase $entity
   * @return mixed
   */
  protected function getKeyId(ContentEntityBase $entity) {
    return preg_replace('/[^a-z0-9\-]/', "_", $entity->getEntityTypeId() . '.' . $entity->bundle() . '.' . $entity->id());
  }
  
  /**
   *
   * @param ContentEntityBase $entity
   * @return string
   */
  protected function getLabelRdv(ContentEntityBase $entity) {
    return $entity->bundle() . ' : ' . $entity->label();
  }
  
}