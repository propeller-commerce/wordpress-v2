<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<fieldset>
    <div class="row form-group">
        <div class="col-form-fields col-12" id="invoice">
            <div class="row g-3 invoice-addresses-wrapper">
                <?php                        
                    foreach ($invoice_addresses as $invoice_address) {
                        if (!empty($invoice_address->street))
                            apply_filters('propel_checkout_invoice_address', $invoice_address, $cart, $obj);
                    }
                ?>
            </div>
        </div>
    </div>
</fieldset>