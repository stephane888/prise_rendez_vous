<?php

namespace Drupal\prise_rendez_vous\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "prise_rendez_vous_example",
 *   admin_label = @Translation("Example"),
 *   category = @Translation("prise rendez vous")
 * )
 */
class ExampleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

}
