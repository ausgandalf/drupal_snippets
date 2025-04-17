<?php

namespace Drupal\onehouse_base\Plugin\Action;

/**
 * Set user's bidder status none.
 *
 * @Action(
 *   id = "action_user_set_bidder_status_none",
 *   label = @Translation("Set bidder status none"),
 *   type = "user"
 * )
 */
class SetBidderStatusNone extends SetBidderStatus {

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
    $this->targetStatus = 0;
    $this->changeStatus($account);
  }

}
