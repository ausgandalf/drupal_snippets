<?php

namespace Drupal\onehouse_base\Plugin\Action;

/**
 * Set user's bidder status expired.
 *
 * @Action(
 *   id = "action_user_set_bidder_status_expired",
 *   label = @Translation("Set bidder status expired"),
 *   type = "user"
 * )
 */
class SetBidderStatusExpired extends SetBidderStatus {

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
    $this->targetStatus = 4;
    $this->changeStatus($account);
  }

}
