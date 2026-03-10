<?php
if ( ! defined( 'ABSPATH' ) ) exit;
foreach ($products as $product) {
        if (!count($product->slug)) // skip products without slug, probably not translated
            continue;
        apply_filters('propel_' . strtolower($product->class) . '_card', $product, $this);
    }