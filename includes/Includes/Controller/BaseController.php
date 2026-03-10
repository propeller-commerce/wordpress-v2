<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Frontend\PropellerAssets;
use stdClass;
use Exception;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\ProductClass;
use Propeller\Includes\Extra\Ga\PropellerGa4;
use Propeller\Propeller;
use Propeller\PropellerApi;
use Propeller\PropellerUtils;

class BaseController extends PropellerApi
{
    protected $model;

    public $default_assets_url;
    public $default_assets_dir;
    public $default_templates_dir;
    public $default_partials_dir;
    public $default_emails_dir;
    public $default_errors_dir;

    public $assets_url;
    public $assets_dir;
    public $templates_dir;
    public $partials_dir;
    public $emails_dir;
    public $errorss_dir;

    public $theme_assets_url;
    public $theme_assets_dir;
    public $theme_templates_dir;
    public $theme_partials_dir;
    public $theme_emails_dir;
    public $theme_errors_dir;

    public $pagename;

    protected $assets;

    const TEXT_FILTERS_KEY = 'textFilters';
    const RANGE_FILTERS_KEY = 'rangeFilters';

    protected $array_filters = ['textFilters', 'rangeFilters'];

    protected $trp_available = false;

    public $analytics = null;

    public function __construct()
    {
        parent::__construct();

        $this->assets = new PropellerAssets();

        $this->register_directories();

        $this->is_trp_available();

        if (defined('PROPELLER_USE_GA4') && PROPELLER_USE_GA4 && defined('PROPELLER_GA4_TRACKING') && PROPELLER_GA4_TRACKING) {
            $this->analytics = PropellerGa4::instance();
        }
    }

    private function register_directories()
    {
        $ds = DIRECTORY_SEPARATOR;

        // initial template/assets paths
        $this->assets_url = PROPELLER_ASSETS_URL;
        $this->assets_dir = PROPELLER_ASSETS_DIR;
        $this->templates_dir = PROPELLER_TEMPLATES_DIR;
        $this->partials_dir = PROPELLER_PARTIALS_DIR;
        $this->emails_dir = PROPELLER_EMAILS_DIR;
        $this->errorss_dir = PROPELLER_ERROR_DIR;

        // default template/assets paths
        $this->default_assets_url = PROPELLER_ASSETS_URL;
        $this->default_assets_dir = PROPELLER_ASSETS_DIR;
        $this->default_templates_dir = PROPELLER_TEMPLATES_DIR;
        $this->default_partials_dir = PROPELLER_PARTIALS_DIR;
        $this->default_emails_dir = PROPELLER_EMAILS_DIR;
        $this->default_errors_dir = PROPELLER_ERROR_DIR;

        // theme template/assets paths
        $this->theme_assets_url = get_theme_file_uri() . '/propeller/assets';
        $this->theme_assets_dir = get_theme_file_path() . $ds . 'propeller' . $ds . 'assets';
        $this->theme_templates_dir = get_theme_file_path() . $ds . 'propeller' . $ds . 'templates';
        $this->theme_partials_dir = get_theme_file_path() . $ds . 'propeller' . $ds . 'partials';
        $this->theme_emails_dir = get_theme_file_path() . $ds . 'propeller' . $ds . 'email';
        $this->theme_errors_dir = get_theme_file_path() . $ds . 'propeller' . $ds . 'error';

        if (defined('PROPELLER_PLUGIN_EXTEND_DIR') && defined('PROPELLER_PLUGIN_EXTEND_URL')) {

            $extends_dir = PROPELLER_PLUGIN_EXTEND_DIR;
            $extends_url = PROPELLER_PLUGIN_EXTEND_URL;

            if (is_dir($extends_dir . $ds . 'public' . $ds . 'assets')) {
                $this->assets_url = $extends_url . '/public/assets';
                $this->assets_dir = $extends_dir . $ds . 'public' . $ds . 'assets';
            }

            if (is_dir($extends_dir . $ds . 'public' . $ds . 'templates')) {
                $this->templates_dir = $extends_dir . $ds . 'public' . $ds . 'templates';
            }

            if (is_dir($extends_dir . $ds . 'public' . $ds . 'partials')) {
                $this->partials_dir = $extends_dir . $ds . 'public' . $ds . 'partials';
            }

            if (is_dir($extends_dir . $ds . 'public' . $ds . 'email')) {
                $this->emails_dir = $extends_dir . $ds . 'public' . $ds . 'email';
            }

            if (is_dir($extends_dir . $ds . 'public' . $ds . 'error')) {
                $this->errorss_dir = $extends_dir . $ds . 'public' . $ds . 'error';
            }
        }
    }

