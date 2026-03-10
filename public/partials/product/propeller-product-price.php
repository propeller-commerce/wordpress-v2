<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

if ($product->priceData->display == "FROM_FOR") { ?>
    <?php if ($user_prices == false) { ?>
        <div class="product-current-price has-discount d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->price->gross) ); ?> <?php echo esc_html( __('excl. VAT', 'propeller-ecommerce-v2') ); ?></span></div>
        <div class="product-old-price d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->priceData->suggested) ); ?> </span></div>
    <?php } else { ?>
        <div class="product-current-price has-discount d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->price->net) ); ?> <?php echo esc_html( __('incl. VAT', 'propeller-ecommerce-v2') ); ?></span></div>
    <?php } ?>

<?php } else if ($user_prices == false) { ?>
    <div class="product-current-price"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->price->gross) ); ?> <?php echo esc_html( __('excl. VAT', 'propeller-ecommerce-v2') ); ?></span></div>
<?php } else { ?>
    <div class="product-current-price"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->price->net) ); ?> <?php echo esc_html( __('incl. VAT', 'propeller-ecommerce-v2') ); ?></span></div>
<?php } ?>
<div class="product-package-details">
    <span class="product-package"></span>
    <?php if ($user_prices == true) { ?>
        <span class="product-price-tax"><?php echo esc_html(__('Retail suggested price', 'propeller-ecommerce-v2')); ?> <?php echo esc_html( PropellerHelper::currency() ); ?> <?php echo esc_html( PropellerHelper::formatPrice($product->price->net) ); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span>
    <?php } ?>
</div>