<?php

namespace Drupal\vud\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines a plugin base implementation that corresponding plugins will extend.
 */
abstract class VoteUpDownWidgetBase extends PluginBase implements VoteUpDownWidgetInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getWidgetId() {
    return $this->getPluginDefinition()['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function getWidgetTemplate() {
    return $this->getPluginDefinition()['widget_template'];
  }

  /**
   * {@inheritdoc}
   */
  public function ajaxRender($type, $entity_id, $value, $tag, $token, $widget) {
    return;
  }

  /**
   * {@inheritdoc}
   */
  public function alterTemplateVars($widget_template, &$variables) {
    return;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('vud')->getPath();
    return [
      '#theme' => 'vud_widget',
      '#widget_template' => $this->getWidgetId(),
      '#base_path' => $module_path,
      '#widget_name' => $this->getWidgetId(),
    ];
  }

}