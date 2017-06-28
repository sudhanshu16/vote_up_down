<?php

namespace Drupal\vud;

/**
 * Implements lazy loading.
 */
class VotingApiLoader {

  /**
   * Build the form for a widget
   *
   * @param string $plugin_id
   *
   * @return mixed
   */
  public function getVotingWidget($plugin_id) {
    $manager = \Drupal::service('plugin.manager.vud');
    $definitions = $manager->getDefinitions();
    $plugin = $manager->createInstance($plugin_id, $definitions[$plugin_id]);
    // $vote = $this->getEntityForVoting($entity_type, $entity_bundle, $entity_id, $vote_type, $field_name)
    // $gtentity_id = $entity->getVotedEntityId();
    // return $plugin->buildVotingWidget($vote, $plugin_id, $entity_id);
    return TRUE; // placeholder
  }

}
