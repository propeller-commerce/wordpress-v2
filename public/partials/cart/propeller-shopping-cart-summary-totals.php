<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);
?>
<div class="shopping-cart-totals">
    <div class="row align-items-baseline">
        <?php
        $count = 0;
        foreach ($order->items as $item) {
            if ($item->class === 'product') {
                $count++;
            }
        }
        ?>
        <div class="col-12">
            <div class="sc-items">
                <?php
                echo esc_html(__('Overview', 'propeller-ecommerce-v2'));
                ?> (<span class="propel-total-items"><?php echo esc_html($count); ?></span> <?php echo esc_html(__('products', 'propeller-ecommerce-v2')); ?>)
            </div>
            <hr>
        </div>
    </div>

    <div class="row align-items-baseline sc-calculation">
        <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html(__('Subtotal', 'propeller-ecommerce-v2')); ?></div>
        <div class="col-4 col-lg-4 ms-auto sc-price text-end">
            <div class="sc-total">

                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-subtotal">
                    <?php if ($user_prices == false)

                        echo esc_html(PropellerHelper::formatPrice($order->total->gross));
                    else
                        echo esc_html(PropellerHelper::formatPrice($order->total->net)); ?>
                </span>
            </div>
        </div>
    </div>
    <?php if (!empty($order->total->discountPercentage)) { ?>
        <div class="row align-items-baseline sc-calculation propel-discount">
            <div class="col-8 col-lg-5"><?php echo esc_html(__('Discount', 'propeller-ecommerce-v2')); ?></div>
            <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                <div class="sc-total">
                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-voucher"><?php echo esc_html(PropellerHelper::formatPrice($order->total->discountPercentage)); ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="row align-items-baseline sc-calculation">
        <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html(__('Shipping costs', 'propeller-ecommerce-v2')); ?></div>
        <div class="col-4 col-lg-4 ms-auto sc-price text-end">
            <div class="sc-total">
                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-shipping"><?php echo esc_html(PropellerHelper::formatPrice($order->postageData->gross)); ?></span>
            </div>
        </div>
    </div>
    <div class="row align-items-baseline sc-calculation">
        <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html(__('Total excl. VAT', 'propeller-ecommerce-v2')); ?></div>
        <div class="col-4 col-lg-4 ms-auto sc-price text-end">
            <div class="sc-total">
                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-excl-btw"><?php echo esc_html(PropellerHelper::formatPrice($order->total->gross)); ?></span>
            </div>
        </div>
    </div>
    <div class="row align-items-baseline sc-calculation">

        <?php
        $taxPercentage = '';
        if (!empty($order->total->taxPercentages)) {
            foreach ($order->total->taxPercentages as $taxPercentages) {
                $taxPercentage = $taxPercentages->percentage;
            }
        }

        ?>
        <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html($taxPercentage); ?>% <?php echo esc_html(__('VAT', 'propeller-ecommerce-v2')); ?></div>
        <div class="col-4 col-lg-4 ms-auto sc-price text-end">
            <div class="sc-total-btw">
                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-btw"><?php echo esc_html(PropellerHelper::formatPrice($order->total->tax)); ?></span>
            </div>
        </div>
    </div>
    <div class="sc-grand-total">
        <div class="row align-items-baseline">
            <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html(__('Total', 'propeller-ecommerce-v2')); ?></div>
            <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                <div class="sc-total">
                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-price"><?php echo esc_html(PropellerHelper::formatPrice($order->total->net)); ?></span>
                </div>
            </div>
        </div>

    </div>
</div>