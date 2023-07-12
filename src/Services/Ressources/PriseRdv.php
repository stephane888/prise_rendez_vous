<?php

namespace Drupal\prise_rendez_vous\Services\Ressources;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;
use Drupal\prise_rendez_vous\Entity\EquipesEntity;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\domain\DomainNegotiator;
use Drupal\domain_access\DomainAccessManagerInterface;

/**
 *
 * @author stephane
 *        
 */
class PriseRdv extends ControllerBase {
  protected $maxCreneau = 50;
  
  /**
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeMananger;
  
  /**
   *
   * @var \Drupal\domain\DomainNegotiator
   */
  protected $DomainNegotiator;
  
  function __construct(EntityTypeManager $EntityTypeManager, DomainNegotiator $DomainNegotiator) {
    $this->entityTypeMananger = $EntityTypeManager;
    $this->DomainNegotiator = $DomainNegotiator;
  }
  
  /**
   * permet de fabriquer le tableau des creneaux à partir de la configuration de
   * l'entité "rdv_config_entity".
   *
   * @param ContentEntityBase $entity
   * @throws \Exception
   * @return mixed[][]
   */
  public function getDatasRdv(RdvConfigEntity $entity) {
    if ($entity->isNew()) {
      throw new \Exception(" Contenu non configurée ");
    }
    $confs = $entity->toArray();
    
    $nberDays = $confs['number_week'] * 7;
    $runDateDay = new DrupalDateTime('now', DateTimeItemInterface::STORAGE_TIMEZONE);
    $dateToday = new DrupalDateTime('now', DateTimeItemInterface::STORAGE_TIMEZONE);
    $lastDay = new DrupalDateTime('now', DateTimeItemInterface::STORAGE_TIMEZONE);
    $lastDay->modify("+ " . $nberDays . " days");
    $result['equipes'] = $this->getEquipes($confs);
    $result['equipes_options'] = $this->getEquipesIds($result['equipes']);
    // $result['unvalable'] = $this->getUnvalableCreneaux($dateToday, $lastDay,
    // $confs, $result['equipes']);
    $result['unvalable'] = $this->getCreneauxForPeriode($dateToday, $lastDay, $confs);
    $result['jours'] = [];
    
    for ($i = 0; $i < $nberDays; $i++) {
      $dayConf = $confs['jours'][$runDateDay->format('w')];
      $result['jours'][] = [
        'label' => $runDateDay->format("D.") . '<br>' . $runDateDay->format("j M"),
        'value' => $runDateDay->format("D j M Y"),
        'date' => $runDateDay->format("Y-m-d H:i:s"),
        'conf' => $dayConf,
        'creneau' => $dayConf['status'] ? $this->buildCreneauOfDay($runDateDay, $dateToday, $confs, $dayConf, $result['unvalable'], $result['equipes_options']) : []
      ];
      $runDateDay->modify('+1 day');
    }
    $result['entityType'] = $confs;
    return $result;
  }
  
  /**
   * --
   */
  protected function getEquipes(array $confs) {
    $field_access = DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD;
    $domaineId = $this->DomainNegotiator->getActiveId();
    $entities = $this->entityTypeManager()->getStorage('equipes_entity')->loadByProperties([
      $field_access => $domaineId,
      'rdv_config_entity' => $confs['id']
    ]);
    //
    $equipes = [];
    foreach ($entities as $entity) {
      $equipes[] = [
        'id' => $entity->id(),
        'title' => $entity->label()
      ];
    }
    return $equipes;
  }
  
  protected function getEquipesIds(array $equipes) {
    $ar = [];
    foreach ($equipes as $equipe) {
      $ar[] = $equipe['id'];
    }
    return $ar;
  }
  
  /**
   * Recupere les creneaux non valide.
   */
  protected function getUnvalableCreneaux(DrupalDateTime $dateToday, DrupalDateTime $lastDay, array $confs, array $equipes) {
    $Unvalables = [];
    $domainId = \Drupal\creation_site_virtuel\CreationSiteVirtuel::getActiveDomain();
    $field_access = \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD;
    foreach ($equipes as $equipe) {
      $query = "
      select COUNT(creneau_string) AS cnt, creneau_string, creneau__value, DATE_FORMAT(creneau__value, '%Y-%m-%d') as 'day'  FROM submit_rdv_entity_field_data
      WHERE rdv_config_entity = '" . $confs['id'] . "' and  $field_access = '" . $domainId . "' and equipes_entity=" . $equipe['id'] . "
      GROUP BY day, creneau_string
   ";
      if ($confs['limit_reservation']) {
        $query .= " HAVING cnt >=  " . $confs['limit_reservation'];
      }
      $result = \Drupal::database()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
      if ($result) {
        foreach ($result as $value) {
          $Unvalables[$value['day']][$value['creneau_string']][] = $equipe['id'];
        }
      }
    }
    return $Unvalables;
  }
  
