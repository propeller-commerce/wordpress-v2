<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\SessionController;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);
?>
<div class="col-12 col-lg-4">
    <?php
    if ($cart->total->totalNet >= 0) { ?>
        <div class="shopping-cart-totals">
            <div class="row align-items-baseline d-flex">
                <div class="col-12">
                    <div class="sc-items"><?php echo esc_html(__('Overview', 'propeller-ecommerce-v2')); ?> (<span class="propel-total-items"><?php echo esc_html($this->get_items_count()); ?></span> <?php echo esc_html(__('items', 'propeller-ecommerce-v2')); ?>)</div>
                    <hr>
                </div>
            </div>
            <div class="row align-items-baseline sc-calculation d-flex">
                <div class="col-6 col-lg-6 col-xl-5"><?php echo esc_html(__('Subtotal', 'propeller-ecommerce-v2')); ?></div>
                <div class="col-6 ms-auto sc-price text-end">
                    <div class="sc-total">
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                        <span class="propel-total-subtotal"><?php echo esc_html(PropellerHelper::formatPrice($cart->total->subTotal)); ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php

            if ($cart->total->discount > 0) { ?>
                <div class="row align-items-baseline sc-calculation propel-discount d-flex">
                    <div class="col-6 col-lg-5"><?php echo esc_html(__('Discount', 'propeller-ecommerce-v2')); ?></div>
                    <div class="col-6 ms-auto sc-price text-end">
                        <div class="sc-total">

                            <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                            <span class="propel-total-voucher"><?php echo esc_html(PropellerHelper::formatPrice($cart->total->discount)); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row align-items-baseline sc-calculation d-flex">
                <div class="col-6 col-xl-5"><?php echo esc_html(__('Shipping costs', 'propeller-ecommerce-v2')); ?></div>
                <div class="col-6 ms-auto sc-price text-end">
                    <div class="sc-total">
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-shipping">
                            <?php echo esc_html(PropellerHelper::formatPrice($cart->postageData->price)); ?>
                        </span>
                        <?php /* if (!PROPELLER_SELECTABLE_CARRIERS) { ?>
                        <span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><span class="propel-total-shipping"><?php echo PropellerHelper::formatPrice($cart->postageData->priceNet) ?></span>
                    <?php } else { ?>
                        <span class="propel-total-shipping orangec-c">*<?php echo esc_html(__('Final shipment costs will be calculated in the last overview page before checking out.', 'propeller-ecommerce-v2')); ?></span>
                    <?php } */ ?>
                    </div>
                </div>
            </div>
            <div class="row align-items-baseline sc-calculation d-flex">
                <div class="col-6 col-lg-6 col-xl-5"><?php echo esc_html(__('Total excl. VAT', 'propeller-ecommerce-v2')); ?></div>
                <div class="col-6 ms-auto sc-price text-end">
                    <div class="sc-total">
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-excl-btw"><?php echo esc_html(PropellerHelper::formatPrice($cart->total->totalGross)); ?></span>
                    </div>
                </div>
            </div>
            <?php
            $taxPercentage = '0';
            if (!empty($cart->taxLevels)) {
                foreach ($cart->taxLevels as $taxLevel) {
                    if ($taxLevel->price == 0 || $taxLevel->taxPercentage == 0)
                        continue;
            ?>
                    <div class="row align-items-baseline sc-calculation d-flex">
                        <?php $taxPercentage = $taxLevel->taxPercentage; ?>

                        <div class="col-6 col-xl-5"><?php echo esc_html($taxPercentage); ?>% <?php echo esc_html(__('VAT', 'propeller-ecommerce-v2')); ?></div>

                        <div class="col-6 ms-auto sc-price text-end">
                            <div class="sc-total-btw">
                                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-postage-tax" data-tax="<?php echo esc_html($taxPercentage); ?>"><?php echo esc_html(PropellerHelper::formatPrice($taxLevel->price)); ?></span>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            ?>

            <?php if (count($cart->taxLevels) > 1) { ?>
                <div class="row align-items-baseline sc-calculation d-flex">
                    <div class="col-6 col-xl-5"><?php echo esc_html(__('Total', 'propeller-ecommerce-v2')); ?> <?php echo esc_html(__('VAT', 'propeller-ecommerce-v2')); ?></div>

                    <div class="col-6 ms-auto sc-price text-end">
                        <div class="sc-total-btw">
                            <?php
                            $totalNet = $cart->total->totalNet;
                            $totalGross = $cart->total->totalGross;
                            $totalBTW = $totalNet - $totalGross;
                            ?>
                            <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-btw"><?php echo esc_html(PropellerHelper::formatPrice($totalBTW)); ?></span>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="sc-grand-total">
                <div class="row align-items-baseline d-flex">
                    <div class="col-6 col-xl-5"><?php echo esc_html(__('Total', 'propeller-ecommerce-v2')); ?></div>
                    <div class="col-6 ms-auto sc-price text-end">
                        <div class="sc-total">
                            <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-price"><?php echo esc_html(PropellerHelper::formatPrice($cart->total->totalNet)); ?></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <?php } ?>
</div>