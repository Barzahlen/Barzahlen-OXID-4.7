<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

/**
 * ThankYou View Controller Extension
 * If Barzahlen was choosen the payment slip information will be added to the
 * final checkout success page.
 */
class bz_barzahlen_thankyou extends bz_barzahlen_thankyou_parent
{
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
    public function init()
    {
        parent::init();
        $this->_sInfotextOne = $this->getSession()->getVariable('barzahlenInfotextOne');
    }

    /**
     * Executes parent method parent::render() and unsets session variables.
     */
    public function render()
    {
        $this->getSession()->deleteVariable('barzahlenInfotextOne');
        return parent::render();
    }

    /**
     * Returns the infotext 1.
     *
     * @return string with infotext 1
     */
    public function getInfotextOne()
    {
        return $this->_sInfotextOne;
    }
}
