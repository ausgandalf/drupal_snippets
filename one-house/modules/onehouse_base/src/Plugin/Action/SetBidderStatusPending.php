<?php

namespace Drupal\onehouse_base\Plugin\Action;

/**
 * Set user's bidder status pending.
 *
 * @Action(
 *   id = "action_user_set_bidder_status_pending",
 *   label = @Translation("Set bidder status pending"),
 *   type = "user"
 * )
 */
class SetBidderStatusPending extends SetBidderStatus {

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
    $this->targetStatus = 1;
    $this->changeStatus($account);
  }

}
