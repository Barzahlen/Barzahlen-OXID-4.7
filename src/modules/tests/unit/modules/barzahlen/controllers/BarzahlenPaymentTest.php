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

class Unit_Barzahlen_BarzahlenPaymentTest extends OxidTestCase {

  /**
   * Lets run the render method.
   */
  public function testRender() {

    oxTestModules::addFunction('oxUtils', 'redirect', '{throw new Exception("REDIRECT");}');

    $oView = new barzahlen_payment;

    try {
      $oView->render();
    }
    catch (Exception $e) {
      $this->assertEquals("REDIRECT", $e->getMessage());
    }
  }

  /**
   * Testing that the right sandbox value is returned.
   */
  public function testGetSandbox() {

    $oxConfig = oxConfig::getInstance();
    $sandbox = $oxConfig->getShopConfVar('bzSandbox', $oxConfig->getShopId, oxConfig::OXMODULE_MODULE_PREFIX . 'barzahlen');

    $oView = new barzahlen_payment;

    $this->assertEquals($sandbox, $oView->getSandbox());
  }

  /**
   * Testing the return html code for displaying the Barzahlen retail partners.
   */
  public function testGetPartner() {

    $oView = new barzahlen_payment;

    $partners = '&nbsp;';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_01.png" alt="" style="vertical-align: middle;" height="25px" />';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_02.png" alt="" style="vertical-align: middle;" height="25px" />';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_03.png" alt="" style="vertical-align: middle;" height="25px" />';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_04.png" alt="" style="vertical-align: middle;" height="25px" />';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_05.png" alt="" style="vertical-align: middle;" height="25px" />';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_06.png" alt="" style="vertical-align: middle;" height="25px" />';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_07.png" alt="" style="vertical-align: middle;" height="25px" />';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_08.png" alt="" style="vertical-align: middle;" height="25px" />';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_09.png" alt="" style="vertical-align: middle;" height="25px" />';
    $partners .= '<img src="http://cdn.barzahlen.de/images/barzahlen_partner_10.png" alt="" style="vertical-align: middle;" height="25px" />';

    $this->assertEquals($partners, $oView->getPartner());
  }
}
?>