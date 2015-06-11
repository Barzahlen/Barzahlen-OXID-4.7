<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once getShopBasePath() . 'modules/bz_barzahlen/api/loader.php';

/**
 * Extends Payment Gateway
 * Prepares and performs the payment request in order the receive the payment
 * slip for the customer. Recieved information are saved along with the order.
 */
class bz_barzahlen_payment_gateway extends bz_barzahlen_payment_gateway_parent
{
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
     * Executes payment, returns true on success.
     *
     * @param double $dAmount Goods amount
     * @param object &$oOrder User ordering object
     * @return bool
     */
    public function executePayment($dAmount, &$oOrder)
    {
        if ($oOrder->oxorder__oxpaymenttype->value != 'oxidbarzahlen') {
            return parent::executePayment($dAmount, $oOrder);
        }

        $this->_sLastError = "barzahlen";
        $oCountry = oxNew("oxcountry");
        $oCountry->load($oOrder->oxorder__oxbillcountryid->rawValue);

        $sCustomerEmail = $oOrder->oxorder__oxbillemail->rawValue;
        $sCustomerStreetNr = $oOrder->oxorder__oxbillstreet->rawValue . ' ' . $oOrder->oxorder__oxbillstreetnr->rawValue;
        $sCustomerZipcode = $oOrder->oxorder__oxbillzip->rawValue;
        $sCustomerCity = $oOrder->oxorder__oxbillcity->rawValue;
        $sCustomerCountry = $oCountry->oxcountry__oxisoalpha2->rawValue;
        $dAmount = $oOrder->oxorder__oxtotalordersum->value;
        $sCurrency = $oOrder->oxorder__oxcurrency->rawValue;

        $oRequest = new Barzahlen_Request_Payment($sCustomerEmail, $sCustomerStreetNr, $sCustomerZipcode, $sCustomerCity, $sCustomerCountry, $dAmount, $sCurrency);
        $oPayment = $this->_connectBarzahlenApi($oRequest, $oOrder->getOrderLanguage());

        if ($oPayment->isValid()) {
            $this->getSession()->setVariable('barzahlenPaymentSlipLink', (string) $oPayment->getPaymentSlipLink());
            $this->getSession()->setVariable('barzahlenInfotextOne', (string) $oPayment->getInfotext1());
            $oOrder->oxorder__bztransaction = new oxField((int) $oPayment->getTransactionId());
            $oOrder->oxorder__bzstate = new oxField('pending');
            $oOrder->save();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Performs the api request.
     *
     * @param Barzahlen_Request $oRequest request object
     * @param integer $iOrderLang order language id
     * @return updated request object
     */
    protected function _connectBarzahlenApi($oRequest, $iOrderLang)
    {
        $oApi = $this->_getBarzahlenApi($iOrderLang);

        try {
            $oApi->handleRequest($oRequest);
        } catch (Exception $e) {
            oxRegistry::getUtils()->writeToLog(date('c') . " API/Refund failed: " . $e . "\r\r", self::LOGFILE);
        }

        return $oRequest;
    }

    /**
     * Prepares a Barzahlen API object for the payment request.
     *
     * @param integer $iOrderLang order language id
     * @return Barzahlen_Api
     */
    protected function _getBarzahlenApi($iOrderLang)
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
        $oApi->setLanguage($this->_getOrderLanguage($iOrderLang));
        $oApi->setUserAgent('OXID v' . $oxConfig->getVersion() .  ' / Plugin v1.2.1');
        return $oApi;
    }

    /**
     * Gets the order language code.
     *
     * @param integer $iOrderLang order language id
     * @return string
     */
    protected function _getOrderLanguage($iOrderLang)
    {
        $aLgConfig = $this->getConfig()->getShopConfVar('aLanguageParams');
        return array_search($iOrderLang, $aLgConfig);
    }
}
