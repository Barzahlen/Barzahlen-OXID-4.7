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

class Unit_Barzahlen_RequestResendTest extends OxidTestCase {

  /**
   * Testing the construction of a resend request array.
   */
  public function testBuildRequestArray() {

    $resend = new Barzahlen_Request_Resend('7691945');

    $requestArray = array('shop_id' => '10345',
                          'transaction_id' => '7691945',
                          'language' => 'de',
                          'hash' => '457379510d9371a998920bdb65e6f760f5fdfdfb12e6005c4638ce1ae95fd9f0ec510edf5d17855dc83dfe21bd9dc846cdbf381e1047ad88629dc7a9d1d97f83');

    $this->assertEquals($requestArray, $resend->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
  }

  /**
   * Testing XML parsing with a valid response.
   */
  public function testParseXmlWithValidResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945</transaction-id>
                      <result>0</result>
                      <hash>01a7027be0b36bfaf19cca529652daf6fdb2d9d7537c2d627666f2b7f438b3e382ad5a6d7fdccafca1d1ec30dbde94292695ac67d021616ea98b782442c16c8d</hash>
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertEquals('7691945', $resend->getTransactionId());
    $this->assertTrue($resend->isValid());
  }

  /**
   * Testing XML parsing with an error response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithErrorResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <result>6</result>
                      <error-message>transaction already paid</error-message>
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Testing XML parsing with an empty response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithEmptyResponse() {

    $xmlResponse = '';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Testing XML parsing with an incomplete response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithIncompleteResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945</transaction-id>
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Testing XML parsing with an incorrect return value.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithInvalidResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>1234567</transaction-id>
                      <result>0</result>
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8</hash>
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Testing XML parsing with an invalid xml response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithInvalidXML() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <transaction-id>7691945
                      <result>0
                      <hash>d6b01ae78c6a7d1b6895b0cf08040095b5bd66c4f589556cfa591b956fa94bedfe032de843b17d36b7f865cb6689797cafa40c53815609217fa210e1b0ee9ee8
                    </response>';

    $resend = new Barzahlen_Request_Resend('7691945');
    $resend->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($resend->isValid());
  }

  /**
   * Tests that the right request type is returned.
   */
  public function testGetRequestType() {

    $resend = new Barzahlen_Request_Resend('7691945');
    $this->assertEquals('resend_email', $resend->getRequestType());
  }
}
?>