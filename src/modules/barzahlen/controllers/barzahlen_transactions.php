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

/**
 * Transaction View Controller for Administration Area
 * Provides a quick overview of the Barzahlen transaction and refund status
 * for a choosen order.
 */
class Barzahlen_Transactions extends oxAdminView {

  /**
   * Log file
   */
  const LOGFILE = "barzahlen.log";

  /**
   * Template for order overview tab
   *
   * @var string
   */
  protected $_sThisTemplate = 'barzahlen_transactions.tpl';

 /**
   * Module identifier
   *
   * @var string
   */
  protected $_sModuleId = 'barzahlen';

  /**
   * Executes parent method parent::render() and prepares everything for the
   * payment table with all Barzahlen information.
   *
   * @return string with template file
   */
  public function render() {

    parent::render();

    $oOrder = $this->getEditObject();
    $this->_aViewData["payment"] = $oOrder->oxorder__oxpaymenttype->value;
    $this->_aViewData["transactionId"] = $oOrder->oxorder__bztransaction->value;
    $this->_aViewData["state"] = 'BZ__STATE_' . strtoupper($oOrder->oxorder__bzstate->value);
    $this->_aViewData["currency"] = $oOrder->oxorder__oxcurrency->value;

    if($oOrder->oxorder__bzstate->value == 'paid') {
      if($oOrder->oxorder__bzrefunds->value != "") {
        $aRefundData = unserialize(str_replace("&quot;", "\"", $oOrder->oxorder__bzrefunds->value));
        foreach($aRefundData as $iKey => $aRefund) {
          $aRefundData[$iKey]['state'] = 'BZ__STATE_' . strtoupper($aRefund['state']);
        }
        $this->_aViewData["refunds"] = $aRefundData;
      }
      $this->_aViewData["refundable"] = $this->_getRefundable();
    }

    return $this->_sThisTemplate;
  }

  /**
   * Loads the corresponding order object.
   *
   * @return object
   */
  public function getEditObject() {

    $soxId = $this->getEditObjectId();

    if ($this->_oEditObject === null && isset($soxId) && $soxId != "-1")
    {
      $this->_oEditObject = oxNew("oxorder");
      $this->_oEditObject->load($soxId);
    }

    return $this->_oEditObject;
  }

  /**
   * Calculates the still refundable amount.
   *
   * @return float
   */
  protected function _getRefundable() {

    $oOrder = $this->getEditObject();
    $aRefundData = unserialize(str_replace("&quot;", "\"", $oOrder->oxorder__bzrefunds->value));

    $fRefundable = $oOrder->oxorder__oxtotalordersum->value;
    if($aRefundData) {
      foreach($aRefundData as $aRefund) {
        $fRefundable -= $aRefund['state'] != 'expired' ? $aRefund['amount'] : 0;
      }
    }
    return round($fRefundable,2);
  }

  /**
   * Prepares payment slip resend.
   */
  public function resendPaymentSlip() {

    $oOrder = $this->getEditObject();
    $sTransactionId = $oOrder->oxorder__bztransaction->value;
    $this->_resendSlip($sTransactionId, 'payment');
  }

  /**
   * Prepares refund slip resend.
   */
  public function resendRefundSlip() {

    $sRefundId = filter_var($_POST['refundId'], FILTER_SANITIZE_NUMBER_INT);
    $this->_resendSlip($sRefundId, 'refund');
  }

  /**
   * Resends the requested payment / refund slip and sets the info text.
   *
   * @param integer $id (refund) transaction id
   * @param string $type slip type
   */
  protected function _resendSlip($id, $sType) {

    $oRequest = new Barzahlen_Request_Resend($id);
    $oResend = $this->_connectBarzahlenApi($oRequest);

    if($oResend->isValid()) {
      $this->_aViewData["info"] = array("class" => "messagebox", "message" => "BZ__RESEND_".strtoupper($sType)."_SUCCESS");
    }
    else {
      $this->_aViewData["info"] = array("class" => "errorbox", "message" => "BZ__RESEND_".strtoupper($sType)."_ERROR");
    }
  }

  /**
   * Performs refund requests and set info texts with the result.
   */
  public function requestRefund() {

    $oOrder = $this->getEditObject();
    $sTransactionId = $oOrder->oxorder__bztransaction->value;
    $fAmount = round(filter_var(str_replace(",", ".", $_POST['refund_amount']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),2);

    if($fAmount > $this->_getRefundable()) {
      $this->_aViewData["info"] = array("class" => "errorbox", "message" => "BZ__REFUND_TOO_HIGH");
      return;
    }

    $oRequest = new Barzahlen_Request_Refund($sTransactionId, $fAmount, $oOrder->oxorder__oxcurrency->value);
    $oRefund = $this->_connectBarzahlenApi($oRequest);

    if($oRefund->isValid()){
      $aRefundData = unserialize(str_replace("&quot;", "\"", $oOrder->oxorder__bzrefunds->value));

      if($aRefundData === false) {
        $aRefundData = array();
      }

      $aNewRefund = array("refundid" => $oRefund->getRefundTransactionId(),
                          "amount" => $fAmount,
                          "state" => "pending");

      $aRefundData[] = $aNewRefund;

      $oOrder->oxorder__bzrefunds = new oxField(serialize($aRefundData));
      $oOrder->save();
      $this->_aViewData["info"] = array("class" => "messagebox", "message" => "BZ__REFUND_SUCCESS");
    }
    else {
      $this->_aViewData["info"] = array("class" => "errorbox", "message" => "BZ__REFUND_ERROR");
    }
  }

  /**
   * Performs the api request.
   *
   * @param Barzahlen_Request $oRequest request object
   * @return updated request object
   */
  protected function _connectBarzahlenApi($oRequest) {

    $oApi = $this->_getBarzahlenApi();

    try {
      $oApi->handleRequest($oRequest);
    }
    catch (Exception $e) {
      oxUtils::getInstance()->writeToLog(date('c') . " API/Refund failed: " . $e . "\r\r", self::LOGFILE);
    }

    return $oRequest;
  }

  /**
   * Generates a Barzahlen API object for the request.
   *
   * @return Barzahlen_Api
   */
  protected function _getBarzahlenApi() {

    $oxConfig = oxConfig::getInstance();
    $sShopId = $oxConfig->getShopId();
    $sModule = oxConfig::OXMODULE_MODULE_PREFIX . $this->_sModuleId;

    $sBzShopId = $oxConfig->getShopConfVar('bzShopId', $sShopId, $sModule);
    $sPaymentKey = $oxConfig->getShopConfVar('bzPaymentKey', $sShopId, $sModule);
    $blSandbox = $oxConfig->getShopConfVar('bzSandbox', $sShopId, $sModule);
    $blDebug = $oxConfig->getShopConfVar('bzDebug', $sShopId, $sModule);

    $oApi = new Barzahlen_Api($sBzShopId, $sPaymentKey, $blSandbox);
    $oApi->setDebug($blDebug, self::LOGFILE);
    $oApi->setLanguage($this->_getOrderLanguage());
    return $oApi;
  }

  /**
   * Gets the order language code.
   *
   * @return string
   */
  protected function _getOrderLanguage() {

    $oOrder = $this->getEditObject();
    $oxConfig = oxConfig::getInstance();
    $aLgConfig = $oxConfig->getShopConfVar('aLanguageParams');

    return array_search($oOrder->getOrderLanguage(), $aLgConfig);
  }
}