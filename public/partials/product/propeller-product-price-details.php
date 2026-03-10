<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\SurchargeType;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

?>
<div class="row g-0 align-items-end product-price-details">
    <div class="col-auto">
        <div class="product-price">
            <?php if (!$product->is_price_on_request()) {
            ?>
                <?php if ($product->priceData->display == 'FROM_FOR') { ?>
                    <div class="product-old-price d-md-block"> <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($product->priceData->suggested)); ?></span></div>
                    <?php if ($user_prices == false) { ?>
                        <div class="product-current-price has-discount d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span> <?php echo esc_html(PropellerHelper::formatPrice($product->price->gross)); ?> <?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                    <?php } else { ?>
                        <div class="product-current-price has-discount d-md-inline-flex"> <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($product->price->net)); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                    <?php } ?>

                <?php } else if ($user_prices == false) { ?>
                    <div class="product-current-price"> <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($product->price->gross)); ?> <?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                <?php } else { ?>
                    <div class="product-current-price"> <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($product->price->net)); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                <?php } ?>

                <?php if ($user_prices == false) { ?>
                    <small class="product-customer-price d-flex">
                        <span class="product-price-tax"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span> <?php echo esc_html(PropellerHelper::formatPrice($product->price->net)); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span>
                    </small>
                <?php } else { ?>
                    <small class="product-customer-price d-flex">
                        <span class="product-price-tax"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span> <?php echo esc_html(PropellerHelper::formatPrice($product->price->gross)); ?> <?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></span>
                    </small>
                <?php }
            } else { ?>
                <div class="product-price price-on-request">
                    <div class="price">
                        <?php echo esc_html(__('Price on request', 'propeller-ecommerce-v2')); ?>
                    </div>
                </div>
            <?php } ?>

            <?php echo esc_html(apply_filters('propel_product_surcharges', $product)); ?>
        </div>
    </div>

    <?php echo esc_html(apply_filters('propel_product_stock', $product)); ?>

</div>