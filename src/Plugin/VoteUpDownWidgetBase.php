<?php

namespace Drupal\vud\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\votingapi\VoteResultFunctionManager;


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
   * {@inheritdoc}
   */
  public function build() {
    $entities = [];
    foreach (\Drupal::routeMatch()->getParameters() as $param) {
      if ($param instanceof \Drupal\Core\Entity\EntityInterface) {
        $entities[] = $param;
      }
    }

    $vote_storage = \Drupal::service('entity.manager')->getStorage('vote');

    $currentUser =  \Drupal::currentUser();

    $entity = $entities[0];
    $entityTypeId = $entity->getEntityTypeId();
    $entityId = $entity->id();

    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('vud')->getPath();

    $vote_result_manager = \Drupal::service('plugin.manager.votingapi.resultfunction');

    $up_points = \Drupal::entityQuery('vote')->condition('value', 1)->count()->execute();
    $down_points = \Drupal::entityQuery('vote')->condition('value', -1)->count()->execute();

    $points = $up_points - $down_points;
    $unsigned_points = $up_points + $down_points;

    $variables = [
      '#theme' => 'vud_widget',
      '#widget_template' => $this->getWidgetId(),
      '#base_path' => $module_path,
      '#widget_name' => $this->getWidgetId(),
      '#up_points' => $up_points,
      '#down_points' => $down_points,
      '#points' => $points,
      '#unsigned_points' => $unsigned_points,
      '#vote_label' => 'votes',
      '#attached' => [
        'library' => [
          'vud/' . $this->getPluginDefinition()['id'],
          'vud/ajax',
          'vud/common',
        ]
      ],
    ];

    if(vud_can_vote($currentUser, $entity)){
      $user_votes_current_entity = $vote_storage->getUserVotes(
        $currentUser->id(),
        'points',
        $entityTypeId,
        $entityId
      );

      $variables += [
        '#link_up' => Url::fromRoute('vud.vote', [
          'entityTypeId' => $entityTypeId,
          'entityId' => $entityId,
          'voteValue' => 1,
        ]),
        '#link_down' => Url::fromRoute('vud.vote', [
          'entityTypeId' => $entityTypeId,
          'entityId' => $entityId,
          'voteValue' => -1,
        ]),
        '#link_reset' => Url::fromRoute('vud.reset', [
          'entityTypeId' => $entityTypeId,
          'entityId' => $entityId,
        ]),
        '#show_reset' => TRUE,
        '#reset_long_text' => t('Reset your vote'),
        '#reset_short_text' => t('(reset)'),
        '#link_class_reset' => 'reset element-invisible',
      ];

      if($user_votes_current_entity != NULL){
        $user_vote_id = (int)array_values($user_votes_current_entity)[0];

        $user_vote = $vote_storage->load($user_vote_id)->getValue();


        if($user_vote != 0) {
          if($user_vote == 1){
            $variables['#link_class_up'] = 'up active';
            $variables['#link_class_down'] = 'down inactive';
            $variables['#class_up'] = 'up active';
            $variables['#class_down'] = 'down inactive';
          }
          elseif($user_vote == -1){
            $variables['#link_class_up'] = 'up inactive';
            $variables['#link_class_down'] = 'down active';
            $variables['#class_up'] = 'up inactive';
            $variables['#class_down'] = 'down active';
          }
          $variables['#link_class_reset'] = 'reset';
        }
      }
      else{
        $variables['#link_class_up'] = 'up inactive';
        $variables['#link_class_down'] = 'down inactive';
        $variables['#class_up'] = 'up inactive';
        $variables['#class_down'] = 'down inactive';
      }
    }
    return $variables;
  }

  public function alterTemplateVars($widget_template, &$variables){}

  public function ajaxRender($type, $entity_id, $value, $tag, $token, $widget){}

}