<?php

namespace Drupal\vud\Plugin\VoteUpDownWidget;

use Drupal\vud\Plugin\VoteUpDownWidgetBase;

/**
 * Plugin "thumbs" of type VoteUpDownWidget.
 *
 * @VoteUpDownWidget(
 *   id = "thumbs",
 *   label = @Translation("Thumb style rating widget")
 *  )
 */
class Thumbs extends VoteUpDownWidgetBase {
  
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
    
    $criteria['function'] = 'positives';
    $vote_result = votingapi_select_single_result_value($criteria);
    $variables['up_points'] = $vote_result;
    
    $criteria['function'] = 'negatives';
    $vote_result = votingapi_select_single_result_value($criteria);
    $variables['down_points'] = $vote_result;
  }
  
}
