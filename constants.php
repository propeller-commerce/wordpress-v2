<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

/* The following constants can be overwritten in wp-config.php */

if (!defined('PROPELLER_MAX_API_ATTEMPTS'))
    define('PROPELLER_MAX_API_ATTEMPTS',                    10);

if (!defined('PROPELLER_DEBUG'))
    define('PROPELLER_DEBUG',                               false);

if (!defined('PROPELLER_COOKIE_EXPIRATION'))
    define('PROPELLER_COOKIE_EXPIRATION',                    strtotime('+30 days'));

if (!defined('PROPELLER_CATALOG_DEPTH'))
    define('PROPELLER_CATALOG_DEPTH',                        3);

if (!defined('PROPELLER_SITEMAP_MAX_ITEMS'))
    define('PROPELLER_SITEMAP_MAX_ITEMS',                    50000);

if (!defined('PROPELLER_ERROR_LOG'))
    define('PROPELLER_ERROR_LOG',                           plugin_dir_path(__FILE__) . 'propel-errors.log');

if (!defined('PROPELLER_MACHINES_DEPTH'))
    define('PROPELLER_MACHINES_DEPTH',                        5);


// Flash messages
if (!defined('PROPELLER_FLASH_PREFIX'))
    define('PROPELLER_FLASH_PREFIX',                        'flash_');

// Product Specific
if (!defined('PROPELLER_PRODUCT_IMG_SMALL_WIDTH'))
    define('PROPELLER_PRODUCT_IMG_SMALL_WIDTH',                100);

if (!defined('PROPELLER_PRODUCT_IMG_SMALL_HEIGHT'))
    define('PROPELLER_PRODUCT_IMG_SMALL_HEIGHT',            100);

if (!defined('PROPELLER_PRODUCT_IMG_MEDIUM_WIDTH'))
    define('PROPELLER_PRODUCT_IMG_MEDIUM_WIDTH',            300);

if (!defined('PROPELLER_PRODUCT_IMG_MEDIUM_HEIGHT'))
    define('PROPELLER_PRODUCT_IMG_MEDIUM_HEIGHT',            300);

if (!defined('PROPELLER_PRODUCT_IMG_LARGE_WIDTH'))
    define('PROPELLER_PRODUCT_IMG_LARGE_WIDTH',                800);

if (!defined('PROPELLER_PRODUCT_IMG_LARGE_HEIGHT'))
    define('PROPELLER_PRODUCT_IMG_LARGE_HEIGHT',            800);

if (!defined('PROPELLER_PRODUCT_IMG_CATALOG_WIDTH'))
    define('PROPELLER_PRODUCT_IMG_CATALOG_WIDTH',            300);

if (!defined('PROPELLER_PRODUCT_IMG_CATALOG_HEIGHT'))
    define('PROPELLER_PRODUCT_IMG_CATALOG_HEIGHT',            300);

if (!defined('PROPELLER_PRODUCT_IMG_DETAILS_WIDTH'))
    define('PROPELLER_PRODUCT_IMG_DETAILS_WIDTH',            800);

if (!defined('PROPELLER_PRODUCT_IMG_DETAILS_HEIGHT'))
    define('PROPELLER_PRODUCT_IMG_DETAILS_HEIGHT',            800);

// Cache/Transient specific
if (!defined('PROPELLER_TRANSIENT_EXPIRATION'))
    define('PROPELLER_TRANSIENT_EXPIRATION',                12 * HOUR_IN_SECONDS);

if (!defined('PROPELLER_PAGE_SUFFIX'))
    define('PROPELLER_PAGE_SUFFIX',                            " - Propeller");

if (!defined('PROPELLER_SEARCH_SUGGESTIONS'))
    define('PROPELLER_SEARCH_SUGGESTIONS',                    6);

if (!defined('PROPELLER_FALLBACK_LANG'))
    define('PROPELLER_FALLBACK_LANG',                        "NL");

// payments
if (!defined('PROPELLER_DEFAULT_PAYMETHOD'))
    define('PROPELLER_DEFAULT_PAYMETHOD',                    "REKENING");

if (!defined('PROPELLER_DATE_FORMAT'))
    define('PROPELLER_DATE_FORMAT',                    "d.m.Y");


/**********************************************************************/


/* The following constants must not be overwritten at all costs */

define('PROPELLER_VERSION',                 '1.0.0');
define('PROPELLER_PLUGIN_NAME',            'propeller-ecommerce-v2');
define('PROPELLER_DB_VERSION',             0.6);
define('PROPELLER_DB_VERSION_OPTION',     'propel_db_version');

define('PROPELLER_PLUGIN_DIR',             plugin_dir_path(__FILE__));
define('PROPELLER_PLUGIN_DIR_URL',         plugin_dir_url(__FILE__));

/**
 * Support for third party plugins
 * If those are defined from earlier,the custom folder will have different location
 */
