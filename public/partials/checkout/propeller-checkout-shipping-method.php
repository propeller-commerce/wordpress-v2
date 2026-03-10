<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

?>
<fieldset class="propel-checkout-shipping-info">
    <legend class="checkout-header">
        <?php echo esc_html(__('Shipping method', 'propeller-ecommerce-v2')); ?>
    </legend>
    <div class="row form-group">
        <div class="col-form-fields col-12">
            <div class="row px-2 row g-3 form-check">
                <div class="col-12">
                    <div class="shipping-cost-wrapper justify-content-between d-flex align-items-center">
                        <div class="carrier-name col-6"><?php echo esc_html(__('Shipping costs', 'propeller-ecommerce-v2')); ?></div>
                        <div class="carrier-cost col-4 text-end"><?php echo esc_html(PropellerHelper::currency()); ?> <?php echo esc_html(PropellerHelper::formatPrice($cart->postageData->price)); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</fieldset>