    public function get_no_image()
    {
        $ds = DIRECTORY_SEPARATOR;

        if (file_exists($this->theme_assets_dir . $ds . "img" . $ds . "no-image-card.webp"))
            return $this->theme_assets_url . "/img/no-image-card.webp";
        else if ($this->assets_dir != $this->default_assets_dir && file_exists($this->assets_dir . $ds . "img" . $ds . "no-image-card.webp"))
            return $this->assets_url . "/img/no-image-card.webp";
        else
            return $this->default_assets_url . "/img/no-image-card.webp";
    }

    public function load_template($path, $template)
    {
        $dir = $this->templates_dir;
        $default_dir = $this->default_templates_dir;
        $theme_dir = $this->theme_templates_dir;

        switch ($path) {
            case 'emails':
                $theme_dir = $this->theme_emails_dir;
                $default_dir = $this->default_emails_dir;
                $dir = $this->emails_dir;
                break;
            case 'partials':
                $theme_dir = $this->theme_partials_dir;
                $default_dir = $this->default_partials_dir;
                $dir = $this->partials_dir;
                break;
            case 'templates':
                $theme_dir = $this->theme_templates_dir;
                $default_dir = $this->default_templates_dir;
                $dir = $this->templates_dir;
                break;
            case 'error':
                $theme_dir = $this->theme_errors_dir;
                $default_dir = $this->default_errors_dir;
                $dir = $this->errorss_dir;
                break;
            case 'assets':
                $theme_dir = $this->theme_assets_dir;
                $default_dir = $this->default_assets_dir;
                $dir = $this->assets_dir;
                break;
        }

        if (file_exists($theme_dir . $template))
            return $theme_dir . $template;
        else if (file_exists($dir . $template))
            return $dir . $template;
        else
            return $default_dir . $template;
    }

    /**
     * Returns the assets instance
     * @return PropellerAssets
     */
    public function assets()
    {
        return $this->assets;
    }

    public function load_model($model)
    {
        $default_ref = "Propeller\Includes\Model\\" . ucfirst($model) . 'Model';
        $custom_ref = "Propeller\Custom\Includes\Model\\" . ucfirst($model) . 'Model';

        return class_exists($custom_ref, true) ? new $custom_ref() : new $default_ref();
    }

    public function buildUrl($realm_slug, $slug, $id = null)
    {
        if (empty($slug))
            return home_url($realm_slug . '/');
        else {
            if (!defined('PROPELLER_ID_IN_URL'))
                Propeller::register_behavior();

            if ($id && PROPELLER_ID_IN_URL)
                return home_url($realm_slug . '/' . $id . '/' . $slug . '/');
            else
                return home_url($realm_slug . '/' . $slug . '/');
        }
    }

    public function get_salutation($obj)
    {
        if (!isset($obj->gender))
            return '';

        if ($obj->gender === 'M')
            return __('Mr.', 'propeller-ecommerce-v2');
        else if ($obj->gender === 'F')
            return __('Mrs.', 'propeller-ecommerce-v2');
        else
            return '';
    }

    // Cookies
    public function set_cookie($name, $value, $expiration = PROPELLER_COOKIE_EXPIRATION)
    {
        $res = @setcookie($name, $value, $expiration, "/", $_SERVER['SERVER_NAME']);
    }

    public function has_cookie($name)
    {
        return isset($_COOKIE[$name]);
    }

    public function get_cookie($name)
    {
        if (isset($_COOKIE[$name]))
            return urldecode($_COOKIE[$name]);

        return null;
    }

