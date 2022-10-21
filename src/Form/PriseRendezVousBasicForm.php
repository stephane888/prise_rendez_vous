<?php

namespace Drupal\prise_rendez_vous\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\prise_rendez_vous\Services\PriseRendezVousSimple;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\NestedArray;
use Stephane888\Debug\Repositories\FormUtilityDrupal;
use Drupal\prise_rendez_vous\Entity\EquipesEntity;

// use Drupal\Core\Entity\EntityForm;

/**
 * Cette fonction doit pouvoir utiliser le systeme propre de drupal enfin de
 * fonctionner.
 *
 * @author stephane
 *
 */
class PriseRendezVousBasicForm extends FormBase {
  /**
   *
   * @var integer
   */
  protected $maxStep = 4;

  /**
   *
   * @var PriseRendezVousSimple
   */
  protected $PriseRendezVousSimple;

  function __construct(PriseRendezVousSimple $PriseRendezVousSimple) {
    $this->PriseRendezVousSimple = $PriseRendezVousSimple;
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('prise_rendez_vous.manage.basic'));
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Form\FormInterface::getFormId()
   */
  public function getFormId() {
    return 'prise_rendez_vous_basic_form';
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Form\FormInterface::buildForm()
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = null, $id = null) {
    $this->messenger()->addStatus('Step : ' . $form_state->get('step'), true);
    if ($form_state->has('step')) {
      $step = $form_state->get('step');
      switch ($step) {
        case 1:
          $this->FormStep1($form, $entity_type_id, $id, $form_state);
          break;

        case 2:
          $this->FormStep2($form, $entity_type_id, $id, $form_state);
          break;

        case 3:
          $this->FormStep3($form, $entity_type_id, $id, $form_state);
          break;
        case 4:
          $this->FormStep4($form, $entity_type_id, $id, $form_state);
          break;
      }
    }
    else {
      $form_state->set('step', 1);
      $this->FormStep1($form, $entity_type_id, $id, $form_state);
    }
    // dump($form);
    return $form;
  }

  /**
   * Configuration des RDV.
   */
  protected function FormStep1(&$form, $entity_type_id, $id, FormStateInterface $form_state) {
    $entity = $this->getEntity($entity_type_id, $id);
    if ($entity) {
      $form['config_rdv'] = [
        '#type' => 'fieldset',
        '#title' => 'Configuration de base',
        '#tree' => TRUE
      ];
      $formParents = $this->PriseRendezVousSimple->getConfigForm($entity);
      $a1 = [
        'config_rdv'
      ];
      FormUtilityDrupal::ConvertEntityFormToSimpleForm($formParents, $form['config_rdv'], $a1);
      // dd($form['config_rdv']);
      $this->actionButtons($form, $form_state, 'Suivant', "SaveConfigNextSubmit");
    }
  }

  /**
   * On selectionne les participants.
   */
  protected function FormStep2(&$form, $entity_type_id, $id, FormStateInterface $form_state) {
    $form['equipes_entity'] = [
      '#type' => 'fieldset',
      '#title' => 'Configuration des equipes/personel',
      '#tree' => TRUE
    ];
    $entity = $form_state->get('config_rdv');
    $EntityEquipe = $this->PriseRendezVousSimple->getEntityEquipes($entity);
    // dd($EntityEquipe->get('rdv_config_entity'));
    $form_display = EntityFormDisplay::collectRenderDisplay($EntityEquipe, 'default');
    $form_state->set('equipes_entity', $EntityEquipe);
    $form_state->set('equipes_entity__form_display', $form_display);
    $form_display->buildForm($EntityEquipe, $form['equipes_entity'], $form_state);
    //
    $this->actionButtons($form, $form_state, 'Suivant', "SaveEquipesSubmit");
  }

  /**
   * On configure les dates et creneaux desactivées.
   */
  protected function FormStep3(&$form, $entity_type_id, $id, FormStateInterface $form_state) {
    $form['equipes_entity'] = [
      '#type' => 'fieldset',
      '#title' => 'Configuration des dates et creneaux desactivées',
      '#tree' => TRUE
    ];
    $entity = $form_state->get('config_rdv');
    $EntityDisPeriod = $this->PriseRendezVousSimple->getEntityDisPeriod($entity);
    //
    $form_display = EntityFormDisplay::collectRenderDisplay($EntityDisPeriod, 'default');
    $form_state->set('disperiod_entity', $EntityDisPeriod);
    $form_state->set('disperiod_entity__form_display', $form_display);
    $form_display->extractFormValues($EntityDisPeriod, $form, $form_state);
    // dump($EntityDisPeriod->toArray());
    $form_display->buildForm($EntityDisPeriod, $form['equipes_entity'], $form_state);

    //
    $this->actionButtons($form, $form_state, 'Suivant', "SaveDisPeriodSubmit");
  }

