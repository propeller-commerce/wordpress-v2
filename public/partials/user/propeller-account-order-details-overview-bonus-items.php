<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

$bShowTitle = false;
foreach ($items as $item) {
    if (($item->class == 'product' || $item->class == 'incentive') && $item->isBonus == 'Y') {
        if (!$bShowTitle) {
            $bShowTitle = true; ?>
            <div class="order-bonus-wrapper">
                <div class="row align-items-start">
                    <div class="col me-auto order-header">
                        <h5><?php echo esc_html(__('Bonus items', 'propeller-ecommerce-v2')); ?></h5>
                    </div>
                </div>
                <?php }
            if (isset($item->bonusitems) && count($item->bonusitems)) {
                foreach ($item->bonusitems as $bonusItem) {
                    $bonusItem->product = new Product($bonusItem->product); ?>

                    <div class="order-bonus-item">
                        <div class="row g-0 align-items-start">
                            <div class="col-2 col-md-2 col-lg-1 px-4 order-bonus-image">
                                <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $bonusItem->product->slug[0]->value, $bonusItem->product->urlId)); ?>">
                                    <img class="img-fluid"
                                        loading="lazy"
                                        src="<?php echo esc_url($bonusItem->product->has_images() ? $bonusItem->product->images[0]->images[0]->url : $this->assets_url . '/img/no-image-card.webp'); ?>"
                                        alt="<?php echo esc_attr($bonusItem->product->name[0]->value); ?>">

                                </a>
                            </div>
                            <div class="col-10 col-md-5 col-lg-6 pe-5 ps-0 order-bonus-description">
                                <div class="order-bonus-productname"><a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $bonusItem->product->slug[0]->value, $bonusItem->product->urlId)); ?>"><?php echo esc_html($bonusItem->product->name[0]->value); ?></a></div>
                                <div class="order-bonus-productcode">
                                    <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($bonusItem->sku); ?>
                                </div>
                            </div>

                            <div class="offset-2 offset-md-0 col-2 col-md-2 order-bonus-quantity">
                                <div class="product-quantity no-input">
                                    <div class="d-block d-md-none label-title"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></div>
                                    <?php echo esc_html($bonusItem->quantity); ?>
                                </div>
                            </div>
                            <div class="col-3 order-bonus-price">
                                <div class="d-block d-md-none label-title"><?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?></div>
                                <div class="order-bonus-total"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($bonusItem->totalPrice)); ?></div>
                            </div>

                        </div>
                    </div>
            <?php }
            } ?>
            <?php if ($item->class == 'product' && $item->isBonus == 'Y') { ?>
                <div class="order-bonus-item">
                    <div class="row g-0 align-items-start">
                        <div class="col-2 col-md-2 col-lg-1 px-4 order-bonus-image">
                            <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value,  $item->product->urlId)); ?>">
                                <img class="img-fluid"
                                    loading="lazy"
                                    src="<?php echo esc_url($item->product->has_images() ? $item->product->images[0]->images[0]->url : $this->assets_url . '/img/no-image-card.webp'); ?>"
                                    alt="<?php echo esc_attr($item->product->name[0]->value); ?>">
                            </a>

                        </div>
                        <div class="col-10 col-md-4 col-lg-5 pe-5 ps-0 order-bonus-description">
                            <div class="order-bonus-productname"><a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value, $item->product->urlId)); ?>"><?php echo esc_html($item->name); ?></a></div>
                            <div class="order-bonus-productcode">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->sku); ?>
                            </div>
                        </div>

                        <div class="offset-2 offset-md-0 col-2 col-md-2 order-bonus-quantity">
                            <div class="product-quantity no-input">
                                <div class="d-block d-md-none label-title"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></div>
                                <?php echo esc_html($item->quantity); ?>
                            </div>
                        </div>
                        <div class="col-2 order-bonus-price text-end">
                            <div class="d-block d-md-none label-title"><?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?></div>
                            <div class="order-bonus-total"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($item->priceTotal)); ?></div>
                        </div>

                    </div>
                </div>
            <?php } ?>

        <?php }
}
if ($bShowTitle) { ?>
            </div>
        <?php } ?>
