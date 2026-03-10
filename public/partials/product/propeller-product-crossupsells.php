<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Enum\CrossupsellTypes;

foreach ($product->crossupsells->from as $cu_type => $cu_items) {
    if ($cu_type === $type) { ?>
        <div class="row g-0 propeller-crossup <?php echo esc_html(apply_filters('propel_crossupsell_classes', '')); ?>">
            <div class="col-12">
                <h2 class="product-info-title mt-5 mb-4">
                    <?php
                    switch ($type) {
                        case CrossupsellTypes::ACCESSORIES:
                            echo esc_html(__('Accessories', 'propeller-ecommerce-v2'));
                            break;
                        case CrossupsellTypes::ALTERNATIVES:
                            echo esc_html(__('Alternatives', 'propeller-ecommerce-v2'));
                            break;
                        case CrossupsellTypes::OPTIONS:
                            echo esc_html(__('Options', 'propeller-ecommerce-v2'));
                            break;
                        case CrossupsellTypes::PARTS:
                            echo esc_html(__('Parts', 'propeller-ecommerce-v2'));
                            break;
                        case CrossupsellTypes::RELATED:
                            echo esc_html(__('Related', 'propeller-ecommerce-v2'));
                            break;
                        default:
                            break;
                    }
                    ?>
                </h2>
                <div class="row propeller-slider-wrapper">
                    <div class="col-12 slick-crossup" id="product-<?php echo esc_attr(strtolower(esc_html($type))); ?>-slider">
                        <?php foreach ($cu_items as $crossupsell) { ?>
                            <?php if (!$crossupsell) continue; ?>
                            <div>
                                <?php apply_filters('propel_' . strtolower($crossupsell->class) . '_crossupsell_card', $crossupsell, $obj); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
<?php }
} ?>