    public function remove_cookie($name)
    {
        if (isset($_COOKIE[$name]))
            setcookie($name, '', time() - 3600, "/", $_SERVER['SERVER_NAME']);
    }

    public function ga4_event($data_type)
    {
        if ($this->analytics) {
            require $this->load_template('partials', '/other/propeller-ga4-fire-event.php');
            // return $this->analytics->toJson($data_type);
        }
    }

    public function ga4_data($data_type)
    {
        if ($this->analytics) {
            require $this->load_template('partials', '/other/propeller-ga4-print-data.php');
            // return $this->analytics->toJson($data_type);
        }
    }

    public function firebase_auth_scripts($config, $path)
    {
        require $this->load_template('partials', '/other/propeller-firebase-auth-scripts.php');
    }

    public function gtm_noscript($gtm_key)
    {
        require $this->load_template('partials', '/other/propeller-gtm-noscript.php');
    }

    public function gtm_init($ga4_key)
    {
        require $this->load_template('partials', '/other/propeller-gtm-init.php');
    }

    // Search params builder
    public function build_search_arguments($args)
    {
        error_reporting(E_ERROR | E_PARSE);

        $params = [];

        foreach ($args as $key => $value) {
            try {
                if (empty($value))
                    continue;

                switch ($key) {
                    case "term":    // String
                    case "path":
                        $params[$key] = is_array($value) ? $value[0] : $value;

                        break;
                    case "skus":     //[String!]
                    case "manufacturers":
                        $params[$key] = $value;

                        break;
                    case "supplierCodes":
                    case "suppliers":
                    case "manufacturerCodes":
                    case "EANCodes":
                        $params[$key] = $value;

                        break;
                    case "clusterIds":      //[Int!]
                    case "productIds":
                    case "ids":
                        // convert all strings to integers
                        foreach ($args[$key] as $index => $val)
                            $params[$key][$index] = (int) $val;

                        break;

                    case "class":   //ProductClass: PRODUCT/CLUSTER
                        // Convert to uppercase as GraphQL expects enum values PRODUCT or CLUSTER
                        $params[$key] = $value;

                        break;

                    case "language":    //String = "NL" = "NL"
                        $params[$key] = $value;

                        break;
                    case "page":     //Int = 1 = 1
                    case "ppage":    //Int = 1 = 1
                        if (is_numeric($value))
                            $params['page'] = (int) $value;

                        break;
                    case "userId":
                    case "offset":  //Int = 12 = 12
                        if (is_numeric($value))
                            $params[$key] = (int) $value;

                        break;

                    case "textFilters":     //[TextFilterInput!]
                        if (sizeof($value))
                            $params[$key] = $value;

                        break;

                    case "rangeFilters": // [RangeFilterInput!]
                        if (sizeof($value))
                            $params[$key] = $value;

                        break;

                    case "price":   // PriceFilterInput
                        if (sizeof($value))
                            $params[$key] = $value;

                        break;

                    case "statuses":  // [ProductStatus!] = [ "A" ] = [A]
                        $params[$key] = $value;

                        break;

                    case "hidden":  // Boolean
                        $params[$key] = (bool) $value;

                        break;

                    case "sortInputs":    // [SortInput!]
                    case "sort":    // [SortInput!]
                        if ($key == "sort")
                            $key = "sortInputs";

                        if (is_array($value) && sizeof($value))
                            $params[$key] = $value;
                        else {
                            if ($value == 'default')
                                $value = PROPELLER_DEFAULT_SORT_FIELD . ',' . PROPELLER_SECONDARY_SORT_FIELD . ',' . PROPELLER_DEFAULT_SORT_DIRECTION;

                            $values = explode(',', $value);

                            if (count($values) == 2 && isset($values[0]) && isset($values[1])) {
                                $params[$key] = [
                                    'field' => $values[0],
                                    'order' => $values[1]
                                ];
                            } else if (count($values) == 3 && isset($values[0]) && isset($values[1]) && isset($values[2])) {
                                $params[$key] = [
                                    [
                                        'field' => $values[0],
                                        'order' => $values[2]
                                    ],
                                    [
                                        'field' => $values[1],
                                        'order' => $values[2]
                                    ]
                                ];
                            }
                                
                        }

                        break;

                    case "searchFields":    //[SearchFieldsInput!]
                        // searchFields should be passed as an array structure
                        $params[$key] = $value;

                        break;
                    default:
                        break;
                }
            } catch (Exception $e) {
            }
        }

        // return $this->build_params_array($params);
        return $params;
    }

