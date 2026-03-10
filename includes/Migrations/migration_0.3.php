<?php
    defined( 'ABSPATH' ) || exit;

    // Check if pac_add_contacts column exists before adding
    $column_exists = $wpdb->get_results($wpdb->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'pac_add_contacts'",
        DB_NAME, $tbl_behavior
    ));
    
    if (empty($column_exists)) {
        return @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
            ADD COLUMN `pac_add_contacts` TINYINT(1) NULL DEFAULT 0 AFTER `sso_data`", $tbl_behavior));
    }
    
    return true;