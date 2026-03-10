<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row propeller-desc-media">
    <div class="col-12">
        <?php
        // More robust check for description content - checking both cluster and defaultProduct descriptions
        $hasClusterDescription = false;
        $hasDefaultProductDescription = false;

        if (
            isset($cluster->description) && is_array($cluster->description) &&
            count($cluster->description) > 0 &&
            isset($cluster->description[0]->value) &&
            !empty(trim(wp_strip_all_tags($cluster->description[0]->value)))
        ) {
            $hasClusterDescription = true;
        }

        if (
            isset($cluster->defaultProduct->description) && is_array($cluster->defaultProduct->description) &&
            count($cluster->defaultProduct->description) > 0 &&
            isset($cluster->defaultProduct->description[0]->value) &&
            !empty(trim(wp_strip_all_tags($cluster->defaultProduct->description[0]->value)))
        ) {
            $hasDefaultProductDescription = true;
        }

        $hasAnyDescription = $hasClusterDescription || $hasDefaultProductDescription;
        ?>
        <ul class="nav nav-tabs" id="product-sticky-links">
            <?php if ($hasClusterDescription) { ?>
                <li class="nav-item">
                    <a href="#pane-desc" class="nav-link active"><?php echo esc_html(__('Description', 'propeller-ecommerce-v2')); ?></a>
                </li>
            <?php } else if ($hasDefaultProductDescription) { ?>
                <li class="nav-item">
                    <a href="#pane-desc" class="nav-link active"><?php echo esc_html(__('Description', 'propeller-ecommerce-v2')); ?></a>
                </li>
            <?php } ?>
            <?php
            if (\Propeller\PropellerHelper::spareparts_active())
                apply_filters('propel_spareparts_trigger_button', $cluster);
            ?>
            <li class="nav-item">
                <a href="#pane-specifications" data-loaded="false" data-tab="specifications" data-id="<?php echo esc_attr($cluster_product->productId); ?>" class="nav-link <?php if (!$hasAnyDescription) { ?>active<?php } ?>"><?php echo esc_html(__('Specifications', 'propeller-ecommerce-v2')); ?></a>
            </li>
            <li class="nav-item">
                <a href="#pane-downloads" data-loaded="false" data-tab="downloads" data-id="<?php echo esc_attr($cluster_product->productId); ?>" class="nav-link"><?php echo esc_html(__('Downloads', 'propeller-ecommerce-v2')); ?></a>
            </li>
            <li class="nav-item">
                <a href="#pane-videos" data-loaded="false" data-tab="videos" data-id="<?php echo esc_attr($cluster_product->productId); ?>" class="nav-link"><?php echo esc_html(__('Videos', 'propeller-ecommerce-v2')); ?></a>
            </li>
        </ul>

        <?php echo esc_html(apply_filters('propel_cluster_description', $cluster)); ?>

        <?php echo esc_html(apply_filters('propel_product_specifications', $cluster_product)); ?>

        <?php echo esc_html(apply_filters('propel_product_downloads', $cluster_product)); ?>

        <?php echo esc_html(apply_filters('propel_product_videos', $cluster_product)); ?>

    </div>
</div>