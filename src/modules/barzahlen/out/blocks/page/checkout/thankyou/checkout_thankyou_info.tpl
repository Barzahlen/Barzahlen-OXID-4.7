[{if $oView->getPaymentSlipLink() != ""}]
<iframe src="[{$oView->getPaymentSlipLink()}]" width="0" height="1" frameborder="0"></iframe>
<img src="http://cdn.barzahlen.de/images/barzahlen_logo.png" height="57" width="168" alt="" style="padding:0; margin:0; margin-bottom: 10px;"/>
<hr/>
<br/>
<div style="width:100%;">
  <div style="position: relative; float: left; width: 180px; text-align: center;">
    <a href="[{$oView->getPaymentSlipLink()}]" target="_blank" style="color: #63A924; text-decoration: none; font-size: 1.2em;">
      <img src="http://cdn.barzahlen.de/images/barzahlen_checkout_success_payment_slip.png" height="192" width="126" alt="" style="margin-bottom: 5px;"/><br/>
      <strong>Download PDF</strong>
    </a>
  </div>

  <span style="font-weight: bold; color: #63A924; font-size: 1.5em;">[{ oxmultilang ident="BARZAHLEN__PAGE_CHECKOUT_THANKYOU_TITLE" }]</span>
  <p>[{$oView->getInfotextOne()}]</p>
  <p>[{$oView->getExpirationNotice()}]</p>
  <div style="width:100%;">
    <div style="position: relative; float: left; width: 50px;"><img src="http://cdn.barzahlen.de/images/barzahlen_mobile.png" height="52" width="41" alt=""/></div>
    <p>[{$oView->getInfotextTwo()}]</p>
  </div>

  <br style="clear:both;" /><br/>
</div>
<hr/><br/>

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