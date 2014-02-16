<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/
 *
 * @copyright   Copyright (c) 2012 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once getShopBasePath() . 'modules/barzahlen/api/loader.php';

class barzahlen_callback extends oxUBase {

  const LOGFILE = "barzahlen.log";
  const STATUS_OK = 200;
  const STATUS_BAD_REQUEST = 400;
  const STATE_PENDING = "pending";
  const STATE_PAID = "paid";
  const STATE_EXPIRED = "expired";
  const STATE_REFUND_COMPLETED = "refund_completed";
  const STATE_REFUND_EXPIRED = "refund_expired";

  private $_notification;
  private $_oOrder;

  public function render() {

    $this->_checkGetData();

    if($this->_notification->isValid()) {

      $this->_sendHeader(self::STATUS_OK);
      $this->_getOrder();

      if($this->_oOrder->oxorder__oxid === false) {
        $this->_logIpnError("Unable to load order.");
        return;
      }

      $this->_updateDatabase();
    }

    else {
      $this->_sendHeader(self::STATUS_BAD_REQUEST);
      return;
    }

    return 'page/shop/start.tpl';
  }

  /**
   * Creates the notification object and checks the received data.
   */
  protected function _checkGetData() {

    $oxConfig = oxConfig::getInstance();
    $sShopId = $oxConfig->getShopId();
    $sModule = oxConfig::OXMODULE_MODULE_PREFIX . 'barzahlen';

    $shopId = $oxConfig->getShopConfVar('shopId', $sShopId, $sModule);
    $notificationKey = $oxConfig->getShopConfVar('notificationKey', $sShopId, $sModule);

    $this->_notification = new Barzahlen_Notification($shopId, $notificationKey, $_GET);

    try {
      $this->_notification->validate();
    }
    catch (Exception $e) {
      $this->_logIpnError("Notification failed: " . $e);
    }
  }

  /**
   * Gets the requested order from the database.
   */
  protected function _getOrder() {

    $transaction = $this->_notification->getTransactionId() != null ? $this->_notification->getTransactionId() : $this->_notification->getOriginTransactionId();

    $rs = oxDb::getDb()->Execute("SELECT OXID FROM oxorder WHERE BZTRANSACTION = '". $transaction ."'");
    $this->_oOrder = oxNew("oxorder");
    $this->_oOrder->load($rs->fields[0]);
  }

  /**
   * Calls update mehtods depending on state value.
   */
  protected function _updateDatabase() {

    switch ($this->_notification->getState()) {

      case self::STATE_PAID:
      case self::STATE_EXPIRED:
        $this->_updatePayment();
        break;
      case self::STATE_REFUND_COMPLETED:
      case self::STATE_REFUND_EXPIRED:
        $this->_updateRefund();
        break;
      default:
        $this->_logIpnError("Unable to handle notification state. ". $this->_notification->getState());
        break;
    }
  }

  /**
   * Looks up and updates orders for payment notifications.
   *
   * @return boolean
   */
  protected function _updatePayment() {

    if($this->_notification->getOrderId() != null && $this->_oOrder->oxorder__oxordernr->value != $this->_notification->getOrderId()) {
      $this->_logIpnError("Order ID not valid for " .$this->_notification->getTransactionId());
      return false;
    }

    if($this->_oOrder->oxorder__oxtotalordersum->value != $this->_notification->getAmount()) {
      $this->_logIpnError("Transaction amount not valid for " .$this->_notification->getTransactionId());
      return false;
    }

    if($this->_oOrder->oxorder__oxcurrency->value != $this->_notification->getCurrency()) {
      $this->_logIpnError("Transaction currency not valid for " .$this->_notification->getTransactionId());
      return false;
    }

    if($this->_oOrder->oxorder__bzstate->value != self::STATE_PENDING) {
      $this->_logIpnError("Unable to change state of transaction " .$this->_notification->getTransactionId());
      return false;
    }

    if($this->_notification->getState() == self::STATE_PAID) {
      $this->_oOrder->oxorder__oxpaid->setValue( date( "Y-m-d H:i:s", oxUtilsDate::getInstance()->getTime() ) );
    }
    elseif($this->_notification->getState() == self::STATE_EXPIRED) {
      $this->_oOrder->oxorder__oxstorno = new oxField(1);
    }

    $this->_oOrder->oxorder__bzstate = new oxField($this->_notification->getState());
    $this->_oOrder->save();
    return true;
  }

  /**
   * Looks up and updates orders for refund notifications.
   *
   * @return boolean
   */
  protected function _updateRefund() {

    if($this->_notification->getOriginOrderId() != null && $this->_oOrder->oxorder__oxordernr->value != $this->_notification->getOriginOrderId()) {
      $this->_logIpnError("Order ID not valid for " .$this->_notification->getRefundTransactionId());
      return false;
    }

    $refunds = unserialize(str_replace("&quot;", "\"", $this->_oOrder->oxorder__bzrefunds->value));

    foreach($refunds as $key => $refund) {

      if($refund['refundid'] == $this->_notification->getRefundTransactionId()) {

        if($refund['amount'] != $this->_notification->getAmount()) {
          $this->_logIpnError("Refund amount not valid for " .$this->_notification->getRefundTransactionId());
          return false;
        }

        if($this->_oOrder->oxorder__oxcurrency->value != $this->_notification->getCurrency()) {
          $this->_logIpnError("Refund currency not valid for " .$this->_notification->getRefundTransactionId());
          return false;
        }

        if($refund['state'] != self::STATE_PENDING) {
          $this->_logIpnError("Unable to change state of refund " .$this->_notification->getRefundTransactionId());
          return false;
        }

        $refunds[$key]['state'] = str_replace("refund_", "", $this->_notification->getState());
        $this->_oOrder->oxorder__bzrefunds = new oxField(serialize($refunds));
        $this->_oOrder->save();
        return true;
      }
    }
    $this->_logIpnError("Refund not found for given ID: " .$this->_notification->getRefundTransactionId());
    return false;
  }

  /**
   * Logs error message along with the received data.
   *
   * @param string $message
   */
  protected function _logIpnError($message) {
    $data = $this->_notification->getNotificationArray() == null ? $_GET : $this->_notification->getNotificationArray();
    $message .= ' ' .serialize($data);
    oxUtils::getInstance()->writeToLog(date('c') . ' ' . $message . "\r\r", self::LOGFILE);
  }

  /**
   * Sends out a response header after the notification was checked.
   *
   * @param type $code
   */
  protected function _sendHeader($code) {

    switch ($code) {

      case self::STATUS_OK:
        header("HTTP/1.1 200 OK");
        header("Status: 200 OK");
        break;

      case self::STATUS_BAD_REQUEST:
        header("HTTP/1.1 400 Bad Request");
        header("Status: 400 Bad Request");
        break;
    }
  }
}
?>