<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
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
     * Log file
     */
    const LOGFILE = "barzahlen.log";

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

        if (!$oUpdateHandler->checkData($_GET)) {
            $this->_sendHeader(self::STATUS_BAD_REQUEST);
            return;
        }

        switch($oUpdateHandler->getState()) {
            case self::STATE_PAID:
            case self::STATE_EXPIRED:
                $success = $oUpdateHandler->updatePayment();
                break;
            case self::STATE_REFUND_COMPLETED:
            case self::STATE_REFUND_EXPIRED:
                $success = $oUpdateHandler->updateRefund();
                break;
            default:
                oxRegistry::getUtils()->writeToLog(date('c') . 'Notification failed: Unknown state - ' . $oUpdateHandler->getState() . "\r\r", self::LOGFILE);
                $success = false;
                break;
        }

        if (!$success) {
            $this->_sendHeader(self::STATUS_BAD_REQUEST);
            return;
        }

        $this->_sendHeader(self::STATUS_OK);
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
