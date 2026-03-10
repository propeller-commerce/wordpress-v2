<?php
if ( ! defined( 'ABSPATH' ) ) exit;
foreach ($crosupsells->$type as $crossupsell) { ?>
    <div>
        <?php echo esc_html( apply_filters('propel_' . strtolower($crossupsell->item->class) . '_crossupsell_card', $crossupsell, $obj) ); ?>
    </div>
<?php } ?>
