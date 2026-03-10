<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="order-headers d-none d-md-flex">
    <div class="row w-100 g-0 align-items-center">
        <div class="col-md-7 description">
            <?php echo esc_html(__('Products', 'propeller-ecommerce-v2')); ?>
        </div>
        <!--<div class="col-md-2 reference">
                <?php echo esc_html(__('Reference', 'propeller-ecommerce-v2')); ?>
        </div> -->
        <div class="col-md-2 quantity">
            <?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?>
        </div>
        <div class="col-md-3 price-per-item text-end">
            <?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?>
        </div>
    </div>
</div>