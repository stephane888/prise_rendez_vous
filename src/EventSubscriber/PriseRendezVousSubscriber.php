<?php

namespace Drupal\prise_rendez_vous\EventSubscriber;

use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\vuejs_entity\Event\DuplicateEntityEvent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\prise_rendez_vous\Services\PriseRendezVousSimple;

class PriseRendezVousSubscriber implements EventSubscriberInterface {

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;
  protected $EntityTypeManager;
  protected $PriseRendezVousSimple;

  /**
   * Constructs event subscriber.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *        The messenger.
   */
  public function __construct(MessengerInterface $messenger, EntityTypeManagerInterface $EntityTypeManager, PriseRendezVousSimple $PriseRendezVousSimple) {
    $this->messenger = $messenger;
    $this->EntityTypeManager = $EntityTypeManager;
    $this->PriseRendezVousSimple = $PriseRendezVousSimple;
  }

  /**
   * Permet de savoir qu'un contenu a été cloné.
   *
   * @param DuplicateEntityEvent $DuplicateEntityEvent
   */
  public function DuplicateEntity(DuplicateEntityEvent $DuplicateEntityEvent) {

    /**
     *
     * @var \Drupal\node\Entity\Node $entityClone
     */
    $entityClone = $DuplicateEntityEvent->entityClone;

    /**
     *
     * @var \Drupal\node\Entity\Node $entityClone
     */
    $entity = $DuplicateEntityEvent->entity;

    /**
     *
     * @var \Drupal\Core\Entity\EntityInterface $entityData
     */
    $entityData = $DuplicateEntityEvent->entityDatas;

    if ($entityClone->getEntityType() instanceof \Drupal\Core\Entity\ContentEntityType) {
      if ($entityClone->getEntityType()->hasKey('bundle')) {
        $entityTypeId = $entityClone->getEntityType()->getBundleEntityType();
        $entityType = $this->EntityTypeManager->getStorage($entityTypeId)->load($entityClone->bundle());
        $ThirdPartySettings = $entityType->getThirdPartySettings('prise_rendez_vous');
        //
        if (!empty($ThirdPartySettings['prise_rendez_vous_enabled'])) {
          try {
            $this->PriseRendezVousSimple->CloneFromAnotherEntity($entityClone, $entity, $entityData);
          }
          catch (\Exception $e) {
            \Drupal::logger('prise_rendez_vous')->alert($e->getMessage());
          }
        }
      }
    }
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      DuplicateEntityEvent::EVENT_NAME => [
        'DuplicateEntity'
      ]
    ];
  }

}
