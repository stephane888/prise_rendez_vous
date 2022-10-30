<?php

namespace Drupal\prise_rendez_vous\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the Equipes entity entity.
 *
 * @ingroup prise_rendez_vous
 *
 * @ContentEntityType(
 *   id = "equipes_entity",
 *   label = @Translation("Equipes entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\prise_rendez_vous\EquipesEntityListBuilder",
 *     "views_data" = "Drupal\prise_rendez_vous\Entity\EquipesEntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\prise_rendez_vous\Form\EquipesEntityForm",
 *       "add" = "Drupal\prise_rendez_vous\Form\EquipesEntityForm",
 *       "edit" = "Drupal\prise_rendez_vous\Form\EquipesEntityForm",
 *       "delete" = "Drupal\prise_rendez_vous\Form\EquipesEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\prise_rendez_vous\EquipesEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\prise_rendez_vous\EquipesEntityAccessControlHandler",
 *   },
 *   base_table = "equipes_entity",
 *   translatable = FALSE,
 *   admin_permission = "administer equipes entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/equipes_entity/{equipes_entity}",
 *     "add-form" = "/admin/structure/equipes_entity/add",
 *     "edit-form" = "/admin/structure/equipes_entity/{equipes_entity}/edit",
 *     "delete-form" = "/admin/structure/equipes_entity/{equipes_entity}/delete",
 *     "collection" = "/admin/structure/equipes_entity",
 *   },
 *   field_ui_base_route = "equipes_entity.settings"
 * )
 */
class EquipesEntity extends ContentEntityBase implements EquipesEntityInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   *
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $querys = \Drupal::request()->query->all();
    if (!empty($querys)) {
      if ($querys['rdv_config_entity'])
        $values['rdv_config_entity'] = $querys['rdv_config_entity'];
    }
    $values += [
      'user_id' => \Drupal::currentUser()->id()
    ];
  }

  /**
   *
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')->setLabel(t('Authored by'))->setDescription(t('The user ID of author of the Equipes entity entity.'))->setRevisionable(TRUE)->setSetting('target_type', 'user')->setSetting('handler', 'default')->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'author',
      'weight' => 0
    ])->setDisplayOptions('form', [
      'type' => 'entity_reference_autocomplete',
      'weight' => 5,
      'settings' => [
        'match_operator' => 'CONTAINS',
        'size' => '60',
        'autocomplete_type' => 'tags',
        'placeholder' => ''
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')->setLabel(t("Nom de l'equipe / personel"))->setDescription(t('The name of the Equipes entity entity.'))->setSettings([
      'max_length' => 50,
      'text_processing' => 0
    ])->setDefaultValue('')->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4
    ])->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);

    /**
     * --
     */
    $fields['users'] = BaseFieldDefinition::create('entity_reference')->setLabel(t(" Membre de l'equipe "))->setDescription(t(' The users ID for equipes '))->setSetting('target_type', 'user')->setSetting('handler', 'default')->setDisplayOptions('form', [
      'type' => 'select2_entity_reference',
      'weight' => 40,
      'settings' => [
        'autocomplete' => true
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    /**
     * --
     */
    $fields['rdv_config_entity'] = BaseFieldDefinition::create('entity_reference')->setLabel(t(" Type de rdv config "))->setSetting('target_type', 'rdv_config_entity')->setSetting('handler', 'default')->setDisplayOptions('form', [
      'type' => 'select2_entity_reference',
      'weight' => 40,
      'settings' => [
        'autocomplete' => true
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Equipes entity is published.'))->setDisplayOptions('form', [
      'type' => 'boolean_checkbox',
      'weight' => -3
    ]);

    $fields['created'] = BaseFieldDefinition::create('created')->setLabel(t('Created'))->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')->setLabel(t('Changed'))->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
