<?php
defined('ABSPATH') || exit;

$res = [];

// Check if use_ga4 column exists before adding
$use_ga4_exists = $wpdb->get_results($wpdb->prepare(
    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'use_ga4'",
    DB_NAME,
    $tbl_behavior
));

if (empty($use_ga4_exists)) {
    $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
        ADD COLUMN `use_ga4` TINYINT(1) NULL DEFAULT 0 AFTER `pac_add_contacts`", $tbl_behavior));
}

// Check if ga4_tracking column exists before adding
$ga4_tracking_exists = $wpdb->get_results($wpdb->prepare(
    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'ga4_tracking'",
    DB_NAME,
    $tbl_behavior
));

if (empty($ga4_tracking_exists)) {
    $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
        ADD COLUMN `ga4_tracking` TINYINT(1) NULL DEFAULT 0 AFTER `use_ga4`", $tbl_behavior));
}

// Check if ga4_key column exists before adding
$ga4_key_exists = $wpdb->get_results($wpdb->prepare(
    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'ga4_key'",
    DB_NAME,
    $tbl_behavior
));

if (empty($ga4_key_exists)) {
    $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
        ADD COLUMN `ga4_key` VARCHAR(50) NULL DEFAULT NULL AFTER `ga4_tracking`", $tbl_behavior));
}

// Check if gtm_key column exists before adding
$gtm_key_exists = $wpdb->get_results($wpdb->prepare(
    "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'gtm_key'",
    DB_NAME,
    $tbl_behavior
));

if (empty($gtm_key_exists)) {
    $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
        ADD COLUMN `gtm_key` VARCHAR(50) NULL DEFAULT NULL AFTER `ga4_key`", $tbl_behavior));
}

foreach ($res as $r) {
    if ($r === false)
        return false;
}

return true;
