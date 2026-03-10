<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ($data->pages >= 1) {
    foreach($purchase_authorizations as $purchase_authorization)
        apply_filters('propel_account_purchase_authorizations_table_list_item', $purchase_authorization, $obj);

    apply_filters('propel_account_purchase_authorizations_table_list_paging', $data, $obj);
}