<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);
?>
<div class="propeller-product-card" id="fav_product_<?php echo esc_attr($product->productId); ?>">
    <div class="row g-0 align-items-start">
        <div class="col-2 product-image product-card-image order-1 pe-2">
            <div class="row g-0 align-items-start d-flex align-middle align-items-center">
                <div class="col-4 align-middle align-items-center">
                    <input type="checkbox" class="favorite-item-check me-2" data-class="<?php echo esc_attr($product->class); ?>" value="<?php echo esc_attr($product->productId) . '-' . esc_attr($product->class); ?>" data-orderable="<?php echo esc_attr($product->orderable === 'Y' ? 1 : 0); ?>" data-price_on_request="<?php echo esc_attr($product->is_price_on_request() ? 1 : 0); ?>" />
                </div>
                <div class="col-8 p-2">
                    <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->get_slug(), $product->urlId)); ?>">
                        <?php if ($product->has_images()) { ?>
                            <img <?php if ($lazy_load_images) { ?> class="img-fluid lazy" src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>" data-src="<?php echo esc_url($product->images[0]->images[0]->url); ?>" <?php } else { ?> class="img-fluid" src="<?php echo esc_url($product->images[0]->images[0]->url); ?>" <?php } ?> alt="<?php echo esc_attr((count($product->images[0]->alt) ? $product->images[0]->alt[0]->value : "")); ?>" />
                        <?php } else { ?>
                            <img class="img-fluid" src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>" alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>" />
                        <?php } ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-9 col-md-4 col-xl-5 pe-5 product-description order-2 ps-4">
            <span class="product-name">
                <a class="product-name" href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->get_slug(), $product->urlId)); ?>" title="<?php echo esc_attr($product->get_name()); ?>">
                    <?php echo esc_html($product->get_name()); ?>
                </a>
            </span>
            <div class="product-sku product-code">
                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($product->sku); ?>
            </div>

            <?php echo esc_html(apply_filters('propel_product_surcharges', $product)); ?>

            <div class="stock-status">
                <?php echo esc_html(apply_filters('propel_product_stock', $product)); ?>
                <!-- <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total"><?php echo esc_html($product->inventory->totalQuantity); ?></span>                   -->
            </div>
        </div>

        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4 text-start">
            <?php if ($product->is_price_on_request()) { ?>
                <div class="price">
                    <?php echo esc_html(__('Price on request', 'propeller-ecommerce-v2')); ?>
                </div>
            <?php } else if ($product->price->gross != 0) { ?>
                <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                    <?php echo esc_html(PropellerHelper::formatPrice(!$user_prices ? $product->price->gross : $product->price->net)); ?>
                </div>
                <small><?php echo esc_html(!$user_prices ? __('excl. VAT', 'propeller-ecommerce-v2') : __('incl. VAT', 'propeller-ecommerce-v2')); ?></small>
            <?php } ?>
        </div>
        <div class="col-6 col-md-3 col-xl-2 mb-4 order-5">
            <?php
            if ($product->orderable === 'Y') {
                if ($product->is_price_on_request()) { ?>
                    <a href="#" class="btn btn-price-request d-flex justify-content-center align-items-center btn-price-request" data-id="<?php echo esc_attr($product->productId); ?>" data-code="<?php echo esc_attr($product->sku); ?>" data-name="<?php echo esc_attr($product->name[0]->value); ?>" data-quantity="<?php echo esc_attr($product->minimumQuantity); ?>" data-minquantity="<?php echo esc_attr($product->minimumQuantity); ?>" data-unit="<?php echo esc_attr($product->unit); ?>">
                        <?php echo esc_html(__('Request a price', 'propeller-ecommerce-v2')); ?>
                    </a>
                    <!-- <div class="price-on-request-content">
                            <h6><?php echo esc_html(__('Good to know:', 'propeller-ecommerce-v2')); ?></h6>
                            <p><?php echo esc_html(__('We do not have a current price available for this product. If you would like to receive a quote, please add this item to your request list with the correct amount. When you have added the items to your price list, you can find the items in your account under the heading price list.', 'propeller-ecommerce-v2')); ?></p>
                        </div> -->
                <?php } else { ?>
                    <form class="add-to-basket-form d-flex justify-content-end align-items-center" name="add-product" method="post">
                        <input type="hidden" name="product_id" value="<?php echo esc_attr($product->productId); ?>">
                        <input type="hidden" name="action" value="cart_add_item">
                        <?php $minQuantity = $product->minimumQuantity;
                        if ($product->unit >= $product->minimumQuantity)
                            $minQuantity = $product->unit;
                        ?>
                        <div class="input-group product-quantity">
                            <label class="visually-hidden" for="quantity-item-<?php echo esc_html($product->productId); ?>"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                            <span class="input-group-text incr-decr">
                                <button type="button" class="btn-quantity" data-type="minus">-</button>
                            </span>
                            <input type="number" ondrop="return false;" onpaste="return false;" onkeypress="return event.charCode>=48 && event.charCode<=57" id="quantity-item-<?php echo esc_attr($product->productId); ?>" class="quantity large form-control input-number" name="quantity" value="<?php echo esc_attr($minQuantity); ?>" autocomplete="off" min="<?php echo esc_attr($minQuantity); ?>" data-min="<?php echo esc_attr($minQuantity); ?>" data-unit="<?php echo esc_attr($product->unit); ?>" <?php if (PROPELLER_STOCK_CHECK) { ?> data-stock="<?php echo esc_attr($product->inventory->totalQuantity); ?>" <?php } ?> />
                            <span class="input-group-text incr-decr">
                                <button type="button" class="btn-quantity" data-type="plus">+</button>
                            </span>
                        </div>
                        <button class="btn btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                            <svg class="icon icon-cart" aria-hidden="true">
                                <use href="#shape-shopping-cart"></use>
                            </svg>
                        </button>
                    </form>
                <?php } ?>
            <?php } else { ?>
                <div class="col-12">
                    <div class="alert alert-dark alert-not-available"><?php echo esc_html(__('Product is no longer available', 'propeller-ecommerce-v2')); ?></div>
                </div>
            <?php } ?>
        </div>
        <div class="col-1 d-flex align-items-center justify-content-end order-3 order-md-last">
            <form name="delete-favorite" method="post" class="delete-favorite-item-form">
                <input type="hidden" name="list_id" value="<?php echo esc_attr($obj->data->id); ?>">
                <input type="hidden" name="product_id[]" value="<?php echo esc_attr($product->productId); ?>">
                <input type="hidden" name="action" value="delete_favorite">
                <div class="input-group">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#delete_favorite_<?php echo esc_attr($product->productId); ?>" class="btn-delete d-flex align-items-start align-items-md-center justify-content-end">
                        <svg fill="#000000" version="1.1" id="cross" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 490 490" xml:space="preserve">
                            <polygon stroke="black" stroke-width="20" points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490 
                        489.292,457.678 277.331,245.004 489.292,32.337 " />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php apply_filters('propel_account_favorites_remove_favorite_item_modal', $obj->data, $product, $obj) ?>
