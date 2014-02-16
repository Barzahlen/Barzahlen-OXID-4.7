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

require_once dirname(__FILE__) . '/../api/loader.php';

class barzahlen_order extends barzahlen_order_parent {

  protected $_sModuleId = "barzahlen";
  const LOGFILE = "barzahlen.log";

  /**
   * Expands order finalization for Barzahlen payments to update transaction with order id.
   *
   * @param oxBasket $oBasket Shopping basket object
   * @param object $oUser Current user object
   * @param bool $blRecalculatingOrder Order recalculation
   *
   * @return integer
   */
  public function finalizeOrder( oxBasket $oBasket, $oUser, $blRecalculatingOrder = false ) {

    $parent = parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);

    if ($this->oxorder__oxpaymenttype->value == 'oxidbarzahlen') {
      $transactionId = $this->oxorder__bztransaction->value;
      $orderId = $this->oxorder__oxordernr->value;

      $api = $this->_getBarzahlenApi();
      $update = new Barzahlen_Request_Update($transactionId, $orderId);

      try {
        $api->handleRequest($update);
      }
      catch (Exception $e) {
        oxUtils::getInstance()->writeToLog(date('c') . " Order ID update failed: " . $e . "\r\r", self::LOGFILE);
      }
    }

    return $parent;
  }

  /**
   * Prepares a Barzahlen API object for the payment request.
   *
   * @return Barzahlen_Api
   */
  protected function _getBarzahlenApi() {

    $oxConfig = oxConfig::getInstance();
    $sShopId = $oxConfig->getShopId();
    $sModule = oxConfig::OXMODULE_MODULE_PREFIX . $this->_sModuleId;

    $shopId = $oxConfig->getShopConfVar('shopId', $sShopId, $sModule);
    $paymentKey = $oxConfig->getShopConfVar('paymentKey', $sShopId, $sModule);
    $sandbox = $oxConfig->getShopConfVar('sandbox', $sShopId, $sModule);
    $debug = $oxConfig->getShopConfVar('debug', $sShopId, $sModule);

    $api = new Barzahlen_Api($shopId, $paymentKey, $sandbox);
    $api->setDebug($debug, self::LOGFILE);
    return $api;
  }
}
?>