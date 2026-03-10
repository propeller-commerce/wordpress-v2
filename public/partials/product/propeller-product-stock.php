<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col d-flex justify-content-end">
    <?php if (!empty($product->inventory) && $product->inventory->totalQuantity > 0) { ?>
        <div class="product-stock"><?php echo esc_html( __('Available', 'propeller-ecommerce-v2') ); ?><span class="quantity-stock">: <?php echo esc_html($product->inventory->totalQuantity); ?></span></div>
    <?php }  else { ?>
        <div class="product-stock out-of-stock"><?php echo esc_html( __('Available as backorder', 'propeller-ecommerce-v2') ); ?></div>
    <?php } ?>
</div>