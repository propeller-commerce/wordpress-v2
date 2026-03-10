<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

$bonusItem->product = new Product($bonusItem->product);

?>
<div class="row sc-bonus-item g-0 align-items-start">
    <div class="col-3 col-md-1 sc-bonus-image">
        <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $bonusItem->product->get_slug(), $bonusItem->product->urlId)); ?>">
            <img class="img-fluid"
                src="<?php echo esc_url($bonusItem->product->has_images() ? $bonusItem->product->images[0]->images[0]->url : $obj->assets_url . '/img/no-image-card.webp'); ?>"
                alt="<?php echo esc_attr($bonusItem->product->get_name()); ?>">
        </a>
    </div>
    <div class="col-9 col-md-3 col-lg-6 sc-bonus-description">
        <div class="sc-bonus-productname">
            <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $bonusItem->product->get_slug(), $bonusItem->product->urlId)); ?>">
                <?php echo esc_html($bonusItem->product->get_name()); ?>
            </a>
        </div>
        <div class="sc-bonus-productcode">
            <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($bonusItem->product->sku); ?>
        </div>
    </div>
    <div class="col-8 col-md-2 col-lg-1 ms-auto sc-bonus-quantity">
        <div class="product-quantity no-input text-center">
            <?php echo esc_html($bonusItem->quantity); ?>
        </div>
    </div>
    <div class="col-4 col-md-2 col-lg-2 sc-bonus-price text-start">
        <div class="sc-bonus-total"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span> <?php echo esc_html(PropellerHelper::formatPrice($bonusItem->totalPrice)); ?></div>
    </div>
</div>