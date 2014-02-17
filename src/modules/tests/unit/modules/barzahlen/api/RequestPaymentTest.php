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

class Unit_Barzahlen_RequestPaymentTest extends OxidTestCase {

  /**
   * Tests different custom variable settings.
   */
  public function testSetCustomVar() {

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');

    $payment->setCustomVar('ABC', '{{}}');
    $this->assertAttributeEquals(array('ABC', '{{}}', ''), '_customVar', $payment);

    $payment->setCustomVar('Mein Shopsystem');
    $this->assertAttributeEquals(array('Mein Shopsystem', '', ''), '_customVar', $payment);

    $payment->setCustomVar('Mein Shopsystem', 'OXID', 'eShop');
    $this->assertAttributeEquals(array('Mein Shopsystem', 'OXID', 'eShop'), '_customVar', $payment);

    $payment->setCustomVar();
    $this->assertAttributeEquals(array('', '', ''), '_customVar', $payment);
  }

  /**
   * Testing the construction of a payment request array.
   * Using minimal parameters.
   */
  public function testBuildRequestArrayWithMinimumParameters() {

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');

    $requestArray = array('shop_id' => '10345',
                          'customer_email' => 'mustermann@barzahlen.de',
                          'amount' => '24.95',
                          'currency' => 'EUR',
                          'language' => 'de',
                          'customer_street_nr' => 'Musterstr. 1a',
                          'customer_zipcode' => '12345',
                          'customer_city' => 'Musterhausen',
                          'customer_country' => 'DE',
                          'hash' => '0ecc28babfa392234a1cd5e6768ae69fb5ac359eedbd83ae7932ebbb26e87a7ee416e7d164dbaa74f32b263c5bbd48ca2adbc02c9e427b5fbf4dfafafc80aba4');

    $this->assertEquals($requestArray, $payment->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
  }

  /**
   * Testing the construction of a payment request array.
   * Using one optional parameter.
   */
  public function testBuildRequestArrayWithCurrency() {

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95', 'USD');

    $requestArray = array('shop_id' => '10345',
                          'customer_email' => 'mustermann@barzahlen.de',
                          'amount' => '24.95',
                          'currency' => 'USD',
                          'language' => 'en',
                          'customer_street_nr' => 'Musterstr. 1a',
                          'customer_zipcode' => '12345',
                          'customer_city' => 'Musterhausen',
                          'customer_country' => 'DE',
                          'hash' => 'e79fa5f4e09e1c9ef6a87a3694b3dab29ec41b6a9f91807c96a59e574134165b80293f7c338b3d675ff0289bdda891a942499a14662cded91283934b88e47295');

    $this->assertEquals($requestArray, $payment->buildRequestArray(SHOPID, PAYMENTKEY, 'en'));
  }

  /**
   * Testing the construction of a payment request array.
   * Using all parameters.
   */
  public function testBuildRequestArrayWithCurrencyAndOrderId() {

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95', 'EUR', '42');

    $requestArray = array('shop_id' => '10345',
                          'customer_email' => 'mustermann@barzahlen.de',
                          'amount' => '24.95',
                          'currency' => 'EUR',
                          'language' => 'de',
                          'order_id' => '42',
                          'customer_street_nr' => 'Musterstr. 1a',
                          'customer_zipcode' => '12345',
                          'customer_city' => 'Musterhausen',
                          'customer_country' => 'DE',
                          'hash' => '428c3935c417dc678f3a00c51b565d902c8c624e9a7a63da1ae9761cc4a6ab6a2ceb126fc534b9beb234107ffc072c4c55895421047dfa424c600d89e13d64a9');

    $this->assertEquals($requestArray, $payment->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
  }

  /**
   * Testing XML parsing with a valid response.
   */
  public function testParseXmlWithValidResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7690927</transaction-id>
                      <payment-slip-link>https://api-online-sandbox.barzahlen.de:904/download/2001034500000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
                      <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
                      <infotext-1><![CDATA[Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß]]></infotext-1>
                      <infotext-2><![CDATA[Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter]]></infotext-2>
                      <result>0</result>
                      <hash>931ee5a06cae38c17c2ab2d0e8a7b264882bd4804d9686df096c345c70e330ca935e129717d8e35bc6ab563043910f35ccba2eb407409f511d831ef312da94a3</hash>
                    </response>';

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
    $payment->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertEquals('7690927', $payment->getTransactionId());
    $this->assertEquals('https://api-online-sandbox.barzahlen.de:904/download/2001034500000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf', $payment->getPaymentSlipLink());
    $this->assertEquals('Der Zahlschein ist 14 Tage gültig.', $payment->getExpirationNotice());
    $this->assertEquals('Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß', $payment->getInfoText1());
    $this->assertEquals('Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter', $payment->getInfoText2());
    $this->assertTrue($payment->isValid());
  }

  /**
   * Testing XML parsing with an error response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithErrorResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <result>10</result>
                      <error-message>shop not found</error-message>
                    </response>';

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
    $payment->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($payment->isValid());
  }

  /**
   * Testing XML parsing with an empty response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithEmptyResponse() {

    $xmlResponse = '';

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
    $payment->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($payment->isValid());
  }

  /**
   * Testing XML parsing with an incomplete response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithIncompleteResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7690927</transaction-id>
                      <payment-slip-link>https://api-online-sandbox.barzahlen.de:904/download/2001034500000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
                      <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
                      <result>0</result>
                      <hash>5a175d4002e91f4b16758ff4b8b41ff973ad355e48e73d386195cb8605600d18e443819c4e7044ebb5853a45ff9ffe75b6868e33cc98459494b656301991c18e</hash>
                    </response>';

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
    $payment->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($payment->isValid());
  }

  /**
   * Testing XML parsing with an incorrect return value.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithInvalidResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7690927</transaction-id>
                      <payment-slip-link>https://api-online-sandbox.barzahlen.de:904/download/2001034500000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
                      <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
                      <infotext-1><![CDATA[Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß]]></infotext-1>
                      <infotext-2><![CDATA[Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter]]></infotext-2>
                      <result>0</result>
                      <hash>brokenhash</hash>
                    </response>';

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
    $payment->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($payment->isValid());
  }

  /**
   * Testing XML parsing with an invalid xml response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithInvalidXML() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7690927</some-id>
                      <payment-slip-link>https://api-online-sandbox.barzahlen.de:904/download/2001034500000/b3fc66ebb5f60ddfaa20307c73e0db3b73c0d812c1dc7e64984c5e2d4b64799a/Zahlschein_Barzahlen.pdf</payment-slip-link>
                      <expiration-notice>Der Zahlschein ist 14 Tage gültig.</expiration-notice>
                      <infotext-1><![CDATA[Hallo <b>Welt</b>! <a href="http://www.barzahlen.de">Bar zahlen</a> Infütöxt Äinß]]></infotext-1>
                      <infotext-2><![CDATA[Hallo <i>Welt</i>! <a href="http://www.barzahlen.de?a=b&c=d">Bar zahlen</a> Infütöxt 2% & so weiter]]></infotext-2>
                      <result>0</result>
                      <hash>5a175d4002e91f4b16758ff4b8b41ff973ad355e48e73d386195cb8605600d18e443819c4e7044ebb5853a45ff9ffe75b6868e33cc98459494b656301991c18e</hash>
                    </response>';

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
    $payment->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($payment->isValid());
  }

  /**
   * Tests that the right request type is returned.
   */
  public function testGetRequestType() {

    $payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Musterstr. 1a', '12345', 'Musterhausen', 'DE', '24.95');
    $this->assertEquals('create', $payment->getRequestType());
  }
}
?>