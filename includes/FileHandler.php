<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Exception;

class FileHandler
{
	public static function copy_dir($src, $dst)
	{
		try {
			// Create destination directory if it doesn't exist
			if (!is_dir($dst)) {
				if (!wp_mkdir_p($dst)) {
					throw new Exception("Cannot create destination directory: $dst");
				}
			}

			$dir = opendir($src);

			while (false !== ($file = readdir($dir))) {
				if (($file != '.') && ($file != '..')) {
					if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
						self::copy_dir($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
					} else {
						PropellerHelper::wp_filesys()->copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
					}
				}
			}

			closedir($dir);
		} catch (Exception $ex) {
			propel_log("Error copying $src to $dst: " . $ex->getMessage() . "\r\n");
			throw $ex; // Re-throw to allow calling code to handle the error
		}
	}

	public static function scan_dir($dir, &$results = [])
	{
		$files = scandir($dir);

		foreach ($files as $key => $value) {
			$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
			if (!is_dir($path)) {
				$results['files'][] = $path;
			} else if ($value != "." && $value != "..") {
				self::scan_dir($path, $results);
				$results['dirs'][] = $path;
			}
		}

		return $results;
	}

	public static function rmdir($dir)
	{
		if (is_dir($dir)) {
			$objects = scandir($dir);

			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
						self::rmdir($dir . DIRECTORY_SEPARATOR . $object);
					else
						PropellerHelper::wp_filesys()->delete($dir . DIRECTORY_SEPARATOR . $object);
				}
			}

			PropellerHelper::wp_filesys()->rmdir($dir);
		}
	}
}
