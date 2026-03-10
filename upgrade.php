<?php

    if ( ! defined( 'ABSPATH' ) ) exit;

    use Propeller\PropellerHelper;

    // function load_propel_textdomain() {
    //     $plugin_rel_path = dirname(plugin_basename(__FILE__)) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
    //     $textdomain_loaded = load_plugin_textdomain('propeller-ecommerce-v2', false, $plugin_rel_path);
    // }

    // if (!defined( 'PROPELLER_PLUGIN_EXTEND_DIR' ))
    //     add_action('plugins_loaded', 'load_propel_textdomain');

    function set_propel_locale() {
        $locale = get_locale();
        // $locale = 'nl_NL';
        if (strpos($locale, '_')) 
            $locale = explode('_', $locale)[0];
        
        define('PROPELLER_LANG', strtoupper($locale));
    }

    function get_propel_languages() {
        $plugin_langs_path = plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
        
        if ( defined( 'PROPELLER_PLUGIN_EXTEND_DIR' ) && is_dir(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'languages')) {
			$plugin_langs_path = PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
        }

        $langs = get_available_languages($plugin_langs_path);

        $available_langs = [ PROPELLER_DEFAULT_LOCALE ];

        foreach ($langs as $mo) {
            $chunks = explode('-', $mo);

            if (!in_array($chunks[count($chunks) - 1], $available_langs))
                $available_langs[] = $chunks[count($chunks) - 1];
        }

        return $available_langs;
    }

    function propel_get_countries($file_name = 'Countries.php') {
        if ($file_name != 'Countries.php') {
            $countries_file = include(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . $file_name);

            if ( defined( 'PROPELLER_PLUGIN_EXTEND_DIR' ) && is_file(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . $file_name)) {
                $countries_file = include(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . $file_name);
            }

            // Ensure we return an array
            if (!is_array($countries_file)) {
                $countries_file = [];
            }

            return $countries_file;
        }

        $countries_file = include(plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . $file_name);

        if ( defined( 'PROPELLER_PLUGIN_EXTEND_DIR' ) && is_file(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . $file_name)) {
			$countries_file = include(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . $file_name);
        }

        // Ensure we have an array
        if (!is_array($countries_file)) {
            $countries_file = [];
        }

        if (class_exists('Propeller\Includes\Controller\BaseController')) {
            $baseController = new \Propeller\Includes\Controller\BaseController();

            $countries = $baseController->get_countries();

            if (isset($countries->items) && is_array($countries->items) && count($countries->items) > 0) {
                // empty the countries array from the file
                $countries_file = [];

                foreach ($countries->items as $country) {
                    if ($country->hide)
                        continue;
                    
                    $countries_file[$country->value] = $country->description;
                }
            }
        }

        return $countries_file;
    }

    add_filter('trp_disable_error_manager','__return_true');

    function propel_log($msg) {
        $date = '[' . gmdate('Y-m-d H:i:s') . ']';

        // ob_start();
        // var_dump($msg);
        // $log_data = ob_get_clean();
        // ob_end_clean();

        @error_log($date . $msg . "\r\n", 3, PROPELLER_ERROR_LOG);
    }

    function init_propel_log_file() {
        if (defined('PROPELLER_ERROR_LOG') && !is_file(PROPELLER_ERROR_LOG)) {
            @PropellerHelper::wp_filesys()->chmod(PROPELLER_ERROR_LOG, 0777);
            @PropellerHelper::wp_filesys()->put_contents(PROPELLER_ERROR_LOG, "Error log\r\n");
        }
    }
