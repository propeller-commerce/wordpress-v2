<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ($data->pages >= 1) {
    foreach($contacts as $contact)
        apply_filters('propel_account_purchase_authorizations_contacts_table_list_item', $contact, $obj);

    apply_filters('propel_account_purchase_authorizations_contacts_table_list_paging', $data, $obj);
}