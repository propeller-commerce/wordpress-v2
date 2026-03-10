<?php
/**
* Plugin Name: Propeller Ecommerce v2
* Plugin URI: https://propeller-commerce.com/product/wordpress/
* Description: Propeller is an API-first B2B commerce platform that changes the way your team and customers work together.
* Version: 1.0.0
* Author: Propeller Ecommerce
* Author URI: http://propeller-commerce.com
* Text Domain: propeller-ecommerce-v2
* Domain Path: /public/languages
* License: GPLv3
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'ABSPATH' ) || exit;

require plugin_dir_path(__FILE__) . '/vendor/autoload.php';

require_once(plugin_dir_path(__FILE__) . '/upgrade.php');

global $propel_active, 
	   $propellerSluggablePages,
	   $propel_behavior,
	   $propel_slugs,
	   $propel_rw,
	   $propel;

$active_plugins = (array) get_option('active_plugins', array());
$propel_active = !empty($active_plugins) && in_array(basename(__DIR__) . '/propeller-ecommerce-v2.php', $active_plugins);
	
require_once(plugin_dir_path(__FILE__) . '/constants.php');

function activate_propeller() {
	Propeller\PropellerActivate::activate();	

	if (!wp_next_scheduled('propel_sitemap_cron'))
        wp_schedule_event(strtotime('00:00:00'), 'daily', 'propel_sitemap_cron');

	do_action('propel_after_activation');
}

function deactivate_propeller() {
	Propeller\PropellerDeactivate::deactivate();

	wp_unschedule_event(wp_next_scheduled('propel_sitemap_cron'), 'propel_sitemap_cron');
}

function uninstall_propeller() {
	Propeller\PropellerDeactivate::uninstall();
}

register_activation_hook(__FILE__, 'activate_propeller');
register_deactivation_hook(__FILE__, 'deactivate_propeller');
register_uninstall_hook(__FILE__, 'uninstall_propeller');

if ($propel_active) { 
	require_once(plugin_dir_path(__FILE__) . '/rewrite-rules.php');
	require_once(plugin_dir_path(__FILE__) . '/ajax.php');
	require_once(plugin_dir_path(__FILE__) . '/filters.php');
	require_once(plugin_dir_path(__FILE__) . '/propeller-cron.php');
}

function run_propeller() {
	global $propel_active;
	
	if ($propel_active) {
		$propeller = new Propeller\Propeller();
	
		$propeller->run();
	}
}

run_propeller();
