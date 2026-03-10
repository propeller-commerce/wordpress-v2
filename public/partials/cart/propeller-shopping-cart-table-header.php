<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="container-fluid px-0">
    <div class="row g-0 shopping-cart-headers align-items-center">
        <div class="col-md-5 col-lg-7 description d-none d-md-flex">
            <?php echo esc_html(__('Products', 'propeller-ecommerce-v2')); ?>
        </div>
        <!-- <div class="col-md-2 reference">
            <?php echo esc_html(__('Reference', 'propeller-ecommerce-v2')); ?>
        </div> -->
        <div class="col-md-2 col-lg-2 price-per-item d-none d-md-flex">
            <?php echo esc_html(__('Price per piece', 'propeller-ecommerce-v2')); ?>
        </div>
        <div class="col-md-2 col-lg-1 quantity d-none d-md-flex">
            <?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?>
        </div>
        <div class="col-md-2 price-total d-none d-md-flex">
            <?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?>
        </div>
    </div>
</div>