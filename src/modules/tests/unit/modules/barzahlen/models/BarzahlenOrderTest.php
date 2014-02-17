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

class Unit_Barzahlen_BarzahlenOrderTest extends OxidTestCase
{
    /**
     * Set everything that is needed for the testing up.
     */
    public function setUp()
    {
        $this->restore = false;
    }

    /**
     * Testing the extended finalization of an order where the transaction id is
     * add as well as a transaction state.
     */
    public function testFinalizeOrder()
    {
        $oBasket = new oxBasket;
        $oUser = new oxUser;

        $oOrder = $this->getMock("bz_barzahlen_order", array("_connectBarzahlenApi"));
        $oOrder->expects($this->once())
                ->method('_connectBarzahlenApi');
        $oOrder->oxorder__oxpaymenttype = new oxField('oxidbarzahlen');

        $oOrder->finalizeOrder($oBasket, $oUser);
    }

    /**
     * Testing the downstream payment slip cancelation.
     */
    public function testCancelOrderPendingTransaction()
    {
        $oOrder = $this->getMock("bz_barzahlen_order", array("_connectBarzahlenApi"));
        $oOrder->expects($this->once())
                ->method('_connectBarzahlenApi')
                ->will($this->returnValue(new successRq));
        $oOrder->load('2a289076590d790c6d50aabd6f5974eb');
        $oOrder->cancelOrder();
        $this->assertEquals('canceled', $oOrder->oxorder__bzstate->rawValue);
        $this->restore = true;
    }

    /**
     * Testing that when canceling an order with a paid payment slip, it will not
     * be canceled.
     */
    public function testCancelOrderPaidTransaction()
    {
        $oOrder = $this->getMock("bz_barzahlen_order", array("_connectBarzahlenApi"));
        $oOrder->expects($this->never())
                ->method('_connectBarzahlenApi');
        $oOrder->load('c07fabf21fc080a3d2f81d951a405c37');
        $oOrder->cancelOrder();
        $this->assertEquals('paid', $oOrder->oxorder__bzstate->rawValue);
    }

    /**
     * Testing the downstream payment slip cancelation.
     */
    public function testDeleteOrderPendingTransaction()
    {
        $oOrder = $this->getMock("bz_barzahlen_order", array("_connectBarzahlenApi"));
        $oOrder->expects($this->once())
                ->method('_connectBarzahlenApi')
                ->will($this->returnValue(new successRq));
        $oOrder->load('2a289076590d790c6d50aabd6f5974eb');
        $oOrder->delete();
        $this->assertEquals(null, $oOrder->load('2a289076590d790c6d50aabd6f5974eb'));
    }

    /**
     * Testing the creating of a Barzahlen_Api object.
     */
    public function testGetBarzahlenApi()
    {
        $oOrder = $this->getProxyClass('bz_barzahlen_order');
        $oApi = $oOrder->_getBarzahlenApi();

        $this->assertAttributeEquals(SHOPID, '_shopId', $oApi);
        $this->assertAttributeEquals(PAYMENTKEY, '_paymentKey', $oApi);
        $this->assertAttributeEquals('de', '_language', $oApi);
        $this->assertAttributeEquals(true, '_sandbox', $oApi);
        $this->assertAttributeEquals(0, '_madeAttempts', $oApi);
    }

    /**
     * Unset everything before the next test.
     */
    protected function tearDown()
    {
        if ($this->restore) {
            $dbMaintenance = new dbMaintenance;
            $dbMaintenance->restoreDB();
        }
    }
}
