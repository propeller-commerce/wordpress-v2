<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row">
    <div class="col-12">
        <h3><?php echo esc_html(__('Specifications', 'propeller-ecommerce-v2')); ?></h3>
    </div>
    <div class="col-12 product-specs-rows">
        <?php if (!empty($product->eanCode)) { ?>
            <div class="row g-0 product-specs">
                <div class="col col-sm-6">
                    <?php echo esc_html(__('EAN code', 'propeller-ecommerce-v2')); ?>
                </div>
                <div class="col-6">
                    <?php /* translators: %s: EAN code */ ?>
                    <?php echo esc_html($product->eanCode); ?>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($product->manufacturer)) { ?>
            <div class="row g-0 product-specs">
                <div class="col col-sm-6">
                    <?php echo esc_html(__('Brand', 'propeller-ecommerce-v2')); ?>
                </div>
                <div class="col-6">
                    <?php /* translators: %s: Product brand */ ?>
                    <?php echo esc_html($product->manufacturer); ?>
                </div>
            </div>
        <?php } ?>
        <?php apply_filters('propel_product_specifications_rows', $product); ?>
    </div>
</div>