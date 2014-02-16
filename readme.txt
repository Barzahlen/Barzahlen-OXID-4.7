==Title==
Barzahlen Payment Module (OXID eShop 4.7.X)

==Author==
Alexander Diebler

==Prefix==
Barzahlen

==Version==
1.1.0

==Link==
http://www.barzahlen.de

==Mail==
support@barzahlen.de

==Description==
Integrates Barzahlen payment solution into OXID eSales.

==Extend==
*payment
--getSandbox
--getPartner

*thankyou
--init
--render
--getPaymentSlipLink
--getExpirationNotice
--getInfotextOne
--getInfotextTwo

*oxpaymentgateway
--executePayment

*oxorder
--finalizeOrder

==Installation==
* copy contents from copy_this directory into the shop root
* copy contents from changed_full directory into your template folder
* clear tmp directory

==Modules==
payment => barzahlen/views/barzahlen_payment
thankyou => barzahlen/views/barzahlen_thankyou
oxorder => barzahlen/core/barzahlen_order
oxpaymentgateway => barzahlen/core/barzahlen_payment_gateway

==Libraries==

==Ressources==