  /**
   * Recuperer tous les creneaux invalids pour la periode.
   */
  protected function getCreneauxForPeriode(DrupalDateTime $dateToday, DrupalDateTime $lastDay, array $confs) {
    $Unvalables = [];
    $query = "
      select COUNT(creneau_string) AS cnt, creneau_string, creneau__value, DATE_FORMAT(creneau__value, '%Y-%m-%d') as 'day'  FROM submit_rdv_entity_field_data
      WHERE rdv_config_entity = '" . $confs['id'] . "' and  " . DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD . " = '" . $this->DomainNegotiator->getActiveId() . "' 
      and creneau__value >= '" . $dateToday->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT) . "' 
      and creneau__end_value < '" . $lastDay->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT) . "' 
      GROUP BY day, creneau_string
   ";
    if ($confs['limit_reservation']) {
      $query .= " HAVING cnt >=  " . $confs['limit_reservation'];
    }
    $result = \Drupal::database()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    // dump($result, $confs);
    $result = \Drupal::database()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    if ($result) {
      foreach ($result as $value) {
        $Unvalables[$value['day']][$value['creneau_string']] = $value;
      }
    }
    
    // $query =
    // $this->entityTypeMananger->getStorage('submit_rdv_entity')->getQuery();
    // $query->condition('creneau.value',
    // $dateToday->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT),
    // '>=');
    // $query->condition('creneau.end_value',
    // $lastDay->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<');
    // $query->condition(DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD,
    // $this->DomainNegotiator->getActiveId());
    // if ($confs['limit_reservation']) {
    // //
    // }
    // $ids = $query->execute();
    // if ($ids) {
    // $OldReservations =
    // $this->entityTypeMananger->getStorage('submit_rdv_entity')->loadMultiple($ids);
    // }
    
    return $Unvalables;
  }
  
  /**
   *
   * @param \DateTime $day
   * @param \DateTime $dateToday
   * @param array $entityArray
   * @param array $dayConf
   * @return boolean[][]|NULL[][]
   */
  protected function buildCreneauOfDay(DrupalDateTime $day, DrupalDateTime $dateToday, array $entityArray, array $dayConf, array $Unvalables, array $equipes) {
    $creneaux = [];
    $day_string = $day->format("Y-m-d H:i:s");
    $day_string_small = $day->format("Y-m-d");
    $UnvalablesCreneaux = [];
    // dump($day_string_small);
    foreach ($Unvalables as $k_day_string => $value) {
      // Pour cette journée certains creneaux sont desctivées.
      if ($day_string_small == $k_day_string) {
        $UnvalablesCreneaux = $value;
        break;
      }
    }
    // if ($UnvalablesCreneaux)
    // dump($UnvalablesCreneaux);
    
    $d = new DrupalDateTime($day_string);
    $f = new DrupalDateTime($day_string);
    $d->setTime($dayConf['h_d'], $dayConf['m_d']);
    $f->setTime($dayConf['h_f'], $dayConf['m_f']);
    $interval = !empty($entityArray['interval']) ? $entityArray['interval'] : 30;
    //
    if ($f > $d) {
      $i = 0;
      while ($f > $d && $i < $this->maxCreneau) {
        $temPronEquipe = $equipes;
        $i++;
        $cr = $d->format('H:i');
        $status = true;
        // Ce creneaux est desactivé
        if (!empty($UnvalablesCreneaux[$cr])) {
          if ($equipes) {
            foreach ($UnvalablesCreneaux[$cr] as $id_equipe) {
              $index = array_search($id_equipe, $equipes);
              if ($index !== false) {
                unset($temPronEquipe[$index]);
              }
            }
          }
          else {
            $status = false;
          }
        }
        
        $creneaux[] = [
          'value' => $cr,
          'status' => ($dateToday->getTimestamp() < $d->getTimestamp() && $status) ? true : false,
          'Unvalable' => $UnvalablesCreneaux,
          'equipes' => array_values($temPronEquipe)
        ];
        $d->modify("+ " . $interval . " minutes");
      }
    }
    return $creneaux;
  }
  
}