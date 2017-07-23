<?php

namespace Drupal\vud\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\vud\Plugin\VoteUpDownWidgetManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for our example pages.
 */
class VoteUpDownTestController extends ControllerBase {

  protected $voteUpDownWidgetManager;

  /**
   * VoteUpDownTestController constructor.
   *
   * @param \Drupal\vud\Plugin\VoteUpDownWidgetManager $voteUpDownWidgetManager
   */
  public function __construct(VoteUpDownWidgetManager $voteUpDownWidgetManager) {
    $this->voteUpDownWidgetManager = $voteUpDownWidgetManager;
  }

  public function renderWidget() {
    $build = [];

    $vud_widget_definition = $this->voteUpDownWidgetManager->getDefinition('plain');

    $build['vud_widget'] = [
      '#theme' => 'vud_widget',
      '#attached' => array(
        'library' => array(
          'vud/plain',
        ),
      ),
      '#widget_template' => 'plain',
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Override the parent method so that we can inject our sandwich plugin
   * manager service into the controller.
   *
   * For more about how dependency injection works read
   * https://www.drupal.org/node/2133171
   *
   * @see container
   */
  public static function create(ContainerInterface $container) {
    // Inject the plugin.manager.sandwich service that represents our plugin
    // manager as defined in the plugin_type_example.services.yml file.
    return new static($container->get('plugin.manager.vud'));
  }


}