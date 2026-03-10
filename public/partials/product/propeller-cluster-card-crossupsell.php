<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\PropellerHelper;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\Cluster;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

$crossupsell = new Cluster($crossupsell, false);
$cluster_product = $crossupsell->defaultProduct;
?>

<div class="card propeller-product-card">
    <svg style="display: none;">
        <symbol viewBox="0 0 18 16" id="shape-shopping-cart">
            <title><?php echo esc_html(__('Shopping cart', 'propeller-ecommerce-v2')); ?></title>
            <path d="m16.504 9.416 1.477-6.5A.75.75 0 0 0 17.25 2H4.975L4.69.6a.75.75 0 0 0-.735-.6H.75A.75.75 0 0 0 0 .75v.5c0 .414.336.75.75.75h2.184l2.195 10.732A1.749 1.749 0 0 0 6 16a1.75 1.75 0 0 0 1.224-3h6.552a1.75 1.75 0 1 0 1.987-.325l.173-.759a.75.75 0 0 0-.732-.916H6.816l-.204-1h9.16a.75.75 0 0 0 .732-.584z" fill-rule="nonzero" />
        </symbol>
    </svg>
    <figure class="card-img-top">
        <div class="product-labels">
            <?php if ($cluster_product->has_attributes()) {
                foreach ($cluster_product->get_attributes() as $attribute) {
                    if ($attribute->searchId == 'attr_product_label_1' && !empty($attribute->get_value())) { ?>
                        <div class="product-label label-1 order-1">
                            <span><?php echo esc_html($attribute->get_value()); ?></span>
                        </div>
                    <?php }
                    if ($attribute->searchId == 'attr_product_label_2' && !empty($attribute->get_value())) { ?>
                        <div class="product-label label-2  order-2">
                            <span><?php echo esc_html($attribute->get_value()); ?></span>
                        </div>
            <?php }
                }
            }
            ?>
        </div>

        <div class="product-card-image">
            <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $crossupsell->slug[0]->value, $crossupsell->urlId)); ?>">
                <?php if ($cluster_product->has_images()) { ?>
                    <img <?php if ($lazy_load_images) { ?> class="img-fluid lazy" data-src="<?php echo esc_url($cluster_product->images[0]->images[0]->url); ?>" src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>" <?php } else { ?> class="img-fluid" src="<?php echo esc_url($cluster_product->images[0]->images[0]->url); ?>" <?php } ?> alt="<?php echo esc_attr(count($cluster_product->images[0]->alt) ? intval($cluster_product->images[0]->alt[0]->value) : ""); ?>" width="<?php echo esc_html(PROPELLER_PRODUCT_IMG_CATALOG_WIDTH); ?>" height="<?php echo esc_html(PROPELLER_PRODUCT_IMG_CATALOG_HEIGHT); ?>">
                <?php } else { ?>
                    <img class="img-fluid" src="<?php echo esc_url($obj->assets_url) . '/img/no-image-card.webp'; ?>" alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>" width="300" height="300">
                <?php } ?>
            </a>
        </div>
    </figure>
    <div class="card-body product-card-description">
        <div class="product-code"><?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($crossupsell->sku); ?></div>
        <div class="product-name">

            <!-- build the product urls with the classId of the product (temporary) -->
            <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $crossupsell->slug[0]->value, $crossupsell->urlId)); ?>">
                <?php echo esc_html($crossupsell->name[0]->value); ?>
            </a>
        </div>
    </div>
    <div class="card-footer product-card-footer">
        <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
            <!-- Include the price display template -->
            <?php if (!$cluster_product->is_price_on_request()) { ?>
                <div class="product-price">

                    <?php if ($cluster_product->priceData->display == "FROM_FOR") { ?>
                        <?php if ($user_prices == false) { ?>
                            <div class="product-current-price has-discount d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($cluster_product->price->gross)); ?> <?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                        <?php } else { ?>
                            <div class="product-current-price has-discount d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($cluster_product->price->net)); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                        <?php } ?>
                        <div class="product-old-price d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($cluster_product->priceData->suggested)); ?></span></div>
                    <?php } else if ($user_prices == false) { ?>
                        <div class="product-current-price d-flex"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($cluster_product->price->gross)); ?> <?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                    <?php } else { ?>
                        <div class="product-current-price d-flex"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($cluster_product->price->net)); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                    <?php } ?>
                </div>
                <?php if ($user_prices == false) { ?>
                    <small class="product-customer-price">
                        <span class="product-price-tax"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span> <?php echo esc_html(PropellerHelper::formatPrice($cluster_product->price->net)); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span>
                    </small>
                <?php } else { ?>
                    <small class="product-customer-price">
                        <span class="product-price-tax"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span> <?php echo esc_html(PropellerHelper::formatPrice($cluster_product->price->gross)); ?> <?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></span>
                    </small>
                <?php } ?>
            <?php } else { ?>
                <div class="product-price price-on-request">
                    <div class="price">
                        <?php echo esc_html(__('Price on request', 'propeller-ecommerce-v2')); ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <div class="add-to-cart-stock-wrapper">
            <!-- Include the order button template -->
            <div class="add-to-basket-wrapper">
                <div class="add-to-basket">
                    <a class="btn btn-addtobasket d-flex align-items-center justify-content-center" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $crossupsell->slug[0]->value, $crossupsell->urlId)); ?>">
                        <svg class="d-flex d-md-none icon icon-cart" aria-hidden="true">
                            <use xlink:href="#shape-shopping-cart"></use>
                        </svg>
                        <span class="d-none d-md-flex text"><?php echo esc_html(__('To product', 'propeller-ecommerce-v2')); ?></span>
                    </a>
                </div>
            </div>
            <?php /*<div class="product-code"><?php echo esc_html($cluster_product->manufacturer); ?> / <?php echo esc_html($cluster_product->sku); ?></div> */ ?>
            <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
                <!-- Stock status -->
                <?php if (!empty($cluster_product->inventory) && $cluster_product->inventory->totalQuantity > 0) { ?>
                    <div class="product-status in-stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html(intval($cluster_product->inventory->totalQuantity)); ?></div>
                <?php } else { ?>
                    <div class="product-status out-of-stock"><?php echo esc_html(__('Out of stock', 'propeller-ecommerce-v2')); ?></div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>