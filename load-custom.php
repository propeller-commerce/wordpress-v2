<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Propeller Custom Autoloader
// Note: This autoloader is only used when using the custom folder from within propeller-ecommerce plugin.
// Note: If the custom folder is used from outside this plugin, eg. in mu-plugins, then this is ignored.
// Date: 02-07-2023

spl_autoload_register( function ( $className ) {
	/**
	 * Initial path and vars.
	 */
	$ds  = DIRECTORY_SEPARATOR;
	$dir = dirname( __FILE__ ) . $ds . 'custom';

	/**
	 * Generate the direct path to the class
	 */
	$className = str_replace( 'Propeller\\Custom\\', '', $className );
	$className = str_replace( '\\', $ds, $className );

	/**
	 * Load the class if all fine.
	 */
	$file = "{$dir}{$ds}{$className}.php";
	if ( is_readable( $file ) ) {
		require_once $file;
	}
} );
