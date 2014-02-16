[{if $smarty.get.payerrortext == 'barzahlen'}]
  <div class="status error">
    [{ oxmultilang ident="BARZAHLEN__PAGE_CHECKOUT_PAYMENT_ERROR" }]
  </div>
[{else}]
  [{$smarty.block.parent}]
[{/if}]