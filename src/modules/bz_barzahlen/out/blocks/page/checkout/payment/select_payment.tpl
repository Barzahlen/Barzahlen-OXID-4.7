[{if $sPaymentID == "oxidbarzahlen"}]
[{if $oView->checkCurrency() == true}]
  <dl>
      <dt>
          <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
          <label for="payment_[{$sPaymentID}]"><b><img src="https://cdn.barzahlen.de/images/barzahlen_logo.png" height="45" alt="[{ $paymentmethod->oxpayments__oxdesc->value}]" style="vertical-align:middle;"></b></label>
      </dt>
      <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">

          [{ oxmultilang ident="BZ__PAGE_CHECKOUT_PAYMENT_DESC" }]
          [{if $oView->getSandbox() == 1}]
          [{ oxmultilang ident="BZ__PAGE_CHECKOUT_PAYMENT_SANDBOX" }]
          [{/if}]
          [{ oxmultilang ident="BZ__PAGE_CHECKOUT_PAYMENT_OUR_PARTNER" }]&nbsp;
          [{section name=partner start=1 loop=11}]
          <img src="https://cdn.barzahlen.de/images/barzahlen_partner_[{"%02d"|sprintf:$smarty.section.partner.index}].png" alt="" style="vertical-align: middle;" height="25px" />
          [{/section}]
      </dd>
  </dl>
[{/if}]
[{else}]
  [{$smarty.block.parent}]
[{/if}]