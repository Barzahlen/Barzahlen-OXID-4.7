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
 * Order Fixtures (order_id - state)
 *
 * 1 - pending
 * 2 - paid
 * 3 - expired
 * 4 - paid, one pending refund
 * 5 - paid, one pending refund, one completed refund
 * 6 - paid, two completed refunds (total amount)
 */
class Unit_Barzahlen_BarzahlenCallbackTest extends OxidTestCase {

  /**
   * Testing a valid paid notification and the usage of handle methods.
   */
  public function testRenderValidPaid() {

    $handler = $this->getMock('bz_barzahlen_update_handler', array('checkData', 'getState', 'updatePayment', 'updateRefund'));

    $handler->expects($this->once())
            ->method('checkData')
            ->will($this->returnValue(true));

    $handler->expects($this->once())
            ->method('getState')
            ->will($this->returnValue('paid'));

    $handler->expects($this->once())
            ->method('updatePayment')
            ->will($this->returnValue(null));

    $handler->expects($this->never())
            ->method('updateRefund');

    $callback = $this->getMock('bz_barzahlen_callback', array('_getUpdateHandler', '_sendHeader'));

    $callback->expects($this->once())
             ->method('_sendHeader')
             ->will($this->returnCallback('catchHeader'));

    $callback->expects($this->once())
             ->method('_getUpdateHandler')
             ->will($this->returnValue($handler));

    $this->assertEquals('page/shop/start.tpl', $callback->render());
    $this->assertEquals($_SESSION['headerCode'], 200);
  }

  /**
   * Testing a valid expired notification and the usage of handle methods.
   */
  public function testRenderValidExpired() {

    $handler = $this->getMock('bz_barzahlen_update_handler', array('checkData', 'getState', 'updatePayment', 'updateRefund'));

    $handler->expects($this->once())
            ->method('checkData')
            ->will($this->returnValue(true));

    $handler->expects($this->once())
            ->method('getState')
            ->will($this->returnValue('expired'));

    $handler->expects($this->once())
            ->method('updatePayment')
            ->will($this->returnValue(null));

    $handler->expects($this->never())
            ->method('updateRefund');

    $callback = $this->getMock('bz_barzahlen_callback', array('_getUpdateHandler', '_sendHeader'));

    $callback->expects($this->once())
             ->method('_sendHeader')
             ->will($this->returnCallback('catchHeader'));

    $callback->expects($this->once())
             ->method('_getUpdateHandler')
             ->will($this->returnValue($handler));

    $this->assertEquals('page/shop/start.tpl', $callback->render());
    $this->assertEquals($_SESSION['headerCode'], 200);
  }

  /**
   * Testing a valid refund completed notification and the usage of handle methods.
   */
  public function testRenderValidRefundCompleted() {

    $handler = $this->getMock('bz_barzahlen_update_handler', array('checkData', 'getState', 'updatePayment', 'updateRefund'));

    $handler->expects($this->once())
            ->method('checkData')
            ->will($this->returnValue(true));

    $handler->expects($this->once())
            ->method('getState')
            ->will($this->returnValue('refund_completed'));

    $handler->expects($this->never())
            ->method('updatePayment');

    $handler->expects($this->once())
            ->method('updateRefund')
            ->will($this->returnValue(null));

    $callback = $this->getMock('bz_barzahlen_callback', array('_getUpdateHandler', '_sendHeader'));

    $callback->expects($this->once())
             ->method('_sendHeader')
             ->will($this->returnCallback('catchHeader'));

    $callback->expects($this->once())
             ->method('_getUpdateHandler')
             ->will($this->returnValue($handler));

    $this->assertEquals('page/shop/start.tpl', $callback->render());
    $this->assertEquals($_SESSION['headerCode'], 200);
  }

  /**
   * Testing a valid refund expired notification and the usage of handle methods.
   */
  public function testRenderValidRefundExpired() {

    $handler = $this->getMock('bz_barzahlen_update_handler', array('checkData', 'getState', 'updatePayment', 'updateRefund'));

    $handler->expects($this->once())
            ->method('checkData')
            ->will($this->returnValue(true));

    $handler->expects($this->once())
            ->method('getState')
            ->will($this->returnValue('refund_expired'));

    $handler->expects($this->never())
            ->method('updatePayment');

    $handler->expects($this->once())
            ->method('updateRefund')
            ->will($this->returnValue(null));

    $callback = $this->getMock('bz_barzahlen_callback', array('_getUpdateHandler', '_sendHeader'));

    $callback->expects($this->once())
             ->method('_sendHeader')
             ->will($this->returnCallback('catchHeader'));

    $callback->expects($this->once())
             ->method('_getUpdateHandler')
             ->will($this->returnValue($handler));

    $this->assertEquals('page/shop/start.tpl', $callback->render());
    $this->assertEquals($_SESSION['headerCode'], 200);
  }

  /**
   * Testing a invalid notification and the usage of handle methods.
   */
  public function testRenderInvalidRequest() {

    $handler = $this->getMock('bz_barzahlen_update_handler', array('checkData', 'getState', 'updatePayment', 'updateRefund'));

    $handler->expects($this->once())
            ->method('checkData')
            ->will($this->returnValue(false));

    $handler->expects($this->never())
            ->method('getState');

    $handler->expects($this->never())
            ->method('updatePayment');

    $handler->expects($this->never())
            ->method('updateRefund');

    $callback = $this->getMock('bz_barzahlen_callback', array('_getUpdateHandler', '_sendHeader'));

    $callback->expects($this->once())
             ->method('_sendHeader')
             ->will($this->returnCallback('catchHeader'));

    $callback->expects($this->once())
             ->method('_getUpdateHandler')
             ->will($this->returnValue($handler));

    $this->assertEquals(null, $callback->render());
    $this->assertEquals($_SESSION['headerCode'], 400);
  }

  /**
   * Testing the creation of a update handler object.
   */
  public function testGetUpdateHandler() {

    $oView = $this->getProxyClass('bz_barzahlen_callback');
    $oHandler = $oView->_getUpdateHandler();

    $this->assertAttributeEquals(SHOPID, '_sShopId', $oHandler);
    $this->assertAttributeEquals(NOTIFICATIONKEY, '_sNotificationKey', $oHandler);
  }

  /**
   * Unset everything before the next test.
   */
  public function tearDown() {
    parent::tearDown();
    unset($_SESSION['headerCode']);
  }
}