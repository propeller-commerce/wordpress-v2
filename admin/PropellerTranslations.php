<?php

namespace Propeller\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

use DirectoryIterator;
use Exception;
use Gettext\Generator\MoGenerator;
use Gettext\Generator\PoGenerator;
use Gettext\Loader\PoLoader;
use Gettext\Scanner\PhpScanner;
use Gettext\Translations;
use Propeller\FileHandler;
use Propeller\PropellerHelper;
use Propeller\PropellerUtils;
use stdClass;
use ZipArchive;

class PropellerTranslations
{
	protected $loader;
	protected $mo_generator;
	protected $po_generator;

	protected $translations_path;
	protected $templates = [];
	protected $translations = [];
	protected $generated_translations = [];
	protected $headers = [];

	public function __construct()
	{
		$this->loader       = new PoLoader();
		$this->mo_generator = new MoGenerator();
		$this->po_generator = new PoGenerator();

		$this->translations_path = $this->get_translations_path();

		$this->get_files();
		$this->build_headers();
	}

	public function get_translations_path()
	{
		$translations_path = PROPELLER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'languages';
		if (defined('PROPELLER_PLUGIN_EXTEND_DIR')) {

			$extends_translations_path = PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'languages';
			if (!PropellerHelper::wp_filesys()->is_dir($extends_translations_path)) {
				@PropellerHelper::wp_filesys()->mkdir($extends_translations_path);
			}

			$translations_path = $extends_translations_path;
		}

		return $translations_path;
	}

	public function get_temp_dir()
	{
		$uploads_dir = wp_upload_dir();

		$propel_backup_dir = $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . 'propeller-ecommerce-v2';

		if (! is_dir($propel_backup_dir)) {
			if (! wp_mkdir_p($propel_backup_dir)) {
				propel_log("$propel_backup_dir cannot be created \r\n");
			}
		}

		return $propel_backup_dir;
	}

	public function get_lang_bkp_dir()
	{
		$propel_backup_dir = $this->get_temp_dir();

		if (! is_dir($propel_backup_dir)) {
			if (! wp_mkdir_p($propel_backup_dir)) {
				propel_log("$propel_backup_dir cannot be created \r\n");
			}
		}

		$langs_backup_dir = $propel_backup_dir . DIRECTORY_SEPARATOR . 'languages';

		if (! is_dir($langs_backup_dir)) {
			if (! wp_mkdir_p($langs_backup_dir)) {
				propel_log("$langs_backup_dir cannot be created \r\n");
			}
		}

		return $langs_backup_dir;
	}

	public function backup_languages()
	{
		$backups_dir = $this->get_lang_bkp_dir();

		if (! $this->translations_path) {
			$this->translations_path = $this->get_translations_path();
		}

		$now = gmdate('Y-m-d+H_i_s');

		$backup_dir = $backups_dir . DIRECTORY_SEPARATOR . $now;

		if (! is_dir($backup_dir)) {
			if (wp_mkdir_p($backup_dir)) {
				try {
					FileHandler::copy_dir($this->translations_path, $backup_dir);
				} catch (Exception $ex) {
					propel_log("Error copying translations to backup: " . $ex->getMessage() . "\r\n");
					throw $ex;
				}
			} else {
				$error_msg = "$backup_dir cannot be created";
				propel_log($error_msg . "\r\n");
				throw new Exception(esc_html($error_msg));
			}
		}

		$this->purge_backups();
	}

	public function purge_backups()
	{
		$bkps = $this->get_backups();

		if (count($bkps) > 10) {
			$oldest_backup = $bkps[0];

			try {
				// Use FileHandler::rmdir for recursive directory deletion
				FileHandler::rmdir($oldest_backup);

				// Verify deletion was successful
				if (PropellerHelper::wp_filesys()->is_dir($oldest_backup)) {
					propel_log('Cannot delete backup directory: ' . $oldest_backup . " - Directory still exists after deletion attempt\r\n");
				}
			} catch (Exception $ex) {
				propel_log('Error deleting backup directory: ' . $oldest_backup . ' - ' . $ex->getMessage() . "\r\n");
			}
		}
	}

