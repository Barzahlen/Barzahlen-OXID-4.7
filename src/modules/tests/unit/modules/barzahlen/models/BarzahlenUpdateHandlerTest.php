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
class Unit_Barzahlen_BarzahlenUpdateHandlerTest extends OxidTestCase {

  /**
   * Set everything that is needed for the testing up.
   */
  public function setUp() {

    $this->oUpdateHandler = new bz_barzahlen_update_handler;
    $this->restore = false;
  }

  /**
   * Testing that a valid notification array is checked as valid.
   */
  public function testCheckDataValidData() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '511d030aef4f0caed3b81d0dbb50096b0e1a39c14fb2e161b390e9c2939ee96a5acd2884cd3268d70fa720e9cb32968cda5fd5cea906e73140c04480ea93cfba');

    $this->assertTrue($this->oUpdateHandler->checkData($_GET));
  }

  /**
   * Testing that an invalid notification array is checked as invalid.
   */
  public function testCheckDataInvalidData() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'brokenhash');

    $this->assertFalse($this->oUpdateHandler->checkData($_GET));
  }

  /**
   * Testing the correct state return.
   */
  public function testGetState() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '511d030aef4f0caed3b81d0dbb50096b0e1a39c14fb2e161b390e9c2939ee96a5acd2884cd3268d70fa720e9cb32968cda5fd5cea906e73140c04480ea93cfba');

    $this->assertTrue($this->oUpdateHandler->checkData($_GET));
    $this->assertEquals('paid', $this->oUpdateHandler->getState());
  }

  /**
   * Test updating a pending transaction with a valid paid notification.
   */
  public function testUpdatePaymentValidPaid() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '511d030aef4f0caed3b81d0dbb50096b0e1a39c14fb2e161b390e9c2939ee96a5acd2884cd3268d70fa720e9cb32968cda5fd5cea906e73140c04480ea93cfba');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertTrue($this->oUpdateHandler->updatePayment());
    $this->restore = true;

    $oOrder = new oxOrder;
    $oOrder->load('2a289076590d790c6d50aabd6f5974eb');
    $this->assertEquals('paid', $oOrder->oxorder__bzstate->rawValue);
  }

  /**
   * Test updating a pending transaction with a valid expired notification.
   */
  public function testUpdatePaymentValidExpired() {

    $_GET = array('state' => 'expired',
                  'transaction_id' => '27767255',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'cc5987ebaa343ba52857348a991742bd5cd21b603fd73be54b7ccc87826adca0123c89a07a5f4334c2e1a0dc0d23cb4e85f9a60a772d551e5002dd9e2b2f79e0');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertTrue($this->oUpdateHandler->updatePayment());
    $this->restore = true;

    $oOrder = new oxOrder;
    $oOrder->load('2a289076590d790c6d50aabd6f5974eb');
    $this->assertEquals('expired', $oOrder->oxorder__bzstate->rawValue);
  }

  /**
   * Test updating a pending transaction with an invalid paid notification.
   */
  public function testUpdatePaymentWrongOrderId() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '2',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'd19ddb51fce56d76ac6aa3102b0a4344323ef5a1e91835f94efdfa389102a7506479c36ee7cafa7b8266cc0bc3dd3a8f5e63923e3ccd623d50d8d0ed8c994eff');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertFalse($this->oUpdateHandler->updatePayment());

    $oOrder = new oxOrder;
    $oOrder->load('2a289076590d790c6d50aabd6f5974eb');
    $this->assertEquals('pending', $oOrder->oxorder__bzstate->rawValue);
  }

  /**
   * Test updating a paid transaction with a valid paid notification.
   */
  public function testUpdatePaymentAgainstPaid() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767342',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '2',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'ddfcb25bc5843b105195afb0167c66ff6e8359e9e21f7925247483a54fb3f790a80e4060546892781119237a665a65ac8acc02f5460160ea12fa3882a90d7f60');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertFalse($this->oUpdateHandler->updatePayment());

    $oOrder = new oxOrder;
    $oOrder->load('2a289076590d790c6d50aabd6f5974eb');
    $this->assertEquals('pending', $oOrder->oxorder__bzstate->rawValue);
  }

  /**
   * Test updating a pending refund transaction with a valid refund_completed
   * notification.
   */
  public function testUpdateRefundValidCompleted() {

    $_GET = array('state' => 'refund_completed',
                  'refund_transaction_id' => '27828393',
                  'origin_transaction_id' => '27767585',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '22',
                  'currency' => 'EUR',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '50c1c00ce8a089629e8bd72d0fbaba16472f8fe15a847c9e0ac592f93db28d0a897affbfac45e7241c83783325922d92dcea3c6d7ba0442a1b2a8b405ad3b969');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertTrue($this->oUpdateHandler->updateRefund());
    $this->restore = true;

    $oOrder = new oxOrder;
    $oOrder->load('6988a7466abe756b93c1f0b2b11af7d3');
    $aRefundData = unserialize(str_replace("&quot;", "\"", $oOrder->oxorder__bzrefunds->value));
    foreach($aRefundData as $aRefund) {
      if($aRefund['refundid'] == '27828393') {
        $this->assertEquals('completed', $aRefund['state']);
      }
    }
  }

  /**
   * Test updating a pending refund transaction with a valid refund_expired
   * notification.
   */
  public function testUpdateRefundValidExpired() {

    $_GET = array('state' => 'refund_expired',
                  'refund_transaction_id' => '27828393',
                  'origin_transaction_id' => '27767585',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '22',
                  'currency' => 'EUR',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'ea6faaacdf4d787bc2f88953f1321c3eec9f860a1e7360eb4318d8649824d8689fafcf84465ada314f15011c4fe2e13872cb7c26372cf11ebe50372dbed8ba60');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertTrue($this->oUpdateHandler->updateRefund());
    $this->restore = true;

    $oOrder = new oxOrder;
    $oOrder->load('6988a7466abe756b93c1f0b2b11af7d3');
    $aRefundData = unserialize(str_replace("&quot;", "\"", $oOrder->oxorder__bzrefunds->value));
    foreach($aRefundData as $aRefund) {
      if($aRefund['refundid'] == '27828393') {
        $this->assertEquals('expired', $aRefund['state']);
      }
    }
  }

  /**
   * Test updating a pending refund transaction with an invalid refund_completed
   * notification.
   */
  public function testUpdateRefundWrongOrderId() {

    $_GET = array('state' => 'refund_completed',
                  'refund_transaction_id' => '27828393',
                  'origin_transaction_id' => '27767585',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '22',
                  'currency' => 'EUR',
                  'origin_order_id' => '6',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '5a5b8b2ef80f8908d7ca8c089c12e150d8ee47c05fbe9198064303e08e9c260110ddf2f6d25ec727ed4b4b27def15d72945673d293ec0f6d3a5e128bf4e2bfbe');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertFalse($this->oUpdateHandler->updateRefund());

    $oOrder = new oxOrder;
    $oOrder->load('6988a7466abe756b93c1f0b2b11af7d3');
    $aRefundData = unserialize(str_replace("&quot;", "\"", $oOrder->oxorder__bzrefunds->value));
    foreach($aRefundData as $aRefund) {
      if($aRefund['refundid'] == '27828393') {
        $this->assertEquals('pending', $aRefund['state']);
      }
    }
  }

  /**
   * Test updating a pending refund transaction with an invalid refund_completed
   * notification.
   */
  public function testUpdateRefundWrongRefundId() {

    $_GET = array('state' => 'refund_completed',
                  'refund_transaction_id' => '27828391',
                  'origin_transaction_id' => '27767585',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '22',
                  'currency' => 'EUR',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'a1171147f27d93252626dc74733ad46fe79e52f7d3d4a656b0738b989c0cc21a80bd25b2ad7ca92bd6d51adcd16f3fe2c7fd3d6b3d0f1c48fca3faf117e5f3b8');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertFalse($this->oUpdateHandler->updateRefund());

    $oOrder = new oxOrder;
    $oOrder->load('6988a7466abe756b93c1f0b2b11af7d3');
    $aRefundData = unserialize(str_replace("&quot;", "\"", $oOrder->oxorder__bzrefunds->value));
    foreach($aRefundData as $aRefund) {
      if($aRefund['refundid'] == '27828393') {
        $this->assertEquals('pending', $aRefund['state']);
      }
    }
  }

  /**
   * Test updating a completed refund transaction with an valid refund_completed
   * notification.
   */
  public function testUpdateRefundAgainstCompleted() {

    $_GET = array('state' => 'refund_completed',
                  'refund_transaction_id' => '27828461',
                  'origin_transaction_id' => '27767585',
                  'shop_id' => '10000',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '3.9',
                  'currency' => 'EUR',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'f261e1f2ec063b31c023dfa10b1471ce87bc4e90c2b3a5a554fdf410c444bce58b9030e86d1eb1298445cb2a8d9de3cceb119a04936418c5a29d86b979ce3337');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertFalse($this->oUpdateHandler->updateRefund());

    $oOrder = new oxOrder;
    $oOrder->load('6988a7466abe756b93c1f0b2b11af7d3');
    $aRefundData = unserialize(str_replace("&quot;", "\"", $oOrder->oxorder__bzrefunds->value));
    foreach($aRefundData as $aRefund) {
      if($aRefund['refundid'] == '27828393') {
        $this->assertEquals('pending', $aRefund['state']);
      }
    }
  }

  /**
   * Unset everything before the next test.
   */
  protected function tearDown() {

    unset($this->oUpdateHandler);
    if($this->restore) {
      $dbMaintenance = new dbMaintenance;
      $dbMaintenance->restoreDB();
    }
  }
}