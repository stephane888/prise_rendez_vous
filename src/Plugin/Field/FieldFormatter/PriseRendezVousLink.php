<?php

namespace Drupal\prise_rendez_vous\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'entity reference label' formatter.
 *
 * @FieldFormatter(
 *   id = "prise_rendez_vous_link",
 *   label = @Translation("Affiche le lien de reservation"),
 *   description = @Translation(" Display the label of the referenced entities. "),
 *   field_types = {
 *     "integer",
 *   }
 * )
 */
class PriseRendezVousLink extends FormatterBase {

  /**
   *
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [] + parent::defaultSettings();
  }

  /**
   *
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $item) {
      $node = $item->getEntity();
      $elements[] = [
        '#type' => 'link',
        '#title' => t('Choisir'),
        '#options' => [
          'attributes' => [
            'class' => [
              'sd-btn',
              'sd-btn--small',
              'sd-btn--primary'
            ]
          ]
        ],
        '#url' => Url::fromRoute('prise_rendez_vous.creneau.page_render', [
          'entity_type_id' => $node->getEntityTypeId(),
          'entity_id' => $node->id()
        ])
      ];
    }
    //
    return $elements;
  }

}
