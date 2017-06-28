<?php

namespace Drupal\vud\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Vote Up/Down Field item annotation object.
 *
 * @see \Drupal\vud\Plugin\VoteUpDownFieldManager
 * @see plugin_api
 *
 * @Annotation
 */
class VoteUpDownWidget extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $plugin_id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * TODO: description of variable to be updated
   *
   * @var string
   */
  public $widgetTemplate;

  /**
   * TODO: description of variable to be updated
   *
   * @var string
   */
  public $voteTemplate;

}
