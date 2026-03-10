<?php
if ( ! defined( 'ABSPATH' ) ) exit;
foreach ($obj->get_items() as $item) {
        if(!is_object($item->bundle))
            apply_filters('propel_shopping_cart_table_product_item', $item, $this->cart, $this);
            // require $obj->load_template('partials', '/cart/propeller-shopping-cart-product-item.php');
        else 
            apply_filters('propel_shopping_cart_table_bundle_item', $item, $this->cart, $this);
            // require $obj->load_template('partials', '/cart/propeller-shopping-cart-bundle-item.php');
    }