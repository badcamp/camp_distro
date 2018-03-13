<?php

namespace Drupal\camp_session\Plugin\votingapi_widget;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\votingapi\VoteStorageInterface;
use Drupal\votingapi_widgets\Plugin\VotingApiWidgetBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Assigns ownership of a node to a user.
 *
 * @VotingApiWidget(
 *   id = "session_vote",
 *   label = @Translation("Session Voting"),
 *   values = {
 *    -1 = @Translation("No"),
 *    0 = @Translation("Abstain"),
 *    1 = @Translation("Yes")
 *   },
 * )
 */
class SessionVoting extends VotingApiWidgetBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /** @var \Drupal\votingapi\VoteStorageInterface $voteStorage */
  protected $voteStorage;

  /** @var \Drupal\Core\Session\AccountProxyInterface $accountProxy */
  protected $accountProxy;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, VoteStorageInterface $voteStorage, AccountProxyInterface $accountProxy) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->voteStorage = $voteStorage;
    $this->accountProxy = $accountProxy;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // TODO: Implement create() method.
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager')->getStorage('vote'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getInitialVotingElement(array &$form) {

  }

  public function getVoteBreakdownCount($entity_id, $value){
    $query = $this->voteStorage->getQuery('AND');
    $query->condition('entity_type', 'node');
    $query->condition('entity_id', (int)$entity_id);
    $query->condition('value', $value);
    $results = $query->execute();
    return count($results);
  }

  public function getVoteSummary(ContentEntityInterface $vote) {
    $results = [];
    foreach($this->configuration['values'] AS $key => $label) {
      $count = $this->getVoteBreakdownCount($vote->getVotedEntityId(), $key);
      $results[] = [
        '#markup' => $this->t('@count @label', [ '@label' => $label, '@count' => $count])
      ];
    }
    return [
      '#theme' => 'item_list',
      '#items' => $results
    ];
  }

  /**
   * @param $vote
   * @param bool $account
   *
   * @return bool
   */
  public function canVote($vote, $account = FALSE) {
    $votes = $this->voteStorage->getUserVotes($this->accountProxy->id(), null, $vote->getVotedEntityType(), $vote->getVotedEntityId());
    return !(count($votes) > 0);
  }

}
