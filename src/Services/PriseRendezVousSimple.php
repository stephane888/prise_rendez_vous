<?php

namespace Drupal\prise_rendez_vous\Services;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\prise_rendez_vous\Services\Ressources\EquipesService;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;
use Drupal\prise_rendez_vous\Services\Ressources\DisPeriodService;

/**
 * Permet de gerer les simples RDV.
 *
 * @author stephane
 *
 */
class PriseRendezVousSimple extends ControllerBase {
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
  protected $EquipesService;

  function __construct(EquipesService $EquipesService, DisPeriodService $DisPeriodService) {
    $this->EquipesService = $EquipesService;
    $this->DisPeriodService = $DisPeriodService;
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
    if (!empty($values['id'])) {
      $rdvConfig = $typeRdv->load($values['id']);
      if ($rdvConfig) {
        foreach ($values as $k => $value) {
          $rdvConfig->set($k, $value);
        }
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
    else {
      return $typeRdv->create([
        'id' => $key,
        'label' => $key
      ]);
    }
  }

  /**
   */
  public function getEntityEquipes(RdvConfigEntity $entity) {
    return $this->EquipesService->getEntityEquipes($entity);
  }

  public function getEntityDisPeriod(RdvConfigEntity $entity) {
    return $this->DisPeriodService->getEntityDisPeriod($entity);
  }

  /**
   *
   * @param ContentEntityBase $entity
   * @return mixed
   */
  protected function getKeyId(ContentEntityBase $entity) {
    return preg_replace('/[^a-z0-9\-]/', "_", $entity->getEntityTypeId() . '.' . $entity->bundle() . '.' . $entity->id());
  }

}