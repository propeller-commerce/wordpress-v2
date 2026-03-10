<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="propeller-account-table">
    <h4><?php echo esc_html(__('Authorization requests', 'propeller-ecommerce-v2')); ?></h4>

    <?php if ($purchase_authorizations->itemsFound > 0) { ?>
        <div class="order-sorter">
            <?php apply_filters('propel_account_purchase_authorizations_table_header', $purchase_authorizations); ?>

            <div class="purchase-authorizations-list propeller-account-list">

                <?php apply_filters('propel_account_purchase_authorizations_table_list', $purchase_authorizations->items, $purchase_authorizations, $obj); ?>

            </div>
        </div>
    <?php } else { ?>
        <div class="no-results">
            <?php echo esc_html(__('You do not have any authorization requests yet.', 'propeller-ecommerce-v2')); ?>
        </div>
    <?php } ?>
</div>