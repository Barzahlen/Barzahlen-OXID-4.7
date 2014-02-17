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
                 'shop_id' => '10000',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => '8699e87a282ba4c46053ebcc231746f99ff4aae3c156e314fca83a3e3ba66dc08b8ee3b51b81921ed86796e29307340a3c74ec30a9c513624956f2d78d2722ef'
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
                 'hash' => '303bb2c791c0b049d2a8d7e9af17e6100b067c796c41e1f8e4b31d1db218ec0f0112b31ee7c943443f4e98798ef842621588d05624af9ebc3d43a0b33a73f9f0'
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
                 'shop_id' => '10000',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '2004.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => '7c2b3e167fb924bb33eb51e171e546c2f131326ea5c182fba8a38466604db4c7e9202429bdcb4dc4671a3a2bbe8860147ef252df73ef623851116805158578b8'
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
                 'shop_id' => '10000',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'origin_order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => '0e7e608e2ddfbeb8aa67220f1ca29b0f8d910701e8f8465931fe42633299ca472c2511f8784db1d050ab90d6ad3a52ec203267207f2d47e20066650884c10817',
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
                 'shop_id' => '10000',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'origin_order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => 'd3d976c133074670572d7f4f8c227fdc8a4e736ba66e8899568cdf2954a4f64a8378ac7e0cec0fe7e7be57474fd55fa6f3b19669fa39075542c4dee9637734da',
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
                 'shop_id' => '10000',
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
                 'shop_id' => '10000',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => '93354218f305aca89cc2a222e5bfb8704caa6210af4ea95c7d0891cc06e205ef88b89487b1e90930f5834ef2fbb3f5c03ca7307e7926396f9389fa79868719ed'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);

    $this->assertEquals(null, $notification->getState());
    $this->assertEquals(null, $notification->getTransactionId());
    $this->assertEquals(null, $notification->getShopId());

    $notification->validate();

    $this->assertTrue($notification->isValid());
    $this->assertEquals('payment', $notification->getNotificationType());
    $this->assertEquals('paid', $notification->getState());
    $this->assertEquals('1', $notification->getTransactionId());
    $this->assertEquals('10000', $notification->getShopId());
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
                 'shop_id' => '10000',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'origin_order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen',
                 'hash' => '509a4dedefbbd3ff1bea01e547c1e9e1f0795af83c0227342f4f6ee7e21b26fcd0fb25278b2a478fbe370e92c47c6366ad5013ccf9cc0aad681d90c44d98c5fe',
                 'page' => 'ipn/barzahlen'
                   );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);

    $this->assertEquals(null, $notification->getState());
    $this->assertEquals(null, $notification->getOriginTransactionId());
    $this->assertEquals(null, $notification->getShopId());

    $notification->validate();

    $this->assertTrue($notification->isValid());
    $this->assertEquals('refund', $notification->getNotificationType());
    $this->assertEquals('refund_completed', $notification->getState());
    $this->assertEquals('1', $notification->getRefundTransactionId());
    $this->assertEquals(null, $notification->getTransactionId());
    $this->assertEquals('1', $notification->getOriginTransactionId());
    $this->assertEquals('10000', $notification->getShopId());
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
                 'shop_id' => '10000',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'hash' => '8c1b2431d8cdbf430626d359fc6a96ccc229adb1dc0127611244c04ef1cf78cec561ce22c626917fe10ec08826a15997d91e889325b95ae0a5bd34f19950ca2d'
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
                 'shop_id' => '10000',
                 'customer_email' => 'mustermann@barzahlen.de',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'hash' => '3a536f5c3bc7c21b46c0d871a155f06695379171137f34397b2332f2db7090d85792b538c344e47cbbdbbe8b54aa7c698d478a294410e57df68c967be86bb2e1',
                 'page' => 'ipn/barzahlen'
                 );

    $notification = new Barzahlen_Notification(SHOPID, NOTIFICATIONKEY, $_GET);
    $notification->validate();
    $this->assertTrue($notification->isValid());
  }
}