<?php

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\CrossupsellTypes;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

?>
<svg style="display:none">
    <symbol viewBox="0 0 23 20" id="shape-shopping-cart">
        <title><?php echo esc_html(__('Shopping cart', 'propeller-ecommerce-v2')); ?></title>
        <path d="M18.532 20c.72 0 1.325-.24 1.818-.723a2.39 2.39 0 0 0 .739-1.777c0-.703-.253-1.302-.76-1.797a.899.899 0 0 0-.339-.508 1.002 1.002 0 0 0-.619-.195H7.55l-.48-2.5h13.26a.887.887 0 0 0 .58-.215.995.995 0 0 0 .34-.527l1.717-8.125a.805.805 0 0 0-.18-.781.933.933 0 0 0-.739-.352H5.152L4.832.781a.99.99 0 0 0-.338-.566.947.947 0 0 0-.62-.215H.48a.468.468 0 0 0-.34.137.45.45 0 0 0-.14.332V.78c0 .13.047.241.14.332a.468.468 0 0 0 .34.137h3.155L6.43 15.82c-.452.47-.679 1.042-.679 1.72 0 .676.247 1.256.74 1.737.492.482 1.098.723 1.817.723.719 0 1.324-.24 1.817-.723.493-.481.739-1.074.739-1.777 0-.443-.12-.86-.36-1.25h5.832c-.24.39-.36.807-.36 1.25 0 .703.246 1.296.74 1.777.492.482 1.097.723 1.816.723zm1.518-8.75H6.83l-1.438-7.5h16.256l-1.598 7.5zm-11.742 7.5c-.347 0-.646-.124-.899-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.899-.371c.346 0 .645.124.898.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.898.371zm10.224 0c-.346 0-.645-.124-.898-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.898-.371c.347 0 .646.124.899.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.899.371z" fill-rule="nonzero" />
    </symbol>
    <symbol viewBox="0 0 20 18" id="shape-favorites">
        <title><?php echo esc_html(__('Favorites', 'propeller-ecommerce-v2')); ?></title>
        <path d="M14.549.506a4.485 4.485 0 0 1 3.204 1.106c1.103.982 1.682 2.349 1.741 3.734.06 1.4-.41 2.823-1.417 3.894l-7.525 8.004c-.186.157-.39.242-.588.242a.675.675 0 0 1-.495-.222L1.93 9.248a5.704 5.704 0 0 1-1.4-3.965c.06-1.377.637-2.707 1.718-3.67.94-.838 2.127-1.185 3.298-1.1 1.22.087 2.42.64 3.32 1.597L10 3.309l1.126-1.193C12.191 1.074 13.362.565 14.55.506z" />
    </symbol>
    <symbol viewBox="0 0 14 10" id="shape-checkmark">
        <title><?php echo esc_html(__('Checkmark', 'propeller-ecommerce-v2')); ?></title>
        <path d="M11.918.032 4.725 7.225 2.082 4.582a.328.328 0 0 0-.464 0l-.773.773a.328.328 0 0 0 0 .465l3.648 3.648a.328.328 0 0 0 .464 0l8.198-8.198a.328.328 0 0 0 0-.464l-.773-.774a.328.328 0 0 0-.464 0z" />
    </symbol>
    <symbol viewBox="0 0 25 25" id="shape-plus">
        <title><?php echo esc_html(__('Plus', 'propeller-ecommerce-v2')); ?></title>
        <g fill="none" fill-rule="evenodd">
            <path d="M12.5 0C5.595 0 0 5.595 0 12.5S5.595 25 12.5 25 25 19.405 25 12.5 19.405 0 12.5 0z" fill="#62BC5E" fill-rule="nonzero" />
            <path d="M12.5 6A1.5 1.5 0 0 1 14 7.5V11h3.5a1.5 1.5 0 0 1 0 3H14v3.5a1.5 1.5 0 0 1-3 0V14H7.5a1.5 1.5 0 0 1 0-3H11V7.5A1.5 1.5 0 0 1 12.5 6z" fill="#FFF" />
        </g>
    </symbol>
    <symbol viewBox="0 0 25 25" id="shape-equals">
        <title><?php echo esc_html(__('Equals', 'propeller-ecommerce-v2')); ?></title>
        <g fill="none" fill-rule="evenodd">
            <path d="M12.5 0C5.595 0 0 5.595 0 12.5S5.595 25 12.5 25 25 19.405 25 12.5 19.405 0 12.5 0z" fill="#62BC5E" fill-rule="nonzero" />
            <rect fill="#FFF" x="7" y="8" width="11" height="3" rx="1.5" />
            <rect fill="#FFF" x="7" y="14" width="11" height="3" rx="1.5" />
        </g>
    </symbol>
