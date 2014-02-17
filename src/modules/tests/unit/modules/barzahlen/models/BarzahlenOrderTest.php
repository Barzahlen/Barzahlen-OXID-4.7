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

class Unit_Barzahlen_BarzahlenOrderTest extends OxidTestCase {


  /**
   * Testing the extended finalization of an order where the transaction id is
   * add as well as a transaction state.
   */
  public function testFinalizeOrder() {

    $oBasket = new oxBasket;
    $oUser = new oxUser;

    $oView = $this->getMock("barzahlen_order", array("_connectBarzahlenApi"));
    $oView->expects($this->once())
          ->method('_connectBarzahlenApi');
    $oView->oxorder__oxpaymenttype = new oxField('oxidbarzahlen');

    $oView->finalizeOrder($oBasket, $oUser);
  }

  /**
   * Testing the creating of a Barzahlen_Api object.
   */
  public function testGetBarzahlenApi() {

    $oView = $this->getProxyClass('barzahlen_order');
    $oApi = $oView->_getBarzahlenApi();

    $this->assertAttributeEquals(SHOPID, '_shopId', $oApi);
    $this->assertAttributeEquals(PAYMENTKEY, '_paymentKey', $oApi);
    $this->assertAttributeEquals('de', '_language', $oApi);
    $this->assertAttributeEquals(true, '_sandbox', $oApi);
    $this->assertAttributeEquals(0, '_madeAttempts', $oApi);
  }
}
?>