	public function restore_translations()
	{
		$data = PropellerUtils::sanitize($_POST);

		if (isset($data['nonce']) && !wp_verify_nonce(sanitize_key($data['nonce']), 'propel-ajax-nonce')) {
			die(wp_json_encode([
				'success' => false,
				'message' => __('Security check failed', 'propeller-ecommerce-v2')
			]));
		}

		if (empty($data['backup_date'])) {
			die(wp_json_encode([
				'success' => false,
				'message' => __('Please select a backup', 'propeller-ecommerce-v2')
			]));
		}

		$success = true;

		if (current_user_can('manage_options')) {
			try {
				if (! $this->translations_path) {
					$this->translations_path = $this->get_translations_path();
				}

				$backups_dir = $this->get_lang_bkp_dir();

				$selected_bkp = $backups_dir . DIRECTORY_SEPARATOR . wp_unslash($data['backup_date']);

				FileHandler::copy_dir($selected_bkp, $this->translations_path);

				$msg = __('Translations restored', 'propeller-ecommerce-v2') . ': ' . str_replace('_', ':', wp_unslash($data['backup_date']));
			} catch (Exception $ex) {
				$success = false;
				$msg     = $ex->getMessage();
			}
		} else {
			$success = false;
			$msg     = __('Not enought rights to save translations', 'propeller-ecommerce-v2');
		}

		die(wp_json_encode([
			'success' => $success,
			'message' => $msg
		]));
	}

	public function get_backups()
	{
		$backups_dir = $this->get_lang_bkp_dir();

		$dirs = FileHandler::scan_dir($backups_dir);

		$backup_dirs = isset($dirs['dirs']) ? $dirs['dirs'] : [];

		// Sort backup directories by name (which includes timestamp) to get oldest first
		sort($backup_dirs);

		return $backup_dirs;
	}

	public function load_translations_backups()
	{
		$this->purge_backups();

		$success = true;
		$msg     = '';
		$opts    = [];

		try {
			$backups_dir = $this->get_lang_bkp_dir();

			$backups = FileHandler::scan_dir($backups_dir)['dirs'];

			if (count($backups)) {
				$opts[] = '<option value="">' . __('Restore translations', 'propeller-ecommerce-v2') . '</option>';

				foreach ($backups as $bkp) {
					$opts[] = '<option value="' . wp_basename($bkp) . '">' . str_replace('+', ' ', str_replace('_', ':', wp_basename($bkp))) . '</option>';
				}
			}
		} catch (Exception $ex) {
			$success = false;
			$msg     = $ex->getMessage();
		}

		die(wp_json_encode(['success' => $success, 'message' => $msg, 'options' => implode('', $opts)]));
	}

	public function get_files()
	{
		$files = array_diff(scandir($this->translations_path), array('.', '..'));

		if (count($files)) {
			foreach ($files as $file) {
				$info = pathinfo($file);

				// Check if extension key exists to avoid undefined array key error
				if (isset($info['extension'])) {
					if ($info['extension'] == 'pot') {
						$this->templates[] = $this->translations_path . DIRECTORY_SEPARATOR . $file;
					} else if ($info['extension'] == 'po') {
						$this->translations[] = $this->translations_path . DIRECTORY_SEPARATOR . $file;
					} else if ($info['extension'] == 'mo') {
						$this->generated_translations[] = $this->translations_path . DIRECTORY_SEPARATOR . $file;
					}
				}
			}
		}
	}

	public function get_templates()
	{
		return $this->templates;
	}

	public function get_translations()
	{
		return $this->translations;
	}

	public function load_translation($file)
	{
		$translation = $this->get_translations_file($file);

		if ($translation->position > -1 && count($this->translations)) {
			try {
				return $this->loader->loadFile($this->translations[$translation->position]);
			} catch (Exception $ex) {
				propel_log($ex->getMessage());
			}
		}

		return [];
	}

	public function get_translations_file($file)
	{
		$res           = new stdClass();
		$res->position = -1;
		$res->file     = '';

		if (count($this->translations)) {
			foreach ($this->translations as $translation_file) {
				if (basename($translation_file) == $file) {
					$res->position++;
					$res->file = $translation_file;

					break;
				}

				$res->position++;
			}
		}

		return $res;
	}

	public function get_available_languages()
	{
		$locales = include PROPELLER_PLUGIN_DIR . '/includes/Locales.php';

		return $locales;
	}

