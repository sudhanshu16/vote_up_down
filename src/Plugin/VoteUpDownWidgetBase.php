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
   * Get label for the plugin
   *
   * @return mixed
   */
  public function get_label() {
    return $this->getPluginDefinition()['label'];
  }

  /**
   * @param $template_type
   * @param $variables
   */

  function alter_template_vars($template_type, &$variables) {
    if ($template_type == 'thumbs') {
      $criteria['entity_id'] = $variables['entity_id'];
      $criteria['entity_type'] = $variables['type'];
      $criteria['value_type'] = 'points';
      $criteria['tag'] = $variables['tag'];
      $criteria['function'] = 'sum';
      $vote_sum = votingapi_select_single_result_value($criteria);
      $variables['vote_sum'] = ($vote_sum) ? $vote_sum : 0;
    }
    else {
      $criteria = [
        'entity_type' => $variables['type'],
        'entity_id' => $variables['entity_id'],
        'value_type' => 'points',
        'tag' => $variables['tag'],
        'function' => 'positives',
      ];
      $positives = (int) votingapi_select_single_result_value($criteria);
      $variables['up_points'] = $positives;

      $criteria = [
        'entity_type' => $variables['type'],
        'entity_id' => $variables['entity_id'],
        'value_type' => 'points',
        'tag' => $variables['tag'],
        'function' => 'negatives',
      ];
      $negatives = (int) votingapi_select_single_result_value($criteria);
      $variables['down_points'] = $negatives;
    }
  }

}