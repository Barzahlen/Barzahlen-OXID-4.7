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

class Unit_Barzahlen_RequestRefundTest extends OxidTestCase {

  /**
   * Testing the construction of a refund request array.
   * Using minimal parameters.
   */
  public function testBuildRequestArrayWithMinimumParameters() {

    $refund = new Barzahlen_Request_Refund('7690927', '24.95');

    $requestArray = array('shop_id' => '10000',
                          'transaction_id' => '7690927',
                          'amount' => '24.95',
                          'currency' => 'EUR',
                          'language' => 'de',
                          'hash' => '458f66fc498ab846d408020dbe8b695ca94ca6b4bddf7c95e247a9284af196d98ff3b97951200eff4c051b4d37f6927033fadae0e320eae9beacbe0fcbc0b36f');

    $this->assertEquals($requestArray, $refund->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
  }

  /**
   * Testing the construction of a refund request array.
   * Using all parameters.
   */
  public function testBuildRequestArrayWithCurrency() {

    $refund = new Barzahlen_Request_Refund('7690927', '24.95', 'USD');

    $requestArray = array('shop_id' => '10000',
                          'transaction_id' => '7690927',
                          'amount' => '24.95',
                          'currency' => 'USD',
                          'language' => 'de',
                          'hash' => 'df51482cc0d25a9d70bfd321d622cc6e1965b1cf8a4302628d51d23b8f7ec1d20798a6d98c5c6f8e758d052fd336891b9b4230a1a4b381d278affdbe8a09c349');

    $this->assertEquals($requestArray, $refund->buildRequestArray(SHOPID, PAYMENTKEY, 'de'));
  }

  /**
   * Testing XML parsing with a valid response.
   */
  public function testParseXmlWithValidResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <origin-transaction-id>7690927</origin-transaction-id>
                      <refund-transaction-id>7691945</refund-transaction-id>
                      <result>0</result>
                      <hash>3516dd06b410ab05d41c50136686bb53c71a701e0c57fdb156839ab0c8b60cfa07f75f49a8b682ea768b5474b4c3891cfd93fed9811af05dba8306b32077b133</hash>
                    </response>';

    $refund = new Barzahlen_Request_Refund('7690927', '24.95');
    $refund->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertEquals('7690927', $refund->getOriginTransactionId());
    $this->assertEquals('7691945', $refund->getRefundTransactionId());
    $this->assertEquals(array('origin-transaction-id' => '7690927', 'refund-transaction-id' => '7691945'), $refund->getXmlArray());
    $this->assertTrue($refund->isValid());
  }

  /**
   * Testing XML parsing with an error response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithErrorResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <result>22</result>
                      <error-message>amount not valid</error-message>
                    </response>';

    $refund = new Barzahlen_Request_Refund('7690927', '124.95');
    $refund->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($refund->isValid());
  }

  /**
   * Testing XML parsing with an empty response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithEmptyResponse() {

    $xmlResponse = '';

    $refund = new Barzahlen_Request_Refund('7690927', '24.95');
    $refund->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($refund->isValid());
  }

  /**
   * Testing XML parsing with an incomplete response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithIncompleteResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <origin-transaction-id>7690927</origin-transaction-id>
                      <result>0</result>
                      <hash>f53bff1be34d4d98fef8660d6bdf6988b55d14e81163b4c9e983abee09d24304a46edc79d1e19f3c45bc5c2265ac740d092210c1d278999808c470b59e61ef79</hash>
                    </response>';

    $refund = new Barzahlen_Request_Refund('7690927', '24.95');
    $refund->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($refund->isValid());
  }

  /**
   * Testing XML parsing with an incorrect return value.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithInvalidResponse() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <origin-transaction-id>7690927</origin-transaction-id>
                      <refund-transaction-id>7691945</refund-transaction-id>
                      <result>0</result>
                      <hash>somerandomhash</hash>
                    </response>';

    $refund = new Barzahlen_Request_Refund('7690927', '24.95');
    $refund->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($refund->isValid());
  }

  /**
   * Testing XML parsing with an invalid xml response.
   *
   * @expectedException Barzahlen_Exception
   */
  public function testParseXmlWithInvalidXML() {

    $xmlResponse = '<?xml version="1.0" encoding="UTF-8"?>
                    <response>
                      <origin-transaction-id>7690927</>
                      <refund-transaction-id>7691945</refund-transaction-id>
                      <result>0</result>
                      <hash>f53bff1be34d4d98fef8660d6bdf6988b55d14e81163b4c9e983abee09d24304a46edc79d1e19f3c45bc5c2265ac740d092210c1d278999808c470b59e61ef79</hash>
                    </response>';

    $refund = new Barzahlen_Request_Refund('7690927', '24.95');
    $refund->parseXml($xmlResponse, PAYMENTKEY);

    $this->assertFalse($refund->isValid());
  }

  /**
   * Tests that the right request type is returned.
   */
  public function testGetRequestType() {

    $refund = new Barzahlen_Request_Refund('7690927', '24.95');
    $this->assertEquals('refund', $refund->getRequestType());
  }
}