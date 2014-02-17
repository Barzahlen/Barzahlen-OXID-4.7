==Title==
Barzahlen Payment Module (OXID eShop 4.7.X)

==Author==
Zerebro Internet GmbH

==Prefix==
bz

==Version==
1.1.2

==Link==
http://www.barzahlen.de

==Mail==
support@barzahlen.de

==Description==
Integrates Barzahlen payment solution into OXID eShop.

==Extend==
*payment
--render

*thankyou
--init
--render

*oxpaymentgateway
--executePayment

*oxorder
--finalizeOrder

==Installation==
* copy contents from copy_this directory into the shop root
* use Service/Tools in admin area to upload install.sql
* activate Barzahlen module
* clear tmp directory

==Ressources==
Full User Manual: http://www.barzahlen.de/partner/integration/shopsysteme/4/oxid-eshop