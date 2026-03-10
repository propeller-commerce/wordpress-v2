<?php
    defined( 'ABSPATH' ) || exit;

    $res = [];
    
    // Check if use_cxml column exists before adding
    $use_cxml_exists = $wpdb->get_results($wpdb->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'use_cxml'",
        DB_NAME, $tbl_behavior
    ));
    
    if (empty($use_cxml_exists)) {
        $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
            ADD COLUMN `use_cxml` TINYINT(1) NULL DEFAULT 0 AFTER `gtm_key`", $tbl_behavior));
    }

    // Check if cxml_contact_id column exists before adding
    $cxml_contact_id_exists = $wpdb->get_results($wpdb->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'cxml_contact_id'",
        DB_NAME, $tbl_behavior
    ));
    
    if (empty($cxml_contact_id_exists)) {
        $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
            ADD COLUMN `cxml_contact_id` TEXT NULL DEFAULT NULL AFTER `use_cxml`", $tbl_behavior));
    }

    foreach ($res as $r) {
        if ($r === false) 
            return false;
    }

    return true;