	public function save_translations()
	{
		$_data = PropellerUtils::sanitize($_POST);

		if (isset($_data['nonce']) && !wp_verify_nonce(sanitize_key($_data['nonce']), 'propel-ajax-nonce')) {
			die(wp_json_encode([
				'success' => false,
				'message' => __('Security check failed', 'propeller-ecommerce-v2')
			]));
		}

		$msg     = __('Translations saved', 'propeller-ecommerce-v2');
		$success = true;

		if (current_user_can('manage_options')) {
			if (empty($_data['po_file'])) {
				die(wp_json_encode([
					'success' => false,
					'message' => __('Please select a translations file', 'propeller-ecommerce-v2')
				]));
			}

			$this->backup_languages();

			$translations      = $this->load_translation(wp_unslash($_data['po_file']));
			$translations_file = $this->get_translations_file(wp_unslash($_data['po_file']));

			for ($i = 0; $i <= count($_data['original']); $i++) {
				try {
					if (empty($_data['original'][$i])) {
						continue;
					}

					$_data['original'][$i] = htmlspecialchars_decode($_data['original'][$i]);
					$_data['original'][$i] = stripslashes($_data['original'][$i]);

					$translation = $translations->find(null, $_data['original'][$i]);

					if (!$translation)
						$translation = $translations->find(PropellerValuesets::VALUESETS_CONTEXT, $_data['original'][$i]);

					if ($translation) {
						$_data['translation'][$i] = htmlspecialchars_decode($_data['translation'][$i]);
						$_data['translation'][$i] = wp_unslash($_data['translation'][$i]);
						$translation->translate($_data['translation'][$i]);
					} else {
						propel_log("- Original string for " . $_data['original'][$i] . " not found\r\n");
					}
				} catch (Exception $ex) {
					$success = false;
					$msg     = $ex->getMessage();
					propel_log($ex->getMessage());
				}
			}

			try {
				$this->apply_headers($translations);

				$this->po_generator->generateFile($translations, $translations_file->file);
			} catch (Exception $ex) {
				$success = false;
				$msg     = $ex->getMessage();
				propel_log($ex->getMessage());
			}
		} else {
			$success = false;
			$msg     = __('Not enought rights to save translations', 'propeller-ecommerce-v2');
		}

		die(wp_json_encode(['success' => $success, 'message' => $msg]));
	}

	public function scan_translations()
	{
		$success = true;
		$msg     = '';
		$count = 0;

		if (isset($_POST['nonce']) && !wp_verify_nonce(sanitize_key($_POST['nonce']), 'propel-ajax-nonce')) {
			die(wp_json_encode([
				'success' => false,
				'message' => __('Security check failed', 'propeller-ecommerce-v2')
			]));
		}

		if (current_user_can('manage_options')) {

			wp_raise_memory_limit('admin');

			try {
				$this->backup_languages();
				$phpScanner = new PhpScanner(
					Translations::create(PROPELLER_PLUGIN_NAME)
				);

				$phpScanner->setDefaultDomain(PROPELLER_PLUGIN_NAME);

				$phpScanner->extractCommentsStartingWith('__(');

				$files = [];

				$this->get_all_directory_and_files(PROPELLER_PLUGIN_DIR, $files);

				if (defined('PROPELLER_PLUGIN_EXTEND_DIR'))
					$this->get_all_directory_and_files(PROPELLER_PLUGIN_EXTEND_DIR, $files);

				//Scan files
				foreach ($files as $file) {
					$phpScanner->scanFile($file);
				}

				foreach ($phpScanner->getTranslations() as $domain => $translations) {
					$count = count($translations);

					$this->apply_headers($translations);

					$this->po_generator->generateFile($translations, $this->translations_path . DIRECTORY_SEPARATOR . $domain . '.pot');
				}

				$msg = __('New translations template file created', 'propeller-ecommerce-v2');
			} catch (Exception $ex) {
				$success = false;
				$msg     = $ex->getMessage();
				propel_log($ex);
			}
		} else {
			$success = false;
			$msg     = __('Not enought rights to scan for new translations', 'propeller-ecommerce-v2');
		}

		die(wp_json_encode(['success' => $success, 'total' => $count, 'message' => $msg]));
	}

	public function create_translations_file()
	{
		$data = PropellerUtils::sanitize($_REQUEST);

		if (isset($data['nonce']) && !wp_verify_nonce(sanitize_key($data['nonce']), 'propel-ajax-nonce')) {
			die(wp_json_encode([
				'success' => false,
				'message' => __('Security check failed', 'propeller-ecommerce-v2')
			]));
		}

		$success = true;
		$msg     = '';

		$localeName = '';

		if (current_user_can('manage_options')) {

			$this->backup_languages();

			try {
				$translations = $this->loader->loadFile($this->templates[0]);

				$new_translations = $translations;

				if (isset($data['merge']) && ! empty($data['merge'])) {
					$old_translations = $this->load_translation(wp_unslash($data['merge']));

					$new_translations = $old_translations->mergeWith($translations);
				}

				$this->apply_headers($new_translations);

				$localeName = wp_unslash($data['locale']);

				$this->po_generator->generateFile($new_translations, $this->translations_path . DIRECTORY_SEPARATOR . PROPELLER_PLUGIN_NAME . '-' . $localeName . '.po');

				$msg = __('New translations file created', 'propeller-ecommerce-v2') . ': ' . PROPELLER_PLUGIN_NAME . '-' . $localeName . '.po';
			} catch (Exception $ex) {
				$success = false;
				$msg     = $ex->getMessage();
				propel_log($ex->getMessage());
			}
		} else {
			$success = false;
			$msg     = __('Not enought rights to create translations file', 'propeller-ecommerce-v2');
		}

		die(wp_json_encode([
			'success' => $success,
			'file'    => PROPELLER_PLUGIN_NAME . '-' . $localeName . '.po',
			'tab'     => sanitize_text_field($data['tab']),
			'page'    => sanitize_text_field($data['page']),
			'action'  => 'open_translation',
			'message' => $msg
		]));
	}

