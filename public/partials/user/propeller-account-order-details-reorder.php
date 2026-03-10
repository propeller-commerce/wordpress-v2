<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="add-to-basket d-inline-flex "> 
    <form name="add-to-basket" class="replenish-form" method="post">
        <input type="hidden" name="action" value="do_replenish">
        <input type="hidden" name="items" value="<?php echo esc_attr( implode(',', $reorder_item_ids) ); ?>">
        <button type="submit" class="btn-replenish">
            <span>
                <?php echo esc_html( __('Order again', 'propeller-ecommerce-v2') ); ?>
            </span>
        </button> 
    </form>
</div>