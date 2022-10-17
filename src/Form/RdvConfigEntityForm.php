<?php

namespace Drupal\prise_rendez_vous\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RdvConfigEntityForm.
 */
class RdvConfigEntityForm extends EntityForm {

  /**
   *
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $rdv_config_entity = $this->entity;
    // dump($this->entity->toArray());
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $rdv_config_entity->label(),
      '#description' => $this->t("Label for the Rdv config entity."),
      '#required' => TRUE
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $rdv_config_entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\prise_rendez_vous\Entity\RdvConfigEntity::load'
      ],
      '#disabled' => !$rdv_config_entity->isNew()
    ];

    /* You will need additional form elements for your custom properties. */
    $jours = \Drupal\prise_rendez_vous\PriseRendezVousInterface::jours;
    if (!empty($rdv_config_entity->get('jours'))) {
      $jours = $rdv_config_entity->get('jours');
    }
    $form['jours'] = [
      '#type' => 'fieldset',
      '#title' => 'Configuration des dates',
      '#tree' => TRUE
    ];
    foreach ($jours as $i => $val) {
      $form['jours'][$i] = [
        "#type" => 'details',
        '#title' => $val['label'],
        '#open' => false
      ];
      $form['jours'][$i]['label'] = [
        "#type" => 'textfield',
        '#title' => 'Label',
        '#default_value' => $val['label']
      ];
      $form['jours'][$i]['status'] = [
        "#type" => 'checkbox',
        '#title' => 'Status',
        '#default_value' => $val['status']
      ];
      $form['jours'][$i]['h_d__m_d'] = [
        "#type" => 'textfield',
        '#title' => 'Heure debut (00:00)',
        '#default_value' => $val['h_d'] . ':' . $val['m_d']
      ];
      $form['jours'][$i]['h_f__m_f'] = [
        "#type" => 'textfield',
        '#title' => 'Heure fin (00:00)',
        '#default_value' => $val['h_f'] . ':' . $val['m_f']
      ];
    }

    $form['interval'] = [
      '#type' => 'number',
      '#title' => "Durée d'un creneau en minutes",
      '#default_value' => $rdv_config_entity->get('interval')
    ];

    $form['decalage'] = [
      '#type' => 'number',
      '#title' => "Decallage entre deux creneau",
      '#default_value' => $rdv_config_entity->get('decalage')
    ];

    $form['limit_reservation'] = [
      '#type' => 'number',
      '#title' => "Nombre de reservation par creneaux ",
      '#default_value' => $rdv_config_entity->get('limit_reservation')
    ];
    return $form;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $rdv_config_entity = $this->entity;
    $status = $rdv_config_entity->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Rdv config entity.', [
          '%label' => $rdv_config_entity->label()
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Rdv config entity.', [
          '%label' => $rdv_config_entity->label()
        ]));
    }
    $form_state->setRedirectUrl($rdv_config_entity->toUrl('collection'));
  }

}
