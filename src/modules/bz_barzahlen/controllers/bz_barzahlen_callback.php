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
 * @copyright   Copyright (c) 2013 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once getShopBasePath() . 'modules/bz_barzahlen/api/loader.php';

/**
 * Callback Controller
 * Reachable from outside to handle HTTP push notifications on status changes
 * for Barzahlen transactions.
 */
class bz_barzahlen_callback extends oxView
{
    /**
     * HTTP Header Codes
     */
    const STATUS_OK = 200;
    const STATUS_BAD_REQUEST = 400;

    /**
     * Transaction status codes.
     */
    const STATE_PENDING = "pending";
    const STATE_PAID = "paid";
    const STATE_EXPIRED = "expired";
    const STATE_REFUND_COMPLETED = "refund_completed";
    const STATE_REFUND_EXPIRED = "refund_expired";

    /**
     * Kicks off the notification process and sends out the header after a
     * successful or not successful hash validation.
     *
     * @return string current template file name
     */
    public function render()
    {
        parent::render();

        $oUpdateHandler = $this->_getUpdateHandler();

        if ($oUpdateHandler->checkData($_GET)) {

            $this->_sendHeader(self::STATUS_OK);
            $state = $oUpdateHandler->getState();

            if ($state == self::STATE_PAID || $state == self::STATE_EXPIRED) {
                $oUpdateHandler->updatePayment();
            } elseif ($state == self::STATE_REFUND_COMPLETED || $state == self::STATE_REFUND_EXPIRED) {
                $oUpdateHandler->updateRefund();
            }
        } else {
            $this->_sendHeader(self::STATUS_BAD_REQUEST);
            return;
        }

        return 'page/shop/start.tpl';
    }

    protected function _getUpdateHandler()
    {
        return oxNew('bz_barzahlen_update_handler');
    }

    /**
     * Sends out a response header after the notification was checked.
     *
     * @param integer $code
     */
    protected function _sendHeader($iCode)
    {
        if ($iCode == self::STATUS_OK) {
            header("HTTP/1.1 200 OK");
            header("Status: 200 OK");
        } elseif ($iCode == self::STATUS_BAD_REQUEST) {
            header("HTTP/1.1 400 Bad Request");
            header("Status: 400 Bad Request");
        }
    }
}
