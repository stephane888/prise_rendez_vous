<?php

namespace Drupal\prise_rendez_vous\Services\Ressources;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;
use Drupal\prise_rendez_vous\Entity\EquipesEntity;

/**
 *
 * @author stephane
 *
 */
class PriseRdv extends ControllerBase {
  protected $maxCreneau = 50;

  /**
   * permet de fabriquer le tableau des creneaux
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
    // dump($confs);
    $nberDays = $confs['number_week'] * 7;
    $runDateDay = new \DateTime('Now');
    $dateToday = new \DateTime('Now');
    $lastDay = new \DateTime('Now');
    $lastDay->modify("+ " . $nberDays . " days");
    $result['equipes'] = $this->getEquipes($confs);
    $result['equipes_options'] = $this->getEquipesIds($result['equipes']);
    $result['unvalable'] = $this->getUnvalableCreneaux($dateToday, $lastDay, $confs, $result['equipes']);
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
    $field_access = \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD;
    $domaineId = \Drupal\creation_site_virtuel\CreationSiteVirtuel::getActiveDomain();
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
  protected function getUnvalableCreneaux(\DateTime $dateToday, \DateTime $lastDay, array $confs, array $equipes) {
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
   *
   * @param \DateTime $day
   * @param \DateTime $dateToday
   * @param array $entityArray
   * @param array $dayConf
   * @return boolean[][]|NULL[][]
   */
  protected function buildCreneauOfDay(\DateTime $day, $dateToday, array $entityArray, array $dayConf, array $Unvalables, array $equipes) {
    $creneaux = [];
    $day_string = $day->format("Y-m-d H:i:s");
    $day_string_small = $day->format("Y-m-d");
    $UnvalablesCreneaux = [];

    foreach ($Unvalables as $k_day_string => $value) {
      // Pour cette journée certains creneaux sont desctivées.
      if ($day_string_small == $k_day_string) {
        $UnvalablesCreneaux = $value;
        break;
      }
    }

    $d = new \DateTime($day_string);
    $f = new \DateTime($day_string);
    $d->setTime($dayConf['h_d'], $dayConf['m_d']);
    $f->setTime($dayConf['h_f'], $dayConf['m_f']);
    $interval = !empty($entityArray['interval']) ? $entityArray['interval'] : 30;

    if ($f > $d) {
      $i = 0;
      while ($f > $d && $i < $this->maxCreneau) {
        $temPronEquipe = $equipes;
        $i++;
        $cr = $d->format('H:i');
        $status = true;
        // ce creneaux est desactivé
        if (!empty($UnvalablesCreneaux[$cr])) {
          foreach ($UnvalablesCreneaux[$cr] as $id_equipe) {
            $index = array_search($id_equipe, $equipes);
            if ($index !== false) {
              unset($temPronEquipe[$index]);
            }
          }
        }
        $creneaux[] = [
          'value' => $cr,
          'status' => ($dateToday < $d && $status) ? true : false,
          'Unvalable' => $UnvalablesCreneaux,
          'equipes' => array_values($temPronEquipe)
        ];
        $d->modify("+ " . $interval . " minutes");
      }
    }
    return $creneaux;
  }

}