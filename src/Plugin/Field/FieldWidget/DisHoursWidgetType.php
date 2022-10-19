<?php

namespace Drupal\prise_rendez_vous\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Annotation\Translation;
use Drupal\Component\Serialization\Json;

/**
 * Plugin implementation of the 'dis_hours_widget_type' widget.
 *
 * @FieldWidget(
 *   id = "dis_hours_widget_type",
 *   module = "prise_rendez_vous",
 *   label = @Translation("Disabled hours widget type"),
 *   field_types = {
 *     "dis_hours_field_type"
 *   }
 * )
 */
class DisHoursWidgetType extends WidgetBase {

  /**
   *
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'placeholder' => ''
    ] + parent::defaultSettings();
  }

  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    return $elements;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    return $summary;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->value) ? Json::decode($items[$delta]->value) : [];
    $element['value'] = [
      '#type' => 'fieldset',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#tree' => TRUE
    ] + $element;

    $increment = 0;
    $date_type = 'none';
    $time_type = 'time';
    $element['value']['heure_debut'] = [
      "#type" => 'datetime',
      '#title' => 'Heure debut (00:00)',
      '#default_value' => isset($value['heure_debut']) ? $value['heure_debut'] : NULL,
      '#date_date_element' => $date_type,
      '#date_time_element' => $time_type,
      '#date_increment' => $increment
    ];
    $element['value']['heure_fin'] = [
      "#type" => 'datetime',
      '#title' => 'Heure fin (00:00)',
      '#default_value' => isset($value['heure_fin']) ? $value['heure_fin'] : NULL,
      '#date_date_element' => $date_type,
      '#date_time_element' => $time_type,
      '#date_increment' => $increment
    ];
    $jours = \Drupal\prise_rendez_vous\PriseRendezVousInterface::jours;
    // Format jour to list.
    $options = [];
    foreach ($jours as $key => $value) {
      $options[$key] = $value['label'];
    }
    $element['value']['jours'] = [
      "#type" => 'checkboxes',
      '#title' => 'Jours',
      '#options' => $options
      // '#default_value' => isset($value['jours']) ? $value['jours'] : NULL
    ];
    $increment = 0; // set 0 to remove seconde.
    $date_part_order = [
      'day',
      'month',
      'year'
    ];
    // $date_format = '';
    $date_type = 'date';
    $time_type = 'none'; // 'time';
    $element['value']['dates'] = [
      "#type" => 'datetime',
      '#title' => 'Dates',
      '#date_increment' => $increment,
      '#date_part_order' => $date_part_order,
      '#date_timezone' => date_default_timezone_get(),
      // '#date_date_format' => $date_format,
      '#date_date_element' => $date_type,
      '#date_time_element' => $time_type,
      '#date_time_callbacks' => []
    ];

    return $element;
  }

  public function massageFormValues($values, $form, $form_state) {
    $value = parent::massageFormValues($values, $form, $form_state);
    return $value;
  }

}
