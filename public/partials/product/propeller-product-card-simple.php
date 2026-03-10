<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\PropellerHelper;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

?>

<div>
    <div class="card propeller-product-card propeller-product-card-small">
        <figure class="card-img-top">
            <div class="product-labels">
                <?php if ($product->has_attributes()) {
                    foreach ($product->get_attributes() as $attribute) {
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
                <!-- build the product urls with the classId of the product (temporary) -->

                <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId)); ?>">
                    <?php if ($product->has_images()) { ?>
                        <img <?php if ($lazy_load_images) { ?>
                            class="img-fluid lazy"
                            data-src="<?php echo esc_url($product->images[0]->images[0]->url); ?>"
                            src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                            <?php } else { ?>
                            class="img-fluid"
                            src="<?php echo esc_url($product->images[0]->images[0]->url); ?>"
                            <?php } ?>
                            alt="<?php echo esc_attr( (count($product->images[0]->alt) ? $product->images[0]->alt[0]->value : "") ); ?>"
                            width="<?php echo esc_html( PROPELLER_PRODUCT_IMG_CATALOG_WIDTH ); ?>"
                            height="<?php echo esc_html( PROPELLER_PRODUCT_IMG_CATALOG_HEIGHT ); ?>">
                    <?php } else { ?>
                        <img class="img-fluid no-image-card"
                            src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>"
                            alt="<?php echo esc_attr( __('No image found', 'propeller-ecommerce-v2') ); ?>"
                            width="300" height="300">
                    <?php } ?>
                </a>
            </div>
        </figure>
        <div class="card-body product-card-description">
            <div class="product-name">
                <!-- build the product urls with the classId of the product (temporary) -->
                <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId)); ?>">
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
                            <a class="btn btn-addtobasket d-flex align-items-center justify-content-center" href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId)); ?>">
                                <svg class="d-flex d-md-none icon icon-cart" aria-hidden="true">
                                    <use xlink:href="#shape-shopping-cart"></use>
                                </svg>
                                <span class="d-none d-md-flex text"><?php echo esc_html( __('To product', 'propeller-ecommerce-v2') ); ?></span>
                            </a>
                        </div>
                    </div>
                </div>

            <?php } else { ?>
                <!-- Include the price display template -->

                <?php if (!$product->is_price_on_request()) { ?>
                    <div class="product-price">

                        <?php
                        if ($product->priceData->display == "FROM_FOR") { ?>
                            <?php if ($user_prices == false) { ?>
                                <div class="product-current-price has-discount d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->price->gross) ); ?> <?php echo esc_html( __('excl. VAT', 'propeller-ecommerce-v2') ); ?></span></div>
                                <div class="product-old-price d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->priceData->suggested) ); ?> </span></div>
                            <?php } else { ?>
                                <div class="product-current-price has-discount d-md-inline-flex"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->price->net) ); ?> <?php echo esc_html( __('incl. VAT', 'propeller-ecommerce-v2') ); ?></span></div>
                            <?php } ?>

                        <?php } else if ($user_prices == false) { ?>
                            <div class="product-current-price"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->price->gross) ); ?> <?php echo esc_html( __('excl. VAT', 'propeller-ecommerce-v2') ); ?></span></div>
                        <?php } else { ?>
                            <div class="product-current-price"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($product->price->net) ); ?> <?php echo esc_html( __('incl. VAT', 'propeller-ecommerce-v2') ); ?></span></div>
                        <?php } ?>
                    </div>
                    <?php if ($user_prices == false) { ?>
                        <small class="product-customer-price d-flex">
                            <span class="product-price-tax"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span> <?php echo esc_html( PropellerHelper::formatPrice($product->price->net) ); ?> <?php echo esc_html( __('incl. VAT', 'propeller-ecommerce-v2') ); ?></span>
                        </small>
                    <?php } else { ?>
                        <small class="product-customer-price d-flex">
                            <span class="product-price-tax"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span> <?php echo esc_html( PropellerHelper::formatPrice($product->price->gross) ); ?> <?php echo esc_html( __('excl. VAT', 'propeller-ecommerce-v2') ); ?></span>
                        </small>
                    <?php } ?>
                    <div class="add-to-cart-stock-wrapper">
                        <!-- Include the order button template -->
                        <div class="add-to-basket-wrapper">
                            <?php /*if( $product->orderable === 'Y') { */ ?>
                            <div class="add-to-basket">
                                <form class="add-to-basket-form d-flex" name="add-product" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo esc_attr($product->productId); ?>">
                                    <input type="hidden" name="action" value="cart_add_item">

                                    <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId)); ?>" class="btn btn-addtobasket d-flex align-items-center justify-content-center">
                                        <!-- <svg class="d-flex icon icon-cart" aria-hidden="true">
                                                <use xlink:href="#shape-shopping-cart"></use>
                                            </svg>     -->
                                        <span class="d-flex text"><?php echo esc_html( __('To product', 'propeller-ecommerce-v2') ); ?></span>
                                    </a>

                                </form>
                            </div>
                            <?php /* } else { */ ?>
                            <!--<div class="alert alert-dark alert-not-available"><?php echo esc_html( __('Product is no longer available', 'propeller-ecommerce-v2') ); ?></div> --->
                            <?php /* } */ ?>
                        </div>

                        <div class="product-code"><?php echo esc_html($product->sku); ?></div>
                    </div>
                <?php } else { ?>
                    <div class="product-price price-on-request">
                        <div class="price">
                            <?php echo esc_html( __('Price on request', 'propeller-ecommerce-v2') ); ?>
                        </div>
                    </div>
                    <a href="#" class="btn-addtobasket d-flex justify-content-center align-items-center btn-price-request" data-id="<?php echo esc_attr($product->productId); ?>" data-code="<?php echo esc_attr($product->sku); ?>" data-name="<?php echo esc_attr($product->name[0]->value); ?>" data-quantity="<?php echo esc_attr($product->minimumQuantity); ?>" data-minquantity="<?php echo esc_attr($product->minimumQuantity); ?>" data-unit="<?php echo esc_attr($product->unit); ?>">
                        <?php echo esc_html( __('Request a price', 'propeller-ecommerce-v2') ); ?>
                    </a>
                <?php } ?>

            <?php } ?>
        </div>


    </div>
</div>