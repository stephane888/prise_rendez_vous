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
    $id = 'default_configs';
    $label = 'default configs';
    if (\Drupal::moduleHandler()->moduleExists('domain')) {
      /**
       *
       * @var \Drupal\domain_source\HttpKernel\DomainSourcePathProcessor $domain_source
       */
      $domain_source = \Drupal::service('domain_source.path_processor');
      $domain = $domain_source->getActiveDomain();
      // Pour cette environnement on force l'u
      if ($domain) {
        // ( si c'est encore la valeur par defaut, on definie le domaine. )
        // Le veritable test doit consister à verifier si l'utilisateur à
        // activer la surcharger de la configuration.
        if (!empty($configs['id']))
          if ($configs['id'] == $id) {
            $configs['id'] = $id = $domain->id();
            $configs['label'] = $label = $domain->getHostname();
          }
          else {
            $configs['id'] = $id = $domain->id();
            $configs['label'] = $label = $domain->getHostname();
          }
      }
    }
    // from prise_rendez_vous/src/Form/RdvConfigEntityForm.php
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => isset($configs['label']) ? $configs['label'] : $label,
      '#description' => $this->t("Label for the Rdv config entity."),
      '#required' => TRUE
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => isset($configs['id']) ? $configs['id'] : $id,
      '#machine_name' => [
        'exists' => '\Drupal\prise_rendez_vous\Entity\RdvConfigEntity::load'
      ]
    ];
    $form['url_redirect'] = [
      '#type' => 'select',
      '#title' => $this->t('url redirect'),
      '#maxlength' => 255,
      '#default_value' => isset($configs['url_redirect']) ? $configs['url_redirect'] : '',
      '#options' => [
        '' => 'Aucun action',
        '/' => 'Home',
        'current' => 'Reactualise la page encours'
      ]
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
    $confs = $this->config($this->keySettings);
    $id = $form_state->getValue('id');
    $label = $form_state->getValue('label');
    $confs->set('label', $label);
    $confs->set('id', $id);
    $confs->set('url_redirect', $form_state->getValue('url_redirect'));
    $confs->save();
    parent::submitForm($form, $form_state);
    /**
     * On verifie si l'entité RdvConfigEntity existe deja, non on le cre et on
     * redire l'utilisateur pour affiner la configuration
     */
    $confEntity = \Drupal\prise_rendez_vous\Entity\RdvConfigEntity::load($id);
    if (!$confEntity) {
      $confEntity = \Drupal\prise_rendez_vous\Entity\RdvConfigEntity::create([
        'id' => $id,
        'label' => $label,
        'jours' => \Drupal\prise_rendez_vous\PriseRendezVousInterface::jours
      ]);
      $confEntity->save();
    }
    $form_state->setRedirect('entity.rdv_config_entity.edit_form', [
      'rdv_config_entity' => $id
    ]);
  }
  
}
