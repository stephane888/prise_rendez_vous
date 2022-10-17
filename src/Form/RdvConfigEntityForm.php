<?php

namespace Drupal\prise_rendez_vous\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RdvConfigEntityForm.
 */
class RdvConfigEntityForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $rdv_config_entity = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $rdv_config_entity->label(),
      '#description' => $this->t("Label for the Rdv config entity."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $rdv_config_entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\prise_rendez_vous\Entity\RdvConfigEntity::load',
      ],
      '#disabled' => !$rdv_config_entity->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $rdv_config_entity = $this->entity;
    $status = $rdv_config_entity->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Rdv config entity.', [
          '%label' => $rdv_config_entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Rdv config entity.', [
          '%label' => $rdv_config_entity->label(),
        ]));
    }
    $form_state->setRedirectUrl($rdv_config_entity->toUrl('collection'));
  }

}
