<?php

namespace Drupal\prise_rendez_vous\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\prise_rendez_vous\Services\PriseRendezVousSimple;

/**
 *
 * @author stephane
 *
 */
class PriseRendezVousBasic extends FormBase {

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
   * @see \Drupal\Core\Form\FormInterface::getFormId()
   */
  public function getFormId() {
    return 'prise_rendez_vous_basic';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}