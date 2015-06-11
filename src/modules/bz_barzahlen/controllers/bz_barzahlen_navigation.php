<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once getShopBasePath() . 'modules/bz_barzahlen/api/version_check.php';

/**
 * Navigation Controller Extension
 * Checks for a new Barzahlen plugin version once a week.
 */
class bz_barzahlen_navigation extends bz_barzahlen_navigation_parent
{
    /**
     * @const Current Plugin Version
     */
    const CURRENTVERSION = "1.2.1";

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
        $sShopsystem = 'OXID 4.7/5.0';
        $sShopsystemVersion = $oxConfig->getVersion();
        $sPluginVersion = self::CURRENTVERSION;

        try {
            $oChecker = new Barzahlen_Version_Check();
            $newAvailable = $oChecker->isNewVersionAvailable($sBzShopId, $sShopsystem, $sShopsystemVersion, $sPluginVersion);
        } catch (Exception $e) {
            oxRegistry::getUtils()->writeToLog(date('c') . " " . $e . "\r\r", self::LOGFILE);
        }

        if($newAvailable) {
            $aMessage['warning'] .= ((!empty($aMessage['warning'])) ? "<br>" : '') . sprintf(oxRegistry::getLang()->translateString('BZ__NEW_PLUGIN_AVAILABLE'), $oChecker->getNewPluginVersion(), $oChecker->getNewPluginUrl());
        }

        return $aMessage;
    }
}
