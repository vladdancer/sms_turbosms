<?php
/**
 * @file
 * TurboSMS class file definition.
 *
 * @author: Vladyslav Moyseenko <vlad.dancer@gmail.com>
 * @author: Valentine Matsveiko <mve@drupalway.net>
 */

/**
 * Class TurboSMS.
 */
class TurboSMS {
  /**
   * Sign.
   *
   * @var string
   */
  private $sign = '';
  /**
   * Status data array.
   *
   * @var array
   */
  private $status = array();
  /**
   * User login table name.
   *
   * @var string
   */
  private $userLogin = 'table_name';

  /**
   * Basic constructor method.
   */
  public function __construct() {
    // Set the Sign value.
    $this->setSign(variable_get('sms_turbosms_sender', ''));
    // Set the default value to the status array.
    $this->setStatus();
    // Connect to the TurboSMS database.
    $this->connect();
  }

  /**
   * Set sign.
   *
   * @param string $sign
   *   Sign value.
   */
  private function setSign($sign) {
    $this->sign = $sign;
  }

  /**
   * Get sign.
   *
   * @return string
   *   Sign value.
   */
  public function getSign() {
    return $this->sign;
  }

  /**
   * Connect to the TurboSMS database.
   */
  private function connect() {
    try {
      db_set_active('sms_turbosms');
      Database::getConnection();
    }
    catch (Exception $e) {
      $this->setStatus(FALSE, 'Failed to connect to your database server. The server reports the following message: %error.<ul><li>Is the database server running?</li><li>Does the database exist, and have you entered the correct database name?</li><li>Have you entered the correct username and password?</li><li>Have you entered the correct database hostname?</li></ul>', array('%error', $e->getMessage()));
    }
  }

  /**
   * Disconnect from the TurboSMS database.
   */
  private function disconnect() {
    db_set_active();
  }

  /**
   * Sender method.
   *
   * @param string $number
   *   Telephone number.
   *
   * @param string $message
   *   Message to send.
   */
  public function send($number, $message) {
    if (empty($number)) {
      $this->setStatus(FALSE, 'Empty phone number: %number', array('%number', $number));
      return;
    }
    $fields = array(
      'number'  => $number,
      'sign'    => $this->sign,
      'message' => $message,
    );
    $transaction_id = db_insert($this->userLogin)->fields($fields)->execute();
    if (empty($transaction_id)) {
      $message = 'Something happened wrong while inserting new entry to "%table" table';
      $this->setStatus(FALSE, $message, array('%table', $this->userLogin));
    }
    else {
      $this->setStatus();
    }
    $this->disconnect();
  }

  /**
   * Set the status data.
   *
   * @param bool $status
   *   Status data.
   *
   * @param string $message
   *   Status message.
   *
   * @param array $variables
   *   Status variables.
   */
  public function setStatus($status = TRUE, $message = NULL, $variables = array()) {
    $this->status['status']    = $status;
    $this->status['message']   = $message;
    $this->status['variables'] = $variables;
  }

  /**
   * Get status.
   *
   * @return mixed
   *   Status data.
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Convert status data to string.
   *
   * @return string
   *   Status string.
   */
  public function __toString() {
    $status = $this->getStatus();
    return $status['status'] ? '0' : '1';
  }

}
