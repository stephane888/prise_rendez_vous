<?php

namespace Drupal\prise_rendez_vous\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Annotation\Translation;
use Drupal\Component\Serialization\Json;
use Drupal\datetime_range\Plugin\Field\FieldWidget\DateRangeDefaultWidget;

/**
 * Plugin implementation of the 'dis_hours_widget_type' widget.
 *
 * @FieldWidget(
 *   id = "dis_hours_date_time_widget",
 *   module = "prise_rendez_vous",
 *   label = @Translation("Disabled hours date time widget"),
 *   field_types = {
 *     "daterange"
 *   }
 * )
 */
class DisHoursDateTimeWidget extends DateRangeDefaultWidget {

  /**
   *
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'date_type' => 'date',
      'time_type' => 'none',
      'date_increment' => 0
    ] + parent::defaultSettings();
  }

  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];
    $elements['date_type'] = [
      '#type' => 'select',
      '#title' => $this->t('date_type'),
      '#default_value' => $this->getSetting('date_type'),
      '#options' => [
        'none' => $this->t('None'),
        'date' => $this->t('date')
      ]
    ];
    //
    $elements['time_type'] = [
      '#type' => 'select',
      '#title' => $this->t('time_type'),
      '#default_value' => $this->getSetting('time_type'),
      '#options' => [
        'none' => $this->t('None'),
        'time' => $this->t('time')
      ]
    ];
    //
    $elements['date_increment'] = [
      '#type' => 'select',
      '#title' => $this->t('time_type'),
      '#default_value' => $this->getSetting('time_type'),
      '#options' => [
        'none' => $this->t('None'),
        'time' => $this->t('time')
      ]
    ];
    //
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
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    //
    if ($element['value']['#date_increment'])
      $element['value']['#date_increment'] = $this->getSetting('date_increment');
    //
    if ($element['value']['#date_date_element'])
      $element['value']['#date_date_element'] = $this->getSetting('date_type');
    //
    if ($element['value']['#date_time_element'])
      $element['value']['#date_time_element'] = $this->getSetting('time_type');
    //
    if ($element['end_value']['#date_time_element'])
      $element['end_value']['#date_time_element'] = $this->getSetting('time_type');
    //
    if ($element['end_value']['#date_date_element'])
      $element['end_value']['#date_date_element'] = $this->getSetting('date_type');
    //
    return $element;
  }

}
