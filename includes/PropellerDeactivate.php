<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

class PropellerDeactivate {
    public static function deactivate() {
        
    }

    public static function uninstall() {
        global $table_prefix, $wpdb;

        $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %s', $table_prefix . PROPELLER_SETTINGS_TABLE));
        $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %s', $table_prefix . PROPELLER_PAGES_TABLE));
        $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %s', $table_prefix . PROPELLER_BEHAVIOR_TABLE));
        $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %s', $table_prefix . PROPELLER_SLUGS_TABLE));
    }
}