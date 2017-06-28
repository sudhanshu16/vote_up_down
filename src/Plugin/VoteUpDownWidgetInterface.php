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
  public function getWidgetId();

  /**
   * Returns the widget template for a specific plugin instance
   *
   * @return mixed
   */
  public function getWidgetTemplate();

  /**
   * Alters template variables on render
   *
   * 'id' of the widget for the current instance
   * @param $widget_template
   * Array containing all info for a plugin instance
   * @param $variables
   *
   * @return mixed
   */
  public function alterTemplateVars($widget_template, &$variables);

  /**
   * Renders ajax commands when the widget is in use.
   *
   * @param $type
   * ID of the referenced entity
   * @param $entity_id
   * Value of vote casted by the user
   * @param $value
   * Voting API tag
   * @param $tag
   * Token used for security
   * @param $token
   * @param $widget
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function ajaxRender($type, $entity_id, $value, $tag, $token, $widget);

  /**
   * Returns renderable array for the plugin
   *
   * @return array
   */
  public function build();

}
