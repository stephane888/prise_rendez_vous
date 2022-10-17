<?php

namespace Drupal\prise_rendez_vous\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the Rdv config entity entity.
 *
 * @ConfigEntityType(
 *   id = "rdv_config_entity",
 *   label = @Translation("Rdv config entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\prise_rendez_vous\RdvConfigEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\prise_rendez_vous\Form\RdvConfigEntityForm",
 *       "edit" = "Drupal\prise_rendez_vous\Form\RdvConfigEntityForm",
 *       "delete" = "Drupal\prise_rendez_vous\Form\RdvConfigEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\prise_rendez_vous\RdvConfigEntityHtmlRouteProvider",
 *     },
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "format_time",
 *     "jours",
 *     "interval",
 *     "decalage",
 *     "number_week",
 *     "limit_reservation"
 *   },
 *   config_prefix = "rdv_config_entity",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/rdv_config_entity/{rdv_config_entity}",
 *     "add-form" = "/admin/structure/rdv_config_entity/add",
 *     "edit-form" = "/admin/structure/rdv_config_entity/{rdv_config_entity}/edit",
 *     "delete-form" = "/admin/structure/rdv_config_entity/{rdv_config_entity}/delete",
 *     "collection" = "/admin/structure/rdv_config_entity"
 *   }
 * )
 */
class RdvConfigEntity extends ConfigEntityBase implements RdvConfigEntityInterface {

  /**
   * The Rdv config entity ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Rdv config entity label.
   *
   * @var string
   */
  protected $label;

  /**
   * Le format de durée ( minutes, heures, jours ).
   *
   * @var string
   */
  protected $format_time;

  /**
   * Jours activé par defaut.
   *
   * @var array
   */
  protected $jours = [];

  /**
   * Durée d'un creneau
   *
   * @var integer
   */
  protected $interval = 60;

  /**
   * Decalage entre deux creneaux.
   *
   * @var integer
   */
  protected $decalage = 0;

  /**
   * Nombre de semaine à afficher
   *
   * @var integer
   */
  protected $number_week = 6;

  /**
   * Limitation du nombre de reservation par equipe ou par personne.
   *
   * @var integer
   */
  protected $limit_reservation = 1;

  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    $jours = $this->get('jours');
    if (!empty($jours))
      foreach ($jours as $k => $val) {
        if (!empty($val['h_d__m_d'])) {
          $d = explode(":", $val['h_d__m_d']);
          $jours[$k]['h_d'] = $d[0];
          $jours[$k]['m_d'] = isset($d[1]) ? $d[1] : 0;
          unset($jours[$k]['h_d__m_d']);
        }
        if (!empty($val['h_f__m_f'])) {
          $f = explode(":", $val['h_f__m_f']);
          $jours[$k]['h_f'] = $f[0];
          $jours[$k]['m_f'] = isset($f[1]) ? $f[1] : 0;
          unset($jours[$k]['h_f__m_f']);
        }
      }
    $this->set('jours', $jours);
  }

}
