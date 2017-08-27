<?php

namespace Drupal\vud\Plugin;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Vote Up/Down Widget plugins.
 */
interface VoteUpDownWidgetInterface extends PluginInspectionInterface {

  /**
   * Returns the label of the specific plugin instance
   *
   * @return mixed
   */
  public function getWidgetId();

  /**
   * Returns the widget template for a specific plugin instance
   *
   * @return mixed
   */
  public function getWidgetTemplate();

  /**
   * @param $widget_template
   * @param $variables
   *
   * @return mixed
   */
  public function alterTemplateVariables(&$variables);

  /**
   * Returns renderable array for the plugin
   *
   * @param $entity EntityInterface
   *
   * @return array
   */
  public function build($entity);

}
