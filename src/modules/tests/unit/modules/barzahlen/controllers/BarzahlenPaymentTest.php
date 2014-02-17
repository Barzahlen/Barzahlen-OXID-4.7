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

    $oView = new bz_barzahlen_payment;

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
    $sandbox = $oxConfig->getShopConfVar('bzSandbox', $oxConfig->getShopId, oxConfig::OXMODULE_MODULE_PREFIX . 'bz_barzahlen');

    $oView = new bz_barzahlen_payment;

    $this->assertEquals($sandbox, $oView->getSandbox());
  }

  /**
   * Testing that the standard currency is valid.
   */
  public function testCheckCurrency() {

    $oView = new bz_barzahlen_payment;
    $this->assertEquals(true, $oView->checkCurrency());
  }
}