<?php

namespace Drupal\vud\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;


/**
 * Defines a base block implementation that most blocks plugins will extend.
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
   * @param bool $js
   * @param int  $code
   *
   * @return mixed
   */
  public function vud_denied_vote($js = FALSE, $code=VUD_WIDGET_MESSAGE_ERROR) {
    $widget_message_codes = array(
      VUD_WIDGET_MESSAGE_ERROR => t('Sorry, there was problem on the vote.'),
      VUD_WIDGET_MESSAGE_DENIED => t('You are not allowed to vote.'),
    );
    drupal_alter('vud_widget_message_codes', $widget_message_codes);
    if ($js) {
      ctools_include('ajax');
      ctools_include('modal');
      ctools_modal_render('', $widget_message_codes[$code]);
    }
    else {
      return $widget_message_codes[$code];
    }
  }

  /**
   * Implementation of votingapi hook_votingapi_results_alter().
   *
   * Add positive/negative aggregations for VotingAPI cache points.
   */
  public function vud_votingapi_results_alter(&$cache, $entity_type, $entity_id) {
    // positive points
    $sql  = "SELECT SUM(v.value) as value_positives, v.tag ";
    $sql .= "FROM {votingapi_vote} v ";
    $sql .= "WHERE v.entity_type = :entity_type AND v.entity_id = :entity_id AND v.value_type = 'points' AND v.value > 0 ";
    $sql .= "GROUP BY v.value_type, v.tag";
    $result = db_query($sql, array(':entity_type' => $entity_type, ':entity_id' => $entity_id));
    foreach ($result as $record) {
      $cache[$record->tag]['points']['positives'] = $record->value_positives;
    }

    // negative points
    $sql  = "SELECT SUM(v.value) as value_negatives, v.tag ";
    $sql .= "FROM {votingapi_vote} v ";
    $sql .= "WHERE v.entity_type = :entity_type AND v.entity_id = :entity_id AND v.value_type = 'points' AND v.value < 0 ";
    $sql .= "GROUP BY v.value_type, v.tag";
    $result = db_query($sql, array(':entity_type' => $entity_type, ':entity_id' => $entity_id));
    foreach ($result as $record) {
      $cache[$record->tag]['points']['negatives'] = $record->value_negatives;
    }
  }



  /**
   * {@inheritdoc}
   */
  public function build($entity) {
    $entityTypeId = $entity->getEntityTypeId();
    $entityId = $entity->id();

    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('vud')->getPath();
    return [
      '#theme' => 'vud_widget',
      '#link_up' => "nojs/vote/" . $entityTypeId . '/' . $entityId . '/1',
      '#link_down' => "nojs/vote/" . $entityTypeId . '/' . $entityId . '/-1',
      '#link_class_up' => 'up-inactive use-ajax',
      '#link_class_down' => 'down-inactive use-ajax',
      '#class_up' => 'up-inactive',
      '#class_down' => 'down-inactive',
      '#widget_template' => $this->getWidgetId(),
      '#base_path' => $module_path,
      '#widget_name' => $this->getWidgetId(),
      '#attached' => [
        'library' => [
          'vud/' . $this->getPluginDefinition()['id'],
          'vud/ajax',
        ]
      ],
    ];
  }

  public function alterTemplateVars($widget_template, &$variables){}

  public function ajaxRender($type, $entity_id, $value, $tag, $token, $widget){}

}