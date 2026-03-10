<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\Cluster;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);
?>
<svg style="display:none;">
    <symbol viewBox="0 0 18 21" id="shape-delete">
        <title>Delete</title>
        <path d="M11.562 17.375h.625c.173 0 .313-.14.313-.312V6.438a.313.313 0 0 0-.313-.313h-.625a.313.313 0 0 0-.312.313v10.625c0 .172.14.312.312.312zm-6.25 0h.625c.173 0 .313-.14.313-.312V6.438a.313.313 0 0 0-.313-.313h-.625A.313.313 0 0 0 5 6.438v10.625c0 .172.14.312.312.312zM17.187 3h-4.062l-1.313-1.75a1.87 1.87 0 0 0-1.5-.75H7.187a1.87 1.87 0 0 0-1.5.75L4.375 3H.312A.313.313 0 0 0 0 3.313v.625c0 .172.14.312.312.312h.938v14.375c0 1.035.84 1.875 1.875 1.875h11.25c1.035 0 1.875-.84 1.875-1.875V4.25h.937c.173 0 .313-.14.313-.312v-.625A.313.313 0 0 0 17.187 3zm-10.5-1a.627.627 0 0 1 .5-.25h3.125c.205 0 .386.098.5.25l.75 1H5.937l.75-1zM15 18.625c0 .345-.28.625-.625.625H3.125a.625.625 0 0 1-.625-.625V4.25H15v14.375zm-6.563-1.25h.625c.173 0 .313-.14.313-.312V6.438a.313.313 0 0 0-.313-.313h-.625a.313.313 0 0 0-.312.313v10.625c0 .172.14.312.312.312z" />
    </symbol>
    <symbol viewBox="0 0 10 10" id="shape-remove">
        <title>Delete</title>
        <path d="M1.282.22 5 3.937 8.718.22A.751.751 0 1 1 9.78 1.282L6.063 5 9.78 8.718A.751.751 0 0 1 8.718 9.78L5 6.063 1.282 9.78A.751.751 0 1 1 .22 8.718L3.937 5 .22 1.282A.751.751 0 0 1 1.282.22z" fill="#005FAD" fill-rule="evenodd" />
    </symbol>
    <symbol viewBox="0 0 13 8" id="shape-arrow-up">
        <title>Arrow up</title>
        <path d="M.724 7.225a.927.927 0 0 1 .03-1.243L5.938.74A.784.784 0 0 1 6.5.5c.202 0 .404.08.562.24l5.182 5.242c.329.334.342.89.032 1.243a.777.777 0 0 1-1.155.034L6.5 2.556 1.88 7.259a.778.778 0 0 1-1.157-.034z" />
    </symbol>
    <symbol viewBox="0 0 23 20" id="shape-shopping-cart">
        <title>Shopping cart</title>
        <path d="M18.532 20c.72 0 1.325-.24 1.818-.723a2.39 2.39 0 0 0 .739-1.777c0-.703-.253-1.302-.76-1.797a.899.899 0 0 0-.339-.508 1.002 1.002 0 0 0-.619-.195H7.55l-.48-2.5h13.26a.887.887 0 0 0 .58-.215.995.995 0 0 0 .34-.527l1.717-8.125a.805.805 0 0 0-.18-.781.933.933 0 0 0-.739-.352H5.152L4.832.781a.99.99 0 0 0-.338-.566.947.947 0 0 0-.62-.215H.48a.468.468 0 0 0-.34.137.45.45 0 0 0-.14.332V.78c0 .13.047.241.14.332a.468.468 0 0 0 .34.137h3.155L6.43 15.82c-.452.47-.679 1.042-.679 1.72 0 .676.247 1.256.74 1.737.492.482 1.098.723 1.817.723.719 0 1.324-.24 1.817-.723.493-.481.739-1.074.739-1.777 0-.443-.12-.86-.36-1.25h5.832c-.24.39-.36.807-.36 1.25 0 .703.246 1.296.74 1.777.492.482 1.097.723 1.816.723zm1.518-8.75H6.83l-1.438-7.5h16.256l-1.598 7.5zm-11.742 7.5c-.347 0-.646-.124-.899-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.899-.371c.346 0 .645.124.898.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.898.371zm10.224 0c-.346 0-.645-.124-.898-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.898-.371c.347 0 .646.124.899.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.899.371z" fill-rule="nonzero" />
    </symbol>
