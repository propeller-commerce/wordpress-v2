<?php
if ( ! defined( 'ABSPATH' ) ) exit;
apply_filters('propel_machine_listing_pre_grid', $machines, $obj, $sort, $prop_name, $prop_value, $do_action); ?>

<section class="propeller-products-wrapper">
    <div class="row propeller-product-list <?php echo esc_attr($display_class); ?>">

        <?php // apply_filters('propel_category_gecommerce_listing', $products, $obj); ?>
        
        <?php apply_filters('propel_machine_listing_machines', $machines, $parts, $obj); ?>

        <?php apply_filters('propel_machine_listing_pagination', $parts, $prop_name, $prop_value, $do_action); ?>

    </div>
</section>