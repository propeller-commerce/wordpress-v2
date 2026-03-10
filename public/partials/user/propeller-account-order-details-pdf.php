<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<a href="#" data-action="download_order_pdf" target="_blank" data-order="<?php echo esc_attr($order->id); ?>" class="order-pdf-btn"><?php echo esc_html(__('Order Confirmation (PDF)', 'propeller-ecommerce-v2')); ?></a>