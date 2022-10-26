<?php

namespace Drupal\prise_rendez_vous\Services\Ressources;

use Drupal\Core\Controller\ControllerBase;
use Drupal\prise_rendez_vous\Entity\RdvConfigEntity;
use Drupal\prise_rendez_vous\Entity\EquipesEntity;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\Renderer;

/**
 *
 * @author stephane
 *
 */
class EquipesService extends ControllerBase {
  use RessourcesTrait;
  /**
   *
   * @var string
   */
  protected const entityEquipes = 'equipes_entity';

  /**
   * Permet de lister les equipes + le bouton permettant d'ajouter une nouvelle
   * equipe.
   */
  public function ListBuilder(RdvConfigEntity $entity) {
    // Table header
    $header = array(
      'id' => t("id"),
      'title' => t(" Nom de l'equipe / Personnel "),
      'action' => t("Action")
    );
    $field_access = \Drupal\domain_access\DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD;
    $domaineId = \Drupal\creation_site_virtuel\CreationSiteVirtuel::getActiveDomain();
    $equipes = $this->entityTypeManager()->getStorage(self::entityEquipes)->loadByProperties([
      'rdv_config_entity' => $entity->id(),
      $field_access => $domaineId
    ]);
    $rows = [];
    foreach ($equipes as $equipe) {
      /**
       *
       * @var EquipesEntity $equipe
       */
      $rows[] = [
        'id' => $equipe->id(),
        'title' => $equipe->label(),
        'action' => $this->buildOperations($entity)
      ];
    }
    $link = [
      '#title' => $this->t(' + Ajouter une equipe '),
      '#type' => 'link',
      '#url' => Url::fromRoute('entity.equipes_entity.add_form', [], [
        'query' => [
          'rdv_config_entity' => $entity->id()
        ]
      ]),
      "#options" => [
        'attributes' => [
          'target' => '_blank'
        ]
      ]
    ];
    $build = [
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => $header
    ];
    return [
      $link,
      $build
    ];
  }

  /**
   * Gets this list's default operations.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *        The entity the operations are for.
   *
   * @return array The array structure is identical to the return value of
   *         self::getOperations().
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = [];
    if ($entity->access('update') && $entity->hasLinkTemplate('edit-form')) {
      $operations['edit'] = [
        'title' => $this->t('Edit'),
        'weight' => 10,
        'url' => $this->ensureDestination($entity->toUrl('edit-form'))
      ];
    }
    if ($entity->access('delete') && $entity->hasLinkTemplate('delete-form')) {
      $operations['delete'] = [
        'title' => $this->t('Delete'),
        'weight' => 100,
        'url' => $this->ensureDestination($entity->toUrl('delete-form'))
      ];
    }

    return $operations;
  }

  public function buildOperations(EntityInterface $entity) {
    $build = [
      '#type' => 'operations',
      '#links' => $this->getOperations($entity)
    ];
    /**
     *
     * @var Renderer $renderer
     */
    $renderer = \Drupal::service('renderer');
    $build = $renderer->renderRoot($build);
    return $build;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = $this->getDefaultOperations($entity);
    uasort($operations, '\Drupal\Component\Utility\SortArray::sortByWeightElement');

    return $operations;
  }

  /**
   * Ensures that a destination is present on the given URL.
   *
   * @param \Drupal\Core\Url $url
   *        The URL object to which the destination should be added.
   *
   * @return \Drupal\Core\Url The updated URL object.
   */
  protected function ensureDestination(Url $url) {
    return $url->mergeOptions([
      'query' => $this->getRedirectDestination()->getAsArray()
    ]);
  }

  /**
   * On aurra un contenu equipe pour un type de reservation.
   *
   * @param RdvConfigEntity $entity
   * @deprecated
   */
  public function getEntityEquipes(RdvConfigEntity $entity) {
    $equipes = $this->entityTypeManager()->getStorage(self::entityEquipes)->loadByProperties([
      'rdv_config_entity' => $entity->id()
    ]);
    //
    if (!empty($equipes)) {
      return reset($equipes);
    }
    else {
      $values = [
        'rdv_config_entity' => $entity->id()
      ];
      $Entity = $this->entityTypeManager()->getStorage(self::entityEquipes)->create($values);
      $this->addDomain($Entity);
      return $Entity;
    }
  }

  /**
   * --
   *
   * @param RdvConfigEntity $entity
   */
  public function clone(RdvConfigEntity $entity, $domainId = null) {
    $equipe = $this->getEntityEquipes($entity);
    $cloneEquipe = $equipe->createDuplicate();
    $this->addDomain($cloneEquipe, $domainId);
    $cloneEquipe->save();
  }

}