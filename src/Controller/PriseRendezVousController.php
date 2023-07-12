<?php

namespace Drupal\prise_rendez_vous\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Stephane888\Debug\ExceptionExtractMessage;
use Drupal\Component\Serialization\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\prise_rendez_vous\Services\PriseRendezEntiy;
use Drupal\prise_rendez_vous\Services\Ressources\PriseRdv;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Stephane888\DrupalUtility\HttpResponse;
use Stephane888\Debug\Repositories\ConfigDrupal;

/**
 * Returns responses for prise rendez vous routes.
 */
class PriseRendezVousController extends ControllerBase {
  /**
   *
   * @var PriseRendezEntiy
   */
  protected $PriseRendezEntiy;
  
  /**
   *
   * @var PriseRdv
   */
  protected $PriseRdv;
  
  function __construct(PriseRendezEntiy $PriseRendezEntiy, PriseRdv $PriseRdv) {
    $this->PriseRendezEntiy = $PriseRendezEntiy;
    $this->PriseRdv = $PriseRdv;
  }
  
  /**
   * Builds the response.
   */
  public function build() {
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!')
    ];
    
    return $build;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('prise_rendez_vous.manage.basic'), $container->get('prise_rendez_vous.manage.PriseRdv'));
  }
  
  /**
   *
   * @param Request $request
   * @param string $entity_type_id
   * @throws \Exception
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function LoadCreneauRdv(Request $request, string $entity_type_id, $entity_id) {
    $entity = $this->entityTypeManager()->getStorage($entity_type_id)->load($entity_id);
    if (!empty($entity)) {
      $creneaux = $this->PriseRendezEntiy->getCreneaux($entity);
      $BaseConfig = $this->PriseRendezEntiy->getConfigEntity($entity);
      return HttpResponse::response([
        'data_creneaux' => $creneaux,
        'data_to_rdv' => $this->getDataToRdv($entity_type_id, $entity_id),
        'rdv_config_entity' => $BaseConfig->id()
      ]);
    }
    throw new \Exception("Le contenu n'est pas definit");
  }
  
  /**
   * Charge la configuration par defautl.
   *
   * @see #tache /mdoule_drupal_9_2023_4_360/144/24
   *     
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function LoadDefaultConfigsCreneauRdv(string $entity_type_id, $entity_id) {
    try {
      $content = ConfigDrupal::config('prise_rendez_vous.default_configs');
      if (!empty($content['id']) && $entity = \Drupal\prise_rendez_vous\Entity\RdvConfigEntity::load($content['id'])) {
        
        $creneaux = $this->PriseRdv->getDatasRdv($entity);
        $results = [
          'data_creneaux' => $creneaux,
          'data_to_rdv' => [],
          'rdv_config_entity' => $content['id'],
          'action_after_save' => $content['url_redirect']
        ];
        // Check si c'est une maj.
        $submitEntities = $this->entityTypeManager()->getStorage('submit_rdv_entity')->loadByProperties([
          'entity_id' => $entity_id,
          'entity_type' => $entity_type_id
        ]);
        if ($submitEntities) {
          $submitEntity = reset($submitEntities);
          $results['submit_rdv_entity_id'] = $submitEntity->id();
        }
        return HttpResponse::response($results);
      }
      else
        throw new \Exception("L'entité RdvConfigEntity n'est pas configurer");
    }
    catch (\Exception $e) {
      return $this->reponse(ExceptionExtractMessage::errorAll($e), 435, $e->getMessage());
    }
  }
  
  protected function getDataToRdv(string $entity_type_id, $entity_id) {
    $datas = $this->entityTypeManager()->getStorage($entity_type_id)->load($entity_id);
    if ($datas) {
      return $datas->toArray();
    }
    return [];
  }
  
  public function PageRender(Request $request) {
    $build['content'] = [
      '#type' => 'html_tag',
      '#tag' => 'section',
      "#attributes" => [
        'id' => 'app-prise-rdv-v1',
        'class' => [
          'm-5',
          'p-5'
        ]
      ]
    ];
    $build['content']['#attached']['library'][] = 'prise_rendez_vous/prise_rdv';
    return $build;
  }
  
  /**
   *
   * @return string[]|\Drupal\Core\StringTranslation\TranslatableMarkup[]
   */
  public function SaveSouscriptionRdv(Request $request, string $entity_type_id, $entity_id) {
    try {
      $datas = Json::decode($request->getContent());
      $content = $this->entityTypeManager()->getStorage($entity_type_id)->load($entity_id);
      if ($content && !empty($datas['creneau'])) {
        $BaseConfig = $this->PriseRendezEntiy->getConfigEntity($content)->toArray();
        $day = new \DateTime($datas['creneau']['date']);
        $time = explode(":", $datas['creneau']['value']);
        $values = [
          'name' => $content->label(),
          'creneau' => [
            'value' => $day->setTime($time[0], $time[1])->format("Y-m-d\TH:i:s"),
            'end_value' => $day->modify("+ " . $BaseConfig["interval"] . " minutes")->format("Y-m-d\TH:i:s")
          ],
          'creneau_string' => $datas['creneau']['value'],
          'rdv_config_entity' => $datas['rdv_config_entity'],
          'equipes_entity' => isset($datas['equipe']) ? $datas['equipe'] : null,
          'entity_id' => $datas['entity_id'],
          'entity_type_id' => $datas['entity_type_id'],
          'entity_type' => $datas['entity_type']
        ];
        if (!empty($datas['submit_rdv_entity_id']))
          $values['id'] = $datas['submit_rdv_entity_id'];
        $RdvEntity = $this->PriseRendezEntiy->SaveRdvEntityService->saveRdv($values);
        // $this->PriseRendezEntiy->SaveRdvEntityService->saveRdv($values);
        return $this->reponse([
          'rdvEntyity' => $RdvEntity->toArray(),
          'values' => $values
        ]);
      }
      throw new \Exception("Le contenu n'est pas definit ...");
    }
    catch (\Exception $e) {
      return $this->reponse(ExceptionExtractMessage::errorAll($e), 435, $e->getMessage());
    }
  }
  
  /**
   *
   * @param Array|string $configs
   * @param number $code
   * @param string $message
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  protected function reponse($configs, $code = null, $message = null) {
    if (!is_string($configs))
      $configs = Json::encode($configs);
    $reponse = new JsonResponse();
    if ($code)
      $reponse->setStatusCode($code, $message);
    $reponse->setContent($configs);
    return $reponse;
  }
  
}
