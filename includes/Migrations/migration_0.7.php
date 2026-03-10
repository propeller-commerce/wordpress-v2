<?php
    defined( 'ABSPATH' ) || exit;

    $res = [];
    
    // Check if secondary_sort_column column exists before adding
    $secondary_sort_column_exists = $wpdb->get_results($wpdb->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'secondary_sort_column'",
        DB_NAME, $tbl_behavior
    ));
    
    if (empty($secondary_sort_column_exists)) {
        $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
            ADD COLUMN `secondary_sort_column` VARCHAR(30) NULL DEFAULT 'NAME' AFTER `default_sort_column`", $tbl_behavior));
    }

    foreach ($res as $r) {
        if ($r === false) 
            return false;
    }

    return true;