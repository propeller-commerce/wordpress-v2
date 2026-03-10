<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="container-fluid px-0 checkout-header-wrapper">
    <div class="row align-items-start">
        <div class="col-12 col-sm me-auto checkout-header">
            <?php if ($order->status == 'REQUEST') { ?>
                <h1><?php echo esc_html(__('Your payment is being cancelled.', 'propeller-ecommerce-v2')); ?></h1>
            <?php } else { ?>
                <h1><?php echo esc_html(__('Your payment is being cancelled.', 'propeller-ecommerce-v2')); ?></h1>
            <?php } ?>
        </div>
    </div>
</div>