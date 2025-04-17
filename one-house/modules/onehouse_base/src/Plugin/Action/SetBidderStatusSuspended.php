<?php

namespace Drupal\onehouse_base\Plugin\Action;

/**
 * Set user's bidder status suspended.
 *
 * @Action(
 *   id = "action_user_set_bidder_status_suspended",
 *   label = @Translation("Set bidder status suspended"),
 *   type = "user"
 * )
 */
class SetBidderStatusSuspended extends SetBidderStatus {

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
    $this->targetStatus = 4;
    $this->changeStatus($account);
  }

}
