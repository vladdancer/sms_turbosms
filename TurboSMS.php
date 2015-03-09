<?php
/**
 * @file
 * Contains TurboSMS.php.
 * @author: Vladyslav Moyseenko <vlad.dancer@gmail.com>
 * @since: 3/6/15 5:51 AM
 */

/**
 * Class TurboSMS.
 */
final class TurboSMS {
  private $_sign;
  private $_status;

  /**
   * Basic constructor method.
   */
  public function __construct() {
    $this->connect();
  }

  /**
   * Connect to the TurboSMS database.
   *
   * @return array|bool
   */
  private function connect() {
    try {
      db_set_active('sms_turbosms');
      Database::getConnection();
      return TRUE;
    }
    catch (Exception $e) {
      $result = array(
        'status'    => FALSE,
        'message'   => 'Failed to connect to your database server. The server reports the following message: %error.<ul><li>Is the database server running?</li><li>Does the database exist, and have you entered the correct database name?</li><li>Have you entered the correct username and password?</li><li>Have you entered the correct database hostname?</li></ul>',
        'variables' => array('%error', $e->getMessage()),
      );
      return $result;
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
   *
   * @return array
   *   Result array.
   *
   * @throws \Exception
   */
  public function send($number, $message) {
    if (empty($number)) {
      return array(
        'status'    => FALSE,
        'message'   => 'Empty phone number: %number',
        'variables' => array('%number', $number),
      );
    }

    $transaction_id = db_insert($user_login)
      ->fields(array(
        'number'  => $number,
        'sign'    => variable_get("sms_turbosms_sender", ""),
        'message' => $message,
      ))
      ->execute();

    if (!empty($transaction_id)) {
      $result = array('status' => TRUE);
    }
    else {
      $result = array(
        'status'    => FALSE,
        'message'   => 'Something happened wrong while inserting new entry to "%table" table',
        'variables' => array('%table', $user_login),
      );
    }
    $this->disconnect();
    return $result;
  }

  public function get() {}

  public function setStatus($status, $message = NULL, $variables = NULL) {
    $this->_status['status'] = $status;
    if ($message) {
      $this->_status['message'] = $message;
      if ($variables) {
        $this->_status['variables'] = $variables;
      }
    }
  }

  public function getStatus() {
    return $this->_status;
  }

  public function __toString() {
    $status = $this->getStatus();
    return $status['status'] ? '0' : '1';
  }
}
