<?php

namespace Drupal\onehouse_base\Plugin\Action;

/**
 * Set user's bidder status active.
 *
 * @Action(
 *   id = "action_user_set_bidder_status_active",
 *   label = @Translation("Set bidder status active"),
 *   type = "user"
 * )
 */
class SetBidderStatusActive extends SetBidderStatus {

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
    $this->targetStatus = 2;
    $this->changeStatus($account);
  }

}