if (defined('PROPELLER_PLUGIN_EXTEND_DIR')) {
    // 	define( 'PROPELLER_PLUGIN_EXTEND_DIR', plugin_dir_path( __FILE__ ) . 'custom' );
    // 	define( 'PROPELLER_PLUGIN_EXTEND_URL', plugin_dir_url( __FILE__ ) . 'custom' );
    require_once  plugin_dir_path(__FILE__) . 'load-custom.php';
}

/**
 * Template constants
 */
define('PROPELLER_EMAILS_DIR',            plugin_dir_path(__FILE__) . 'public' . DIRECTORY_SEPARATOR . 'email');
define('PROPELLER_PARTIALS_DIR',        plugin_dir_path(__FILE__) . 'public' . DIRECTORY_SEPARATOR . 'partials');
define('PROPELLER_TEMPLATES_DIR',        plugin_dir_path(__FILE__) . 'public' . DIRECTORY_SEPARATOR . 'templates');
define('PROPELLER_ERROR_DIR',            plugin_dir_path(__FILE__) . 'public' . DIRECTORY_SEPARATOR . 'error');
define('PROPELLER_ASSETS_DIR',            plugin_dir_path(__FILE__) . 'public' . DIRECTORY_SEPARATOR . 'assets');
define('PROPELLER_ASSETS_URL',            plugin_dir_url(__FILE__) . 'public/assets');

// Database globals
define('PROPELLER_SETTINGS_TABLE',         'propel_settings');
define('PROPELLER_PAGES_TABLE',         'propel_pages');
define('PROPELLER_BEHAVIOR_TABLE',         'propel_behavior');
define('PROPELLER_SLUGS_TABLE',         'propel_slugs');

// Session/user based globals
define('PROPELLER_USER_ID',             'user_id');
define('PROPELLER_CART_ID',             'cart_id');
define('PROPELLER_AUTHORIZER_CART_ID',  'authorizer_cart_id');
define('PROPELLER_CART_INITIALIZED',    'cart_initialized');
define('PROPELLER_CART_USER_SET',        'cart_user_set');
define('PROPELLER_CART_ID_ATTR',        'PROPELLER_CART_ID_ATTR');
define('PROPELLER_CART',                 'cart');
define('PROPELLER_TEMP_CART',             'temp_cart');
define('PROPELLER_UID',                    'uid');
define('PROPELLER_SESSION',                'propeller_session');
define('PROPELLER_USER_DATA',            'user_data');
define('PROPELLER_CONTACT_COMPANY_ID',    'contact_company_id');
define('PROPELLER_CONTACT_COMPANY_NAME', 'contact_company_name');
define('PROPELLER_USER_ADD_INFO',        'user_additional_info');
define('PROPELLER_CREDENTIAL',            'credential');
define('PROPELLER_ACCESS_TOKEN',        'access_token');
define('PROPELLER_REFRESH_TOKEN',        'refresh_token');
define('PROPELLER_EXPIRATION_TIME',        'expiration_time');
define('PROPELLER_ORDER_TYPE',            'order_type');
define('PROPELLER_RES_JS_DATE',            'js_last_mod');
define('PROPELLER_RES_CSS_DATE',        'css_last_mod');
define('PROPELLER_ID_TOKEN',            'propel_id_token');

// Cookies
define('PROPELLER_RECENT_PRODS_COOKIE',                    'recently_viewed_products');
define('PROPELLER_NO_CACHE',                            'wordpress_nocache');
define('PROPELLER_USER_SESSION',                        'propel_login');
define('PROPELLER_RESOURCE_LAST_MOD',                    'propel_resource_mod');

// User specific
define('PROPELLER_SPECIFIC_PRICES',                        'show_user_prices');
define('PROPELLER_ORDER_PLACED',                        'order_placed');
define('PROPELLER_USER_ATTR',                            'user_attribute');
define('PROPELLER_USER_ATTR_VALUE',                        'user_attribute_value');

define('PROPELLER_SESSION_LANG',                         "session_locale");
define('PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED',    "default_delivery_address_changed");
define('PROPELLER_DEFAULT_INVOICE_ADDRESS_CHANGED',    "default_invoice_address_changed");
define('PROPELLER_ORDER_STATUS_TYPE',                    "order_status_type");

define('PROPELLER_VIEWING_CLUSTER',                        "propeller_viewing_cluster");
define('PROPELLER_PRICE_REQUEST',                        "propeller_price_request");

define('PROPELLER_USER_FAV_LISTS',                      "fav_lists");

// Security
define('PROPELLER_NONCE_KEY_FRONTEND',                  'propeller_frontend');

define('PROPELLER_GRECAPTCHA_VERIFY_URL',               'https://www.google.com/recaptcha/api/siteverify');

define('PROPELLER_PDP_BEHAVIOR',                        'propeller_pdp_behavior');

define('PROPELLER_QUERY_REQUEST',                       'propeller_query_request');
/**********************************************************************/
