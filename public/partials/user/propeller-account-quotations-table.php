<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="propeller-account-table">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><?php echo esc_html(__('My quotes', 'propeller-ecommerce-v2')); ?></h4>
        <?php if (isset($orders) && sizeof($orders)) { ?>
            <!-- Mobile sorting dropdown -->
            <div class="d-md-none dropdown sticky-dropdown-menu">
                <select class="form-control form-control-sm orders-sort-mobile" name="orders-sort" id="orders_sort">
                    <option value="date|DESC" <?php selected($current_sort_field === 'date' && $current_sort_order === 'DESC'); ?>>
                        <?php echo esc_html(__('Date (Newest first)', 'propeller-ecommerce-v2')); ?>
                    </option>
                    <option value="date|ASC" <?php selected($current_sort_field === 'date' && $current_sort_order === 'ASC'); ?>>
                        <?php echo esc_html(__('Date (Oldest first)', 'propeller-ecommerce-v2')); ?>
                    </option>
                    <option value="quotenumber|DESC" <?php selected($current_sort_field === 'quotenumber' && $current_sort_order === 'DESC'); ?>>
                        <?php echo esc_html(__('Quote number (High to Low)', 'propeller-ecommerce-v2')); ?>
                    </option>
                    <option value="quotenumber|ASC" <?php selected($current_sort_field === 'quotenumber' && $current_sort_order === 'ASC'); ?>>
                        <?php echo esc_html(__('Quote number (Low to High)', 'propeller-ecommerce-v2')); ?>
                    </option>
                    <option value="total|DESC" <?php selected($current_sort_field === 'total' && $current_sort_order === 'DESC'); ?>>
                        <?php echo esc_html(__('Total (High to Low)', 'propeller-ecommerce-v2')); ?>
                    </option>
                    <option value="total|ASC" <?php selected($current_sort_field === 'total' && $current_sort_order === 'ASC'); ?>>
                        <?php echo esc_html(__('Total (Low to High)', 'propeller-ecommerce-v2')); ?>
                    </option>
                    <option value="valid_until|DESC" <?php selected($current_sort_field === 'valid_until' && $current_sort_order === 'DESC'); ?>>
                        <?php echo esc_html(__('Valid until (Newest first)', 'propeller-ecommerce-v2')); ?>
                    </option>
                    <option value="valid_untildate|ASC" <?php selected($current_sort_field === 'valid_until' && $current_sort_order === 'ASC'); ?>>
                        <?php echo esc_html(__('Valid until (Oldest first)', 'propeller-ecommerce-v2')); ?>
                    </option>
                </select>
            </div>
        <?php } ?>
    </div>
    <?php if (isset($orders) && sizeof($orders)) { ?>
        <?php echo esc_html(apply_filters('propel_account_quotations_table_header', $orders)); ?>

        <div class="quotations-list propeller-account-list">
            <?php echo esc_html(apply_filters('propel_account_quotations_table_list', $orders, $data, $obj)); ?>
        </div>

    <?php } else { ?>
        <div class="no-results">
            <?php echo esc_html(__('You have no quotes.', 'propeller-ecommerce-v2')); ?>
        </div>
    <?php } ?>
</div>