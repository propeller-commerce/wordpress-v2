<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ($data->pages >= 1) {
    foreach($orders as $order) {
        if (!$order->public)
            continue;

        apply_filters('propel_account_quotations_table_list_item', $order, $obj);
    }
        
    apply_filters('propel_account_quotations_table_list_paging', $data, $obj);
}