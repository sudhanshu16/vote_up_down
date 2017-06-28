<?php

namespace Drupal\vud\Plugin;

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
  public function getLabel();
  
  /**
   * Returns the widget template for a specific plugin instance
   *
   * @return mixed
   */
  public function getWidgetTemplate();

  /**
   * Alters template variables on render
   *
   * @param $widget_template
   * @param $variables
   *
   * @return mixed
   */
  public function alterTemplateVars($widget_template, $variables);

  /**
   * Renders Ajax for the view
   *
   * @param $type
   * @param $entity_id
   * @param $value
   * @param $tag
   * @param $token
   * @param $widget
   *
   * @return mixed
   */
  public function ajaxRender($type, $entity_id, $value, $tag, $token, $widget);
  
  /**
   * Returns renderable array for the plugin
   *
   * @return mixed
   */
  public function build();

}
