# Barzahlen Payment Module (OXID 4.7 - 4.9 / 5.0 - 5.2)

## User Manual
DE - https://integration.barzahlen.de/de/shopsysteme/oxid/nutzerhandbuch-47  
EN - https://integration.barzahlen.de/en/shopsystems/oxid/user-manual-47

## Current Version
1.2.1

## Changelog

### 1.2.1 (11.06.2015)
* improved payment selection
* updated to Barzahlen PHP SDK v1.1.8

### 1.2.0 (20.11.2014)
* updated Barzahlen PHP API SDK (1.1.7)
* callback returns 200 (OK) only after successful database update
* improved payment selection

### 1.1.4 (15.05.2013)
* updated Barzahlen PHP API SDK (1.1.6)
* added automatic payment slip cancelation on order cancelation
* automatic plugin version check once a week
* PSR2 coding standard

### 1.1.3 (11.03.2013)
* remaining transaction amounts under 0.50 Euros can now be refunded
* added prefix to module and classes names
* changed image urls to make use of https
* updated cURL certificate bundle
* removed T_PAAMAYIM_NEKUDOTAYIM that let PHP 5.2 crash

### 1.1.2 (28.01.2013)
* redesigned payment selection and thankyou page

### 1.1.1 (14.11.2012)
* restructured files and updated metadata.php

### 1.1.0 (01.11.2012)
* implemented new api geocoding feature to get the closest points of sales
* reduced the maximum amount to 999.99 Euros
* small changes in the language files

### 1.0.0 (26.09.2012)
* initial release

## Support
The Barzahlen Team will happily assist you with any problems or questions. Send us an email to support@barzahlen.de or use the contact form at https://integration.barzahlen.de/en/support.

## Copyright
(c) 2015, Cash Payment Solutions GmbH  
https://www.barzahlen.de

## NOTICE OF LICENSE
This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; version 3 of the License

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see http://www.gnu.org/licenses/