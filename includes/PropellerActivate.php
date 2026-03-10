<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\PageController;

class PropellerActivate
{
    public static function activate()
    {
        global $table_prefix, $wpdb;

        $tbl_settings   = $table_prefix . PROPELLER_SETTINGS_TABLE;
        $tbl_pages      = $table_prefix . PROPELLER_PAGES_TABLE;
        $tbl_behavior   = $table_prefix . PROPELLER_BEHAVIOR_TABLE;
        $tbl_slugs      = $table_prefix . PROPELLER_SLUGS_TABLE;

        $charset_collate = $wpdb->get_charset_collate();

        // Check to see if the table exists already, if not, then create it
        if ($wpdb->get_var("SHOW TABLES LIKE '$tbl_settings'") != $tbl_settings) {

            $sql = "CREATE TABLE $tbl_settings (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    api_url VARCHAR(200) NOT NULL,
                    api_key VARCHAR(200) NOT NULL,
                    order_api_key VARCHAR(200) NOT NULL,
                    anonymous_user VARCHAR(200) NOT NULL,
                    catalog_root VARCHAR(200) NOT NULL,
                    site_id VARCHAR(200) NOT NULL,
                    default_locale VARCHAR(10) NOT NULL,
                    cc_email VARCHAR(100) DEFAULT NULL,
                    bcc_email VARCHAR(100) DEFAULT NULL,
                    currency VARCHAR(100) DEFAULT 'EUR',
                    UNIQUE KEY id (id)
            ) $charset_collate;";


            require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '$tbl_pages'") != $tbl_pages) {
            $sql = "CREATE TABLE $tbl_pages (
                        id INT(11) NOT NULL AUTO_INCREMENT,
                        page_name VARCHAR(200) NOT NULL,
                        page_slug VARCHAR(200) NOT NULL,
                        page_sluggable TINYINT(1) DEFAULT 0,
                        page_shortcode VARCHAR(200) NOT NULL,
                        page_type VARCHAR(200) NOT NULL,
                        is_my_account_page TINYINT(1) DEFAULT 0,
                        account_page_is_parent TINYINT(1) DEFAULT 0,
                        UNIQUE KEY id (id)
                ) $charset_collate;";

            require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
            dbDelta($sql);

            // insert default pages in plugin
            PageController::insert_default_pages();

            // insert default pages in WP
            PageController::create_pages();

            // Flush r/w rules due to custom rw logic from the plugin
            flush_rewrite_rules();
        }

        if ($wpdb->get_var("SHOW TABLES LIKE '$tbl_behavior'") != $tbl_behavior) {

            $sql = "CREATE TABLE $tbl_behavior (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    wordpress_session TINYINT(1) DEFAULT 0,
                    closed_portal TINYINT(1) DEFAULT 0,
                    semiclosed_portal TINYINT(1) DEFAULT 0,
                    excluded_pages TEXT DEFAULT NULL,
                    track_user_attr TEXT DEFAULT NULL,
                    track_company_attr TEXT DEFAULT NULL,
                    track_product_attr TEXT DEFAULT NULL,
                    track_category_attr TEXT DEFAULT NULL,
                    reload_filters TINYINT(1) DEFAULT 0,
                    use_recaptcha TINYINT(1) DEFAULT 0,
                    recaptcha_site_key VARCHAR(200) DEFAULT NULL,
                    recaptcha_secret_key VARCHAR(200) DEFAULT NULL,
                    recaptcha_min_score FLOAT DEFAULT NULL,
                    register_auto_login TINYINT(1) DEFAULT 1,
                    assets_type TINYINT(1) DEFAULT 0,
                    stock_check TINYINT(1) DEFAULT 0,
                    load_specifications TINYINT(1) DEFAULT 1,
                    ids_in_urls TINYINT(1) DEFAULT 1,
                    partial_delivery TINYINT(1) DEFAULT 0,
                    selectable_carriers TINYINT(1) DEFAULT 0,
                    show_actioncode TINYINT(1) DEFAULT 1,
                    show_order_type TINYINT(1) DEFAULT 1,
                    use_datepicker TINYINT(1) DEFAULT 0,
                    edit_addresses TINYINT(1) DEFAULT 0,
                    lang_for_attrs TINYINT(1) DEFAULT 1,
                    lazyload_images TINYINT(1) DEFAULT 0,
                    anonymous_orders TINYINT(1) DEFAULT 0,
                    pdp_new_window TINYINT(1) DEFAULT 0,
                    icp_country VARCHAR(10) DEFAULT 'NL',
                    onacc_payments TEXT DEFAULT NULL,
                    default_incl_vat TINYINT(1) NULL DEFAULT 0,
                    default_sort_column VARCHAR(20) NOT NULL DEFAULT 'CATEGORY_ORDER',
                    default_sort_direction VARCHAR(10) NOT NULL DEFAULT 'ASC',
                    default_offset INT(11) NOT NULL DEFAULT 12,
                    use_sso TINYINT(1) NULL DEFAULT 0,
                    sso_provider VARCHAR(50) NULL DEFAULT NULL,
                    sso_data TEXT NULL DEFAULT NULL,
                    pac_add_contacts TINYINT(1) NULL DEFAULT 0,
                    use_ga4 TINYINT(1) NULL DEFAULT 0, 
                    ga4_tracking TINYINT(1) NULL DEFAULT 0, 
                    ga4_key VARCHAR(50) NULL DEFAULT NULL,
                    gtm_key VARCHAR(50) NULL DEFAULT NULL,
                    use_cxml TINYINT(1) NULL DEFAULT 0, 
                    cxml_contact_id TEXT NULL DEFAULT NULL,
                    UNIQUE KEY id (id)
            ) $charset_collate;";

            require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
            dbDelta($sql);

            $wpdb->insert($tbl_behavior, array(
                'wordpress_session' => 0,
                'closed_portal' => 0,
                'semiclosed_portal' => 0,
                'excluded_pages' => '',
                'track_user_attr' => '',
                'track_company_attr' => '',
                'track_product_attr' => '',
                'reload_filters' => 0,
                'assets_type' => 1,
                'onacc_payments' => 'REKENING',
                'default_incl_vat' => 0,
                'default_sort_column' => 'CATEGORY_ORDER',
                'default_sort_direction' => 'ASC',
                'default_offset' => 12,
            ));
        }

        // if($wpdb->get_var("SHOW TABLES LIKE '$tbl_slugs'") != $tbl_slugs) {

        //     $sql = "CREATE TABLE `$tbl_slugs` (
        //             `id` INT NOT NULL AUTO_INCREMENT,
        //             `page_id` INT NOT NULL,
        //             `language` VARCHAR(45) NOT NULL,
        //             `slug` VARCHAR(255) NOT NULL,
        //             PRIMARY KEY (`id`))
        //     ) $charset_collate;";

        //     require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
        //     dbDelta($sql);
        // }
    }
}
