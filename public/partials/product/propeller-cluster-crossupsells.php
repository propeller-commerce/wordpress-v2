<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (count($cluster_product->crossupsells)) { ?>
    <div class="row g-0 propeller-crossup <?php echo esc_html(apply_filters('propel_crossupsell_classes', '')); ?>">
        <div class="col-12">
            <h2 class="product-info-title mt-5 mb-4"><?php echo esc_html(__('Related products', 'propeller-ecommerce-v2')); ?></h2>
            <div class="row propeller-slider-wrapper">
                <div class="col-12 slick-crossup" id="product-related-slider">
                    <?php foreach ($cluster_product->crossupsells as $crossupsell) { ?>
                        <div>
                            <?php echo esc_html(apply_filters('propel_' . $crossupsell->item->class . '_crossupsell_card', $crossupsell, $obj)); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
