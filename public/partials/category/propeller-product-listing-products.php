<?php
if ( ! defined( 'ABSPATH' ) ) exit;
foreach ($products as $product) { 
    if (!$product)
        continue;
        
    if (!count($product->slug)) // skip products without slug, probably not translated
        continue;
?>     

    <div class="propeller-list-item col-12 col-sm-6 col-xl-4">
        <?php apply_filters('propel_' . strtolower($product->class) . '_card', $product, $this); ?>
    </div>
<?php } ?>
