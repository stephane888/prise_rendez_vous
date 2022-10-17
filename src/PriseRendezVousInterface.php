<?php

/**
 *
 * @file
 * Provides Drupal\icecream\FlavorInterface
 */
namespace Drupal\prise_rendez_vous;

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

}