<?php
/**
 * Barzahlen Payment Module SDK
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

class Unit_Barzahlen_NotificationTest extends OxidTestCase {

  /**
   * Test that empty arrays are decleared not valid.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testValidateWithEmptyNotification() {

    $_GET = array();

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertFalse($notification->isValid());
  }

  /**
   * Test that incomplete arrays are decleared not valid.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testValidateWithIncompleteNotification() {

    $_GET = array('state' => 'paid',
                  'transaction_id' => '5');

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertFalse($notification->isValid());
  }

  /**
   * Test function with invalid values. (Transaction ID)
   *
   * @expectedException Barzahlen_Exception
   */
  public function testValidateWithInvalidValueTransactionId() {

   $_GET = array('state' => 'paid',
                 'transaction_id' => '<hack>',
                 'shop_id' => '10345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => 'a633383609d6cb82e54bb9a1e7e82f4f5fb3c32053699496a8cbb697d0577d9b1c7994f437f775dbf9021e534b0caf0f3ca0287bb3d33b7fd8eebdc26b9b3d31'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertFalse($notification->isValid());
  }

  /**
   * Test function with invalid values. (Shop ID)
   *
   * @expectedException Barzahlen_Exception
   */
  public function testValidateWithInvalidValueShopId() {

   $_GET = array('state' => 'paid',
                 'transaction_id' => '1',
                 'shop_id' => '12345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => 'f993a1694ad7f845548ba4fa47980c4e20d20a07e64c203f6d85fe23512c5789c659a2d159b8fbc64b39df782c0827c16f86cd1a10f5df10f8b0c564f39d002c'
                );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertFalse($notification->isValid());
  }

  /**
   * Test function with invalid values. (Amount)
   *
   * @expectedException Barzahlen_Exception
   */
  public function testValidateWithInvalidValueAmount() {

   $_GET = array('state' => 'paid',
                 'transaction_id' => '1',
                 'shop_id' => '10345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '2004.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => 'ec07bb52c48ceed5209f614b5182be8a33336f8a115b916c35a64335830cccfa77ad64a7927b814e610708724fd89753850198700f365d72007b15636376a2f2'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertFalse($notification->isValid());
  }

  /**
   * Test function with invalid values. (Refund Transaction ID)
   *
   * @expectedException Barzahlen_Exception
   */
  public function testValidateWithInvalidValueRefundTransactionId() {

   $_GET = array('state' => 'refund_completed',
                 'refund_transaction_id' => '123abc',
                 'origin_transaction_id' => '1',
                 'shop_id' => '10345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'origin_order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => '442bd7a1a11416bf7dd3582091cdebd2da8cb81d4f4ee54781b65293c42f0549d2890970977dd00a80956c2c71517d1a39c0676481d875ea2d0a381700657578',
                 'page' => 'ipn/barzahlen'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertFalse($notification->isValid());
  }

  /**
   * Test function with invalid values. (Origin Transaction ID)
   *
   * @expectedException Barzahlen_Exception
   */
  public function testValidateWithInvalidValueOriginTransactionId() {

   $_GET = array('state' => 'refund_completed',
                 'refund_transaction_id' => '1',
                 'origin_transaction_id' => '<iframe src="example.com">1</iframe>',
                 'shop_id' => '10345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'origin_order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => 'd31fce626ac5187146f67762c45dfe7d05dceb4af4a99f6085b226863cf247648421194eb5f853d99e19c4ed96810877bda3289ed4a5e073a3fdc1db786a2609',
                 'page' => 'ipn/barzahlen'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertFalse($notification->isValid());
  }

  /**
   * Test function with invalid hash paid notification.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testValidateWithInvalidHash() {

   $_GET = array('state' => 'paid',
                 'transaction_id' => '1',
                 'shop_id' => '10345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => '85d13e7eda95276a655ef86947409f095be8ccd17abcd9d54a88fc9ce2ac5353964b33d8143439354ee46fa3ce0a7ea07c49429ae3bdbfeca4f2ab1990c15367'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertFalse($notification->isValid());
  }

  /**
   * Test function with valid paid notification.
   */
  public function testValidateWithValidPaidNotification() {

   $_GET = array('state' => 'paid',
                 'transaction_id' => '1',
                 'shop_id' => '10345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => '85ce0dd1368ba048533e2fd44a4120d88d2e52510fb51fb08049ac5625ecfcb0a69cc388ef3caae4b0b4a37cb8697337e2b33a1c5b5fabf8ceccea629c6f22c8'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();

    $this->assertTrue($notification->isValid());
    $this->assertEquals('payment', $notification->getNotificationType());
    $this->assertEquals('paid', $notification->getState());
    $this->assertEquals('1', $notification->getTransactionId());
    $this->assertEquals('10345', $notification->getShopId());
    $this->assertEquals('mustermann@barzahlen.de', $notification->getCustomerEmail());
    $this->assertEquals('24.95', $notification->getAmount());
    $this->assertEquals('EUR', $notification->getCurrency());
    $this->assertEquals('1', $notification->getOrderId());
    $this->assertEquals('PHP SDK', $notification->getCustomVar0());
    $this->assertEquals('Euro 2012', $notification->getCustomVar1());
    $this->assertEquals('Barzahlen', $notification->getCustomVar2());
    $this->assertEquals(array('PHP SDK', 'Euro 2012', 'Barzahlen'), $notification->getCustomVar());

    $this->assertEquals($_GET, $notification->getNotificationArray());
  }

  /**
   * Test function with valid refund notification.
   */
  public function testValidateWithValidRefundNotification() {

   $_GET = array('state' => 'refund_completed',
                 'refund_transaction_id' => '1',
                 'origin_transaction_id' => '1',
                 'shop_id' => '10345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'origin_order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => '5b58a72495385dadb9069b4331e31a838aec08c04f00c8030e66768514eb7f5ec5de2b06face567fe5c053d40ad6393979bb81905c72c94d827dc719344912a4',
                 'page' => 'ipn/barzahlen'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();

    $this->assertTrue($notification->isValid());
    $this->assertEquals('refund', $notification->getNotificationType());
    $this->assertEquals('refund_completed', $notification->getState());
    $this->assertEquals('1', $notification->getRefundTransactionId());
    $this->assertEquals(null, $notification->getTransactionId());
    $this->assertEquals('1', $notification->getOriginTransactionId());
    $this->assertEquals('10345', $notification->getShopId());
    $this->assertEquals('mustermann@barzahlen.de', $notification->getCustomerEmail());
    $this->assertEquals('24.95', $notification->getAmount());
    $this->assertEquals('EUR', $notification->getCurrency());
    $this->assertEquals(null, $notification->getOrderId());
    $this->assertEquals('1', $notification->getOriginOrderId());
    $this->assertEquals('PHP SDK', $notification->getCustomVar0());
    $this->assertEquals('Euro 2012', $notification->getCustomVar1());
    $this->assertEquals('Barzahlen', $notification->getCustomVar2());
    $this->assertEquals(array('PHP SDK', 'Euro 2012', 'Barzahlen'), $notification->getCustomVar());

    $this->assertEquals($_GET, $notification->getNotificationArray());
  }

  /**
   * Test function with valid expired notification without custom vars.
   */
  public function testValidateWithValidExpiredShortNotification() {

   $_GET = array('state' => 'expired',
                 'transaction_id' => '1',
                 'shop_id' => '10345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'hash' => 'b66fdabdc6e71ea7e741179e61cd7c83cd356d96ae1c7f59308e28a264529fe735044509b8cd3c56f4d1b9019f957dbd83892aae087d66ed09b24f462cb9ced4'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertTrue($notification->isValid());
  }

  /**
   * Test function with valid refund notification without custom vars and origin order id.
   */
  public function testValidateWithValidShortRefundNotification() {

   $_GET = array('state' => 'refund_completed',
                 'refund_transaction_id' => '1',
                 'origin_transaction_id' => '1',
                 'shop_id' => '10345',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'hash' => '8ee791355c3fea0c7bee50a8b7cf1112d75319d7063a2ce7d653ef9214d5a1505a2c2ae5bb2098bc507f468a5fb3d63220e0c55339e782e3fc3f6c75b5ea889b',
                 'page' => 'ipn/barzahlen'
                 );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertTrue($notification->isValid());
  }
}
?>