</svg>

<div class="container-fluid px-0 propeller-product-details <?php apply_filters('propel_product_details_classes', ''); ?>" data-id="<?php echo esc_attr($this->product->urlId); ?>" data-slug="<?php echo esc_attr($this->product->slug[0]->value); ?>">
    <?php apply_filters('propel_product_gecommerce', $this->product, $this); ?>

    <div class="row">
        <div class="col">
            <?php
            $breadcrumb_paths = $this->build_breadcrumbs($this->product);

            apply_filters('propel_breadcrumbs', $breadcrumb_paths);
            ?>
        </div>
    </div>

    <div class="row product-gallery-price-wrapper">
        <!-- Product gallery -->
        <div class="col-12 col-lg-7 gallery-wrapper">
            <?php apply_filters('propel_product_name_mobile', $this->product); ?>

            <?php apply_filters('propel_product_meta_mobile', $this->product, $this); ?>

            <?php apply_filters('propel_product_gallery', $this->product, $this); ?>

            <?php apply_filters('propel_product_desc_media', $this->product, $this); ?>

        </div>

        <!-- Product name, pricing, short description -->
        <div class="col-12 col-lg-5">
            <div class="product-price-description-wrapper">
                <?php apply_filters('propel_product_name', $this->product); ?>

                <?php apply_filters('propel_product_meta', $this->product, $this); ?>

                <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
                    <?php apply_filters('propel_product_price_details', $this->product, $this); ?>
                <?php } ?>
                <?php apply_filters('propel_product_short_desc', $this->product); ?>

                <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
                    <?php apply_filters('propel_product_bulk_prices', $this->product); ?>
                <?php } ?>

                <div class="row justify-content-between add-to-basket-wrapper d-none d-md-flex">
                    <?php
                    if ($this->product->orderable === 'Y') {
                        if ($this->product->is_price_on_request())
                            apply_filters('propel_product_add_to_price_request', $this->product);
                        else
                            apply_filters('propel_product_add_to_basket', $this->product);
                    ?>
                        <div class="col-auto">
                            <?php
                            if (UserController::is_propeller_logged_in())
                                apply_filters('propel_product_add_favorite', $this->product, $this);
                            ?>
                        </div>
                    <?php } else { ?>
                        <div class="col-12">
                            <div class="alert alert-dark alert-not-available"><?php echo esc_html(__('Product is no longer available', 'propeller-ecommerce-v2')); ?></div>
                        </div>
                    <?php } ?>
                </div>
                <?php /*
                        if (sizeof($this->product->bulkPrices) > 1) { ?>
                        </div>
                    <?php } */ ?>
            </div>
        </div>
    </div>

    <?php
    if (PropellerHelper::spareparts_active())
        apply_filters('propel_spareparts_content', $this->product);
    ?>

    <?php apply_filters('propel_product_bundles', $this->product, $this); ?>

    <?php
    if (isset($this->product->crossupsells->from) && count($this->product->crossupsells->from)) {
        apply_filters('propel_product_crossupsells', $this->product, $this, CrossupsellTypes::ACCESSORIES);
        apply_filters('propel_product_crossupsells', $this->product, $this, CrossupsellTypes::ALTERNATIVES);
        apply_filters('propel_product_crossupsells', $this->product, $this, CrossupsellTypes::RELATED);
        apply_filters('propel_product_crossupsells', $this->product, $this, CrossupsellTypes::OPTIONS);
        apply_filters('propel_product_crossupsells', $this->product, $this, CrossupsellTypes::PARTS);
    }
    ?>

    <div id="fixed-wrapper" class="d-md-none fixed-wrapper <?php if (isset($this->product->bulkPrices) && sizeof($this->product->bulkPrices) > 1) { ?>has-bulk-prices<?php } ?>">
        <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
            <?php apply_filters('propel_product_bulk_prices', $this->product); ?>
        <?php } ?>
        <div class="row align-items-center justify-content-between">

            <?php apply_filters('propel_product_add_to_basket', $this->product); ?>
            <?php if (UserController::is_propeller_logged_in()) { ?>
                <div class='col-auto pr-30'>
                    <?php
                    apply_filters('propel_product_add_favorite', $this->product, $this);
                    ?>
                </div>
            <?php } else { ?>
                <div class="pe-4"></div>
            <?php } ?>
        </div>
    </div>

</div>
</div>