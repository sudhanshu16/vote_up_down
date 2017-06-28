<?php

namespace Drupal\vud\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Base class for Vote Up/Down Widget plugins.
 */
abstract class VoteUpDownWidgetBase extends PluginBase implements VoteUpDownWidgetInterface {
  
  use StringTranslationTrait;
  
  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->getPluginDefinition()['label'];
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
  function alterTemplateVars($widget_template, &$variables) {
    $criteria = [
      'entity_type' => $variables['entity_type'],
      'entity_id' => $variables['entity_id'],
      'value_type' => $variables['points'],
      'tag' => $variables['tag'],
    ];
    
    $criteria['function'] = 'sum';
    $vote_result = votingapi_select_single_result_value($criteria);
    $variables['vote_sum'] = ($vote_result) ? $vote_result : 0;
    
    $criteria['function'] = 'positives';
    $vote_result = votingapi_select_single_result_value($criteria);
    $variables['up_points'] = $vote_result;
    
    $criteria['function'] = 'negatives';
    $vote_result = votingapi_select_single_result_value($criteria);
    $variables['down_points'] = $vote_result;
  }
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'vud_widget',
      '#widget_template' => $this->getWidgetTemplate(),
    ];
  }
  
}