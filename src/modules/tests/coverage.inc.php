<?php

// Add "PHPUnit_Util_Filter::addFileToWhitelist( PATH_TO_FILE )" to add files to coverage
// you can use the 'oxPATH' constant as the path to the shops root

// PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/vendor/mymodule/core/myarticle.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/api/api.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/api/base.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/api/exception.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/api/notification.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/api/request_base.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/api/request_payment.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/api/request_refund.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/api/request_resend.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/api/request_update.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/controllers/bz_barzahlen_callback.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/controllers/bz_barzahlen_payment.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/controllers/bz_barzahlen_thankyou.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/controllers/bz_barzahlen_transactions.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/models/bz_barzahlen_order.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/models/bz_barzahlen_payment_gateway.php' );
PHPUnit_Util_Filter::addFileToWhitelist( oxPATH . '/modules/bz_barzahlen/models/bz_barzahlen_update_handler.php' );