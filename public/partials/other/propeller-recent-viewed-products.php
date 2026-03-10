<?php
if ( ! defined( 'ABSPATH' ) ) exit;
foreach($products as $product)
        apply_filters('propel_' . strtolower($product->class) . '_card', $product, $this);
?>

