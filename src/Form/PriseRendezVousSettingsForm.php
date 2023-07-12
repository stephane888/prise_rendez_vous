<?php

namespace Drupal\prise_rendez_vous\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Stephane888\Debug\Repositories\ConfigDrupal;

/**
 * Configuration par defaut.
 * Configure prise rendez vous settings for this site.
 */
class PriseRendezVousSettingsForm extends ConfigFormBase {
  protected $keySettings = 'prise_rendez_vous.default_configs';
  
  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *        The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RequestStack $RequestStack) {
    parent::__construct($config_factory);
    $this->request = $RequestStack->getCurrentRequest();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'), $container->get('request_stack'));
  }
  
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
    $configs = ConfigDrupal::config($this->keySettings);
    $id = 'default_configs';
    $label = 'default configs';
    
    if (\Drupal::moduleHandler()->moduleExists('domain')) {
      /**
       *
       * @var \Drupal\domain_source\HttpKernel\DomainSourcePathProcessor $domain_source
       */
      $domain_source = \Drupal::service('domain_source.path_processor');
      $domain = $domain_source->getActiveDomain();
      $query = $this->request->query->get('domain_config_ui_domain');
      if (empty($query)) {
        if ($domain) {
          $url = Url::fromRoute("prise_rendez_vous.default_settings_form", [], [
            'query' => [
              'domain_config_ui_domain' => $domain->id(),
              'domain_config_ui_language' => ''
            ],
            'absolute' => TRUE
          ]);
          return new RedirectResponse($url->toString());
        }
      }
      // Pour cette environnement on force le domaine à etre identique.
      if ($domain) {
        // ( si c'est encore la valeur par defaut, on definie le domaine. )
        // Le veritable test doit consister à verifier si l'utilisateur à
        // activer la surcharger de la configuration.
        // if (!empty($configs['id'] !== $domain->id())) {
        $id = $domain->id();
        $label = $domain->getHostname();
        $domain_ovh_entity = \Drupal::entityTypeManager()->getStorage('domain_ovh_entity')->loadByProperties([
          'domain_id_drupal' => $domain->id()
        ]);
        if ($domain_ovh_entity) {
          $domain_ovh_entity = reset($domain_ovh_entity);
          $id = str_replace("-", "_", $domain_ovh_entity->get('sub_domain')->value);
          $label = $domain_ovh_entity->get('sub_domain')->value;
        }
        $configs['id'] = $id;
        $configs['label'] = $label;
        // }
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
  /**
   * L'entite cree à ce niveau n'est pas editable, mais cela smble etre du a la
   * redirection.
   */
    // $confEntity = \Drupal\prise_rendez_vous\Entity\RdvConfigEntity::load($id
    // . 'ee');
    // if (!$confEntity) {
    // // $confEntity =
    // // \Drupal\prise_rendez_vous\Entity\RdvConfigEntity::create();
    // // $confEntity->set('id', $id);
    // // $confEntity->set('label', $label);
    // // $confEntity->set('jours',
    // // \Drupal\prise_rendez_vous\PriseRendezVousInterface::jours);
    // // $confEntity->save();
    // /**
    // *
    // * @var \Drupal\Core\Config\Entity\ConfigEntityStorage $configStorage
    // */
    
    // //
    // // $form_state->setRedirect('entity.rdv_config_entity.edit_form', [
    // // 'rdv_config_entity' => $enti2->id()
    // // ]);
    // }
  }
  
}
