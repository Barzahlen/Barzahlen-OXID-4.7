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
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
  'id'           => 'barzahlen',
  'title'        => 'Barzahlen',
  'description'  => array(
    'de' => 'Barzahlen bietet Ihren Kunden die M&ouml;glichkeit, online bar zu bezahlen. Sie werden in Echtzeit &uuml;ber die Zahlung benachrichtigt und profitieren von voller Zahlungsgarantie und neuen Kundengruppen. Sehen Sie wie Barzahlen funktioniert: <a href="http://www.barzahlen.de/partner/funktionsweise" target="_blank" style="color: #63A924;">http://www.barzahlen.de/partner/funktionsweise</a>',
    'en' => 'Barzahlen let\'s your customers pay cash online. You get a payment confirmation in real-time and you benefit from our payment guarantee and new customer groups. See how Barzahlen works: <a href="http://www.barzahlen.de/partner/funktionsweise" target="_blank" style="color: #63A924;">http://www.barzahlen.de/partner/funktionsweise</a>'
  ),
  'lang' => 'de',
  'thumbnail'    => 'barzahlen-logo.png',
  'version'      => '1.1.2',
  'author'       => 'Zerebro Internet GmbH',
  'url'          => 'http://www.barzahlen.de',
  'email'        => 'support@barzahlen.de',
  'extend'       => array(
    'payment'          => 'barzahlen/controllers/barzahlen_payment',
    'thankyou'         => 'barzahlen/controllers/barzahlen_thankyou',
    'oxorder'          => 'barzahlen/models/barzahlen_order',
    'oxpaymentgateway' => 'barzahlen/models/barzahlen_payment_gateway'
  ),
  'files' => array(
    'barzahlen_callback' => 'barzahlen/controllers/barzahlen_callback.php',
    'barzahlen_transactions' => 'barzahlen/controllers/barzahlen_transactions.php',
    'barzahlen_update_handler' => 'barzahlen/models/barzahlen_update_handler.php'
  ),
  'blocks'       => array(
    array('template' => 'page/checkout/payment.tpl',  'block' => 'select_payment',          'file' => 'out/blocks/page/checkout/payment/select_payment'),
    array('template' => 'page/checkout/payment.tpl',  'block' => 'checkout_payment_errors', 'file' => 'out/blocks/page/checkout/payment/checkout_payment_errors'),
    array('template' => 'page/checkout/thankyou.tpl', 'block' => 'checkout_thankyou_info',  'file' => 'out/blocks/page/checkout/thankyou/checkout_thankyou_info')
  ),
  'settings'     => array(
    array('group' => 'main', 'name' => 'bzSandbox',         'type' => 'bool', 'value' => 'false'),
    array('group' => 'main', 'name' => 'bzShopId',          'type' => 'str',  'value' => ''),
    array('group' => 'main', 'name' => 'bzPaymentKey',      'type' => 'str',  'value' => ''),
    array('group' => 'main', 'name' => 'bzNotificationKey', 'type' => 'str',  'value' => ''),
    array('group' => 'main', 'name' => 'bzDebug',           'type' => 'bool', 'value' => 'false')
  ),
  'templates' => array(
    'barzahlen_transactions.tpl' => 'barzahlen/out/admin/tpl/barzahlen_transactions.tpl'
  )
);