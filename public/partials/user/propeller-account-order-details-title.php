<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="order-number"><strong><?php echo esc_html( __('Order number', 'propeller-ecommerce-v2') ); ?>:</strong> <?php echo esc_attr($order->id); ?></div>