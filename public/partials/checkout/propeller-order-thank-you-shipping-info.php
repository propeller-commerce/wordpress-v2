<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

?>
<div class="order-shipping-details">
    <?php echo esc_html(__('Shipping costs', 'propeller-ecommerce-v2')); ?>: 
    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="shipping-costs"><?php echo esc_html(PropellerHelper::formatPrice($order->postageData->gross)); ?></span><br>
    <!--  <?php echo esc_html(__('Expected delivery', 'propeller-ecommerce-v2')); ?>: <span class="order-delivery-date">Wednesday July 28</span> -->
</div>