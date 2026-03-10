<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\ProductClass;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

?>
<div class="shopping-cart-summary-items">
    <?php foreach ($order->items as $item) {

        if ($item->class == ProductClass::Product && $item->isBonus !== 'Y') { ?>

            <div class="row g-0 align-items-start align-items-md-center sc-item">
                <div class="col-2 col-md-1 product-image">
                    <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value)); ?>">
                        <?php if ($item->product->has_images()) { ?>
                            <img class="img-fluid" src="<?php echo esc_url($item->product->images[0]->images[0]->url); ?>" alt="<?php echo esc_html($item->product->name[0]->value); ?>">
                        <?php } else { ?>
                            <img class="img-fluid"
                                src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>"
                                alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>">
                        <?php } ?>
                    </a>
                </div>
                <div class="col-10 col-md-7 product-description">
                    <div class="product-name">
                        <a class="product-name" href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value)); ?>">
                            <?php echo esc_html($item->name); ?>
                        </a>
                    </div>
                    <div class="product-sku">
                        <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->sku); ?>
                    </div>
                </div>
                <div class="pl-22 col-10 col-md-4 ms-auto d-flex align-items-center justify-content-md-between">
                    <div class="item-quantity">
                        <span class="label d-block d-md-inline-flex"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></span>
                        <?php echo esc_html($item->quantity); ?>
                    </div>
                    <div class="ps-5 item-price">
                        <span class="label d-block d-md-none"><?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?></span>
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                        <?php if ($user_prices == false)
                            echo esc_html(PropellerHelper::formatPrice($item->priceTotal));
                        else
                            echo esc_html(PropellerHelper::formatPrice($item->priceTotalNet)); ?>

                    </div>
                </div>
            </div>
        <?php } else if ((isset($item->bonusitems) && count($item->bonusitems) > 0) or ($item->class == ProductClass::Product && $item->isBonus == 'Y')) { ?>
            <div class="order-bonus-wrapper">
                <div class="row g-0 align-items-start">
                    <div class="col me-auto order-header">
                        <h5><?php echo esc_html(__('Bonus items', 'propeller-ecommerce-v2')); ?></h5>
                    </div>
                </div>
                <?php if (isset($item->bonusitems) && count($item->bonusitems) > 0) {
                    foreach ($item->bonusitems as $bonusItem) {
                        $bonusItem->product = new Product($bonusItem->product); ?>
                        <div class="order-bonus-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2 col-md-1 order-bonus-image">
                                    <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $bonusItem->product->slug[0]->value)); ?>">
                                        <img class="img-fluid"
                                            loading="lazy"
                                            src="<<?php echo esc_url($bonusItem->product->has_images() ? $bonusItem->product->images[0]->images[0]->url : $this->assets_url . '/img/no-image-card.webp'); ?>"
                                            alt="<?php echo esc_attr($bonusItem->product->name[0]->value); ?>">
                                    </a>

                                </div>
                                <div class="col-10 col-md-7 order-bonus-description">
                                    <div class="order-bonus-productname">
                                        <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $bonusItem->product->slug[0]->value)); ?>">
                                            <?php echo esc_html($bonusItem->name); ?>
                                        </a>
                                    </div>
                                    <div class="order-bonus-productcode">
                                        <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($bonusItem->sku); ?>
                                    </div>
                                </div>

                                <div class="pl-22 col-10 col-md-4 ms-auto d-flex align-items-center justify-content-md-between order-bonus-quantity">
                                    <div class="item-quantity no-input">
                                        <span class="label d-block d-md-inline-flex"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></span>
                                        <?php echo esc_html($bonusItem->quantity); ?>
                                    </div>
                                    <div class="ps-5 item-price">
                                        <span class="label d-block d-md-none"><?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?></span>
                                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                        0,00
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else if ($item->class == ProductClass::Product && $item->isBonus == 'Y') { ?>
                    <div class="order-bonus-item">
                        <div class="row g-0 align-items-center">
                            <div class="col-2 col-md-1 order-bonus-image">
                                <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value)); ?>">
                                    <img class="img-fluid"
                                        src="<<?php echo esc_url($item->product->has_images() ? $item->product->images[0]->images[0]->url : $this->assets_url . '/img/no-image-card.webp'); ?>"
                                        alt="<?php echo esc_attr($item->product->name[0]->value); ?>">

                                </a>

                            </div>
                            <div class="col-10 col-md-7 order-bonus-description">
                                <div class="order-bonus-productname">
                                    <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value)); ?>">
                                        <?php echo esc_html($item->name); ?>
                                    </a>
                                </div>
                                <div class="order-bonus-productcode">
                                    <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->sku); ?>
                                </div>
                            </div>

                            <div class="pl-22 col-10 col-md-4 ms-auto d-flex align-items-center justify-content-md-between order-bonus-quantity">
                                <div class="item-quantity no-input">
                                    <span class="label d-block d-md-inline-flex"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></span>
                                    <?php echo esc_html($item->quantity); ?>
                                </div>
                                <div class="ps-5 item-price">
                                    <span class="label d-block d-md-none"><?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?></span>
                                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                    <?php echo esc_html(PropellerHelper::formatPrice($item->priceTotal)); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
    <?php }
    } ?>
</div>