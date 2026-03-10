<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

class PropellerMigrate {
	private $option_name;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->option_name = PROPELLER_DB_VERSION_OPTION;
	}

	/**
	 * Updates version
	 *
	 * @param $version
	 *
	 * @return void
	 */
	public function update_version( $version ) {
		update_option( $this->option_name, $version );
	}

	/**
	 * The latest database version, when last migrations were ran.
	 * @return false|mixed|null
	 */
	public function get_db_version() {
		return get_option( $this->option_name );
	}

	/**
	 * The latest file system version, up to date version with the migrations on the file system.
	 * @return float|string
	 */
	public function get_fs_version() {
		return defined( 'PROPELLER_DB_VERSION' ) && PROPELLER_DB_VERSION ? PROPELLER_DB_VERSION : 0.0;
	}

	public function get_last_migration_version() {
		$path  = PROPELLER_PLUGIN_DIR . 'includes' . DIRECTORY_SEPARATOR . 'Migrations' . DIRECTORY_SEPARATOR . '*.php';
		$files = glob( $path );

		if ( sizeof($files) && preg_match( '/migration_(.*?)\.php/', $files[count($files) - 1], $match ) ) {
			return $match[1];
		}

		return null;
	}

	/**
	 * Run the migrations
	 * @return void
	 */
	public function run() {

		$db_version = (float) $this->get_db_version();
		$migration_version = (float) $this->get_last_migration_version();
		
		if ($migration_version > $db_version) {
			$this->migrate( $db_version );
		} 
	}

	/**
	 * Migrates the database
	 *
	 * @param $dbVersion
	 * @param $fsVersion
	 *
	 * @return void
	 */
	private function migrate( $dbVersion ) {
		global $table_prefix, $wpdb;

		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

		$tbl_settings = $table_prefix . PROPELLER_SETTINGS_TABLE;
		$tbl_pages    = $table_prefix . PROPELLER_PAGES_TABLE;
		$tbl_behavior = $table_prefix . PROPELLER_BEHAVIOR_TABLE;
		$tbl_slugs 	  = $table_prefix . PROPELLER_SLUGS_TABLE;

		$charset_collate = $wpdb->get_charset_collate();

		$path  = PROPELLER_PLUGIN_DIR . 'includes' . DIRECTORY_SEPARATOR . 'Migrations' . DIRECTORY_SEPARATOR . '*.php';
		$files = glob( $path );

		foreach ( $files as $filePath ) {
			if ( preg_match( '/migration_(.*?)\.php/', $filePath, $match ) ) {
				$fileVersion   = (float) $match[1];
				$shouldMigrate = $fileVersion >= $dbVersion;

				$wpdb->hide_errors();

				if ( $shouldMigrate ) {
					$result = include $filePath;

					if ( $result )
						$this->update_version( $fileVersion );
					else {
						if ($wpdb->last_error !== '' && strpos($wpdb->last_error, 'Duplicate column') !== false)
							$this->update_version( $fileVersion );
					}
				}
			}
		}
	}
}