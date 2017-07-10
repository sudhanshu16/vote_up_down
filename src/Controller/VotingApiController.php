<?php

namespace Drupal\vud\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\votingapi\Entity\Vote;
use Drupal\votingapi\Entity\VoteType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResult;

class VotingApiController extends ControllerBase implements ContainerInjectionInterface {

  protected function canVote($account){
    return $account->hasPermission("add or remove points votes on {$entity->getEntityTypeId()}")
      || $account->hasPermission("add or remove points votes on {$entity->bundle()} of {$entity->getEntityTypeId()}");
  }

  /**
   * @param $entityId
   * @param $entityTypeId
   * @param $voteValue
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function vote($entityId, $entityTypeId, $voteValue, Request $request) {

    $entity = $this->entityTypeManager()
      ->getStorage($entityTypeId)
      ->load($entityId);

    $vote_storage = $this->entityTypeManager()->getStorage('vote');

    $vote_type_id = 'points';

    $user_votes = $vote_storage->getUserVotes(
      $this->currentUser()->id(),
      $vote_type_id,
      $entityTypeId,
      $entityId
    );

    $vote_type = VoteType::load($vote_type_id);
    $vote = Vote::create(['type' => $vote_type_id]);
    $vote->setVotedEntityId($entityId);
    $vote->setVotedEntityType($entityTypeId);
    $vote->setValueType($vote_type->getValueType());
    $vote->setValue($voteValue);
    $vote->save();

    $this->entityTypeManager()
      ->getViewBuilder($entityTypeId)
      ->resetCache([$entity]);

    $criteria = [
      'entity_type' => $entityTypeId,
      'entity_id' => $entityId,
      'value_type' => $vote_type_id,
    ];

    return new JsonResponse([
      'vote' => $voteValue,
      'message_type' => 'status',
      'operation' => 'voted',
      'message' => t('Your vote was added.'),
    ]);

  }

  /**
   * @param $entityTypeId
   * @param $entityId
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function resetVote($entityTypeId, $entityId, Request $request){
    $entity = $this->entityTypeManager()
      ->getStorage($entityTypeId)
      ->load($entityId);

    $vote_storage = $this->entityTypeManager()->getStorage('vote');

    $vote_type_id = 'points';

    $vote_storage->deleteUserVotes(
      $this->currentUser()->id(),
      $vote_type_id,
      $entityTypeId,
      $entityId
    );

    $this->entityTypeManager()
      ->getViewBuilder($entityTypeId)
      ->resetCache([$entity]);

    return new JsonResponse([
      'message_type' => 'status',
      'operation' => 'reset',
      'message' => t('Your vote was reset.'),
    ]);

  }

  /**
   * Checks if the currentUser is allowed to vote.
   *
   * @param string $entityTypeId
   *   The entity type ID.s
   * @param string $entityId
   *   The entity ID.
   *
   * @return \Drupal\Core\Access\AccessResult|\Drupal\Core\Access\AccessResultAllowed
   *   The access result.
   */
  public function voteAccess($entityTypeId, $entityId) {
    $entity = $this->entityTypeManager()->getStorage($entityTypeId)->load($entityId);
    // Check if user has permission to vote.
    if (!$this->canVote($this->currentUser())) {
      return AccessResult::forbidden();
    }
    else {
      return AccessResultAllowed::allowed();
    }
  }

}
