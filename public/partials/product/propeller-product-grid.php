<?php
if ( ! defined( 'ABSPATH' ) ) exit;
apply_filters('propel_product_listing_pre_grid', $paging_data, $obj, $sort, $prop_name, $prop_value, $do_action, $obid); ?>

<section class="propeller-products-wrapper">
    <div class="row propeller-product-list <?php echo esc_attr($display_class); ?>">

        <?php apply_filters('propel_category_gecommerce_listing', $products, $obj); ?>
        
        <?php apply_filters('propel_category_listing_products', $products, $obj); ?>

        <?php apply_filters('propel_category_listing_pagination', $paging_data, $prop_name, $prop_value, $do_action, $obid); ?>

    </div>
</section>