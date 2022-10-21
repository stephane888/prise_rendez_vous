<?php

/**
 *
 * @file
 * Provides Drupal\icecream\FlavorInterface
 */
namespace Drupal\prise_rendez_vous;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for ice cream flavor plugins.
 */
interface PriseRendezVousInterface {
  const jours = [
    [
      'label' => 'Dimanche',
      'status' => false,
      'h_d' => 7,
      'm_d' => 0,
      'h_f' => 17,
      'm_f' => 0
    ],
    [
      'label' => 'Lundi',
      'status' => true,
      'h_d' => 7,
      'm_d' => 0,
      'h_f' => 17,
      'm_f' => 0
    ],
    [
      'label' => 'Mardi',
      'status' => true,
      'h_d' => 7,
      'm_d' => 0,
      'h_f' => 17,
      'm_f' => 0
    ],
    [
      'label' => 'Mercredi',
      'status' => true,
      'h_d' => 7,
      'm_d' => 0,
      'h_f' => 17,
      'm_f' => 0
    ],
    [
      'label' => 'Jeudi',
      'status' => true,
      'h_d' => 7,
      'm_d' => 0,
      'h_f' => 17,
      'm_f' => 0
    ],
    [
      'label' => 'Vendredi',
      'status' => true,
      'h_d' => 7,
      'm_d' => 0,
      'h_f' => 17,
      'm_f' => 0
    ],
    [
      'label' => 'Samedi',
      'status' => false,
      'h_d' => 7,
      'm_d' => 0,
      'h_f' => 17,
      'm_f' => 0
    ]
  ];

  /**
   *
   * @var array
   */
  const field_default_value = [
    'heure_debut' => '07:00',
    'heure_fin' => '13:30',
    'jours' => [
      0,
      6
    ],
    'dates' => [
      '2022-02-02',
      '2022-02-03'
    ],
    'dates_periodes' => [
      'debut' => '2022-03-10',
      'fin' => '2022-03-25'
    ]
  ];

}