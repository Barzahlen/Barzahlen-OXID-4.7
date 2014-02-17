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
 * @copyright   Copyright (c) 2013 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once getShopBasePath() . 'modules/bz_barzahlen/api/loader.php';

/**
 * Extends Order manager
 * Adds Barzahlen payment information to order on finalization.
 */
class bz_barzahlen_order extends bz_barzahlen_order_parent
{
    /**
     * Transaction status codes.
     */
    const STATE_PENDING = "pending";
    const STATE_CANCELED = "canceled";

    /**
     * Log file
     */
    const LOGFILE = "barzahlen.log";

    /**
     * Module identifier
     *
     * @var string
     */
    protected $_sModuleId = 'bz_barzahlen';

    /**
     * Expands order finalization for Barzahlen payments to update transaction with order id.
     *
     * @param oxBasket $oBasket Shopping basket object
     * @param object $oUser Current user object
     * @param bool $blRecalculatingOrder Order recalculation
     *
     * @return integer
     */
    public function finalizeOrder(oxBasket $oBasket, $oUser, $blRecalculatingOrder = false)
    {
        $iParent = parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);

        if ($this->oxorder__oxpaymenttype->value == 'oxidbarzahlen') {

            $sTransactionId = $this->oxorder__bztransaction->value;
            $sOrderId = $this->oxorder__oxordernr->value;

            $oRequest = new Barzahlen_Request_Update($sTransactionId, $sOrderId);
            $this->_connectBarzahlenApi($oRequest);
        }

        return $iParent;
    }

    /**
     * Extends the order cancelation to cancel pending Barzahlen payment slips
     * at the same time.
     */
    public function cancelOrder()
    {
        parent::cancelOrder();

        if ($this->oxorder__oxpaymenttype->value == 'oxidbarzahlen' && $this->oxorder__bzstate->value == self::STATE_PENDING) {

            $sTransactionId = $this->oxorder__bztransaction->value;

            $oRequest = new Barzahlen_Request_Cancel($sTransactionId);
            $cancel = $this->_connectBarzahlenApi($oRequest);

            if ($cancel->isValid()) {
                $this->oxorder__bzstate = new oxField(self::STATE_CANCELED);
                $this->save();
            }
        }
    }

    /**
     * Extends the order deletion to cancel pending Barzahlen payment slips
     * at the same time.
     *
     * @param string $sOxId Ordering ID (default null)
     * @return bool
     */
    public function delete($sOxId = null)
    {
        if ($sOxId) {
            if (!$this->load($sOxId)) {
                return false;
            }
        } elseif (!$sOxId) {
            $sOxId = $this->getId();
            $this->load($sOxId);
        }

        if ($this->oxorder__oxpaymenttype->value == 'oxidbarzahlen' && $this->oxorder__bzstate->value == self::STATE_PENDING) {

            $sTransactionId = $this->oxorder__bztransaction->value;

            $oRequest = new Barzahlen_Request_Cancel($sTransactionId);
            $cancel = $this->_connectBarzahlenApi($oRequest);

            if ($cancel->isValid()) {
                $this->oxorder__bzstate = new oxField(self::STATE_CANCELED);
                $this->save();
            }
        }

        return parent::delete($sOxId);
    }

    /**
     * Performs the api request.
     *
     * @param Barzahlen_Request $oRequest request object
     */
    protected function _connectBarzahlenApi($oRequest)
    {
        $oApi = $this->_getBarzahlenApi();

        try {
            $oApi->handleRequest($oRequest);
        } catch (Exception $e) {
            oxRegistry::getUtils()->writeToLog(date('c') . " API connection failed: " . $e . "\r\r", self::LOGFILE);
        }

        return $oRequest;
    }

    /**
     * Prepares a Barzahlen API object for the payment request.
     *
     * @return Barzahlen_Api
     */
    protected function _getBarzahlenApi()
    {
        $oxConfig = $this->getConfig();
        $sShopId = $oxConfig->getShopId();
        $sModule = oxConfig::OXMODULE_MODULE_PREFIX . $this->_sModuleId;

        $sBzShopId = $oxConfig->getShopConfVar('bzShopId', $sShopId, $sModule);
        $sPaymentKey = $oxConfig->getShopConfVar('bzPaymentKey', $sShopId, $sModule);
        $blSandbox = $oxConfig->getShopConfVar('bzSandbox', $sShopId, $sModule);
        $blDebug = $oxConfig->getShopConfVar('bzDebug', $sShopId, $sModule);

        $oApi = new Barzahlen_Api($sBzShopId, $sPaymentKey, $blSandbox);
        $oApi->setDebug($blDebug, self::LOGFILE);
        return $oApi;
    }
}