    public function process_filters($applied_filters)
    {
        error_reporting(E_ERROR | E_PARSE);

        $filters = [];

        foreach ($applied_filters as $key => $value) {
            try {
                $filter = [];

                if (is_array($value)) {
                    if (isset($value['from']) && isset($value['to'])) {
                        if ($key == 'price') {
                            $filters['price'] = [
                                'from' => (float) $value['from'],
                                'to' => (float) $value['to']
                            ];
                        } else {
                            $filter['name'] = $key;
                            $filter['from'] = (float) $value['from'];
                            $filter['to'] = (float) $value['to'];
                            $filter['exclude'] = false;

                            $filters['rangeFilters'][] = $filter;
                        }
                    } else {
                        if (!isset($value[sizeof($value) - 1]['type']))
                            continue;

                        $type = '';

                        $type = $value[sizeof($value) - 1]['type'];

                        $vals = [];

                        foreach ($value as $v) {
                            if (!isset($v['type'])) {
                                if (str_contains(strval($v), "\'"))
                                    $v = wp_unslash(strval($v));

                                $vals[] = strval(rawurldecode($v));
                            }
                        }

                        $filter['name'] = $key;
                        $filter['values'] = $vals;
                        $filter['exclude'] = false;
                        $filter['type'] = is_array($type) ? $type[0] : $type;

                        $filters['textFilters'][] = $filter;
                    }
                }
            } catch (Exception $e) {
            }
        }

        return $filters;
    }

    protected function get_selected_filters($all_filters)
    {
        $selected_filters = [];

        foreach ($all_filters as $filter) {
            if (isset($filter->attributeDescription->name) && isset($_REQUEST[$filter->attributeDescription->name])) {
                $filter_vals = [];

                foreach ($_REQUEST[$filter->attributeDescription->name] as $selected_filter) {
                    if (!is_array($selected_filter))
                        $filter_vals[] = wp_unslash(rawurldecode($selected_filter));
                    else
                        $filter_vals[] = wp_unslash(rawurldecode($selected_filter[0]));
                }

                $available_vals = $filter->textFilters;

                foreach ($available_vals as $val_obj) {
                    // Skip empty values
                    if (empty($val_obj->value)) {
                        continue;
                    }

                    if (in_array(wp_unslash($val_obj->value), $filter_vals)) {
                        $sel_filter = new stdClass();
                        $sel_filter->filter = $filter;
                        $sel_filter->value = $val_obj->value;

                        $selected_filters[] = $sel_filter;
                    }
                }
            } else if (isset($_REQUEST['price']) && $filter->type == 'price') {
                $sel_filter = new stdClass();
                $sel_filter->filter = $filter;
                $sel_filter->values = new stdClass();

                $sel_filter->values->from = $_REQUEST['price']['from'];
                $sel_filter->values->to = $_REQUEST['price']['to'];

                $selected_filters[] = $sel_filter;
            }
        }

        return $selected_filters;
    }

    private function is_trp_available()
    {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $this->trp_available = is_plugin_active('translatepress-multilingual/index.php') && class_exists('TRP_Translate_Press');
    }

    public function get_languages()
    {
        $this->is_trp_available();

        if ($this->trp_available)
            return $this->get_trp_languages();

        return get_propel_languages();
    }

    public function get_default_lang()
    {
        $this->is_trp_available();

        if ($this->trp_available) {
            $trp_options = get_option('trp_settings');

            return $trp_options['default-language'];
        }

        return PROPELLER_DEFAULT_LOCALE;
    }

    public function get_trp_languages()
    {
        $this->is_trp_available();

        if ($this->trp_available) {
            $trp_options = get_option('trp_settings');

            return $trp_options['translation-languages'];
        } else {
            return get_propel_languages();
        }
    }

