<?php

namespace Drupal\prise_rendez_vous\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Stephane888\Debug\Utility;
use Drupal\Component\Serialization\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\prise_rendez_vous\Services\PriseRendezVousSimple;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for prise rendez vous routes.
 */
class PriseRendezVousController extends ControllerBase {
  /**
   *
   * @var PriseRendezVousSimple
   */
  protected $PriseRendezVousSimple;

  function __construct(PriseRendezVousSimple $PriseRendezVousSimple) {
    $this->PriseRendezVousSimple = $PriseRendezVousSimple;
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
    return new static($container->get('prise_rendez_vous.manage.basic'));
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
      $creneaux = $this->PriseRendezVousSimple->getCreneaux($entity);
      return $this->reponse([
        'data_creneaux' => $creneaux,
        'data_to_rdv' => $this->getDataToRdv($entity_type_id, $entity_id)
      ]);
    }
    throw new \Exception("Le contenu n'est pas definit");
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
      $datas = $request->getContent();
      $datas = Json::decode($datas);
      $content = $this->entityTypeManager()->getStorage($entity_type_id)->load($entity_id);
      if ($content && !empty($datas['creneau'])) {
        $BaseConfig = $this->PriseRendezVousSimple->getConfigEntity($content)->toArray();
        $day = new \DateTime($datas['creneau']['date']);
        $time = explode(":", $datas['creneau']['value']);
        $values = [
          'name' => $content->label(),
          'creneau' => [
            'value' => $day->setTime($time[0], $time[1])->format("Y-m-d\TH-i-s"),
            'end_value' => $day->modify("+ " . $BaseConfig["interval"] . " minutes")->format("Y-m-d\TH-i-s")
          ],
          'creneau_string' => $datas['creneau']['value'],
          'rdv_config_entity' => $BaseConfig['id'],
          'equipes_entity' => isset($datas['equipe']) ? $datas['equipe'] : null
        ];
        $RdvEntity = $this->PriseRendezVousSimple->SaveRdvEntityService->saveRdv($values);
        // $this->PriseRendezVousSimple->SaveRdvEntityService->saveRdv($values);
        return $this->reponse([
          'rdvEntyity' => $RdvEntity->toArray(),
          'values' => $values
        ]);
      }
      throw new \Exception("Le contenu n'est pas definit ...");
    }
    catch (\Exception $e) {
      return $this->reponse(Utility::errorAll($e), 400, $e->getMessage());
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
