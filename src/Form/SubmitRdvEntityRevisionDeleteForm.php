<?php

namespace Drupal\prise_rendez_vous\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Submit rdv entity revision.
 *
 * @ingroup prise_rendez_vous
 */
class SubmitRdvEntityRevisionDeleteForm extends ConfirmFormBase {


  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The Submit rdv entity revision.
   *
   * @var \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface
   */
  protected $revision;

  /**
   * The Submit rdv entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $submitRdvEntityStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->submitRdvEntityStorage = $container->get('entity_type.manager')->getStorage('submit_rdv_entity');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'submit_rdv_entity_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.submit_rdv_entity.version_history', ['submit_rdv_entity' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $submit_rdv_entity_revision = NULL) {
    $this->revision = $this->SubmitRdvEntityStorage->loadRevision($submit_rdv_entity_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->SubmitRdvEntityStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Submit rdv entity: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Submit rdv entity %title has been deleted.', ['%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.submit_rdv_entity.canonical',
       ['submit_rdv_entity' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {submit_rdv_entity_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.submit_rdv_entity.version_history',
         ['submit_rdv_entity' => $this->revision->id()]
      );
    }
  }

}
