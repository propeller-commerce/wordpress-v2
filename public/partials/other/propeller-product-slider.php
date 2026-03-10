<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row propeller-slider-wrapper">
    <?php if ($no_results) { ?>
        <h3><?php echo esc_html( __('No results', 'propeller-ecommerce-v2') ); ?></h3>
    <?php } else { ?>
        <div id="product_slider_<?php echo esc_attr($slider_id); ?>" data-slider_id="<?php echo esc_attr($slider_id); ?>" class="col-12 propeller-slider" 
        <?php foreach($data_attrs as $key => $value) { ?>data-<?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>" <?php }; ?>></div>
    <?php } ?>
</div>