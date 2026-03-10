<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

if ( defined( 'PROPELLER_PLUGIN_EXTEND_DIR' ) ) {
	if (file_exists( PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-filters.php' ) )
		require_once( PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-filters.php' );

	if (file_exists( PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-shortcodes.php' ) )
		require_once( PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-shortcodes.php' );
}