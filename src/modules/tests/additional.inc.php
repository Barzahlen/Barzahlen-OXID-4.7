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
require_once getShopBasePath().'/modules/bz_barzahlen/api/loader.php';

// test cases settings
define('SHOPID', '10000');
define('PAYMENTKEY', '5c37177432e340946389f0c46c73b55e96a723dd');
define('NOTIFICATIONKEY', '51b475b7cee1d05024f3c4fd53057662934eed58');

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