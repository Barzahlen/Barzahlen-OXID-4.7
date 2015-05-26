[{if $sPaymentID == "oxidbarzahlen"}]
[{if $oView->checkCurrency() == true}]
  <dl>
      <dt>
          <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
          <label for="payment_[{$sPaymentID}]"><b><img id="barzahlen_logo" src="https://cdn.barzahlen.de/images/barzahlen_logo.png" height="45" alt="[{ $paymentmethod->oxpayments__oxdesc->value}]" style="vertical-align:middle;"></b></label>
      </dt>
      <dd class="[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]activePayment[{/if}]">
        <div id="barzahlen_description" style="padding-top: 1em;">
          <img id="barzahlen_special" src="https://cdn.barzahlen.de/images/barzahlen_special.png" style="float: right; margin-left: 10px; max-width: 180px; max-height: 180px;">
          [{ oxmultilang ident="BZ__PAGE_CHECKOUT_PAYMENT_DESC" }]
          [{ oxmultilang ident="BZ__PAGE_CHECKOUT_PAYMENT_PAY_AT" }]&nbsp;
          [{section name=partner start=1 loop=11}]
          <img src="https://cdn.barzahlen.de/images/barzahlen_partner_[{"%02d"|sprintf:$smarty.section.partner.index}].png" alt="" style="height: 1em; vertical-align: -0.1em;" />
          [{/section}]
        </div>
        <script src="https://cdn.barzahlen.de/js/selection.js"></script>
      </dd>
  </dl>
[{/if}]
[{else}]
  [{$smarty.block.parent}]
[{/if}]