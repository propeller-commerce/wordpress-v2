<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\PropellerHelper;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

$href_behavior = $obj->get_cookie(PROPELLER_PDP_BEHAVIOR) && $obj->get_cookie(PROPELLER_PDP_BEHAVIOR) == 'true' ? ' target="_blank"' : '';

?>

<div class="card propeller-product-card" data-sku="<?php echo esc_attr($product->sku); ?>">
    <svg style="display: none;">
        <symbol viewBox="0 0 23 20" id="shape-shopping-cart">
            <title><?php echo esc_html(__('Shopping cart', 'propeller-ecommerce-v2')); ?></title>
            <path d="M18.532 20c.72 0 1.325-.24 1.818-.723a2.39 2.39 0 0 0 .739-1.777c0-.703-.253-1.302-.76-1.797a.899.899 0 0 0-.339-.508 1.002 1.002 0 0 0-.619-.195H7.55l-.48-2.5h13.26a.887.887 0 0 0 .58-.215.995.995 0 0 0 .34-.527l1.717-8.125a.805.805 0 0 0-.18-.781.933.933 0 0 0-.739-.352H5.152L4.832.781a.99.99 0 0 0-.338-.566.947.947 0 0 0-.62-.215H.48a.468.468 0 0 0-.34.137.45.45 0 0 0-.14.332V.78c0 .13.047.241.14.332a.468.468 0 0 0 .34.137h3.155L6.43 15.82c-.452.47-.679 1.042-.679 1.72 0 .676.247 1.256.74 1.737.492.482 1.098.723 1.817.723.719 0 1.324-.24 1.817-.723.493-.481.739-1.074.739-1.777 0-.443-.12-.86-.36-1.25h5.832c-.24.39-.36.807-.36 1.25 0 .703.246 1.296.74 1.777.492.482 1.097.723 1.816.723zm1.518-8.75H6.83l-1.438-7.5h16.256l-1.598 7.5zm-11.742 7.5c-.347 0-.646-.124-.899-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.899-.371c.346 0 .645.124.898.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.898.371zm10.224 0c-.346 0-.645-.124-.898-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.898-.371c.347 0 .646.124.899.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.899.371z" fill-rule="nonzero" />
        </symbol>
    </svg>
    <figure class="card-img-top">

        <div class="product-labels">
            <?php if ($product->has_attributes()) {
                foreach ($product->get_attributes() as $attribute) {
                    // if ($attribute->get_name() == "example")
                }
            }
            ?>
        </div>
        <div class="product-card-image">

            <a class="propeller-product-card-link" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId)); ?>" <?php echo esc_html($href_behavior); ?>>
                <?php
                if ($product->has_images()) { ?>
                    <img <?php if ($lazy_load_images) { ?>
                        class="img-fluid lazy"
                        src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                        data-src="<?php echo esc_url($product->images[0]->images[0]->url); ?>"
                        <?php } else { ?>
                        class="img-fluid"
                        src="<?php echo esc_url($product->images[0]->images[0]->url); ?>"
                        <?php } ?>
                        alt="<?php echo esc_attr((count($product->images[0]->alt) ? $product->images[0]->alt[0]->value : "")); ?>"
                        width="<?php echo esc_html(PROPELLER_PRODUCT_IMG_CATALOG_WIDTH); ?>" height="<?php echo esc_html(PROPELLER_PRODUCT_IMG_CATALOG_HEIGHT); ?>">
                <?php } else { ?>
                    <img class="img-fluid"
                        src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                        alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>"
                        width="300" height="300">
                <?php } ?>
            </a>
        </div>
    </figure>
    <div class="card-body product-card-description">
        <div class="product-code"><?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($product->sku); ?></div>
        <div class="product-name">

            <a class="propeller-product-card-link" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId)); ?>" <?php echo esc_html($href_behavior); ?>>
                <?php echo esc_html($product->name[0]->value); ?>
            </a>
        </div>
    </div>
    <div class="card-footer product-card-footer">
        <?php if (!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL) { ?>
            <div class="add-to-cart-stock-wrapper">
                <!-- Include the order button template -->
                <div class="add-to-basket-wrapper">
                    <div class="add-to-basket">
                        <a class="propeller-product-card-link" class="btn btn-addtobasket d-flex align-items-center justify-content-center" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId)); ?>">
                            <svg class="d-flex d-md-none icon icon-cart" aria-hidden="true">
                                <use xlink:href="#shape-shopping-cart"></use>
                            </svg>
                            <span class="d-none d-md-flex text"><?php echo esc_html(__('See', 'propeller-ecommerce-v2')); ?></span>
                        </a>
                    </div>
                </div>
            </div>

        <?php } else { ?>
            <!-- Include the price display template -->

            <?php if (!$product->is_price_on_request()) { ?>
                <div class="product-price-wrapper">
                    <div class="product-price">

                        <?php
                        if ($product->priceData->display == "FROM_FOR") { ?>
                            <?php if ($user_prices == false) { ?>
                                <div class="product-current-price has-discount d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($product->price->gross)); ?> <?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                                <div class="product-old-price d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($product->priceData->suggested)); ?> </span></div>
                            <?php } else { ?>
                                <div class="product-current-price has-discount d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($product->price->net)); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                            <?php } ?>

                        <?php } else if ($user_prices == false) { ?>
                            <div class="product-current-price"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($product->price->gross)); ?> <?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                        <?php } else { ?>
                            <div class="product-current-price"><span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($product->price->net)); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span></div>
                        <?php } ?>
                    </div>
                    <?php if ($user_prices == false) { ?>
                        <small class="product-customer-price d-flex">
                            <span class="product-price-tax"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span> <?php echo esc_html(PropellerHelper::formatPrice($product->price->net)); ?> <?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></span>
                        </small>
                    <?php } else { ?>
                        <small class="product-customer-price d-flex">
                            <span class="product-price-tax"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span> <?php echo esc_html(PropellerHelper::formatPrice($product->price->gross)); ?> <?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></span>
                        </small>
                    <?php } ?>
                </div>
                <div class="add-to-cart-stock-wrapper">
                    <!-- Include the order button template -->
                    <div class="add-to-basket-wrapper">
                        <?php /*if( $product->orderable === 'Y') { */ ?>
                        <div class="add-to-basket">
                            <form class="add-to-basket-form d-flex" name="add-product" method="post">
                                <?php $minQuantity = $product->minimumQuantity;
                                if ($product->unit >= $product->minimumQuantity)
                                    $minQuantity = $product->unit;
                                ?>
                                <input type="hidden" name="product_id" value="<?php echo esc_attr($product->productId); ?>">
                                <input type="hidden" name="action" value="cart_add_item">
                                <div class="input-group product-quantity">
                                    <label class="visually-hidden" for="quantity-item-<?php echo esc_html($product->productId); ?>"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <span class="input-group-text incr-decr">
                                        <button type="button" class="btn-quantity"
                                            data-type="minus">-</button>
                                    </span>
                                    <input

                                        type="number"
                                        ondrop="return false;"
                                        onpaste="return false;"
                                        onkeypress="return event.charCode>=48 && event.charCode<=57"
                                        id="quantity-item-<?php echo esc_attr($product->productId); ?>"
                                        class="quantity large form-control input-number product-quantity-input"
                                        name="quantity"
                                        value="<?php echo esc_attr($minQuantity); ?>"
                                        autocomplete="off"
                                        min="<?php echo esc_attr($minQuantity); ?>"
                                        data-min="<?php echo esc_attr($minQuantity); ?>"
                                        data-unit="<?php echo esc_attr($product->unit); ?>"
                                        <?php if (PROPELLER_STOCK_CHECK) { ?>
                                        data-stock="<?php echo esc_attr($product->inventory->totalQuantity); ?>"
                                        <?php } ?>>
                                    <span class="input-group-text incr-decr">
                                        <button type="button" class="btn-quantity" data-type="plus">+</button>
                                    </span>
                                </div>
                                <button class="btn btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                    <svg class="d-flex d-md-none icon icon-cart" aria-hidden="true">
                                        <use xlink:href="#shape-shopping-cart"></use>
                                    </svg>
                                    <span class="d-none d-md-flex text"><?php echo esc_html(__('In cart', 'propeller-ecommerce-v2')); ?></span>
                                </button>
                            </form>
                        </div>
                        <?php /* } else { */ ?>
                        <!--<div class="alert alert-dark alert-not-available"><?php echo esc_html(__('Product is no longer available', 'propeller-ecommerce-v2')); ?></div> --->
                        <?php /* } */ ?>
                    </div>

                    <!-- Stock status -->
                    <?php if (!empty($product->inventory) && $product->inventory->totalQuantity > 0) { ?>
                        <div class="product-status in-stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?><span class="quantity-stock">: <?php echo esc_html($product->inventory->totalQuantity); ?></span></div>
                    <?php } else { ?>
                        <div class="product-status out-of-stock"><?php echo esc_html(__('Out of stock', 'propeller-ecommerce-v2')); ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>

                <div class="product-price-wrapper product-price price-on-request">
                    <div class="price">
                        <?php echo esc_html(__('Price on request', 'propeller-ecommerce-v2')); ?>
                    </div>
                </div>
                <?php if (!UserController::is_propeller_logged_in()) { ?>
                    <a class="btn btn-addtobasket d-flex align-items-center justify-content-center" href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::LOGIN_PAGE))); ?>">
                        <span class="d-flex text"><?php echo esc_html(__('Request a price', 'propeller-ecommerce-v2')); ?></span>
                    </a>

                <?php } else { ?>
                    <a href="#" class="btn-addtobasket d-flex justify-content-center align-items-center btn-price-request" data-id="<?php echo esc_attr($product->productId); ?>" data-code="<?php echo esc_attr($product->sku); ?>" data-name="<?php echo esc_attr($product->name[0]->value); ?>" data-quantity="<?php echo esc_attr($product->minimumQuantity); ?>" data-minquantity="<?php echo esc_attr($product->minimumQuantity); ?>" data-unit="<?php echo esc_attr($product->unit); ?>">
                        <?php echo esc_html(__('Request a price', 'propeller-ecommerce-v2')); ?>
                    </a>

            <?php }
            } ?>
        <?php } ?>
    </div>
</div>