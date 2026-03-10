<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row propeller-desc-media">
    <div class="col-12">
        <?php
        // More robust check for description content
        $hasDescription = false;
        if (
            isset($product->description) && is_array($product->description) &&
            count($product->description) > 0 &&
            isset($product->description[0]->value) &&
            !empty(trim(wp_strip_all_tags($product->description[0]->value)))
        ) {
            $hasDescription = true;
        }
        ?>
        <ul class="nav nav-tabs" id="product-sticky-links">
            <?php if ($hasDescription) { ?>
                <li class="nav-item">
                    <a href="#pane-description" class="nav-link active"><?php echo esc_html(__('Description', 'propeller-ecommerce-v2')); ?></a>
                </li>
            <?php } ?>
            <?php
            if (\Propeller\PropellerHelper::spareparts_active())
                apply_filters('propel_spareparts_trigger_button', $product);
            ?>
            <li class="nav-item">
                <a href="#pane-specifications" data-loaded="false" data-tab="specifications" data-id="<?php echo esc_attr($product->productId); ?>" class="nav-link <?php if (!$hasDescription) { ?>active<?php } ?>"><?php echo esc_html(__('Specifications', 'propeller-ecommerce-v2')); ?></a>
            </li>
            <li class="nav-item">
                <a href="#pane-downloads" data-loaded="false" data-tab="downloads" data-id="<?php echo esc_attr($product->productId); ?>" class="nav-link"><?php echo esc_html(__('Downloads', 'propeller-ecommerce-v2')); ?></a>
            </li>
            <li class="nav-item">
                <a href="#pane-videos" data-loaded="false" data-tab="videos" data-id="<?php echo esc_attr($product->productId); ?>" class="nav-link"><?php echo esc_html(__('Videos', 'propeller-ecommerce-v2')); ?></a>
            </li>
        </ul>

        <?php apply_filters('propel_product_description', $product); ?>

        <?php apply_filters('propel_product_specifications', $product); ?>

        <?php apply_filters('propel_product_downloads', $product); ?>

        <?php apply_filters('propel_product_videos', $product); ?>

    </div>
</div>