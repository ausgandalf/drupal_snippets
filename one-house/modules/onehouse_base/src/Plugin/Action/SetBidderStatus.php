<?php

namespace Drupal\onehouse_base\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Set user's bidder status active.
 *
 * @Action(
 *   id = "action_user_set_bidder_status_active",
 *   label = @Translation("Set bidder status active"),
 *   type = "user"
 * )
 */
class SetBidderStatus extends ActionBase {

  /**
   * Target status to be changed to.
   *
   * @var int
   */
  public $targetStatus = NULL;

  /**
   * Core function to change status, will be used in inherited classes.
   */
  public function changeStatus($account = NULL) {
    if (is_null($account) || is_null($this->targetStatus)) {
      return;
    }
    // Skip action if their bidder status are already active.
    if ($account !== FALSE && $account->field_user_bidder_status->value != $this->targetStatus) {
      $account->set('field_user_bidder_status', $this->targetStatus);
      //
      // Set bidder status changed date
      // .
      $now = new DrupalDateTime();
      $now->setTimezone(new \DateTimezone(DateTimeItemInterface::STORAGE_TIMEZONE));
      $now_str = $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
      $account->set('field_user_bdr_stts_changed_on', $now_str);

      $account->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\user\UserInterface $object */
    $access = $object->status->access('edit', $account, TRUE)
      ->andIf($object->access('update', $account, TRUE));

    return $return_as_object ? $access : $access->isAllowed();
  }

}
