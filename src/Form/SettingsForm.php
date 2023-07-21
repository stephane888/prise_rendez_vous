<?php

namespace Drupal\prise_rendez_vous\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure prise rendez vous settings for this site.
 */
class SettingsForm extends ConfigFormBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'prise_rendez_vous_settings';
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'prise_rendez_vous.settings'
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['send_mail'] = [
      '#type' => 'checkbox',
      '#title' => $this->t(" Envoit de mail apres la sauvegarde d'une reservation "),
      '#default_value' => $this->config('prise_rendez_vous.settings')->get('send_mail')
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
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('prise_rendez_vous.settings');
    $config->set('send_mail', $form_state->getValue('send_mail'));
    $config->save();
    parent::submitForm($form, $form_state);
  }
  
}
