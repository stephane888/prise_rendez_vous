<?php

namespace Drupal\prise_rendez_vous\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SubmitRdvEntityController.
 *
 *  Returns responses for Submit rdv entity routes.
 */
class SubmitRdvEntityController extends ControllerBase implements ContainerInjectionInterface {

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
   * Displays a Submit rdv entity revision.
   *
   * @param int $submit_rdv_entity_revision
   *   The Submit rdv entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($submit_rdv_entity_revision) {
    $submit_rdv_entity = $this->entityTypeManager()->getStorage('submit_rdv_entity')
      ->loadRevision($submit_rdv_entity_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('submit_rdv_entity');

    return $view_builder->view($submit_rdv_entity);
  }

  /**
   * Page title callback for a Submit rdv entity revision.
   *
   * @param int $submit_rdv_entity_revision
   *   The Submit rdv entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($submit_rdv_entity_revision) {
    $submit_rdv_entity = $this->entityTypeManager()->getStorage('submit_rdv_entity')
      ->loadRevision($submit_rdv_entity_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $submit_rdv_entity->label(),
      '%date' => $this->dateFormatter->format($submit_rdv_entity->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Submit rdv entity.
   *
   * @param \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface $submit_rdv_entity
   *   A Submit rdv entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(SubmitRdvEntityInterface $submit_rdv_entity) {
    $account = $this->currentUser();
    $submit_rdv_entity_storage = $this->entityTypeManager()->getStorage('submit_rdv_entity');

    $langcode = $submit_rdv_entity->language()->getId();
    $langname = $submit_rdv_entity->language()->getName();
    $languages = $submit_rdv_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $submit_rdv_entity->label()]) : $this->t('Revisions for %title', ['%title' => $submit_rdv_entity->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all submit rdv entity revisions") || $account->hasPermission('administer submit rdv entity entities')));
    $delete_permission = (($account->hasPermission("delete all submit rdv entity revisions") || $account->hasPermission('administer submit rdv entity entities')));

    $rows = [];

    $vids = $submit_rdv_entity_storage->revisionIds($submit_rdv_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\prise_rendez_vous\Entity\SubmitRdvEntityInterface $revision */
      $revision = $submit_rdv_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $submit_rdv_entity->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.submit_rdv_entity.revision', [
            'submit_rdv_entity' => $submit_rdv_entity->id(),
            'submit_rdv_entity_revision' => $vid,
          ]))->toString();
        }
        else {
          $link = $submit_rdv_entity->toLink($date)->toString();
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
              Url::fromRoute('entity.submit_rdv_entity.translation_revert', [
                'submit_rdv_entity' => $submit_rdv_entity->id(),
                'submit_rdv_entity_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.submit_rdv_entity.revision_revert', [
                'submit_rdv_entity' => $submit_rdv_entity->id(),
                'submit_rdv_entity_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.submit_rdv_entity.revision_delete', [
                'submit_rdv_entity' => $submit_rdv_entity->id(),
                'submit_rdv_entity_revision' => $vid,
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

    $build['submit_rdv_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
