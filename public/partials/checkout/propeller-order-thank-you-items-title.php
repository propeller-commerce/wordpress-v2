<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col-12">
    <div class="order-summary-title">
        <?php 
            if ($order->status == 'REQUEST') 
                echo esc_html(__('Your quote request', 'propeller-ecommerce-v2')); 
            else 
                echo esc_html(__('Your order', 'propeller-ecommerce-v2')); 
        ?>
    </div>
</div>