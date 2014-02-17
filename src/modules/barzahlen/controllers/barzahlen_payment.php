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

/**
 * Payment View Controller Extension
 * Integrates the Barzahlen payment method information for the payment
 * selection page depending on sandbox setting.
 */
class barzahlen_payment extends barzahlen_payment_parent {

  /**
   * Module identifier
   *
   * @var string
   */
  protected $_sModuleId = "barzahlen";

  /**
   * Executes parent method parent::render().
   */
  public function render() {

    return parent::render();
  }

  /**
   * Returns the sandbox setting.
   *
   * @return boolean
   */
  public function getSandbox() {

    $oxConfig = oxConfig::getInstance();
    $sShopId = $oxConfig->getShopId();
    $sModule = oxConfig::OXMODULE_MODULE_PREFIX . $this->_sModuleId;
    return $oxConfig->getShopConfVar('bzSandbox', $sShopId, $sModule);
  }

  /**
   * Generates the html code with the retail partner logos.
   *
   * @return string
   */
  public function getPartner() {

    $sPartner = '&nbsp;';

    for($i = 1; $i <= 10; $i++) {
      $count = str_pad($i,2,"0",STR_PAD_LEFT);
      $sPartner .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_'.$count.'.png" alt="" style="vertical-align: middle;" height="25px" />';
    }

    return $sPartner;
  }
}
