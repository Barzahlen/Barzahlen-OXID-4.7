[{if $smarty.get.payerrortext == 'barzahlen'}]
  <div class="status error">
    [{ oxmultilang ident="BZ__PAGE_CHECKOUT_PAYMENT_ERROR" }]
  </div>
[{else}]
  [{$smarty.block.parent}]
[{/if}]