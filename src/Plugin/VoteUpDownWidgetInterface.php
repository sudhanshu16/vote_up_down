<?php

namespace Drupal\vud\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Vote Up/Down Widget plugins.
 */
interface VoteUpDownWidgetInterface extends PluginInspectionInterface {

  /**
   * Method to return the label of the plugin whenever required
   *
   * @return mixed
   */
  public function get_label();

  /**
   * Method to alter template variables on render
   * @param $widget_template
   * @param $variables
   *
   * @return mixed
   */
  public function alter_template_variables($widget_template, $variables);

  /**
   * Method to render ajax
   * @param $type
   * @param $entity_id
   * @param $value
   * @param $tag
   * @param $token
   * @param $widget
   *
   * @return mixed
   */
  public function ajax_render($type, $entity_id, $value, $tag, $token, $widget);

}
