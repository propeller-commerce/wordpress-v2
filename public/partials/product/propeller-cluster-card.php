<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Object\Attribute;
use Propeller\PropellerHelper;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\PdpNewWindow;
use Propeller\Includes\Object\Product;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

$cluster_product = $product->defaultProduct;

$href_behavior = $obj->get_cookie(PROPELLER_PDP_BEHAVIOR) && $obj->get_cookie(PROPELLER_PDP_BEHAVIOR) == 'true' ? ' target="_blank"' : '';

?>

<div class="card propeller-product-card">
    <svg style="display: none;">
        <symbol viewBox="0 0 23 20" id="shape-shopping-cart">
            <title><?php echo esc_html(__('Shopping cart', 'propeller-ecommerce-v2')); ?></title>
            <path d="M18.532 20c.72 0 1.325-.24 1.818-.723a2.39 2.39 0 0 0 .739-1.777c0-.703-.253-1.302-.76-1.797a.899.899 0 0 0-.339-.508 1.002 1.002 0 0 0-.619-.195H7.55l-.48-2.5h13.26a.887.887 0 0 0 .58-.215.995.995 0 0 0 .34-.527l1.717-8.125a.805.805 0 0 0-.18-.781.933.933 0 0 0-.739-.352H5.152L4.832.781a.99.99 0 0 0-.338-.566.947.947 0 0 0-.62-.215H.48a.468.468 0 0 0-.34.137.45.45 0 0 0-.14.332V.78c0 .13.047.241.14.332a.468.468 0 0 0 .34.137h3.155L6.43 15.82c-.452.47-.679 1.042-.679 1.72 0 .676.247 1.256.74 1.737.492.482 1.098.723 1.817.723.719 0 1.324-.24 1.817-.723.493-.481.739-1.074.739-1.777 0-.443-.12-.86-.36-1.25h5.832c-.24.39-.36.807-.36 1.25 0 .703.246 1.296.74 1.777.492.482 1.097.723 1.816.723zm1.518-8.75H6.83l-1.438-7.5h16.256l-1.598 7.5zm-11.742 7.5c-.347 0-.646-.124-.899-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.899-.371c.346 0 .645.124.898.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.898.371zm10.224 0c-.346 0-.645-.124-.898-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.898-.371c.347 0 .646.124.899.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.899.371z" fill-rule="nonzero" />
        </symbol>
    </svg>
    <?php if ($cluster_unavailable) { ?>
        <figure class="card-img-top">
            <div class="product-labels"></div>
            <div class="product-card-image">
                <img class="img-fluid" src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>" alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>" width="300" height="300">
            </div>
        </figure>
        <div class="card-body product-card-description">
            <div class="product-code"></div>
            <div class="product-name">
                <?php echo esc_html(__('Not available:', 'propeller-ecommerce-v2') . ' ' . esc_html($product->name[0]->value)); ?>
            </div>
        </div>
        <div class="card-footer product-card-footer"></div>
    <?php } else { ?>
        <figure class="card-img-top">
            <?php /*
            <div class="product-labels">
                <?php if ($cluster_product->has_attributes()) {
                    foreach ($cluster_product->get_attributes() as $attribute) {
                        $attribute = new Attribute($attribute);

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
            */ ?>

            <div class="product-card-image">
                <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $product->slug[0]->value, $product->urlId)); ?>" <?php echo esc_html($href_behavior); ?>>
                    <?php
                    if ($cluster_product->has_images()) { ?>
                        <img <?php if ($lazy_load_images) { ?> class="img-fluid lazy" data-src="<?php echo esc_url($cluster_product->images[0]->images[0]->url); ?>" src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>" <?php } else { ?> class="img-fluid" src="<?php echo esc_url($cluster_product->images[0]->images[0]->url); ?>" <?php } ?> alt="<?php echo esc_attr((count($cluster_product->images[0]->alt) ? $cluster_product->images[0]->alt[0]->value : "")); ?>" width="<?php echo esc_html(PROPELLER_PRODUCT_IMG_CATALOG_WIDTH); ?>" height="<?php echo esc_html(PROPELLER_PRODUCT_IMG_CATALOG_HEIGHT); ?>">
                    <?php } else { ?>
                        <img class="img-fluid" src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>" alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>" width="300" height="300">
                    <?php } ?>
                </a>
            </div>
        </figure>
        <div class="card-body product-card-description">
            <div class="product-code"><?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($product->sku); ?></div>
            <div class="product-name">

                <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $product->slug[0]->value, $product->urlId)); ?>" <?php echo esc_html($href_behavior); ?>>
                    <?php echo esc_html($product->name[0]->value); ?>
                </a>
            </div>
        </div>
        <div class="card-footer product-card-footer">
            <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
                <!-- Include the price display template -->
                <?php if (!$cluster_product->is_price_on_request()) { ?>
                    <div class="product-price-wrapper">
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
                    </div>
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
                        <a class="btn btn-addtobasket d-flex align-items-center justify-content-center" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $product->slug[0]->value, $product->urlId)); ?>">
                            <svg class="d-flex d-md-none icon icon-cart" aria-hidden="true">
                                <use xlink:href="#shape-shopping-cart"></use>
                            </svg>
                            <span class="d-none d-md-flex text"><?php echo esc_html(__('To product', 'propeller-ecommerce-v2')); ?></span>
                        </a>
                    </div>
                </div>
                <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
                    <!-- Stock status -->
                    <?php if (!empty($cluster_product->inventory) and $cluster_product->inventory->totalQuantity > 0) { ?>
                        <div class="product-status in-stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?><span class="quantity-stock">: <?php echo esc_html($cluster_product->inventory->totalQuantity); ?></span></div>
                    <?php } else { ?>
                        <div class="product-status out-of-stock"><?php echo esc_html(__('Out of stock', 'propeller-ecommerce-v2')); ?>
                        </div>
                <?php }
                } ?>
            </div>
        </div>
    <?php } ?>
</div>