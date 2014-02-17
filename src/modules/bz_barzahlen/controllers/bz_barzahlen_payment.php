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

/**
 * Payment View Controller Extension
 * Integrates the Barzahlen payment method information for the payment
 * selection page depending on sandbox setting.
 */
class bz_barzahlen_payment extends bz_barzahlen_payment_parent
{
    /**
     * Currencies supported by Barzahlen
     *
     * @var array
     */
    private $_supportedCurrencies = array('EUR');

    /**
     * Module identifier
     *
     * @var string
     */
    protected $_sModuleId = "bz_barzahlen";

    /**
     * Executes parent method parent::render().
     */
    public function render()
    {
        return parent::render();
    }

    /**
     * Returns the sandbox setting.
     *
     * @return boolean
     */
    public function getSandbox()
    {
        $oxConfig = $this->getConfig();
        $sShopId = $oxConfig->getShopId();
        $sModule = oxConfig::OXMODULE_MODULE_PREFIX . $this->_sModuleId;
        return $oxConfig->getShopConfVar('bzSandbox', $sShopId, $sModule);
    }

    /**
     * Checks if current shop currency is support by Barzahlen.
     *
     * @return boolean
     */
    public function checkCurrency()
    {
        $oxConfig = $this->getConfig();
        $oCurrency = $oxConfig->getActShopCurrencyObject();
        return in_array($oCurrency->name, $this->_supportedCurrencies);
    }
}
