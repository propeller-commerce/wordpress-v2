<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);
?>
<div class="shopping-cart-totals">
    <div class="row align-items-baseline">
        <div class="col-12">
            <div class="sc-items"><?php echo esc_html(__('Overview', 'propeller-ecommerce-v2')); ?> (<span class="propel-total-items"><?php echo esc_html($obj->get_items_count()); ?></span> <?php echo esc_html(__('items', 'propeller-ecommerce-v2')); ?>)</div>
            <hr>
        </div>
    </div>
    <?php foreach ($obj->get_items() as $item) {
    ?>
        <div class="row align-items-start sc-item">
            <div class="col-3 product-image">
                <?php if ($item->product->has_images()) { ?>
                    <img class="img-fluid" src="<?php echo esc_url($item->product->images[0]->images[0]->url); ?>" alt="<?php echo esc_attr($item->product->get_name()); ?>">
                <?php } else { ?>
                    <img class="img-fluid" src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>" alt="<?php echo esc_html(__('No image found', 'propeller-ecommerce-v2')); ?>">
                <?php } ?>
            </div>
            <div class="col-9 product-description">
                <?php if (!empty($item->bundle)) { ?>
                    <div class="product-name">
                        <?php echo esc_html($item->bundle->name); ?>
                    </div>
                    <ul class="product-bundle-items">
                        <?php echo esc_html(__("Combo products:", 'propeller-ecommerce-v2')); ?>
                        <?php foreach ($item->bundle->items as $bundleItem) {
                            $bundleItem->product = new Product($bundleItem->product);
                        ?>
                            <li><?php echo esc_html($bundleItem->product->get_name()); ?></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <div class="product-sku">
                        <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->product->sku); ?>
                    </div>
                    <div class="product-name row justfiy-content-between">
                        <div class="col">
                            <?php echo esc_html($item->product->get_name()); ?>
                        </div>
                        <?php if (isset($item->childItems) && count($item->childItems)) { ?>
                            <div class="col-auto">
                                <span class="item-price">
                                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                    <?php
                                    echo esc_html(PropellerHelper::formatPrice($item->totalPrice));
                                    ?>
                                </span>
                            </div>
                        <?php } ?>
                    </div>
                    <?php echo esc_html(wp_kses_post(apply_filters('propel_product_surcharges', $item))); ?>
                <?php } ?>
                <?php if (empty($item->bundle) && isset($item->childItems) && count($item->childItems)) { ?>
                    <div class="product-child-items d-block">
                        <ul class="item-children">
                            <?php foreach ($item->childItems as $child_item) {
                                $child_item->product = new Product($child_item->product);
                            ?>
                                <li class="row justify-content-between">
                                    <div class="col-auto">
                                        <?php echo esc_html(__('x', 'propeller-ecommerce-v2') . $child_item->quantity); ?>
                                        <?php echo esc_html($child_item->product->get_name()); ?>
                                    </div>
                                    <div class="col-auto">
                                        <?php echo esc_html(PropellerHelper::currency()); ?> <?php echo esc_html(PropellerHelper::formatPrice($child_item->totalPrice)); ?>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
            <div class="col-9 ms-auto d-flex align-items-center justify-content-between">
                <span class="item-quantity"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->quantity); ?></span>
                <span class="item-price">
                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                    <?php echo esc_html(PropellerHelper::formatPrice($item->totalSum)); ?>
                </span>
            </div>
        </div>
    <?php } ?>
    <?php if (is_array($cart->bonusItems) && sizeof($cart->bonusItems)) { ?>
        <div class="row">
            <div class="col-12">
                <h4 class="sc-bonus-items"><?php echo esc_html(__('Bonus items', 'propeller-ecommerce-v2')); ?></h4>
            </div>
        </div>
        <?php foreach ($cart->bonusItems as $item) {
            $item->product = new Product($item->product);
        ?>
            <div class="row align-items-start sc-item">
                <div class="col-3 product-image">
                    <?php if ($item->product->has_images()) { ?>
                        <img class="img-fluid" src="<?php echo esc_url($item->product->images[0]->images[0]->url); ?>" alt="<?php echo esc_attr($item->product->get_name()); ?>">
                    <?php } else { ?>
                        <img class="img-fluid" src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>" alt="<?php echo esc_html(__('No image found', 'propeller-ecommerce-v2')); ?>">
                    <?php } ?>
                </div>
                <div class="col-9 product-description">

                    <div class="product-sku">
                        <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->product->sku); ?>
                    </div>
                    <div class="product-name row justfiy-content-between">
                        <div class="col">
                            <?php echo esc_html($item->product->get_name()); ?>
                        </div>
                        <?php if (isset($item->childItems) && count($item->childItems)) { ?>
                            <div class="col-auto">
                                <span class="item-price">
                                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                    <?php
                                    echo esc_html(PropellerHelper::formatPrice($item->totalPrice));
                                    ?>
                                </span>
                            </div>
                        <?php } ?>
                    </div>
                    <?php echo wp_kses_post(apply_filters('propel_product_surcharges', $item)); ?>


                </div>
                <div class="col-9 ms-auto d-flex align-items-center justify-content-between">
                    <span class="item-quantity"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->quantity); ?></span>
                    <span class="item-price">
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                        <?php echo esc_html(PropellerHelper::formatPrice($item->totalPrice)); ?>
                    </span>
                </div>
            </div>
    <?php }
    }  ?>

    <div class="row align-items-baseline sc-calculation sc-subtotal">
        <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html(__('Subtotal', 'propeller-ecommerce-v2')); ?></div>
        <div class="col-4 col-lg-4 ms-auto sc-price text-end">
            <div class="sc-total">
                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-subtotal">
                    <?php echo esc_html(PropellerHelper::formatPrice($cart->total->subTotal)); ?>
            </div>
        </div>
    </div>
    <?php if (!empty($cart->total->discount)) { ?>
        <div class="row align-items-baseline sc-calculation propel-discount">
            <div class="col-8 col-lg-5"><?php echo esc_html(__('Discount', 'propeller-ecommerce-v2')); ?></div>
            <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                <div class="sc-total">
                    <span class="propel-total-voucher"><?php echo esc_html(PropellerHelper::formatPrice($cart->total->discount)); ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="row align-items-baseline sc-calculation">
        <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html(__('Shipping costs', 'propeller-ecommerce-v2')); ?></div>
        <div class="col-4 col-lg-4 ms-auto sc-price text-end">
            <div class="sc-total">
                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-shipping">
                    <?php echo esc_html(PropellerHelper::formatPrice($cart->postageData->price)); ?>
                </span>
            </div>
        </div>
    </div>
    <?php
    if ($cart->paymentData->price > 0) { ?>
        <div class="row align-items-baseline sc-calculation">
            <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html(__('Transaction costs', 'propeller-ecommerce-v2')); ?></div>
            <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                <div class="sc-total">
                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-shipping">
                        <?php echo esc_html(PropellerHelper::formatPrice($cart->paymentData->price)); ?>
                    </span>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="row align-items-baseline sc-calculation">
        <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html(__('Total excl. VAT', 'propeller-ecommerce-v2')); ?></div>
        <div class="col-4 col-lg-4 ms-auto sc-price text-end">
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
            <div class="row align-items-baseline sc-calculation">
                <?php $taxPercentage = $taxLevel->taxPercentage; ?>

                <div class="col-6 col-xl-5"><?php echo esc_html($taxPercentage); ?>% <?php echo esc_html(__('VAT', 'propeller-ecommerce-v2')); ?></div>

                <div class="col-6 ms-auto sc-price text-end">
                    <div class="sc-total-btw">
                        <?php
                        $totalNet = $cart->total->totalNet;
                        $totalGross = $cart->total->totalGross;
                        $totalBTW = $totalNet - $totalGross;
                        ?>
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-btw"><?php echo esc_html(PropellerHelper::formatPrice($taxLevel->price)); ?></span>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
    <div class="row align-items-baseline sc-calculation">
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
    <div class="sc-grand-total">
        <div class="row align-items-baseline">
            <div class="col-8 col-lg-6 col-xl-5"><?php echo esc_html(__('Total', 'propeller-ecommerce-v2')); ?></div>
            <div class="col-4 col-lg-4 ms-auto sc-price text-end">
                <div class="sc-total">
                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="propel-total-price"><?php echo esc_html(PropellerHelper::formatPrice($cart->total->totalNet)); ?></span>
                </div>
            </div>
        </div>

    </div>
</div>