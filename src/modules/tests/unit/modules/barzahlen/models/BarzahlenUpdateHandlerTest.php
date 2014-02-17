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

    $this->oUpdateHandler = new barzahlen_update_handler;
    $this->restore = false;
  }

  /**
   * Testing that a valid notification array is checked as valid.
   */
  public function testCheckDataValidData() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'f3479069b1da59796662584cd255cbe0742192bc1c8496671fde59d31c06f4057887944548d344629516720d4e4caf61d5477129c85ede17e4b29ca280c0c325');

    $this->assertTrue($this->oUpdateHandler->checkData($_GET));
  }

  /**
   * Testing that an invalid notification array is checked as invalid.
   */
  public function testCheckDataInvalidData() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10345',
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
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'f3479069b1da59796662584cd255cbe0742192bc1c8496671fde59d31c06f4057887944548d344629516720d4e4caf61d5477129c85ede17e4b29ca280c0c325');

    $this->assertTrue($this->oUpdateHandler->checkData($_GET));
    $this->assertEquals('paid', $this->oUpdateHandler->getState());
  }

  /**
   * Test updating a pending transaction with a valid paid notification.
   */
  public function testUpdatePaymentValidPaid() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'f3479069b1da59796662584cd255cbe0742192bc1c8496671fde59d31c06f4057887944548d344629516720d4e4caf61d5477129c85ede17e4b29ca280c0c325');

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
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '5e729b53abc2028f60688b6f512023f076c0ca0d631c51ec53e9dcdd95d7fdb7d4fe9a2963667f2b74729ecdcc840ed7673f1995ba6c82945a621109146b00d0');

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
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '2',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '00859ac77303fea14f2e985640dc35d50656d17cfc2be146328fd9716d83dfef49b0b5ce4113d19d3c6dfc35559849cab3d5fa9ee8eb520a02767be9c450a1d9');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertFalse($this->oUpdateHandler->updatePayment());

    $oOrder = new oxOrder;
    $oOrder->load('2a289076590d790c6d50aabd6f5974eb');
    $this->assertEquals('pending', $oOrder->oxorder__bzstate->rawValue);
  }

  /**
   * Test updating a pending transaction with an invalid paid notification.
   */
  public function testUpdatePaymentWrongAmount() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '19.99',
                  'currency' => 'EUR',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '5e23cfabc47ba174e2556ead1f60c91dd23c4618547309e6ab23f9a5c00cf753e3346c47becfd7117eb868568a86466d3f0b76e44367f662f14a4575d7cf229a');

    $this->oUpdateHandler->checkData($_GET);
    $this->assertFalse($this->oUpdateHandler->updatePayment());

    $oOrder = new oxOrder;
    $oOrder->load('2a289076590d790c6d50aabd6f5974eb');
    $this->assertEquals('pending', $oOrder->oxorder__bzstate->rawValue);
  }

  /**
   * Test updating a pending transaction with an invalid paid notification.
   */
  public function testUpdatePaymentWrongCurrency() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '27767255',
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'USD',
                  'order_id' => '1',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '4cd860853130ade46c1e96885fcc3e47b33fc62effbc8cdb9e63df1add605c0519c5b7666ac753e409f5563a8e382468e9111f37b44c1350968116ed60ae1740');

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
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '25.9',
                  'currency' => 'EUR',
                  'order_id' => '2',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '60aea3d6e9d7c0715c2ba12b8a05bbc950ff9b7e1b797871efa0b81a601ced16095757606675214ee96892ce2e94b7e3f93c2566957e057df3d03bbb38d11db4');

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
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '22',
                  'currency' => 'EUR',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '21bb8a26e37b7acb71c139ed7aa81186d520de59999303e4ed7f04540c042933bc1d465743b9db5d9c92ec469a1041abeb2478febc149c0a87ccc77cfda819a0');

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
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '22',
                  'currency' => 'EUR',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '16e32df7d6b6a469e5cd83b9bd226859b869f8eba2412c7e3fd3a83fe213ff609add792d1ce19ff7cb5dbe59e4890da2f9b0fb4c57c82073632895bf4dc46563');

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
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '22',
                  'currency' => 'EUR',
                  'origin_order_id' => '6',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '30efb9b2e1d4f2a8b95165281d661c81e446d77af9c2b05413df70ace93ecb8402c85a0a21ada40e2b1a923d2df46d49055e498fa176ae6f79eb7bd53e636615');

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
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '22',
                  'currency' => 'EUR',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => '06d69502750669f2974b756242a2eccb83a751df136855ada1877ffaa47d5f3b7d1173bc4461a3cd713bbb720911a9835093a22543360d8106431e87358db798');

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
  public function testUpdateRefundWrongAmount() {

    $_GET = array('state' => 'refund_completed',
                  'refund_transaction_id' => '27828393',
                  'origin_transaction_id' => '27767585',
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '50',
                  'currency' => 'EUR',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'c55bae1acd023a789aeac6e089a9d53e10af8134aadfccae3389680884d960dcc85ae4a512998fff274626b8ecd986cda068d2823965bd162a45518cc89fc4b2');

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
  public function testUpdateRefundWrongCurrency() {

    $_GET = array('state' => 'refund_completed',
                  'refund_transaction_id' => '27828393',
                  'origin_transaction_id' => '27767585',
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '22',
                  'currency' => 'USD',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'c7167299e3e5b2bed681449f0453361184d60f52de70502a5764098ea192bbafbc520cb902ad34b2e1618e34844e5ab6e1195beffb4d19543c9a5283932922d9');

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
                  'shop_id' => '10345',
                  'customer_email' => 'mustermann@barzahlen.de',
                  'amount' => '3.9',
                  'currency' => 'EUR',
                  'origin_order_id' => '5',
                  'customer_var_0' => '',
                  'customer_var_1' => '',
                  'customer_var_2' => '',
                  'hash' => 'da98d27899c4c355afcd27a1adebfec0abe0b05f1ba9cffdc443980591ed6645e917e4ef33ae9cdd77f8defb150e59c7eb3bfb9b1dd93b9325a4fb7da45a8abc');

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
?>