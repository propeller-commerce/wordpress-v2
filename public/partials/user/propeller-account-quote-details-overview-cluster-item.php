<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

?>
<div class="order-cluster-items">
    <div class="row g-0">
        <div class="col-10 col-md-4 col-lg-5 pe-5 product-description offset-2 offset-lg-1">
            <?php echo esc_html($item->product->get_name()); ?>
        </div>
        <div class="offset-2 offset-md-0 col-2 col-md-2 order-3">
            <div class="d-block d-md-none label-title"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></div>
            <span class="product-quantity"><?php echo esc_html($item->quantity); ?></span>
        </div>
        <div class="col-3 col-md-2 order-discount order-4">
            <div class="d-block d-md-none label-title"><?php echo esc_html(__('Discount', 'propeller-ecommerce-v2')); ?></div>
            <?php if ($item->discount > 0) { ?>
                <span class="discount">
                    <?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;<?php echo esc_html(PropellerHelper::formatPrice($item->discount * $item->quantity)); ?>
                    <?php echo esc_html($item->product->price->gross > 0 ? '(' . PropellerHelper::percentage_from_price($item->discount, $item->product->price->gross) . '%)' : ''); ?>
                </span>
            <?php } else if ($item->price < $item->originalPrice) { ?>
                <span class="discount">
                    <?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;<?php echo esc_html(PropellerHelper::formatPrice($item->originalPrice - $item->price)); ?>
                    <?php echo esc_html('(' . PropellerHelper::percentage_from_price($item->price, $item->originalPrice) . '%)'); ?>
                </span>
            <?php } ?>
        </div>
        <div class="col-3 col-md-2 price-per-item order-5 text-end">
            <div class="d-block d-md-none label-title"><?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?></div>
            <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                <?php echo esc_html(PropellerHelper::formatPrice($item->priceTotal)); ?>
            </span>
        </div>

    </div>
</div>