<?php
defined('ABSPATH') || exit;

    $res = [];
    
    // Check if use_sso column exists before adding
    $use_sso_exists = $wpdb->get_results($wpdb->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'use_sso'",
        DB_NAME, $tbl_behavior
    ));
    
    if (empty($use_sso_exists)) {
        $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
            ADD COLUMN `use_sso` TINYINT(1) NULL DEFAULT 0 AFTER `default_offset`", $tbl_behavior));
    }

    // Check if sso_provider column exists before adding
    $sso_provider_exists = $wpdb->get_results($wpdb->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'sso_provider'",
        DB_NAME, $tbl_behavior
    ));
    
    if (empty($sso_provider_exists)) {
        $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
            ADD COLUMN `sso_provider` VARCHAR(50) NULL DEFAULT NULL AFTER `use_sso`", $tbl_behavior));
    }

    // Check if sso_data column exists before adding
    $sso_data_exists = $wpdb->get_results($wpdb->prepare(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'sso_data'",
        DB_NAME, $tbl_behavior
    ));
    
    if (empty($sso_data_exists)) {
        $res[] = @$wpdb->query($wpdb->prepare("ALTER TABLE %i 
            ADD COLUMN `sso_data` TEXT NULL DEFAULT NULL AFTER `sso_provider`", $tbl_behavior));
    }

    foreach ($res as $r) {
        if ($r === false) 
            return false;
    }

    return true;