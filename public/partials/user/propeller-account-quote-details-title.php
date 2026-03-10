<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="order-number"><strong><?php echo esc_html( __('Quote number', 'propeller-ecommerce-v2') ); ?>:</strong> <?php echo esc_html($order->id); ?></div>