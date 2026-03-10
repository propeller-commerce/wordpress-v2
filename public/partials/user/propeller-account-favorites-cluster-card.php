<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);
?>
<div class="propeller-product-card" id="fav_product_<?php echo esc_attr($cluster->clusterId); ?>">
    <div class="row g-0 align-items-start">
        <div class="col-2 product-image product-card-image order-1 pe-2">
            <div class="row g-0 align-items-start d-flex align-middle align-items-center">
                <div class="col-4 align-middle align-items-center">
                    <input type="checkbox"
                        class="favorite-item-check me-2"
                        data-class="<?php echo esc_attr($cluster->class); ?>"
                        value="<?php echo esc_attr($cluster->clusterId) . '-' . esc_attr($cluster->class); ?>"
                        data-orderable="<?php echo esc_attr($cluster->defaultProduct->orderable === 'Y' ? 1 : 0); ?>"
                        data-price_on_request="<?php echo esc_attr($cluster->defaultProduct->is_price_on_request() ? 1 : 0); ?>" />
                </div>
                <div class="col-8 p-2">
                    <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $cluster->get_slug(), $cluster->urlId)); ?>">
                        <?php if ($cluster->defaultProduct->has_images()) { ?>
                            <img <?php if ($lazy_load_images) { ?>
                                class="img-fluid lazy"
                                src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                                data-src="<?php echo esc_url($cluster->defaultProduct->images[0]->images[0]->url); ?>"
                                <?php } else { ?>
                                class="img-fluid"
                                src="<?php echo esc_url($cluster->defaultProduct->images[0]->images[0]->url); ?>"
                                <?php } ?>
                                alt="<?php echo esc_attr((count($cluster->defaultProduct->images[0]->alt) ? $cluster->defaultProduct->images[0]->alt[0]->value : "")); ?>" />
                        <?php } else { ?>
                            <img class="img-fluid"
                                src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                                alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>" />
                        <?php } ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-9 col-md-4 col-xl-5 pe-5 product-description order-2 ps-4">
            <span class="product-name">
                <a class="product-name" href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $cluster->get_slug(), $cluster->urlId)); ?>" title="<?php echo esc_attr($cluster->get_name()); ?>">
                    <?php echo esc_html($cluster->get_name()); ?>
                </a>
            </span>
            <div class="product-sku product-code">
                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($cluster->sku); ?>
            </div>
            <div class="stock-status">
                <?php echo esc_html(apply_filters('propel_product_stock', $cluster)); ?></div>
        </div>

        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4 text-start">
            <div class="price">
                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                <?php echo esc_html(PropellerHelper::formatPrice(!$user_prices ? $cluster->defaultProduct->price->gross : $cluster->defaultProduct->price->net)); ?>
            </div>
            <small><?php echo esc_html(!$user_prices ? __('excl. VAT', 'propeller-ecommerce-v2') : __('incl. VAT', 'propeller-ecommerce-v2')); ?></small>
        </div>
        <div class="col-6 col-md-3 col-xl-2 mb-4 order-5">
            <?php if ($cluster->defaultProduct->orderable === 'Y') { ?>
                <a class="btn btn-cluster d-flex align-items-center justify-content-center" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $cluster->get_slug(), $cluster->urlId)); ?>">
                    <svg class="d-flex d-md-none icon icon-cart" aria-hidden="true">
                        <use xlink:href="#shape-shopping-cart"></use>
                    </svg>
                    <span class="d-none d-md-flex text"><?php echo esc_html(__('View', 'propeller-ecommerce-v2')); ?></span>
                </a>
            <?php } else { ?>
                <div class="col-12">
                    <div class="alert alert-dark alert-not-available"><?php echo esc_html(__('Product is no longer available', 'propeller-ecommerce-v2')); ?></div>
                </div>
            <?php } ?>
        </div>
        <div class="col-1 d-flex align-items-center justify-content-end order-3 order-md-last">
            <form name="delete-favorite" method="post" class="delete-favorite-item-form">
                <input type="hidden" name="list_id" value="<?php echo esc_attr($obj->data->id); ?>">
                <input type="hidden" name="cluster_id[]" value="<?php echo esc_attr($cluster->clusterId); ?>">
                <input type="hidden" name="action" value="delete_favorite">
                <div class="input-group">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#delete_favorite_<?php echo esc_attr($cluster->clusterId); ?>" class="btn-delete d-flex align-items-start align-items-md-center justify-content-end">
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

<?php apply_filters('propel_account_favorites_remove_favorite_item_modal', $obj->data, $cluster, $obj) ?>
