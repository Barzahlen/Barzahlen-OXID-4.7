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

class Unit_Barzahlen_BarzahlenPaymentGatewayTest extends OxidTestCase {

  /**
   * Testing payment execution with an expected success response.
   */
  public function testExecutePaymentUpdateSuccess() {

      $dAmount = 10.10;
      $oOrder = new oxOrder();
      $oOrder->oxorder__oxpaymenttype = new oxField('oxidbarzahlen');

      $oView = $this->getMock("barzahlen_payment_gateway", array("_connectBarzahlenApi"));
      $oView->expects($this->once())
            ->method('_connectBarzahlenApi')
            ->will($this->returnValue(new successRq));

      $this->assertTrue($oView->executePayment($dAmount, $oOrder));
  }

  /**
   * Testing payment execution with an expected failure response.
   */
  public function testExecutePaymentUpdateFailure() {

      $dAmount = 10.10;
      $oOrder = new oxOrder();
      $oOrder->oxorder__oxpaymenttype = new oxField('oxidbarzahlen');

      $oView = $this->getMock("barzahlen_payment_gateway", array("_connectBarzahlenApi"));
      $oView->expects($this->once())
            ->method('_connectBarzahlenApi')
            ->will($this->returnValue(new failureRq));

      $this->assertFalse($oView->executePayment($dAmount, $oOrder));
  }


  /**
   * Testing payment execution with a different payment method.
   */
  public function testExecutePaymentOtherPaymentMethod() {

      $dAmount = 24.95;
      $oOrder = new oxOrder();
      $oOrder->oxorder__oxpaymenttype = new oxField('oxideasypaying');

      $oView = $this->getMock("barzahlen_payment_gateway", array("_connectBarzahlenApi"));
      $oView->expects($this->never())
            ->method('_connectBarzahlenApi');

      $oView->executePayment($dAmount, $oOrder);
  }

  /**
   * Testing the creating of a Barzahlen_Api object.
   */
  public function testGetBarzahlenApi() {

    modConfig::setParameter("oxid", "a6f9bc61ce7aec5dabb7600636f5ce1d");

    $oView = $this->getProxyClass('barzahlen_payment_gateway');
    $oApi = $oView->_getBarzahlenApi(0);

    $this->assertAttributeEquals(SHOPID, '_shopId', $oApi);
    $this->assertAttributeEquals(PAYMENTKEY, '_paymentKey', $oApi);
    $this->assertAttributeEquals('de', '_language', $oApi);
    $this->assertAttributeEquals(true, '_sandbox', $oApi);
    $this->assertAttributeEquals(0, '_madeAttempts', $oApi);
  }
}
?>