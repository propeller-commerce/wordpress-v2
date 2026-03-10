<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Enum\CrossupsellTypes;

?>

<div class="row g-0 propeller-crossup <?php echo esc_html( apply_filters('propel_crossupsell_classes', '') ); ?>">
    <div class="col-12">
        <h2 class="product-info-title mt-5 mb-4">
            <?php 
                if (strtolower($type) == CrossupsellTypes::ACCESSORIES) echo esc_html(__('Accessories products', 'propeller-ecommerce-v2')); 
                else if (strtolower($type) == CrossupsellTypes::ALTERNATIVES) echo esc_html(__('Alternative products', 'propeller-ecommerce-v2'));
                else if (strtolower($type) == CrossupsellTypes::RELATED) echo esc_html(__('Related products', 'propeller-ecommerce-v2'));
            ?>
        </h2>
        <div class="row propeller-slider-wrapper">
            <div class="col-12 slick-crossup crossupsells-slider" data-slug="<?php echo esc_attr($obj->slug); ?>" data-id="<?php echo esc_attr($obj->product->urlId); ?>" data-type="<?php echo esc_attr($type); ?>" data-class="<?php echo esc_attr($obj->product->class) ?>" id="product-<?php echo esc_attr($type); ?>-slider"></div>
        </div>
    </div>
</div>