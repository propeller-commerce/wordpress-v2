<?php
if ( ! defined( 'ABSPATH' ) ) exit;
foreach ($product->$type as $crossupsell) {
    if (!is_null($crossupsell->item) && $crossupsell->item->status !== 'N') {
        if (!count($crossupsell->item->slug)) continue; ?>
        <div>
            <?php echo esc_html( apply_filters('propel_' . strtolower($crossupsell->item->class) . '_crossupsell_card', $crossupsell, $obj) ); ?>
        </div>
<?php }
} ?>
