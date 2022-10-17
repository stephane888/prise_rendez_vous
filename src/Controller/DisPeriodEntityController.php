<?php

namespace Drupal\prise_rendez_vous\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\prise_rendez_vous\Entity\DisPeriodEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DisPeriodEntityController.
 *
 *  Returns responses for Disable periode entity routes.
 */
class DisPeriodEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Disable periode entity revision.
   *
   * @param int $dis_period_entity_revision
   *   The Disable periode entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($dis_period_entity_revision) {
    $dis_period_entity = $this->entityTypeManager()->getStorage('dis_period_entity')
      ->loadRevision($dis_period_entity_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('dis_period_entity');

    return $view_builder->view($dis_period_entity);
  }

  /**
   * Page title callback for a Disable periode entity revision.
   *
   * @param int $dis_period_entity_revision
   *   The Disable periode entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($dis_period_entity_revision) {
    $dis_period_entity = $this->entityTypeManager()->getStorage('dis_period_entity')
      ->loadRevision($dis_period_entity_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $dis_period_entity->label(),
      '%date' => $this->dateFormatter->format($dis_period_entity->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Disable periode entity.
   *
   * @param \Drupal\prise_rendez_vous\Entity\DisPeriodEntityInterface $dis_period_entity
   *   A Disable periode entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DisPeriodEntityInterface $dis_period_entity) {
    $account = $this->currentUser();
    $dis_period_entity_storage = $this->entityTypeManager()->getStorage('dis_period_entity');

    $langcode = $dis_period_entity->language()->getId();
    $langname = $dis_period_entity->language()->getName();
    $languages = $dis_period_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $dis_period_entity->label()]) : $this->t('Revisions for %title', ['%title' => $dis_period_entity->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all disable periode entity revisions") || $account->hasPermission('administer disable periode entity entities')));
    $delete_permission = (($account->hasPermission("delete all disable periode entity revisions") || $account->hasPermission('administer disable periode entity entities')));

    $rows = [];

    $vids = $dis_period_entity_storage->revisionIds($dis_period_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\prise_rendez_vous\Entity\DisPeriodEntityInterface $revision */
      $revision = $dis_period_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $dis_period_entity->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.dis_period_entity.revision', [
            'dis_period_entity' => $dis_period_entity->id(),
            'dis_period_entity_revision' => $vid,
          ]))->toString();
        }
        else {
          $link = $dis_period_entity->toLink($date)->toString();
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.dis_period_entity.translation_revert', [
                'dis_period_entity' => $dis_period_entity->id(),
                'dis_period_entity_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.dis_period_entity.revision_revert', [
                'dis_period_entity' => $dis_period_entity->id(),
                'dis_period_entity_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.dis_period_entity.revision_delete', [
                'dis_period_entity' => $dis_period_entity->id(),
                'dis_period_entity_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['dis_period_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
