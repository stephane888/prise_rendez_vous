<?php

namespace Drupal\prise_rendez_vous\Plugin\Commerce\CheckoutPane;

use Drupal\commerce\InlineFormManager;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;

/**
 * Provides the billing information pane.
 *
 * @CommerceCheckoutPane(
 *   id = "prise_rendez_vous",
 *   label = @Translation("PriseRendezVous"),
 *   default_step = "priserendezvous_checkf",
 *   wrapper_element = "fieldset",
 * )
 */
class PriseRendezVous extends CheckoutPaneBase implements CheckoutPaneInterface {
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form['html_reservation'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => 'Formulaire de reservation'
    ];
    $pane_form['content_form'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      "#attributes" => [
        'id' => 'app-prise-rdv-v1',
        'url-creneaux' => '/prise-rendez-vous/load-default-configs',
        'dynamic-url' => 'false',
        'class' => [
          'm-5',
          'p-5'
        ]
      ]
    ];
    $pane_form['content_form']['#attached']['library'][] = 'prise_rendez_vous/prise_rdv';
    return $pane_form;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase::buildConfigurationForm()
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    //
    return $form;
  }
  
  public function buildPaneSummary() {
    $summary = parent::buildPaneSummary();
    $summary['html_reservation'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => 'Formulaire de reservation'
    ];
    return $summary;
  }
  
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    //
    parent::submitPaneForm($pane_form, $form_state, $complete_form);
  }
  
}