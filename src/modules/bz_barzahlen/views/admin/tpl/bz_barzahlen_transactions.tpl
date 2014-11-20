[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
  [{ $oViewConf->getHiddenSid() }]
  <input type="hidden" name="cur" value="[{ $oCurr->id }]">
  <input type="hidden" name="oxid" value="[{ $oxid }]">
  <input type="hidden" name="cl" value="bz_barzahlen_transactions">
</form>

[{if ($oView->getInfoMessage())}]
  <div class="[{$oView->getInfoClass()}]">
    [{oxmultilang ident=$oView->getInfoMessage()}]
    [{if $oView->getInfoMessage() == 'BZ__REFUND_TOO_HIGH' }]
    [{$oView->getTransactionRefundable()|number_format:2:',':'.'}] [{ $order->oxorder__oxcurrency->value }] EUR
    [{/if}]
  </div>
[{/if}]

<div style="position: relative; float: left;">
<h2><img src="https://cdn.barzahlen.de/images/checkout_barzahlen_logo.png" alt="[{oxmultilang ident="BZ__BARZAHLEN"}]"></h2>
<div style="font-size: 1.1em; font-weight: bold; line-height: 1.5em; margin-left: 10px;">
<img src="https://cdn.barzahlen.de/images/barzahlen_icon_website.png" width="16" height="16" alt="" style="vertical-align: -3px;"/>&nbsp;<a href="https://www.barzahlen.de" target="_blank">[{oxmultilang ident="BZ__WEBSITE"}]</a><br>
<img src="https://cdn.barzahlen.de/images/barzahlen_icon_merchant_area.png" width="16" height="16" alt="" style="vertical-align: -3px;"/>&nbsp;<a href="https://partner.barzahlen.de" target="_blank">[{oxmultilang ident="BZ__MERCHANT_AREA"}]</a><br>
<img src="https://cdn.barzahlen.de/images/barzahlen_icon_support.png" width="16" height="16" alt="" style="vertical-align: -3px;"/>&nbsp;<a href="mailto:support@barzahlen.de">[{oxmultilang ident="BZ__SUPPORT"}]</a>
</div>
</div>

<div style="position: relative; float: left; margin: 0px 0px 0px 30px;">
[{if ($oView->getTransactionId())}]
        <h3>[{oxmultilang ident="BZ__PAYMENT"}]</h3>
        <table cellspacing="0" cellpadding="0" border="0"  style="width: 600px; text-align: center;">
          <tr>
            <td class="listheader first" width="30%">[{oxmultilang ident="BZ__TRANSACTION_ID"}]</td>
            <td class="listheader" width="30%">[{oxmultilang ident="BZ__STATE"}]</td>
            <td class="listheader" width="40%">&nbsp;</td>
          </tr>
          <tr>
            <td>[{ $oView->getTransactionId() }]</td>
            <td>[{oxmultilang ident=$oView->getTransactionState()}]</td>
            <td>
              [{if $oView->getTransactionState() == 'BZ__STATE_PENDING'}]
              <form action="[{ $oViewConf->getSelfLink() }]" method="post">
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="cur" value="[{ $oCurr->id }]">
                <input type="hidden" name="oxid" value="[{ $oxid }]">
                <input type="hidden" name="cl" value="bz_barzahlen_transactions">
                <input type="hidden" name="fnc" value="resendPaymentSlip">
                <input type="submit" value="[{oxmultilang ident="BZ__RESEND_PAYMENT_SLIP"}]">
              </form>
              [{/if}]
            </td>
          </tr>
        </table>

[{if ($oView->getTransactionRefunds())}]
        <br/><br/><h3>[{oxmultilang ident="BZ__REFUNDS"}]</h3>
  <table cellspacing="0" cellpadding="0" border="0"  style="width: 600px; text-align: center;">
    <tr>
      <td class="listheader first" width="30%">[{oxmultilang ident="BZ__REFUND_TRANSACTION_ID"}]</td>
      <td class="listheader" width="15%">[{oxmultilang ident="BZ__AMOUNT"}]</td>
      <td class="listheader" width="15%">[{oxmultilang ident="BZ__STATE"}]</td>
      <td class="listheader" width="40%">&nbsp;</td>
    </tr>
    [{foreach from=$oView->getTransactionRefunds() item=refund}]
    <tr>
      <td>[{ $refund.refundid }]</td>
      <td>[{ $refund.amount|number_format:2:',':'.' }] [{ $currency }]</td>
      <td>[{oxmultilang ident=$refund.state}]</td>
      <td>
        [{if $refund.state == 'BZ__STATE_PENDING'}]
        <form action="[{ $oViewConf->getSelfLink() }]" method="post">
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cur" value="[{ $oCurr->id }]">
          <input type="hidden" name="oxid" value="[{ $oxid }]">
          <input type="hidden" name="cl" value="bz_barzahlen_transactions">
          <input type="hidden" name="fnc" value="resendRefundSlip">
          <input type="hidden" name="refundId" value="[{ $refund.refundid }]">
          <input type="submit" value="[{oxmultilang ident="BZ__RESEND_REFUND_SLIP"}]">
        </form>
        [{/if}]
      </td>
    </tr>
    [{/foreach}]
  </table>
[{/if}]
[{if round($oView->getTransactionRefundable(),2) > 0}]
<br><br>
  <table cellspacing="0" cellpadding="0" border="0"  style="width: 600px; text-align: center;">
    <tr>
    <form action="[{ $oViewConf->getSelfLink() }]" method="post">
      [{ $oViewConf->getHiddenSid() }]
      <input type="hidden" name="cur" value="[{ $oCurr->id }]">
      <input type="hidden" name="oxid" value="[{ $oxid }]">
      <input type="hidden" name="cl" value="bz_barzahlen_transactions">
      <input type="hidden" name="fnc" value="requestRefund">
      <td class="listheader first" width="40%">
        [{oxmultilang ident="BZ__NEW_REFUND"}] (max. [{$oView->getTransactionRefundable()|number_format:2:',':'.'}] [{ $currency }])
      </td>
      <td class="listheader" width="20%">
        <input type="text" name="refund_amount" size="10">&nbsp;[{ $currency }]
      </td>
      <td class="listheader" width="40%">
        <input type="submit" value="[{oxmultilang ident="BZ__REQUEST_REFUND"}]">
      </td>
    </form>
    </tr>
  </table>
[{/if}]

[{else}]
<br/>
<strong>[{oxmultilang ident="BZ__NOT_BARZAHLEN"}]</strong>
[{/if}]
</div>