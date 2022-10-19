<?php

namespace Drupal\prise_rendez_vous\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'dis_hours_field_type' field type.
 *
 * @FieldType(
 *   id = "dis_hours_field_type",
 *   label = @Translation("Disable hours field type"),
 *   description = @Translation("Permet de sauvegarder les données sur la desactivation d'un periode en function des heures"),
 *   default_widget = "dis_hours_widget_type",
 *   default_formatter = "dis_hours_formatter_type"
 * )
 */
class DisHoursFieldType extends FieldItemBase {

  /**
   *
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'max_length' => 255,
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE
    ] + parent::defaultStorageSettings();
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['value'] = DataDefinition::create('string')->setLabel(new TranslatableMarkup(' Disable hours JSON data '));

    return $properties;
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'value' => [
          'type' => 'text',
          'size' => 'big'
        ]
      ]
    ];

    return $schema;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    // if ($max_length = $this->getSetting('max_length')) {
    // $constraint_manager =
    // \Drupal::typedDataManager()->getValidationConstraintManager();
    // $constraints[] = $constraint_manager->create('ComplexData', [
    // 'value' => [
    // 'Length' => [
    // 'max' => $max_length,
    // 'maxMessage' => t('%name: may not be longer than @max characters.', [
    // '%name' => $this->getFieldDefinition()->getLabel(),
    // '@max' => $max_length
    // ])
    // ]
    // ]
    // ]);
    // }
    return $constraints;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $elements = [];

    // $elements['max_length'] = [
    // '#type' => 'number',
    // '#title' => t('Maximum length'),
    // '#default_value' => $this->getSetting('max_length'),
    // '#required' => TRUE,
    // '#description' => t('The maximum length of the field in characters.'),
    // '#min' => 1,
    // '#disabled' => $has_data
    // ];

    return $elements;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === [];
  }

}
