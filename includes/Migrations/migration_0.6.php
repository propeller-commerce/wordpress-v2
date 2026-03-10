<?php
defined('ABSPATH') || exit;

$res = [];

// Check if contact_root column exists before dropping
$contact_root_exists = $wpdb->get_results($wpdb->prepare(
    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'contact_root'",
    DB_NAME, $tbl_settings
));

if (!empty($contact_root_exists)) {
    $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
        DROP COLUMN `contact_root`", $tbl_settings));
}

// Check if customer_root column exists before dropping
$customer_root_exists = $wpdb->get_results($wpdb->prepare(
    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'customer_root'",
    DB_NAME, $tbl_settings
));

if (!empty($customer_root_exists)) {
    $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
        DROP COLUMN `customer_root`", $tbl_settings));
}

foreach ($res as $r) {
    if ($r === false)
        return false;
}

return true;
