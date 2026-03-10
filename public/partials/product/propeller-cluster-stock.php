<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col d-flex justify-content-end">
    <?php if (!empty($cluster_product->inventory) && $cluster_product->inventory->totalQuantity > 0) { ?>
        <div class="product-stock"><span class="product-stock-val"><?php echo esc_html( __('Available', 'propeller-ecommerce-v2') ); ?></span><span class="quantity-stock">: <?php echo esc_html($cluster_product->inventory->totalQuantity); ?></span></div>
    <?php }  else { ?>
        <div class="product-stock out-of-stock"><span class="product-stock-val"><?php echo esc_html( __('Available as backorder', 'propeller-ecommerce-v2') ); ?></span></div>
    <?php } ?>
</div>