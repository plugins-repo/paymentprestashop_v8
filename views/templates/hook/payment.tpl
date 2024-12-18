<form action="{$paymentUrl}" method="post">
    <div class="paymentprestashop_v8-container">
        <p>
            {l s='You have selected to pay with our Paymentprestashop_v8 payment gateway.' mod='paymentprestashop_v8'}
        </p>

        {* Loop through payload to send hidden data *}
        {foreach from=$payload key=key item=value}
            <input type="hidden" name="{$key}" value="{$value}">
        {/foreach}
    </div>
</form>

