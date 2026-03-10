<?php
    defined( 'ABSPATH' ) || exit;

    // Check if track_company_attr column exists before adding
    $column_exists = $wpdb->get_results($wpdb->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'track_company_attr'",
        DB_NAME, $tbl_behavior
    ));
    
    if (empty($column_exists)) {
        return @$wpdb->query($wpdb->prepare("ALTER TABLE %i ADD COLUMN `track_company_attr` TEXT NULL DEFAULT NULL AFTER `track_user_attr`", $tbl_behavior));
    }
    
    return true;