    public function get_slugs()
    {
        $this->is_trp_available();

        if ($this->trp_available) {
            $trp_options = get_option('trp_settings');

            return $trp_options['url-slugs'];
        }

        return [];
    }

    public function clear_menu()
    {
        global $table_prefix, $wpdb;

        try {
            $wpdb->query($wpdb->prepare("DELETE FROM %i  
                                         WHERE option_name LIKE %s  
                                            OR option_name LIKE %s", $table_prefix . "options", '_transient_propeller%', '_transient_timeout_propeller%'));

            do_action('propel_cache_destroyed');

            $message = __('Caches cleared', 'propeller-ecommerce-v2');
        } catch (Exception $ex) {
            $success = false;
            $message = $ex->getMessage();
        }
    }

    public function get_locale()
    {
        $locale = PROPELLER_DEFAULT_LOCALE;
        $language = PROPELLER_LANG;

        $this->is_trp_available();

        if ($this->trp_available) {
            $trp_options = get_option('trp_settings');

            $found = array_filter($trp_options['url-slugs'], function ($item) use ($language) {
                return strtolower($item) == strtolower($language);
            });

            if ($found)
                $locale = key($found);
        }

        return $locale;
    }

    public function build_breadcrumbs($object)
    {
        $breadcrumb_paths = [];

        $index = 0;

        if (isset($object->categoryPath)) {
            foreach ($object->categoryPath as $path) {
                if ($index > 0) {
                    if (isset($path->slug) && count($path->slug)) {
                        $breadcrumb_paths[] = [
                            $this->buildUrl(PageController::get_slug(PageType::CATEGORY_PAGE), $path->slug[0]->value, $path->urlId),
                            $path->name[0]->value
                        ];
                    }
                }

                $index++;
            }
        }

        if (isset($object->class)) {
            $breadcrumb_paths[] = [
                $this->buildUrl(PageController::get_slug($object->class == ProductClass::Product ? PageType::PRODUCT_PAGE : PageType::CLUSTER_PAGE), $object->slug[0]->value, $object->urlId),
                $object->name[0]->value
            ];
        } else {
            $breadcrumb_paths[] = [
                $this->buildUrl(PageController::get_slug(PageType::CATEGORY_PAGE), $object->slug[0]->value, $object->urlId),
                $object->name[0]->value
            ];
        }

        return $breadcrumb_paths;
    }

    public function get_countries()
    {
        if (false === ($result = CacheController::get('propeller_SYSTEM_ADDRESS_COUNTRIES'))) {
            $valsets = new ValuesetController();

            $result = $valsets->get_valueset(['names' => ['SYSTEM_ADDRESS_COUNTRIES']]);

            CacheController::set('propeller_SYSTEM_ADDRESS_COUNTRIES', $result, WEEK_IN_SECONDS);
        }

        return is_object($result) ? $result : null;
    }


    /**
     * Check the front-end request
     *
     * @param $nonce_data_key
     * @param string $nonce_action_key
     *
     * @return false|int
     */
    protected function validate_form_request($nonce_data_key, $nonce_action_key = PROPELLER_NONCE_KEY_FRONTEND)
    {
        $nonce = isset($_REQUEST[$nonce_data_key]) ? sanitize_text_field($_REQUEST[$nonce_data_key]) : false;

        if (false === $nonce) {
            return false;
        }

        return wp_verify_nonce($nonce, $nonce_action_key);
    }

    /**
     * Validates the ajax request
     * @param string $nonce_data_key
     * @param string $nonce_action_key
     *
     * @return bool
     */
    protected function validate_ajax_request($nonce_data_key, $nonce_action_key = PROPELLER_NONCE_KEY_FRONTEND)
    {
        return (bool) check_ajax_referer($nonce_action_key, $nonce_data_key, false);
    }

    /**
     * Check if request is post
     * @return bool
     */
    protected function is_post_request()
    {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Sanitize frontend input
     *
     * @param $data
     *
     * @return array
     */
    public function sanitize($data)
    {
        return PropellerUtils::sanitize($data);
    }
}
