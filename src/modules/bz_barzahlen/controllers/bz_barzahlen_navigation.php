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
 * Navigation Controller Extension
 * Checks for a new Barzahlen plugin version once a week.
 */
class bz_barzahlen_navigation extends bz_barzahlen_navigation_parent
{
    /**
     * @const Current Plugin Version
     */
    const CURRENTVERSION = "1.1.4";

    /**
     * @const Log file
     */
    const LOGFILE = "barzahlen.log";

    /**
     * Module identifier
     *
     * @var string
     */
    protected $_sModuleId = "bz_barzahlen";

    /**
     * Extends the startup checks with Barzahlen plugin version check.
     *
     * @return array
     */
    protected function _doStartUpChecks()
    {
        $aMessage = parent::_doStartUpChecks();

        $oxConfig = $this->getConfig();
        $sShopId = $oxConfig->getShopId();
        $sModule = oxConfig::OXMODULE_MODULE_PREFIX . $this->_sModuleId;
        $sPluginCheck = $oxConfig->getShopConfVar('bzPluginCheck', $sShopId, $sModule);

        // only check once a week
        if($sPluginCheck != null && $sPluginCheck > strtotime("-1 week")) {
            return $aMessage;
        }

        $oxConfig->saveShopConfVar('str', 'bzPluginCheck', time(), $sShopId, $sModule);
        $sBzShopId = $oxConfig->getShopConfVar('bzShopId', $sShopId, $sModule);
        $sPaymentKey = $oxConfig->getShopConfVar('bzPaymentKey', $sShopId, $sModule);

        $oChecker = new Barzahlen_Version_Check($sBzShopId, $sPaymentKey);

        $sShopsystem = 'OXID 4.7/5.0';
        $sShopsystemVersion = $oxConfig->getVersion();
        $sPluginVersion = self::CURRENTVERSION;

        try {
            $currentVersion = $oChecker->checkVersion($sShopsystem, $sShopsystemVersion, $sPluginVersion);
        } catch (Exception $e) {
            oxRegistry::getUtils()->writeToLog(date('c') . " " . $e . "\r\r", self::LOGFILE);
        }

        if($currentVersion != false) {
            $aMessage['warning'] .= ((!empty($aMessage['warning'])) ? "<br>" : '') . oxRegistry::getLang()->translateString('BZ__PLUGIN_AVAILABLE') . $currentVersion . '! ' . oxRegistry::getLang()->translateString('BZ__GET_NEW_PLUGIN' );
        }

        return $aMessage;
    }
}
