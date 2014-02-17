[{if $sPaymentID == "oxidbarzahlen"}]
<dl>
    <dt>
        <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
        <label for="payment_[{$sPaymentID}]"><b><img src="http://cdn.barzahlen.de/images/barzahlen_logo.png" height="45" alt="[{ $paymentmethod->oxpayments__oxdesc->value}]" style="vertical-align:middle;"></b></label>
    </dt>
    <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">

        [{ oxmultilang ident="BZ__PAGE_CHECKOUT_PAYMENT_DESC" }]
        [{if $oView->getSandbox() == 1}]
        [{ oxmultilang ident="BZ__PAGE_CHECKOUT_PAYMENT_SANDBOX" }]
        [{/if}]
        [{ oxmultilang ident="BZ__PAGE_CHECKOUT_PAYMENT_OUR_PARTNER" }]
        [{$oView->getPartner()}]
    </dd>
</dl>
[{else}]
  [{$smarty.block.parent}]
[{/if}]