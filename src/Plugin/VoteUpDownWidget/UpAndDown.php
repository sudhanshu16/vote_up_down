<?php

namespace Drupal\vud\Plugin\VoteUpDownWidget;

use Drupal\vud\Plugin\VoteUpDownWidgetBase;

/**
 * Plugin "upanddown" of type VoteUpDownWidget.
 *
 * @VoteUpDownWidget(
 *   id = "upanddown",
 *   label = @Translation("Up and down arrow rating widget with both Up and
 *   Down votes")
 *  )
 */
class UpAndDown extends VoteUpDownWidgetBase {
  
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
  }

}
