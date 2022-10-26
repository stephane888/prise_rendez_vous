<?php

namespace Drupal\prise_rendez_vous\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Equipes entity edit forms.
 *
 * @ingroup prise_rendez_vous
 */
class EquipesEntityForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\prise_rendez_vous\Entity\EquipesEntity $entity */
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Equipes entity.', [
          '%label' => $entity->label()
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Equipes entity.', [
          '%label' => $entity->label()
        ]));
    }
    $form_state->setRedirect('entity.equipes_entity.canonical', [
      'equipes_entity' => $entity->id()
    ]);
  }

}
