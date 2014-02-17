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
 * ThankYou View Controller Extension
 * If Barzahlen was choosen the payment slip information will be added to the
 * final checkout success page.
 */
class barzahlen_thankyou extends barzahlen_thankyou_parent {

  /**
   * Additional Information Text 1.
   *
   * @var string
   */
  protected $_sInfotextOne;

  /**
   * Executes parent method parent::render().
   * Grabs the payment information from the session.
   */
  public function init() {

    parent::init();
    $this->_sInfotextOne = oxSession::getVar('barzahlenInfotextOne');
  }

  /**
   * Executes parent method parent::render() and unsets session variables.
   */
  public function render() {

    oxSession::deleteVar( 'barzahlenInfotextOne' );
    return parent::render();
  }

  /**
   * Returns the infotext 1.
   *
   * @return string with infotext 1
   */
  public function getInfotextOne() {
    return $this->_sInfotextOne;
  }
}