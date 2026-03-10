<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\DiscountType;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);
$subTotal = 0;
$result = 0;

foreach ($order->items as $item)
    if ($item->class == 'product')
        $subTotal += $item->priceTotal;

if (!empty($order->total->discountValue)) {
    if ($order->total->discountType == DiscountType::PERCENTAGE) {
        $result = $subTotal - ($subTotal * $order->total->discountValue / 100);
    } else {
        $result = $subTotal - $order->total->discountValue;
    }
}

?>
<div class="shopping-cart-totals">
    <div class="row align-items-baseline">
        <div class="col-12">
            <div class="sc-items">
                <?php
                if ($order->status == 'REQUEST')
                    echo esc_html(__('Quote totals', 'propeller-ecommerce-v2'));
                else
                    echo esc_html(__('Order totals', 'propeller-ecommerce-v2'));
                ?>
            </div>
            <hr>
        </div>
    </div>

    <div class="row align-items-baseline sc-calculation">
        <div class="col-8 col-lg-6"><?php echo esc_html(__('Subtotal', 'propeller-ecommerce-v2')); ?></div>
        <div class="col-4 col-lg-4 ms-auto sc-price text-end">
            <div class="sc-total">
                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-subtotal"><?php echo esc_html(PropellerHelper::formatPrice($subTotal)); ?></span>
            </div>
        </div>
    </div>
    <?php if ($order->total->discountType != DiscountType::NONE && isset($order->total->discountValue) && !empty($order->total->discountValue)) { ?>
        <div class="row align-items-baseline sc-calculation propel-discount">
            <div class="col-8 col-lg-6"><?php echo esc_html(__('Discount', 'propeller-ecommerce-v2')); ?></div>
            <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                <div class="sc-total">
                    <?php if ($order->total->discountType == DiscountType::AMOUNT) { ?>
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-voucher">-<?php echo esc_html(PropellerHelper::formatPrice($order->total->discountValue)); ?></span>
                    <?php } else if ($order->total->discountType == DiscountType::PERCENTAGE) { ?>
                        <span class="propel-total-voucher">- <?php echo esc_html($order->total->discountValue); ?>&#37;</span>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($result > 0) { ?>
        <div class="row align-items-baseline sc-calculation">
            <div class="col-8 col-lg-6"><?php echo esc_html(__('Subtotal with discount', 'propeller-ecommerce-v2')); ?></div>
            <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                <div class="order-total">
                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="order-total-subtotal"><?php echo esc_html(PropellerHelper::formatPrice($result)); ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($order->paymentData->gross) { ?>
        <div class="row align-items-baseline sc-calculation">
            <div class="col-8 col-lg-6"><?php echo esc_html(__('Transaction costs', 'propeller-ecommerce-v2')); ?></div>
            <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                <div class="order-total">
                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="order-total-excl-btw"><?php echo esc_html(PropellerHelper::formatPrice($order->paymentData->gross)); ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="row align-items-baseline sc-calculation">
        <div class="col-8 col-lg-6"><?php echo esc_html(__('Shipping costs', 'propeller-ecommerce-v2')); ?></div>
        <div class="col-4 col-lg-4 ms-auto sc-price text-end">
            <div class="sc-total">
                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-shipping"><?php echo esc_html(PropellerHelper::formatPrice($order->postageData->gross)); ?></span>
            </div>
        </div>
    </div>
    <div class="row align-items-baseline sc-calculation">
        <div class="col-8 col-lg-6"><?php echo esc_html(__('Total excl. VAT', 'propeller-ecommerce-v2')); ?></div>
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
            foreach ($order->total->taxPercentages as $taxPercentage) {
                if ($taxPercentage->percentage == 0 || $taxPercentage->total == 0)
                    continue;


        ?>
                <div class="col-8 col-lg-6"><?php echo esc_html($taxPercentage->percentage); ?>% <?php echo esc_html(__('VAT', 'propeller-ecommerce-v2')); ?></div>
                <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                    <div class="sc-total-btw">
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-btw"><?php echo esc_html(PropellerHelper::formatPrice($taxPercentage->total)); ?></span>
                    </div>
                </div>
        <?php }
        } ?>
    </div>
    <div class="sc-grand-total">
        <div class="row align-items-baseline">
            <div class="col-8 col-lg-6"><?php echo esc_html(__('Total', 'propeller-ecommerce-v2')); ?></div>
            <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                <div class="sc-total">
                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-price"><?php echo esc_html(PropellerHelper::formatPrice($order->total->net)); ?></span>
                </div>
            </div>
        </div>

    </div>
</div>