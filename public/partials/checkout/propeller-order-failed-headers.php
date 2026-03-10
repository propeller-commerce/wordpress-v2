<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="container-fluid px-0 checkout-header-wrapper">
    <div class="row align-items-start">
        <div class="col-12 col-sm me-auto checkout-header">
            <?php if ($order->status == 'REQUEST') { ?>
                <h1><?php echo esc_html(__('Something went wrong in the payment process', 'propeller-ecommerce-v2')); ?></h1>
            <?php } else { ?>
                <h1><?php echo esc_html(__('Something went wrong in the payment process', 'propeller-ecommerce-v2')); ?></h1>
            <?php } ?>
        </div>
        <div class="col-12 order-details">
            <?php if ($order->status == 'REQUEST') { ?>
                <div><strong><?php echo esc_html(__('Your payment has failed.', 'propeller-ecommerce-v2')); ?></strong></div>
            <?php } else { ?>
                <div><strong><?php echo esc_html(__('Your payment has failed.', 'propeller-ecommerce-v2')); ?></strong></div>
            <?php } ?>
        </div>
    </div>
</div>