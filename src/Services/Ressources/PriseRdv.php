<?php

namespace Drupal\prise_rendez_vous\Services\Ressources;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;

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
    $result['unvalable'] = $this->getUnvalableCreneaux($dateToday, $lastDay, $confs);
    $result['jours'] = [];
    for ($i = 0; $i < $nberDays; $i++) {
      $dayConf = $confs['jours'][$runDateDay->format('w')];
      $result['jours'][] = [
        'label' => $runDateDay->format("D.") . '<br>' . $runDateDay->format("j M"),
        'value' => $runDateDay->format("D j M Y"),
        'date' => $runDateDay->format("Y-m-d H:i:s"),
        'conf' => $dayConf,
        'creneau' => $dayConf['status'] ? $this->buildCreneauOfDay($runDateDay, $dateToday, $confs, $dayConf, $result['unvalable']) : []
      ];
      $runDateDay->modify('+1 day');
    }
    $result['entityType'] = $confs;
    return $result;
  }

  /**
   * Recupere les creneaux non valide.
   */
  protected function getUnvalableCreneaux(\DateTime $dateToday, \DateTime $lastDay, array $confs) {
    $Unvalables = [];
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
  protected function buildCreneauOfDay(\DateTime $day, $dateToday, array $entityArray, array $dayConf, $Unvalables) {
    $creneaux = [];
    $day_string = $day->format("Y-m-d H:i:s");
    $day_string_small = $day->format("Y-m-d");
    $UnvalablesCreneaux = [];
    if (!empty($Unvalables[$day_string_small])) {
      $UnvalablesCreneaux = $Unvalables[$day_string_small];
    }
    $d = new \DateTime($day_string);
    $f = new \DateTime($day_string);
    $d->setTime($dayConf['h_d'], $dayConf['m_d']);
    $f->setTime($dayConf['h_f'], $dayConf['m_f']);
    $interval = !empty($entityArray['interval']) ? $entityArray['interval'] : 30;

    if ($f > $d) {
      $i = 0;
      while ($f > $d && $i < $this->maxCreneau) {
        $i++;
        $cr = $d->format('H:i');
        $status = true;
        if (!empty($UnvalablesCreneaux[$cr])) {
          $status = false;
        }
        $creneaux[] = [
          'value' => $cr,
          'status' => ($dateToday < $d && $status) ? true : false,
          'Unvalable' => $UnvalablesCreneaux,
          'test-sta' => $status
        ];
        $d->modify("+ " . $interval . " minutes");
      }
    }
    return $creneaux;
  }

}