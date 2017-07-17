<?php

namespace Drupal\vud\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Vote Up/Down Widget annotation object.
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
   * Filename of the plugin used.
   *
   * @var string
   */
  public $widget_template;

  /**
   * Filename of the template used for showing votes
   *
   * @var string
   */
  public $vote_template;

}
