<?php
if (! defined('ABSPATH')) exit;

use Propeller\PropellerHelper;
?>

<div class="row d-none d-md-flex orders-header g-0">
    <div class="col-md-2">
        <a href="#"
            class="sort-link <?php echo $current_sort_field === 'ordernumber' ? 'active' : ''; ?>"
            data-sort-field="ordernumber"
            data-sort-order="<?php echo ($current_sort_field === 'ordernumber' && $current_sort_order === 'DESC') ? 'ASC' : 'DESC'; ?>">
            <?php echo esc_html(__('Order number', 'propeller-ecommerce-v2')); ?>
            <?php echo wp_kses_post(PropellerHelper::get_sort_icon('ordernumber', $current_sort_field, $current_sort_order)); ?>
        </a>
    </div>
    <div class="col-md-3">
        <a href="#"
            class="sort-link <?php echo $current_sort_field === 'date' ? 'active' : ''; ?>"
            data-sort-field="date"
            data-sort-order="<?php echo ($current_sort_field === 'date' && $current_sort_order === 'DESC') ? 'ASC' : 'DESC'; ?>">
            <?php echo esc_html(__('Date', 'propeller-ecommerce-v2')); ?>
            <?php echo wp_kses_post(PropellerHelper::get_sort_icon('date', $current_sort_field, $current_sort_order)); ?>
        </a>
    </div>
    <div class="col-xl-1 col-md-1"><?php echo esc_html(__('Qty', 'propeller-ecommerce-v2')); ?></div>
    <div class="col-md-2">
        <a href="#"
            class="sort-link <?php echo $current_sort_field === 'total' ? 'active' : ''; ?>"
            data-sort-field="total"
            data-sort-order="<?php echo ($current_sort_field === 'total' && $current_sort_order === 'DESC') ? 'ASC' : 'DESC'; ?>">
            <?php echo esc_html(__('Order total', 'propeller-ecommerce-v2')); ?>
            <?php echo wp_kses_post(PropellerHelper::get_sort_icon('total', $current_sort_field, $current_sort_order)); ?>
        </a>
    </div>
    <div class="col-md-2">
        <a href="#"
            class="sort-link <?php echo $current_sort_field === 'status' ? 'active' : ''; ?>"
            data-sort-field="status"
            data-sort-order="<?php echo ($current_sort_field === 'status' && $current_sort_order === 'DESC') ? 'ASC' : 'DESC'; ?>">
            <?php echo esc_html(__('Status', 'propeller-ecommerce-v2')); ?>
            <?php echo wp_kses_post(PropellerHelper::get_sort_icon('status', $current_sort_field, $current_sort_order)); ?>
        </a>
    </div>
    <div class="col-md-2">&nbsp;</div>
</div>