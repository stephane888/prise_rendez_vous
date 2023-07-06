<?php

namespace Drupal\prise_rendez_vous\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Submit rdv entity entity.
 *
 * @ingroup prise_rendez_vous
 *
 * @ContentEntityType(
 *   id = "submit_rdv_entity",
 *   label = @Translation("Submit rdv entity"),
 *   handlers = {
 *     "storage" = "Drupal\prise_rendez_vous\SubmitRdvEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\prise_rendez_vous\SubmitRdvEntityListBuilder",
 *     "views_data" = "Drupal\prise_rendez_vous\Entity\SubmitRdvEntityViewsData",
 *     "translation" = "Drupal\prise_rendez_vous\SubmitRdvEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\prise_rendez_vous\Form\SubmitRdvEntityForm",
 *       "add" = "Drupal\prise_rendez_vous\Form\SubmitRdvEntityForm",
 *       "edit" = "Drupal\prise_rendez_vous\Form\SubmitRdvEntityForm",
 *       "delete" = "Drupal\prise_rendez_vous\Form\SubmitRdvEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\prise_rendez_vous\SubmitRdvEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\prise_rendez_vous\SubmitRdvEntityAccessControlHandler",
 *   },
 *   base_table = "submit_rdv_entity",
 *   data_table = "submit_rdv_entity_field_data",
 *   revision_table = "submit_rdv_entity_revision",
 *   revision_data_table = "submit_rdv_entity_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer submit rdv entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/submit_rdv_entity/{submit_rdv_entity}",
 *     "add-form" = "/admin/structure/submit_rdv_entity/add",
 *     "edit-form" = "/admin/structure/submit_rdv_entity/{submit_rdv_entity}/edit",
 *     "delete-form" = "/admin/structure/submit_rdv_entity/{submit_rdv_entity}/delete",
 *     "version-history" = "/admin/structure/submit_rdv_entity/{submit_rdv_entity}/revisions",
 *     "revision" = "/admin/structure/submit_rdv_entity/{submit_rdv_entity}/revisions/{submit_rdv_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/submit_rdv_entity/{submit_rdv_entity}/revisions/{submit_rdv_entity_revision}/revert",
 *     "revision_delete" = "/admin/structure/submit_rdv_entity/{submit_rdv_entity}/revisions/{submit_rdv_entity_revision}/delete",
 *     "translation_revert" = "/admin/structure/submit_rdv_entity/{submit_rdv_entity}/revisions/{submit_rdv_entity_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/submit_rdv_entity",
 *   },
 *   field_ui_base_route = "submit_rdv_entity.settings"
 * )
 */
class SubmitRdvEntity extends EditorialContentEntityBase implements SubmitRdvEntityInterface {
  
  use EntityChangedTrait;
  use EntityPublishedTrait;
  
  /**
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $enityTypeManagerCustom;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id()
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    
    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    
    return $uri_route_parameters;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    
    // On doit verifier que les champs de reference sont bien definit
    if (!$this->checkIfEntityReferenceIsvalid())
      throw new \Exception("L'entite de reference n'est pas definie");
    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);
      
      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }
    
    // If no revision author has been set explicitly,
    // make the submit_rdv_entity owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }
  
  public function checkIfEntityReferenceIsvalid() {
    $entity_type = $this->getEntityTypeManager()->getStorage($this->get('entity_type')->value);
    if ($entity_type) {
      $entity = $entity_type->load($this->get('entity_id')->value);
      if ($entity)
        return true;
    }
    return false;
  }
  
  /**
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected function getEntityTypeManager() {
    if (!$this->enityTypeManagerCustom) {
      $this->enityTypeManagerCustom = \Drupal::entityTypeManager();
    }
    return $this->enityTypeManagerCustom;
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
    
    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')->setLabel(t('Authored by'))->setDescription(t('The user ID of author of the Submit rdv entity entity.'))->setRevisionable(TRUE)->setSetting('target_type', 'user')->setSetting('handler', 'default')->setTranslatable(TRUE)->setDisplayOptions('view', [
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
    
    $fields['name'] = BaseFieldDefinition::create('string')->setLabel(t('Name'))->setDescription(t('The name of the Submit rdv entity entity.'))->setRevisionable(TRUE)->setSettings([
      'max_length' => 250,
      'text_processing' => 0
    ])->setDefaultValue('')->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4
    ])->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    
    $fields['status']->setDescription(t('A boolean indicating whether the Submit rdv entity is published.'))->setDisplayOptions('form', [
      'type' => 'boolean_checkbox',
      'weight' => -3
    ]);
    
    $fields['creneau'] = BaseFieldDefinition::create('daterange')->setLabel(t('Creneau'))->setRevisionable(TRUE)->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    $fields['creneau_string'] = BaseFieldDefinition::create('string')->setLabel(t('Creneau ( brute )'))->setDescription(t(' Creneaux en affichage brute. '));
    $fields['created'] = BaseFieldDefinition::create('created')->setLabel(t('Created'))->setDescription(t('The time that the entity was created.'));
    
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
    
    /**
     * --
     */
    $fields['equipes_entity'] = BaseFieldDefinition::create('entity_reference')->setLabel(t(" Equipe selectionée "))->setSetting('target_type', 'equipes_entity')->setSetting('handler', 'default')->setDisplayOptions('form', [
      'type' => 'select2_entity_reference',
      'weight' => 40,
      'settings' => [
        'autocomplete' => true
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);
    
    /**
     * L'id parent.
     */
    $fields['entity_id'] = BaseFieldDefinition::create('integer')->setLabel(t('Entity id'))->setRevisionable(TRUE)->setSettings([
      'max_length' => 50
    ])->setDefaultValue('')->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4
    ])->setDisplayOptions('form', [
      'type' => 'number_integer',
      'weight' => -4
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    
    /**
     * Reprensente bundle qui peut etre vide.
     */
    $fields['entity_type_id'] = BaseFieldDefinition::create('string')->setLabel(t('Entity type id'))->setRevisionable(TRUE)->setSettings([
      'max_length' => 100,
      'text_processing' => 0
    ])->setDefaultValue('')->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4
    ])->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);
    
    /**
     * Reprensente l'entite ou la reservation s'accroche.
     */
    $fields['entity_type'] = BaseFieldDefinition::create('string')->setLabel(t('entity_type'))->setRevisionable(TRUE)->setSettings([
      'max_length' => 100,
      'text_processing' => 0
    ])->setDefaultValue('')->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4
    ])->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    
    $fields['changed'] = BaseFieldDefinition::create('changed')->setLabel(t('Changed'))->setDescription(t('The time that the entity was last edited.'));
    
    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')->setLabel(t('Revision translation affected'))->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))->setReadOnly(TRUE)->setRevisionable(TRUE)->setTranslatable(TRUE);
    
    return $fields;
  }
  
}