  /**
   * On configure les dates et creneaux desactivées.
   */
  protected function FormStep4(&$form, $entity_type_id, $id, FormStateInterface $form_state) {
    $form['equipes_entity'] = [
      '#type' => 'fieldset',
      '#title' => 'Configuration termineés',
      '#tree' => TRUE
    ];
  }

  /**
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  protected function actionButtons(array &$form, FormStateInterface $form_state, $title_next = "Suivant", $submit_next = 'NextSubmit', $title_preview = "Precedent") {
    $Step = $form_state->has('step') ? $form_state->get('step') : 1;
    $form['container_buttons'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [
          'd-flex',
          'justify-content-around',
          'align-items-center',
          'step-donneesite--submit'
        ]
      ],
      '#weight' => 45
    ];
    if ($Step > 1)
      $form['container_buttons']['preview'] = [
        '#type' => 'submit',
        '#value' => $title_preview,
        '#button_type' => 'secondary',
        '#submit' => [
          [
            $this,
            'PreviewsSubmit'
          ]
        ]
      ];
    if ($Step < $this->maxStep)
      $form['container_buttons']['next'] = [
        '#type' => 'submit',
        '#value' => $title_next,
        '#button_type' => 'secondary',
        '#submit' => [
          [
            $this,
            $submit_next
          ]
        ]
      ];
    if ($Step >= $this->maxStep) {
      // $form = parent::buildForm($form, $form_state);
      if (!empty($form['actions']['submit'])) {
        $form['actions']['submit']['#value'] = ' Terminer le processus ';
      }
    }
  }

  /**
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function SaveConfigNextSubmit(array &$form, FormStateInterface $form_state) {
    // Save datas.
    $values = $form_state->getValues();
    /**
     * --
     */
    if (!empty($values['config_rdv'])) {
      // Create or udate rdv config, and Save rdv config in tempon
      $form_state->set('config_rdv', $this->PriseRendezVousSimple->saveConfigForm($values['config_rdv']));
    }
    $this->NextSubmit($form, $form_state);
  }

  /**
   * --
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function SaveEquipesSubmit(array &$form, FormStateInterface $form_state) {
    /**
     * Recupere l'entite
     *
     * @var EquipesEntity $entity
     */
    $entity = $form_state->get('equipes_entity');
    /**
     *
     * @var EntityFormDisplay $form_display
     */
    $form_display = $form_state->get('equipes_entity__form_display');
    $form_display->extractFormValues($entity, $form, $form_state);
    // dd($entity->toArray());
    $entity->save();
    $this->NextSubmit($form, $form_state);
  }

  /**
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function SaveDisPeriodSubmit(array &$form, FormStateInterface $form_state) {
    /**
     * Recupere l'entite
     *
     * @var EquipesEntity $entity
     */
    $entity = $form_state->get('disperiod_entity');
    /**
     *
     * @var EntityFormDisplay $form_display
     */
    // $form_display = $form_state->get('disperiod_entity__form_display');
    // $form_display->extractFormValues($entity, $form, $form_state);
    // $entity->save();

    $this->NextSubmit($form, $form_state);
  }

  /**
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function NextSubmit(array &$form, FormStateInterface $form_state) {
    if ($form_state->has('step')) {
      $nextStep = $form_state->get('step') + 1;
    }
    else
      $nextStep = 2;
    //
    $this->messenger()->addWarning('step submit : ' . $nextStep, true);
    if ($nextStep > $this->maxStep)
      $nextStep = $this->maxStep;
    $form_state->set('step', $nextStep);
    $form_state->setRebuild(TRUE);
  }

  /**
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function PreviewsSubmit(array &$form, FormStateInterface $form_state) {
    $pvStep = $form_state->get('step') - 1;
    if ($pvStep <= 0)
      $pvStep = 1;
    $form_state->set('step', $pvStep);
    $form_state->setRebuild();
  }

  /**
   *
   * {@inheritdoc}
   * @see \Drupal\Core\Form\FormInterface::submitForm()
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // parent::submitForm($form, $form_state);
  }

  /**
   *
   * @param
   *        $entity_type_id
   * @param
   *        $id
   */
  protected function getEntity($entity_type_id, $id) {
    return \Drupal::entityTypeManager()->getStorage($entity_type_id)->load($id);
  }

  /**
   * Process callback: assigns weights and hides extra fields.
   *
   * @see \Drupal\Core\Entity\EntityForm::form()
   */
  public function processForm($element, FormStateInterface $form_state, $form) {
    // If the form is cached, process callbacks may not have a valid reference
    // to the entity object, hence we must restore it.
    // $this->entity = $form_state->getFormObject()->getEntity();
    return $element;
  }

}