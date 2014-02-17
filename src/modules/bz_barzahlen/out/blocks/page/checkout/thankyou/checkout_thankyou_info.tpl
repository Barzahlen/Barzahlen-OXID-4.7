[{if $oView->getInfotextOne() != ""}]
[{$oView->getInfotextOne()}]
<br>
<br>
[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_THANKYOU1" }] [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_THANKYOU2" }] [{ $oxcmp_shop->oxshops__oxname->value }]. <br>
[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_REGISTEREDYOUORDERNO1" }] [{ $order->oxorder__oxordernr->value }] [{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_REGISTEREDYOUORDERNO2" }]</h2><br>
[{if !$oView->getMailError() }]
[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_YOURECEIVEDORDERCONFIRM" }]<br>
[{else}]<br>
[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_CONFIRMATIONNOTSUCCEED" }]<br>
[{/if}]
<br>
[{ oxmultilang ident="PAGE_CHECKOUT_THANKYOU_WEWILLINFORMYOU" }]<br><br>
[{else}]
  [{$smarty.block.parent}]
[{/if}]