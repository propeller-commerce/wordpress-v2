<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$childrenMap = [];

foreach ($items as $item) {
    if (
        $item->class === 'product' &&
        $item->isBonus !== 'Y' &&
        !empty($item->parentOrderItemId)
    ) {
        $childrenMap[$item->parentOrderItemId][] = $item;
    }
}
?>
<div class="propeller-order-product-list">
    <?php
    foreach ($items as $item) {
        if (
            $item->class === 'product' &&
            $item->isBonus !== 'Y' &&
            !is_null($item->product) &&
            empty($item->parentOrderItemId)
        ) {
            apply_filters('propel_order_details_overview_item', $item, $obj);

            if (isset($childrenMap[$item->id])) {
                foreach ($childrenMap[$item->id] as $child) {
                    apply_filters('propel_order_details_overview_cluster_item', $child, $obj);
                }
            }
        }
    } ?>
</div>