<?php
// including vfsStream library
require_once dirname( __FILE__ ) . "/libs/vfsStream/vfsStream.php";

// wheter to use the original "aModules" chain from the shop
// methods like "initFromMetadata" and "addChain" will append data to the original chain
oxTestModuleLoader::useOriginalChain( false );

// initiates the module from the metadata file
// does nothing if metadata file is not found
oxTestModuleLoader::initFromMetadata();

// appends the module extension chain with the given module files
oxTestModuleLoader::append( array(
    //"oxarticle" => "vendor/mymodule/core/myarticle.php",
));

// for some reason not defined
define('OXID_VERSION_EE', false);

// sdk loader
require_once getShopBasePath().'/modules/barzahlen/api/loader.php';

// test cases settings
define('SHOPID', '10345');
define('PAYMENTKEY', '6dcbf0d746ee6fa996f240cd93860f5682b2b7fc');
define('NOTIFICATIONKEY', '18de27b879961ef38396295e4fbc19b7d79e3bbd');

// helper function to catch header code instead of sending out headers
function catchHeader($code) {
  $_SESSION['headerCode'] = $code;
}

/**
 * Mock success request class with example values.
 */
class successRq {

  /**
   * Returns that the request was successful.
   * @return boolean
   */
  public function isValid() {
    return true;
  }

  /**
   * Returns a fake transaction id.
   * @return int
   */
  public function getTransactionId() {
    return 1234567;
  }

  /**
   * Returns a fake payment slip link.
   * @return string
   */
  public function getPaymentSlipLink() {
    return 'http://www.example.com/';
  }

  /**
   * Returns a fake expiration notice.
   * @return string
   */
  public function getExpirationNotice() {
    return 'The payment slip will expire in 10 days.';
  }

  /**
   * Returns a fake infotext 1.
   * @return string
   */
  public function getInfotext1() {
    return 'This is an info text.';
  }

  /**
   * Returns a fake infotext 2.
   * @return string
   */
  public function getInfotext2() {
    return 'This is another info text.';
  }

  /**
   * Returns a fake refund transaction id.
   * @return int
   */
  public function getRefundTransactionId() {
    return 7654321;
  }
}

/**
 * Mock failure request class.
 */
class failureRq {

  /**
   * Returns that the request was not successful.
   * @return boolean
   */
  public function isValid() {
    return false;
  }
}