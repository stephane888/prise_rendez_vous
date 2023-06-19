<?php

namespace Drupal\prise_rendez_vous\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration par defaut.
 * Configure prise rendez vous settings for this site.
 */
class PriseRendezVousSettingsForm extends ConfigFormBase {
  protected $keySettings = 'prise_rendez_vous.default_configs';
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'prise_rendez_vous_default_configs';
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      $this->keySettings
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attributes']['class'][] = 'container';
    $configs = $this->config($this->keySettings)->getRawData();
    // from prise_rendez_vous/src/Form/RdvConfigEntityForm.php
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => isset($configs['label']) ? $configs['label'] : 'Prise de rendez',
      '#description' => $this->t("Label for the Rdv config entity."),
      '#required' => TRUE
    ];
    
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => isset($configs['id']) ? $configs['id'] : 'default_configs',
      '#machine_name' => [
        'exists' => '\Drupal\prise_rendez_vous\Entity\RdvConfigEntity::load'
      ]
    ];
    
    /* You will need additional form elements for your custom properties. */
    $jours = \Drupal\prise_rendez_vous\PriseRendezVousInterface::jours;
    if (!empty($configs['jours'])) {
      $jours = $configs['jours'];
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
    //
    $form['interval'] = [
      '#type' => 'number',
      '#title' => "Durée d'un creneau en minutes",
      '#default_value' => isset($configs['interval']) ? $configs['interval'] : 60
    ];
    //
    $form['decalage'] = [
      '#type' => 'number',
      '#title' => "Decallage entre deux creneau",
      '#default_value' => isset($configs['decalage']) ? $configs['decalage'] : 0
    ];
    //
    $form['limit_reservation'] = [
      '#type' => 'number',
      '#title' => "Nombre de reservation par creneaux ",
      '#default_value' => isset($configs['limit_reservation']) ? $configs['limit_reservation'] : 1
    ];
    //
    $form['number_week'] = [
      '#type' => 'number',
      '#title' => "Nombre de semaine ",
      '#default_value' => isset($configs['number_week']) ? $configs['number_week'] : 3
    ];
    return parent::buildForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // if ($form_state->getValue('example') != 'example') {
    // $form_state->setErrorByName('example', $this->t('The value is not
    // correct.'));
    // }
    parent::validateForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Config\Entity\ConfigEntityBase::preSave()
   */
  public function formatJours($jours) {
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
    return $jours;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $confs = $this->config($this->keySettings);
    $confs->set('label', $form_state->getValue('label'));
    $confs->set('id', $form_state->getValue('id'));
    $confs->set('jours', $this->formatJours($form_state->getValue('jours')));
    $confs->set('interval', $form_state->getValue('interval'));
    $confs->set('decalage', $form_state->getValue('decalage'));
    $confs->set('limit_reservation', $form_state->getValue('limit_reservation'));
    $confs->set('number_week', $form_state->getValue('number_week'));
    $confs->save();
    parent::submitForm($form, $form_state);
  }
  
}
