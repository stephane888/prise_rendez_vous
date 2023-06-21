<?php

namespace Drupal\prise_rendez_vous\Plugin\Commerce\CheckoutFlow;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\MultistepDefault;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the default multistep checkout flow.
 *
 * @CommerceCheckoutFlow(
 *   id = "priserendezvous_checkf",
 *   label = "Multistep - width prise rendez vous",
 * )
 */
class PriseRendezVousCheckoutflow extends MultistepDefault {
  
  /**
   * order_information
   *
   * {@inheritdoc}
   */
  public function getSteps() {
    // we want to add step 'reservation before step 'order_information';
    $re_order_steps = [];
    $steps = parent::getSteps();
    foreach ($steps as $k => $step) {
      if ($k == 'order_information') {
        $re_order_steps['reservation'] = [
          'label' => $this->t('Reservation'),
          'previous_label' => $this->t('Go back'),
          'has_sidebar' => FALSE,
          'next_label' => 'Login'
        ];
        $re_order_steps[$k] = $step;
      }
      else
        $re_order_steps[$k] = $step;
    }
    return $re_order_steps;
  }
  
  public function buildForm(array $form, FormStateInterface $form_state, $step_id = NULL) {
    $form = parent::buildForm($form, $form_state, $step_id);
    // pour l'etape de reservation.
    if ($step_id == 'reservation') {
      $form['actions']['#attributes']['class'][] = 'd-flex';
      $form['actions']['#attributes']['class'][] = 'justify-content-center';
      $form['actions']['#access'] = true;
      $form['actions']['next'] = [
        '#type' => 'submit',
        '#value' => $this->t('Next step'),
        '#button_type' => 'primary',
        // '#disabled' => true,
        '#attributes' => [
          'class' => [
            'btn',
            'w-auto',
            'btn-primary',
            'd-none',
            $this->pluginId
          ]
        ],
        '#submit' => [
          '::submitForm'
        ]
      ];
    }
    
    return $form;
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // put custom code here.
    parent::submitForm($form, $form_state);
  }
  
}