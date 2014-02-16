[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
  [{ $oViewConf->getHiddenSid() }]
  <input type="hidden" name="cur" value="[{ $oCurr->id }]">
  <input type="hidden" name="oxid" value="[{ $oxid }]">
  <input type="hidden" name="cl" value="barzahlen_transactions">
</form>

[{if ($info)}]
  <div class="[{$info.class}]">
    [{oxmultilang ident=$info.message}]
    [{if $info.message == 'BARZAHLEN__REFUND_TOO_HIGH' }]
    [{$refundable|number_format:2:',':'.'}] [{ $order->oxorder__oxcurrency->value }] EUR
    [{/if}]
  </div>
[{/if}]

<div style="position: relative; float: left;">
<h2><img src="http://cdn.barzahlen.de/images/barzahlen_logo.png" alt="[{oxmultilang ident="BARZAHLEN__BARZAHLEN"}]"></h2>
<div style="font-size: 1.1em; font-weight: bold; line-height: 1.5em; margin-left: 10px;">
<img src="http://cdn.barzahlen.de/images/barzahlen_icon_website.png" width="16" height="16" alt="" style="vertical-align: -3px;"/>&nbsp;<a href="http://www.barzahlen.de" target="_blank">[{oxmultilang ident="BARZAHLEN__WEBSITE"}]</a><br>
<img src="http://cdn.barzahlen.de/images/barzahlen_icon_merchant_area.png" width="16" height="16" alt="" style="vertical-align: -3px;"/>&nbsp;<a href="https://www.barzahlen.de/merchant-area/" target="_blank">[{oxmultilang ident="BARZAHLEN__MERCHANT_AREA"}]</a><br>
<img src="http://cdn.barzahlen.de/images/barzahlen_icon_support.png" width="16" height="16" alt="" style="vertical-align: -3px;"/>&nbsp;<a href="mailto:support@barzahlen.de">[{oxmultilang ident="BARZAHLEN__SUPPORT"}]</a>
</div>
</div>

<div style="position: relative; float: left; margin: 0px 0px 0px 30px;">
[{if $transactionId > 0}]
        <h3>[{oxmultilang ident="BARZAHLEN__PAYMENT"}]</h3>
        <table cellspacing="0" cellpadding="0" border="0"  style="width: 600px; text-align: center;">
          <tr>
            <td class="listheader first" width="30%">[{oxmultilang ident="BARZAHLEN__TRANSACTION_ID"}]</td>
            <td class="listheader" width="30%">[{oxmultilang ident="BARZAHLEN__STATE"}]</td>
            <td class="listheader" width="40%"></td>
          </tr>
          <tr>
            <td>[{ $transactionId }]</td>
            <td>[{oxmultilang ident=$state}]</td>
            <td>
              [{if $state == 'BARZAHLEN__STATE_PENDING'}]
              <form action="[{ $oViewConf->getSelfLink() }]" method="post">
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="cur" value="[{ $oCurr->id }]">
                <input type="hidden" name="oxid" value="[{ $oxid }]">
                <input type="hidden" name="cl" value="barzahlen_transactions">
                <input type="hidden" name="fnc" value="resendPaymentSlip">
                <input type="submit" value="[{oxmultilang ident="BARZAHLEN__RESEND_PAYMENT_SLIP"}]">
              </form>
              [{/if}]
            </td>
          </tr>
        </table>

[{if ($refunds)}]
        <br/><br/><h3>[{oxmultilang ident="BARZAHLEN__REFUNDS"}]</h3>
  <table cellspacing="0" cellpadding="0" border="0"  style="width: 600px; text-align: center;">
    <tr>
      <td class="listheader first" width="30%">[{oxmultilang ident="BARZAHLEN__REFUND_TRANSACTION_ID"}]</td>
      <td class="listheader" width="15%">[{oxmultilang ident="BARZAHLEN__AMOUNT"}]</td>
      <td class="listheader" width="15%">[{oxmultilang ident="BARZAHLEN__STATE"}]</td>
      <td class="listheader" width="40%"></td>
    </tr>
    [{foreach from=$refunds item=refund}]
    <tr>
      <td>[{ $refund.refundid }]</td>
      <td>[{ $refund.amount|number_format:2:',':'.' }] [{ $currency }]</td>
      <td>[{oxmultilang ident=$refund.state}]</td>
      <td>
        [{if $refund.state == 'BARZAHLEN__STATE_PENDING'}]
        <form action="[{ $oViewConf->getSelfLink() }]" method="post">
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cur" value="[{ $oCurr->id }]">
          <input type="hidden" name="oxid" value="[{ $oxid }]">
          <input type="hidden" name="cl" value="barzahlen_transactions">
          <input type="hidden" name="fnc" value="resendRefundSlip">
          <input type="hidden" name="refundId" value="[{ $refund.refundid }]">
          <input type="submit" value="[{oxmultilang ident="BARZAHLEN__RESEND_REFUND_SLIP"}]">
        </form>
        [{/if}]
      </td>
    </tr>
    [{/foreach}]
  </table>
[{/if}]
[{if round($refundable) > 0}]
<br><br>
  <table cellspacing="0" cellpadding="0" border="0"  style="width: 600px; text-align: center;">
    <tr>
    <form action="[{ $oViewConf->getSelfLink() }]" method="post">
      [{ $oViewConf->getHiddenSid() }]
      <input type="hidden" name="cur" value="[{ $oCurr->id }]">
      <input type="hidden" name="oxid" value="[{ $oxid }]">
      <input type="hidden" name="cl" value="barzahlen_transactions">
      <input type="hidden" name="fnc" value="requestRefund">
      <td class="listheader first" width="40%">
        [{oxmultilang ident="BARZAHLEN__NEW_REFUND"}] (max. [{$refundable|number_format:2:',':'.'}] [{ $currency }])
      </td>
      <td class="listheader" width="20%">
        <input type="text" name="refund_amount" size="10">&nbsp;[{ $currency }]
      </td>
      <td class="listheader" width="40%">
        <input type="submit" value="[{oxmultilang ident="BARZAHLEN__REQUEST_REFUND"}]">
      </td>
    </form>
    </tr>
  </table>
[{/if}]

[{else}]
<br/>
<strong>[{oxmultilang ident="BARZAHLEN__NOT_BARZAHLEN"}]</strong>
[{/if}]
</div>