	public function generate_translations()
	{
		if (isset($_POST['nonce']) && !wp_verify_nonce(sanitize_key($_POST['nonce']), 'propel-ajax-nonce')) {
			die(wp_json_encode([
				'success' => false,
				'message' => __('Security check failed', 'propeller-ecommerce-v2')
			]));
		}

		$success = true;
		$msg     = '';

		if (current_user_can('manage_options')) {
			$this->backup_languages();

			try {
				$_data = $_POST;

				$translations      = $this->load_translation(sanitize_text_field($_data['po_file']));
				$translations_file = $this->get_translations_file(sanitize_text_field($_data['po_file']));

				$path_parts = pathinfo($translations_file->file);

				// Check if filename key exists to avoid undefined array key error
				$filename = isset($path_parts['filename']) ? $path_parts['filename'] : 'translation';
				$mo_file_path = $this->translations_path . DIRECTORY_SEPARATOR . sanitize_text_field($filename) . '.mo';

				$this->mo_generator->generateFile($translations, $mo_file_path);

				// Force WordPress to reload the text domain
				unload_textdomain('propeller-ecommerce-v2');

				// Reload the text domain from the correct path
				if (defined('PROPELLER_PLUGIN_EXTEND_DIR')) {
					$plugin_rel_path = dirname(plugin_basename(PROPELLER_PLUGIN_EXTEND_DIR)) . DIRECTORY_SEPARATOR . 'propeller-custom' . DIRECTORY_SEPARATOR . 'languages';
					load_muplugin_textdomain('propeller-ecommerce-v2', $plugin_rel_path);
				} 
				// else {
				// 	$plugin_rel_path = dirname(plugin_basename(__FILE__)) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
				// 	load_plugin_textdomain('propeller-ecommerce-v2', false, $plugin_rel_path);
				// }

				$msg = __('New translations are generated', 'propeller-ecommerce-v2');
			} catch (Exception $ex) {
				$success = false;
				$msg     = $ex->getMessage();
				propel_log($ex->getMessage());
			}
		} else {
			$success = false;
			$msg     = __('Not enought rights to generate translations', 'propeller-ecommerce-v2');
		}

		die(wp_json_encode([
			'success' => $success,
			'message' => $msg
		]));
	}

	public function build_headers()
	{
		$this->headers["Project-Id-Version"]        = "Propeller E-Commerce " . PROPELLER_VERSION;
		$this->headers["Report-Msgid-Bugs-To"]      = "https://wordpress.org/support/plugin/propeller-ecommerce-v2";
		$this->headers["Last-Translator"]           = "Propeller <info@propel.us>";
		$this->headers["Language-Team"]             = "NL <info@propel.us>";
		$this->headers["MIME-Version"]              = "1.0";
		$this->headers["Content-Type"]              = "text/plain; charset=UTF-8";
		$this->headers["Content-Transfer-Encoding"] = "8bit";
		$this->headers["POT-Creation-Date"]         = "2022-04-19T10:53:53+00:00";
		$this->headers["PO-Revision-Date"]          = "2022-04-19T10:53:53+00:00";
		$this->headers["X-Generator"]               = "php-gettext";
		$this->headers["X-Domain"]                  = PROPELLER_PLUGIN_NAME;
	}

	public function apply_headers(&$translations)
	{
		foreach ($this->headers as $key => $val) {
			$translations->getHeaders()->set($key, $val);
		}
	}

	private function get_all_directory_and_files($dir, &$files)
	{
		$ignore = [
			'.gitignore',
			'.git',
			'vendor',
			'composer.json',
			'composer.lock',
			'readme.txt',
			'bootstrap.min.css',
			'bootstrap.min.js',
			'jquery.min.js',
		];

		$dh = new DirectoryIterator($dir);

		foreach ($dh as $item) {
			if (! $item->isDot()) {
				if (in_array($item->getBasename(), $ignore)) {
					continue;
				}

				if ($item->isDir()) {
					$this->get_all_directory_and_files("$dir" . DIRECTORY_SEPARATOR . "$item", $files);
				} else {
					$files[] = $dir . DIRECTORY_SEPARATOR . $item->getFilename();
				}
			}
		}
	}
}
