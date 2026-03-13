<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;
?>

<div class="order-product-item">
    <div class="row g-0 align-items-start">
        <div class="col-2 col-md-2 col-lg-1 px-4 product-image order-1">
            <?php if (is_object($item->product->cluster)) { ?>
                <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $item->product->cluster->slug[0]->value, $item->product->cluster->urlId)); ?>">
                <?php } else { ?>
                    <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value, $item->product->urlId)); ?>">
                    <?php } ?>
                    <img class="img-fluid"
                        loading="lazy"
                        src="<?php echo esc_url($item->product->has_images() ? $item->product->images[0]->images[0]->url : $obj->assets_url . '/img/no-image-card.webp'); ?>"
                        alt="<?php echo esc_attr($item->product->name[0]->value); ?>">
                    </a>
        </div>
        <div class="col-10 col-md-5 col-lg-6 pe-5 product-description order-2">
            <?php if (is_object($item->product->cluster)) { ?>
                <a class="product-name" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $item->product->cluster->slug[0]->value, $item->product->cluster->urlId)); ?>">
                <?php } else { ?>
                    <a class="product-name" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value, $item->product->urlId)); ?>">
                    <?php } ?>
                    <?php echo esc_html($item->name); ?>
                    </a>
                    <div class="product-sku">
                        <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->sku); ?>
                    </div>

                    <?php echo esc_html(apply_filters('propel_product_surcharges', $item->product)); ?>

        </div>
        <!-- <div class="offset-2 offset-md-0 col-10 col-md-2 reference order-md-3 order-last ">
            <?php if (!empty($item->notes)) { ?>
                <div class="d-block d-md-none label-title"><?php echo esc_html(__('Reference', 'propeller-ecommerce-v2')); ?></div>
            <?php } ?>
            <?php echo esc_html($item->notes); ?>
        </div>-->
        <div class="offset-2 offset-md-0 col-2 col-md-2 order-4">
            <div class="d-block d-md-none label-title"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></div>
            <span class="product-quantity"><?php echo esc_html($item->quantity); ?></span>
        </div>
        <div class="col-3 order-5 price-per-item text-end">
            <div class="d-block d-md-none label-title"><?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?></div>
            <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                <?php echo esc_html(PropellerHelper::formatPrice($item->priceTotal)); ?>
            </span>
        </div>


    </div>
</div>