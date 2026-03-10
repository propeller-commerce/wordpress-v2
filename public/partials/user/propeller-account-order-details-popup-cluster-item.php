<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

?>
<div class="order-cluster-items">
    <?php echo esc_html($item->quantity); ?> x <?php echo esc_html($item->product->get_name()); ?> (<?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;
    <?php echo esc_html(PropellerHelper::formatPrice($item->totalPrice)); ?>)
</div>