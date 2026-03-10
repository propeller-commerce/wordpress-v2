<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);
?>

<div class="row g-0">
    <div class="container-fluid px-0">
        <?php
        $i = 0;
        foreach ($product->crossupsellsFrom->items as $crossupsell) {
            if ($i < 3) {
                if (isset($crossupsell->productTo) && !empty($crossupsell->productTo)) { ?>
                    <div class="row product-item propeller-product-card align-items-start">
                        <div class="col-2 col-md-1 product-card-image product-image px-0">
                            <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $crossupsell->productTo->slug[0]->value, $crossupsell->productTo->urlId)); ?>">
                                <?php if (!empty($crossupsell->productTo->media->images->items)) { ?>
                                    <img class="img-fluid" src="<?php echo esc_url($crossupsell->productTo->media->images->items[0]->imageVariants[0]->url); ?>" loading="lazy" alt="<?php echo esc_attr($crossupsell->productTo->name[0]->value); ?>">
                                <?php } else { ?>
                                    <img class="img-fluid"
                                        src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                                        loading="lazy" alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>">
                                <?php } ?>
                            </a>
                        </div>
                        <div class="col-10 col-md-4 col-lg-6 product-description">
                            <a class="product-name" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $crossupsell->productTo->slug[0]->value, $crossupsell->productTo->urlId)); ?>">
                                <?php echo esc_html($crossupsell->productTo->name[0]->value); ?>
                            </a>
                            <div class="product-sku product-code">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($crossupsell->productTo->sku); ?>
                            </div>
                            <div class="product-delivery d-none d-md-flex">
                                <?php if (!empty($crossupsell->productTo->inventory) and $crossupsell->productTo->inventory->totalQuantity > 0) { ?>
                                    <div class="product-stock in-stock">
                                        <?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>: <span class="stock-total"><?php echo esc_html($crossupsell->productTo->inventory->totalQuantity); ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="product-stock out-of-stock">
                                        <?php echo esc_html(__('Out of stock', 'propeller-ecommerce-v2')); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="add-to-basket offset-2 offset-md-0 ps-0 ps-0 col-10 col-md-7 col-lg-5">
                            <form name="add-product" method="post" class="add-to-basket-form">
                                <input type="hidden" name="action" value="cart_add_item">
                                <input type="hidden" name="product_id" value="<?php echo esc_attr($crossupsell->productTo->productId); ?>">
                                <div class="row g-0 align-items-start">
                                    <div class="px-22 px-md-0 col-7 col-md-2 col-lg-5 price-per-item">
                                        <div class="product-price product-price-item">
                                            <?php if ($user_prices == false) { ?>
                                                <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                                    <?php echo esc_html(PropellerHelper::formatPrice($crossupsell->productTo->price->gross)); ?>
                                                </span>
                                                <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                                            <?php } else { ?>
                                                <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                                    <?php echo esc_html(PropellerHelper::formatPrice($crossupsell->productTo->price->net)); ?>
                                                </span>
                                                <small><?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></small>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-2 col-md-3 col-lg-2 d-flex align-items-center product-quantity update">
                                        <div class="input-group product-quantity justify-content-end justify-content-lg-start">
                                            <input
                                                type="number"
                                                id="quantity-item-<?php echo esc_attr($crossupsell->productTo->productId); ?>"
                                                class="quantity large form-control input-number"
                                                name="quantity"
                                                value="1"
                                                autocomplete="off"
                                                min="<?php echo esc_attr($crossupsell->productTo->minimumQuantity); ?>"
                                                data-min="<?php echo esc_attr($crossupsell->productTo->minimumQuantity); ?>"
                                                data-unit="<?php echo esc_attr($crossupsell->productTo->unit); ?>"
                                                <?php if (PROPELLER_STOCK_CHECK) { ?>
                                                data-stock="<?php echo esc_attr($crossupsell->productTo->inventory->totalQuantity); ?>"
                                                <?php } ?>>
                                        </div>
                                    </div>
                                    <div class="offset-md-2 offset-lg-0 col-2 col-md-5 col-lg-5 d-flex justify-content-end justify-content-md-between">
                                        <div class="ms-md-4 product-price product-total d-none d-md-flex">
                                            <?php if ($user_prices == false) { ?>
                                                <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                                    <span class="basket-item-price">
                                                        <?php echo esc_html(PropellerHelper::formatPrice($crossupsell->productTo->price->gross)); ?>
                                                    </span>
                                                </span>
                                                <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                                            <?php } else { ?>
                                                <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                                    <span class="basket-item-price">
                                                        <?php echo esc_html(PropellerHelper::formatPrice($crossupsell->productTo->price->net)); ?>
                                                    </span>
                                                </span>
                                                <small><?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></small>
                                            <?php } ?>
                                        </div>
                                        <button class="btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                            <svg class="d-flex icon icon-cart" aria-hidden="true">
                                                <use xlink:href="#shape-shopping-cart"></use>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="d-flex d-md-none offset-2 pl-22 pt-3 col-10">
                            <div class="product-delivery">
                                <?php if (!empty($crossupsell->productTo->inventory) and $crossupsell->productTo->inventory->totalQuantity > 0) { ?>
                                    <div class="product-stock in-stock">
                                        <?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>: <span class="stock-total"><?php echo esc_html($crossupsell->productTo->inventory->totalQuantity); ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="product-stock out-of-stock">
                                        <?php echo esc_html(__('Out of stock', 'propeller-ecommerce-v2')); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php  } else if (isset($crossupsell->clusterTo) && !empty($crossupsell->clusterTo)) {
                    $cluster_product = $crossupsell->clusterTo->defaultProduct;
                ?>
                    <div class="row product-item propeller-product-card align-items-start">
                        <div class="col-2 col-md-1 product-card-image product-image px-0">
                            <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $crossupsell->clusterTo->slug[0]->value, $crossupsell->clusterTo->urlId)); ?>">
                                <?php if (!empty($cluster_product->media->images->items)) { ?>
                                    <img class="img-fluid" src="<?php echo esc_url($cluster_product->media->images->items[0]->imageVariants[0]->url); ?>" loading="lazy" alt="<?php echo esc_attr($crossupsell->clusterTo->name[0]->value); ?>">
                                <?php } else { ?>
                                    <img class="img-fluid"
                                        src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                                        loading="lazy" alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>">
                                <?php } ?>
                            </a>
                        </div>
                        <div class="col-10 col-md-4 col-lg-6 product-description">
                            <a class="product-name" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $crossupsell->clusterTo->slug[0]->value, $crossupsell->clusterTo->urlId)); ?>">
                                <?php echo esc_html($crossupsell->clusterTo->name[0]->value); ?>
                            </a>
                            <div class="product-sku product-code">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($crossupsell->clusterTo->sku); ?>
                            </div>
                            <div class="product-delivery d-none d-md-flex">
                                <?php if (!empty($cluster_product->inventory) and $cluster_product->inventory->totalQuantity > 0) { ?>
                                    <div class="product-stock in-stock">
                                        <?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>: <span class="stock-total"><?php echo esc_html($cluster_product->inventory->totalQuantity); ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="product-stock out-of-stock">
                                        <?php echo esc_html(__('Out of stock', 'propeller-ecommerce-v2')); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="add-to-basket offset-2 offset-md-0 ps-0 pe-0 col-10 col-md-7 col-lg-5">

                            <div class="row g-0 align-items-start">
                                <div class="px-22 px-md-0 col-7 col-md-2 col-lg-5 price-per-item">
                                    <div class="product-price product-price-item">
                                        <?php if ($user_prices == false) { ?>
                                            <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                                <?php echo esc_html(PropellerHelper::formatPrice($cluster_product->price->gross)); ?>
                                            </span>
                                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                                        <?php } else { ?>
                                            <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                                <?php echo esc_html(PropellerHelper::formatPrice($cluster_product->price->net)); ?>
                                            </span>
                                            <small><?php echo esc_html(__('incl. VAT', 'propeller-ecommerce-v2')); ?></small>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="offset-md-2 offset-lg-0 col-4 col-md-8 col-lg-7 d-flex justify-content-end justify-content-md-between">
                                    <a class="btn btn-addtobasket d-flex align-items-center justify-content-center w-100" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $crossupsell->clusterTo->slug[0]->value, $crossupsell->clusterTo->urlId)); ?>">
                                        <span class="d-flex text"><?php echo esc_html(__('To product', 'propeller-ecommerce-v2')); ?></span>
                                    </a>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex d-md-none offset-2 pl-22 pt-3 col-10">
                            <div class="product-delivery">
                                <?php if (!empty($cluster_product->inventory) and $cluster_product->inventory->totalQuantity > 0) { ?>
                                    <div class="product-stock in-stock">
                                        <?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>: <span class="stock-total"><?php echo esc_html($cluster_product->inventory->totalQuantity); ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="product-stock out-of-stock">
                                        <?php echo esc_html(__('Out of stock', 'propeller-ecommerce-v2')); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
        <?php }
            }
            $i++;
        }
        ?>
    </div>
</div>