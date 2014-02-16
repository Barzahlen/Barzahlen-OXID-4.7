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

class barzahlen_thankyou extends barzahlen_thankyou_parent {

  protected $_paymentSlipLink;
  protected $_expirationNotice;
  protected $_infotextOne;
  protected $_infotextTwo;

  /**
   * Grabs the payment information from the session.
   */
  public function init()
  {
    parent::init();

    $this->_paymentSlipLink = oxSession::getVar('barzahlenPaymentSlipLink');
    $this->_expirationNotice = oxSession::getVar('barzahlenExpirationNotice');
    $this->_infotextOne = oxSession::getVar('barzahlenInfotextOne');
    $this->_infotextTwo = oxSession::getVar('barzahlenInfotextTwo');
  }

  /**
   * Executes parent method parent::render() and unsets session variables.
   *
   * @extend render
   */
  public function render() {

    oxSession::deleteVar( 'barzahlenPaymentSlipLink' );
    oxSession::deleteVar( 'barzahlenExpirationNotice' );
    oxSession::deleteVar( 'barzahlenInfotextOne' );
    oxSession::deleteVar( 'barzahlenInfotextTwo' );

    return parent::render();
  }

  /**
   * Returns the payment slip link.
   *
   * @return string with payment slip link
   */
  public function getPaymentSlipLink() {
    return $this->_paymentSlipLink;
  }

  /**
   * Returns the expiration notice.
   *
   * @return string with expiration notice
   */
  public function getExpirationNotice() {
    return $this->_expirationNotice;
  }

  /**
   * Returns the infotext 1.
   *
   * @return string with infotext 1
   */
  public function getInfotextOne() {
    return $this->_infotextOne;
  }

  /**
   * Returns the infotext 2.
   *
   * @return string with infotext 2
   */
  public function getInfotextTwo() {
    return $this->_infotextTwo;
  }
}