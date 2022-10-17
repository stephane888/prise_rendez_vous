<?php

namespace Drupal\prise_rendez_vous\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

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
 *     "label"
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

}