</svg>
<div class="container-fluid px-0 basket-item-container" data-item-id="<?php echo esc_attr($item->id); ?>">
    <div class="row product-item g-0 align-items-start">
        <div class="col-2 col-md-1 product-image mb-3">
            <?php if (is_object($item->product->cluster)) { ?>
                <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $item->product->cluster->get_slug(), $item->product->cluster->urlId)); ?>">
                <?php } else { ?>
                    <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->get_slug(), $item->product->urlId)); ?>">
                    <?php } ?>

                    <?php if ($item->product->has_images()) { ?>
                        <img class="img-fluid" src="<?php echo esc_url($item->product->images[0]->images[0]->url); ?>" alt="<?php echo esc_attr($item->product->name[0]->value); ?>">
                    <?php } else { ?>
                        <img class="img-fluid"
                            src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                            alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>">
                    <?php } ?>
                    </a>
        </div>
        <div class="col-10 col-md-4 col-lg-6 product-description">
            <?php if (is_object($item->product->cluster)) { ?>
                <a class="product-name" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $item->product->cluster->get_slug(), $item->product->cluster->urlId)); ?>">
                    <?php echo esc_html($item->product->cluster->get_name()); ?>
                <?php } else { ?>
                    <a class="product-name" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->get_slug(), $item->product->urlId)); ?>">
                        <?php echo esc_html($item->product->get_name()); ?>
                    <?php } ?>
                    </a>

                    <?php if ($item->id) { ?>
                        <div class="delete-basket-item d-flex d-md-none">
                            <form name="delete-product" method="post" class="delete-basket-item-form">
                                <input type="hidden" name="item_id" value="<?php echo esc_attr($item->id); ?>">
                                <input type="hidden" name="action" value="cart_delete_item">
                                <div class="input-group">
                                    <button class="btn-delete d-flex align-items-start justify-content-end">
                                        <svg class="icon icon-delete" aria-hidden="true">
                                            <use xlink:href="#shape-remove"></use>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php } ?>

                    <?php
                    if (is_object($item->product->cluster))
                        echo esc_html($item->product->get_name());
                    ?>

                    <div class="product-sku">
                        <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->product->sku); ?>
                    </div>

                    <?php echo esc_html(wp_kses_post(apply_filters('propel_product_surcharges', $item))); ?>

                    <?php
                    $show_stock = false;
                    if (!empty($item->product->inventory) && $item->product->inventory->totalQuantity > 0)
                        $show_stock = true;
                    ?>

                    <div class="product-delivery d-none d-md-block">
                        <?php if ($show_stock) { ?>
                            <div class="product-stock in-stock">
                                <?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>: <span class="stock-total"><?php echo esc_html($item->product->inventory->totalQuantity); ?></span>
                            </div>
                        <?php } else { ?>
                            <div class="product-stock out-of-stock">
                                <?php echo esc_html(__('Out of stock', 'propeller-ecommerce-v2')); ?>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if (isset($item->childItems) && count($item->childItems)) { ?>
                        <div class="product-child-items">

                            <?php foreach ($item->childItems as $child_item) {
                                $child_item->product = new Product($child_item->product);
                            ?>
                                <div class="row g-0 item-children">
                                    <div class="col-12 product-child-name">
                                        <?php echo esc_html($child_item->product->get_name()); ?>
                                        <?php if ($user_prices == false) { ?>
                                            (<?php echo esc_html(PropellerHelper::currency()); ?> <?php echo esc_html(PropellerHelper::formatPrice($child_item->totalPrice)); ?>)
                                        <?php } else { ?>
                                            (<?php echo esc_html(PropellerHelper::currency()); ?> <?php echo esc_html(PropellerHelper::formatPrice($child_item->totalPriceNet)); ?>)
                                        <?php } ?>
                                    </div>

                                </div>
                            <?php } ?>

                        </div>
                    <?php } ?>
        </div>
        <form name="add-product" method="post" class="offset-2 offset-md-0 update-basket-item-form col-3 col-md-4 col-lg-3">
            <input type="hidden" name="action" value="cart_update_item">
            <input type="hidden" name="item_id" value="<?php echo esc_attr($item->id); ?>">
            <div class="row g-0">
                <!-- <div class="d-none d-md-block col-md-5 col-lg-5 product-reference">
                    <div class="add-to-basket update notes">
                        <label for="notes" class="visually-hidden"><?php echo esc_html(__('Reference', 'propeller-ecommerce-v2')); ?></label>
                        <input type="text" name="notes" class="form-control" value="<?php echo esc_attr($item->notes); ?>">
                    </div>
                </div> -->
                <div class="col-7 col-md-6 col-lg-8 price-per-item d-none d-md-flex">
                    <div class="product-price product-price-item">
                        <?php if ($user_prices == false) { ?>
                            <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                <?php echo esc_html(PropellerHelper::formatPrice($item->price)); ?>
                            </span>
                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        <?php } else { ?>
                            <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                <?php echo esc_html(PropellerHelper::formatPrice($item->priceNet)); ?>
                            </span>
                            <small><?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        <?php } ?>
                    </div>
                </div>
                <div class="px-22 px-md-0 col-5 col-md-3 col-lg-3 product-quantity add-to-basket update">
                    <div class="input-group product-quantity d-flex align-items-center justify-content-md-end justify-content-lg-center">
                        <?php $minQuantity = $item->product->minimumQuantity;
                        if ($item->product->unit >= $item->product->minimumQuantity)
                            $minQuantity = $item->product->unit;
                        ?>
                        <input
                            type="number"
                            ondrop="return false;"
                            onpaste="return false;"
                            onkeypress="return event.charCode>=48 && event.charCode<=57"
                            class="quantity large form-control input-number"
                            name="quantity"
                            autocomplete="off"
                            data-prev_quantity="<?php echo esc_attr($item->quantity); ?>"
                            value="<?php echo esc_attr($item->quantity); ?>"
                            min="0"
                            max="999999"
                            step="<?php echo esc_attr($item->product->unit); ?>"
                            data-min="<?php echo esc_attr($minQuantity); ?>"
                            data-unit="<?php echo esc_attr($item->product->unit); ?>"
                            <?php if (PROPELLER_STOCK_CHECK) { ?>
                            data-stock="<?php echo esc_attr($item->product->inventory->totalQuantity); ?>"
                            <?php } ?>>
                    </div>
                </div>
            </div>
        </form>

        <div class="ps-4 col-7 col-md-3 col-lg-2 product-total-price d-flex align-items-center justify-content-end justify-content-md-between">
            <div class="product-price product-total text-end text-md-left">
                <?php if ($user_prices == false) { ?>
                    <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                        <span class="basket-item-price">
                            <?php echo esc_html(PropellerHelper::formatPrice($item->totalSum)); ?>
                        </span>
                    </span>
                    <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                <?php } else { ?>
                    <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                        <span class="basket-item-price">
                            <?php echo esc_html(PropellerHelper::formatPrice($item->totalSumNet)); ?>
                        </span>
                    </span>
                    <small><?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></small>
                <?php } ?>
            </div>
            <?php if ($item->id) { ?>
                <div class="add-to-basket d-none d-md-flex">
                    <form name="delete-product" action="#" method="post" class="delete-basket-item-form">
                        <input type="hidden" name="item_id" value="<?php echo esc_attr($item->id); ?>">
                        <input type="hidden" name="action" value="cart_delete_item">
                        <div class="input-group">
                            <button class="btn-delete">
                                <svg class="icon icon-delete" aria-hidden="true">
                                    <use xlink:href="#shape-delete"></use>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
        <div class="offset-2 pl-22 pt-3 col-8 d-flex d-md-none">
            <div class="product-delivery">
                <?php if ($show_stock) { ?>
                    <div class="product-stock in-stock">
                        <?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>: <span class="stock-total"><?php echo esc_html($item->product->inventory->totalQuantity); ?></span>
                    </div>
                <?php } else { ?>
                    <div class="product-stock out-of-stock">
                        <?php echo esc_html(__('Out of stock', 'propeller-ecommerce-v2')); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php
        if ($item->product->crossupsellsFrom->itemsFound > 0) { ?>
            <div class="px-22 offset-2 offset-md-1 col ps-md-4">
                <button class="btn-basket-product-alternatives" type="button" href="#product-alternatives-<?php echo esc_attr($item->product->productId); ?>" data-product_id="<?php echo esc_attr($item->product->productId); ?>">
                    <?php echo esc_html(__('Have you thought about accessories products?', 'propeller-ecommerce-v2')); ?>
                    <svg class="icon icon-svg icon-arrow-up" aria-hidden="true">
                        <use class="nav-shape-arrow-up" xlink:href="#shape-arrow-up"></use>
                    </svg>
                </button>
            </div>
        <?php } ?>
    </div>
</div>

<?php if ($item->product->crossupsellsFrom->itemsFound > 0) { ?>
    <div id="product-alternatives-<?php echo esc_attr($item->product->productId); ?>" class="container-fluid sc-suggestions-wrapper collapse"></div>
<?php } ?>