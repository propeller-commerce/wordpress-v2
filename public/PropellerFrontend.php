<?php

namespace Propeller\Frontend;

if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\AddressController;
use Propeller\Includes\Controller\BaseController;
use Propeller\Includes\Controller\CategoryController;
use Propeller\Includes\Controller\FavoriteController;
use Propeller\Includes\Controller\HomepageController;
use Propeller\Includes\Controller\LanguageController;
use Propeller\Includes\Controller\MachineController;
use Propeller\Includes\Controller\MenuController;
use Propeller\Includes\Controller\OrderController;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\PaymentController;
use Propeller\Includes\Controller\PricerequestController;
use Propeller\Includes\Controller\ProductController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\ShoppingCartController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\PdpNewWindow;
use Propeller\Meta\MetaController;
use Propeller\PropellerHelper;
use Propeller\PropellerSitemap;
use Propeller\RequestHandler;
use ReflectionClass;

global $title, $description;

class PropellerFrontend
{
    protected $propeller;
    protected $version;

    protected $assets_url;
    protected $assets_dir;

    protected $request_handler;
    protected $meta;

    protected $assets;

    public $templates_dir;
    public $partials_dir;
    public $emails_dir;

    protected $min_js_path;
    protected $min_css_path;

    protected $min_js;
    protected $min_css;

    public function __construct($propeller, $version)
    {

        $this->assets = new PropellerAssets();

        $this->propeller = $propeller;
        $this->version = $version;

        $this->assets_url = plugins_url('assets', __FILE__);
        $this->assets_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';

        $this->get_assets_folder();

        $this->templates_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates';
        $this->partials_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'partials';
        $this->emails_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'email';

        $this->min_js_path = $this->assets_dir . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'propel.min.js';
        $this->min_css_path = $this->assets_dir . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'propel.min.css';

        $this->min_js = plugins_url('assets/js/propel.min.js', __FILE__);
        $this->min_css = plugins_url('assets/css/propel.min.css', __FILE__);

        $this->request_handler = new RequestHandler();

        add_filter('wpseo_title', [$this, 'set_title'], 1, 1);
        add_filter('pre_get_document_title', [$this, 'set_title'], 1, 1);
        add_action('parse_request', [$this, 'parse_request'], 3, 1);
    }

    /**
     * @return PropellerAssets
     */
    public function assets()
    {
        if (empty($this->assets)) {
            $this->assets = new PropellerAssets();
        }
        return $this->assets;
    }

    private function get_assets_folder()
    {
        $this->assets_url = plugins_url('assets', __FILE__);

        if (defined('PROPELLER_PLUGIN_EXTEND_DIR') && is_dir(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets')) {
            $this->assets_url = PROPELLER_PLUGIN_EXTEND_URL . '/public/assets';
            $this->assets_dir = PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets';
        }
    }

    public function parse_request($wp)
    {
        $this->request_handler->process($wp->query_vars);

        // used for meta tags, hreflang, etc
        $this->meta = new MetaController();
    }

    public function init_propeller()
    {
        SessionController::start();

        set_propel_locale();

        // Preserve the language in session, will be needed later for the ajax calls
        SessionController::set(PROPELLER_SESSION_LANG, PROPELLER_LANG);

        if (!SessionController::has(PROPELLER_SPECIFIC_PRICES))
            SessionController::set(PROPELLER_SPECIFIC_PRICES, PROPELLER_DEFAULT_INCL_VAT);

        new LanguageController();

        $sitemap = new PropellerSitemap();
        $sitemap->register_actions();

        // Enable GA4 & GTM if configured
        if (defined('PROPELLER_USE_GA4') && PROPELLER_USE_GA4 && defined('PROPELLER_GA4_TRACKING') && PROPELLER_GA4_TRACKING) {
            $ga4 = \Propeller\Includes\Extra\Ga\PropellerGa4::instance();
            $ga4->bootstrap();
        }

        // dump($_SESSION);

        $this->register_frontend_behavior();
    }

    public function set_title($page_title)
    {
        global $propel;

        if (isset($propel['title']))
            $page_title = substr($propel['title'], 0, 60);

        $page_title = str_replace('', PROPELLER_PAGE_SUFFIX, $page_title);

        return $page_title;
    }

    public function wp_head_scripts()
    {
        $header_content = "\r\n" . '<!-- Propeller nonce and js helper -->' . "\r\n";

        $header_content .= $this->build_js_helper();
        $header_content .= "\r\n" . '<meta name="security" content="' . wp_create_nonce(PROPELLER_NONCE_KEY_FRONTEND) . '">' . "\r\n";
        $header_content .= "\r\n" . '<!-- !Propeller nonce and js helper -->' . "\r\n";

        $allowed_html = [
            'script' => [
                'type' => true
            ],
            'meta' => [
                'name' => true,
                'content' => true
            ]
        ];

        echo wp_kses((string) $header_content, $allowed_html);
    }

    public function wp_footer_scripts()
    {
        $bc = new BaseController();

        require $bc->load_template('partials', '/cart/propeller-shopping-cart-popup.php');

        require $bc->load_template('partials', '/cart/propeller-pre-basket-popup.php');

        require $bc->load_template('partials', '/other/propeller-toast.php');
    }

    function propel_error_pages()
    {
        global $wp_query, $propel;

        $bc = new BaseController();

        if (isset($propel['error_404'])) {
            $wp_query->is_404 = TRUE;
            $wp_query->is_page = TRUE;
            $wp_query->is_singular = TRUE;
            $wp_query->is_single = FALSE;
            $wp_query->is_home = FALSE;
            $wp_query->is_archive = FALSE;
            $wp_query->is_category = FALSE;
            add_filter('wp_title', [$this, 'propel_error_title'], 65000, 2);
            add_filter('body_class', [$this, 'propel_error_class']);
            status_header(404);
            nocache_headers();

            include('' !== get_query_template('404') ? get_query_template('404') : $bc->load_template('error', '/404.php', true));

            exit;
        }

        if (isset($propel['error_403'])) {
            $wp_query->is_404 = FALSE;
            $wp_query->is_page = TRUE;
            $wp_query->is_singular = TRUE;
            $wp_query->is_single = FALSE;
            $wp_query->is_home = FALSE;
            $wp_query->is_archive = FALSE;
            $wp_query->is_category = FALSE;
            add_filter('wp_title', [$this, 'propel_error_title'], 65000, 2);
            add_filter('body_class', [$this, 'propel_error_class']);
            status_header(403);
            nocache_headers();

            include('' !== get_query_template('403') ? get_query_template('403') : $bc->load_template('error', '/403.php'));

            exit;
        }

        if (isset($propel['error_401'])) {
            $wp_query->is_404 = FALSE;
            $wp_query->is_page = TRUE;
            $wp_query->is_singular = TRUE;
            $wp_query->is_single = FALSE;
            $wp_query->is_home = FALSE;
            $wp_query->is_archive = FALSE;
            $wp_query->is_category = FALSE;
            add_filter('wp_title', [$this, 'propel_error_title'], 65000, 2);
            add_filter('body_class', [$this, 'propel_error_class']);
            status_header(401);
            nocache_headers();

            include('' !== get_query_template('401') ? get_query_template('401') : $bc->load_template('error', '/401.php'));

            exit;
        }
    }

    function propel_error_title($title = '', $sep = '')
    {
        if (isset($propel['error_403']))
            return "Forbidden " . $sep . " " . get_bloginfo('name');

        if (isset($propel['error_401']))
            return "Unauthorized " . $sep . " " . get_bloginfo('name');
    }

    function propel_error_class($classes)
    {
        if (isset($propel['error_403'])) {
            $classes[] = "propel-error403";
            return $classes;
        }

        if (isset($propel['error_401'])) {
            $classes[] = "propel-error401";
            return $classes;
        }

        return $classes;
    }

    public function get_slugs()
    {
        $page_slugs = [];

        $page_slugs['home']             = PageController::get_slug(PageType::HOMEPAGE);
        $page_slugs['category']         = PageController::get_slug(PageType::CATEGORY_PAGE);
        $page_slugs['product']          = PageController::get_slug(PageType::PRODUCT_PAGE);
        $page_slugs['search']           = PageController::get_slug(PageType::SEARCH_PAGE);
        $page_slugs['brand']            = PageController::get_slug(PageType::BRAND_PAGE);
        $page_slugs['cart']             = PageController::get_slug(PageType::SHOPPING_CART_PAGE);
        $page_slugs['checkout']         = PageController::get_slug(PageType::CHECKOUT_PAGE);
        $page_slugs['checkout_summary'] = PageController::get_slug(PageType::CHECKOUT_SUMMARY_PAGE);
        $page_slugs['thank_you']        = PageController::get_slug(PageType::CHECKOUT_THANK_YOU_PAGE);
        $page_slugs['favorites']        = PageController::get_slug(PageType::FAVORITES_PAGE);
        $page_slugs['invoices']         = PageController::get_slug(PageType::INVOICES_PAGE);
        $page_slugs['orderlist']        = PageController::get_slug(PageType::ORDERLIST_PAGE);
        $page_slugs['quotations']       = PageController::get_slug(PageType::QUOTATIONS_PAGE);
        $page_slugs['quote_details']    = PageController::get_slug(PageType::QUOTATION_DETAILS_PAGE);
        $page_slugs['orders']           = PageController::get_slug(PageType::ORDERS_PAGE);
        $page_slugs['order_details']    = PageController::get_slug(PageType::ORDER_DETAILS_PAGE);
        $page_slugs['addresses']        = PageController::get_slug(PageType::ADDRESSES_PAGE);
        $page_slugs['quick_order']      = PageController::get_slug(PageType::QUICK_ORDER_PAGE);
        $page_slugs['my_account']       = PageController::get_slug(PageType::MY_ACCOUNT_PAGE);
        $page_slugs['account_details']  = PageController::get_slug(PageType::ACCOUNT_DETAILS_PAGE);
        $page_slugs['account_mobile']  = PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE);
        $page_slugs['login']            = PageController::get_slug(PageType::LOGIN_PAGE);
        $page_slugs['register']         = PageController::get_slug(PageType::REGISTER_PAGE);
        $page_slugs['forgot_password']  = PageController::get_slug(PageType::REGISTER_PAGE);
        $page_slugs['reset_password']   = PageController::get_slug(PageType::REGISTER_PAGE);
        $page_slugs['machines']         = PageController::get_slug(PageType::MACHINES_PAGE);

        return $page_slugs;
    }

    public function get_urls()
    {
        $page_urls = [];

        $page_urls['home']             = home_url(PageController::get_slug(PageType::HOMEPAGE) . '/');
        $page_urls['category']         = home_url(PageController::get_slug(PageType::CATEGORY_PAGE) . '/');
        $page_urls['product']          = home_url(PageController::get_slug(PageType::PRODUCT_PAGE) . '/');
        $page_urls['search']           = home_url(PageController::get_slug(PageType::SEARCH_PAGE) . '/');
        $page_urls['brand']            = home_url(PageController::get_slug(PageType::BRAND_PAGE) . '/');
        $page_urls['cart']             = home_url(PageController::get_slug(PageType::SHOPPING_CART_PAGE) . '/');
        $page_urls['checkout']         = home_url(PageController::get_slug(PageType::CHECKOUT_PAGE) . '/');
        $page_urls['checkout_summary'] = home_url(PageController::get_slug(PageType::CHECKOUT_SUMMARY_PAGE) . '/');
        $page_urls['thank_you']        = home_url(PageController::get_slug(PageType::CHECKOUT_THANK_YOU_PAGE) . '/');
        $page_urls['payment_failed']   = home_url(PageController::get_slug(PageType::PAYMENT_FAILED_PAGE) . '/');
        $page_urls['favorites']        = home_url(PageController::get_slug(PageType::FAVORITES_PAGE) . '/');
        $page_urls['invoices']         = home_url(PageController::get_slug(PageType::INVOICES_PAGE) . '/');
        $page_urls['orderlist']        = home_url(PageController::get_slug(PageType::ORDERLIST_PAGE) . '/');
        $page_urls['quotations']       = home_url(PageController::get_slug(PageType::QUOTATIONS_PAGE) . '/');
        $page_urls['quote_details']    = home_url(PageController::get_slug(PageType::QUOTATION_DETAILS_PAGE) . '/');
        $page_urls['orders']           = home_url(PageController::get_slug(PageType::ORDERS_PAGE) . '/');
        $page_urls['order_details']    = home_url(PageController::get_slug(PageType::ORDER_DETAILS_PAGE) . '/');
        $page_urls['addresses']        = home_url(PageController::get_slug(PageType::ADDRESSES_PAGE) . '/');
        $page_urls['quick_order']      = home_url(PageController::get_slug(PageType::QUICK_ORDER_PAGE) . '/');
        $page_urls['my_account']       = home_url(PageController::get_slug(PageType::MY_ACCOUNT_PAGE) . '/');
        $page_urls['account_details']  = home_url(PageController::get_slug(PageType::ACCOUNT_DETAILS_PAGE) . '/');
        $page_urls['login']            = home_url(PageController::get_slug(PageType::LOGIN_PAGE) . '/');
        $page_urls['register']         = home_url(PageController::get_slug(PageType::REGISTER_PAGE) . '/');
        $page_urls['forgot_password']  = home_url(PageController::get_slug(PageType::REGISTER_PAGE) . '/');
        $page_urls['reset_password']   = home_url(PageController::get_slug(PageType::REGISTER_PAGE) . '/');
        $page_urls['machines']         = home_url(PageController::get_slug(PageType::MACHINES_PAGE) . '/');

        return $page_urls;
    }

    public function get_behavior()
    {
        $behaviors = [];

        $behaviors['reload_filters'] = PROPELLER_RELOAD_FILTERS;
        $behaviors['use_recaptcha'] = PROPELLER_USE_RECAPTCHA;
        $behaviors['recaptcha_site_key'] = PROPELLER_RECAPTCHA_SITEKEY;
        $behaviors['stock_check'] = PROPELLER_STOCK_CHECK;
        $behaviors['load_specifications'] = PROPELLER_LOAD_SPECS;
        $behaviors['ids_in_url'] = PROPELLER_ID_IN_URL;
        $behaviors['partial_delivery'] = PROPELLER_PARTIAL_DELIVERY;
        $behaviors['selectable_carriers'] = PROPELLER_SELECTABLE_CARRIERS;
        $behaviors['use_datepicker'] = PROPELLER_USE_DATEPICKER;
        $behaviors['lazyload_images'] = PROPELLER_LAZYLOAD_IMAGES;
        $behaviors['anonymous_orders'] = PROPELLER_ANONYMOUS_ORDERS;
        $behaviors['show_actioncode'] = PROPELLER_SHOW_ACTIONCODE;
        $behaviors['show_order_type'] = PROPELLER_SHOW_ORDER_TYPE;

        return $behaviors;
    }

    public function build_js_helper()
    {
        $slugs = $this->get_slugs();
        $urls = $this->get_urls();
        $behaviors = $this->get_behavior();

        $translations = $this->get_frontend_translations();

        $js_data = [
            'slugs' => $slugs,
            'urls' => $urls,
            'base_assets_url' => PROPELLER_PLUGIN_DIR_URL . 'public/assets/',
            'behavior' => $behaviors,
            'incl_vat' => SessionController::get(PROPELLER_SPECIFIC_PRICES),
            'no_image' => esc_url($this->assets_url . '/img/no-image.webp'),
            'ajax_url' => esc_url(admin_url('admin-ajax.php')),
            'debug' => defined('WP_DEBUG') && WP_DEBUG,
            'language' => PROPELLER_LANG,
            'translations' => $translations,
            'currency' => PropellerHelper::currency(),
            'ga4' => PROPELLER_USE_GA4,
            'ga4_tracking' => PROPELLER_GA4_TRACKING,
            'days' => [
                __('Sunday', 'propeller-ecommerce-v2'),
                __('Monday', 'propeller-ecommerce-v2'),
                __('Tuesday', 'propeller-ecommerce-v2'),
                __('Wednesday', 'propeller-ecommerce-v2'),
                __('Thursday', 'propeller-ecommerce-v2'),
                __('Friday', 'propeller-ecommerce-v2'),
                __('Saturday', 'propeller-ecommerce-v2')
            ],
            'months' => [
                __("January", 'propeller-ecommerce-v2'),
                __("February", 'propeller-ecommerce-v2'),
                __("March", 'propeller-ecommerce-v2'),
                __("April", 'propeller-ecommerce-v2'),
                __("May", 'propeller-ecommerce-v2'),
                __("June", 'propeller-ecommerce-v2'),
                __("July", 'propeller-ecommerce-v2'),
                __("August", 'propeller-ecommerce-v2'),
                __("September", 'propeller-ecommerce-v2'),
                __("October", 'propeller-ecommerce-v2'),
                __("November", 'propeller-ecommerce-v2'),
                __("December", 'propeller-ecommerce-v2')
            ],
            'validator' => [
                "required" => __("This field is required.", 'propeller-ecommerce-v2'),
                "remote" => __("Please fix this field.", 'propeller-ecommerce-v2'),
                "email" => __("Please enter a valid email address.", 'propeller-ecommerce-v2'),
                "url" => __("Please enter a valid URL.", 'propeller-ecommerce-v2'),
                "date" => __("Please enter a valid date.", 'propeller-ecommerce-v2'),
                "dateISO" => __("Please enter a valid date (ISO).", 'propeller-ecommerce-v2'),
                "number" => __("Please enter a valid number.", 'propeller-ecommerce-v2'),
                "digits" => __("Please enter only digits.", 'propeller-ecommerce-v2'),
                "creditcard" => __("Please enter a valid credit card number.", 'propeller-ecommerce-v2'),
                "equalTo" => __("Please enter the same value again.", 'propeller-ecommerce-v2'),
                "accept" => __("Please enter a value with a valid extension.", 'propeller-ecommerce-v2'),
            ],
        ];

        if (defined('PROPELLER_PLUGIN_EXTEND_URL'))
            $js_data['custom_assets_url'] = PROPELLER_PLUGIN_EXTEND_URL . 'public/assets/';

        $refl = new ReflectionClass('Propeller\Includes\Enum\OrderType');
        $js_data['order_types'] = $refl->getConstants();

        $js_content = 'window.PropellerHelper = ' . wp_json_encode($js_data) . ';';

        return "\r\n" . '<script type="text/javascript">' . $js_content . '</script>' . "\r\n";
    }

    private function get_frontend_translations()
    {
        $translations_path = PROPELLER_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'PropellerTranslations.php';

        if (defined('PROPELLER_PLUGIN_EXTEND_DIR') && file_exists(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'PropellerTranslations.php'))
            $translations_path = PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'PropellerTranslations.php';

        return require_once $translations_path;
    }

    private function register_frontend_behavior()
    {
        $obj = new BaseController();

        if (!$obj->has_cookie(PROPELLER_PDP_BEHAVIOR) && defined('PROPELLER_PDP_NEW_TAB') && PROPELLER_PDP_NEW_TAB != PdpNewWindow::HIDDEN) {
            $obj->set_cookie(PROPELLER_PDP_BEHAVIOR, PROPELLER_PDP_NEW_TAB == PdpNewWindow::DEFAULT_ON ? "true" : "false", PROPELLER_COOKIE_EXPIRATION);
        } else {
            if (defined('PROPELLER_PDP_NEW_TAB') && PROPELLER_PDP_NEW_TAB == PdpNewWindow::HIDDEN)
                $obj->set_cookie(PROPELLER_PDP_BEHAVIOR, false, PROPELLER_COOKIE_EXPIRATION);
        }
    }

    /**
     * Propeller shortcodes
     *
     */
    public function draw_menu()
    {
        $menuController = new MenuController();

        return $menuController->draw_menu();
    }

    public function home_page()
    {
        $homePageController = new HomepageController();

        return $homePageController->home_page();
    }

    public function product_listing($applied_filters = [], $is_ajax = false)
    {

        $this->assets()->std_requires_asset(['propeller-filters', 'propeller-paginator', 'propeller-product']);

        $categoryController = new CategoryController();

        $content = $categoryController->product_listing($applied_filters, $is_ajax);

        return $content;
    }

    public function product_details()
    {

        $this->assets()->std_requires_asset(['propeller-product', 'propeller-product-fixed-wrapper', 'propeller-gallery', 'propeller-gallery-item', 'propeller-bulk-prices', 'propeller-favorites']);

        $productController = new ProductController();

        return $productController->product_details();
    }

    public function cluster_details()
    {
        // CLUSTER FIX: Ensure proper script loading for cluster pages
        error_log("PropellerFrontend: cluster_details() called - ensuring proper script loading");

        $this->assets()->std_requires_asset(['propeller-product', 'propeller-product-fixed-wrapper', 'propeller-gallery', 'propeller-gallery-item', 'propeller-bulk-prices', 'propeller-favorites']);

        // CLUSTER FIX: Force enqueue TomSelect for cluster functionality
        // This ensures TomSelect is available for cluster dropdowns regardless of vanilla script settings
        add_action('wp_enqueue_scripts', function () {
            if (!wp_script_is('propeller-droprown', 'enqueued')) {
                wp_enqueue_script('propeller-droprown');
                wp_enqueue_style('propeller-droprown');
                error_log("PropellerFrontend: CLUSTER FIX - Force enqueued TomSelect in cluster_details");
            }
        }, 999);

        $productController = new ProductController();

        return $productController->cluster_details();
    }

    public function product_slider($atts = [], $content = null)
    {

        $args = shortcode_atts(['type' => ''], $atts);

        if ('recently_viewed' === $args['type']) {
            $this->assets()->std_requires_asset(['propeller-slider-recently-viewed', 'propeller-product', 'propeller-quantity']);
        } else {
            $this->assets()->std_requires_asset(['propeller-slider', 'propeller-product', 'propeller-quantity']);
        }

        $productController = new ProductController();

        return $productController->product_slider($atts, $content);
    }

    public function search_products()
    {
        $productController = new ProductController();

        return $productController->search_products();
    }

    public function search()
    {

        $this->assets()->std_requires_asset(['propeller-filters', 'propeller-catalog', 'propeller-paginator']);

        $productController = new ProductController();

        $_REQUEST['term'] = wp_unslash(get_query_var('term'));

        return $productController->search($_REQUEST, false);
    }

    public function brand_listing()
    {

        $this->assets()->std_requires_asset(['propeller-filters', 'propeller-catalog', 'propeller-paginator']);

        $productController = new ProductController();

        $_REQUEST['manufacturers'] = get_query_var('manufacturers');

        return $productController->brand($_REQUEST, false);
    }

    public function brand_listing_content()
    {

        $this->assets()->std_requires_asset(['propeller-filters', 'propeller-catalog', 'propeller-paginator', 'propeller-truncate-content']);

        $productController = new ProductController();

        return $productController->brand_listing_content();
    }

    public function quick_add_to_basket()
    {

        $this->assets()->std_requires_asset(['propeller-cart', 'propeller-quickorder']);

        $shoppingCartController = new ShoppingCartController();

        return $shoppingCartController->quick_add_to_basket();
    }

    public function shopping_cart()
    {

        $this->assets()->std_requires_asset('propeller-cart');

        $shoppingCartController = new ShoppingCartController();

        return $shoppingCartController->shopping_cart();
    }

    public function checkout()
    {

        $this->assets()->std_requires_asset('propeller-checkout');

        $shoppingCartController = new ShoppingCartController();

        return $shoppingCartController->checkout();
    }

    public function checkout_summary()
    {

        $this->assets()->std_requires_asset('propeller-checkout');

        $shoppingCartController = new ShoppingCartController();

        return $shoppingCartController->checkout_summary();
    }

    public function checkout_thank_you()
    {

        $this->assets()->std_requires_asset('propeller-checkout');

        $paymentController = new PaymentController();

        return $paymentController->payment_success();
    }

    public function payment_failed()
    {

        $this->assets()->std_requires_asset('propeller-checkout');

        $paymentController = new PaymentController();

        return $paymentController->payment_failed();
    }

    public function payment_processed()
    {

        $this->assets()->std_requires_asset('propeller-checkout');

        $paymentController = new PaymentController();

        return $paymentController->payment_processed();
    }

    public function payment_cancelled()
    {

        $this->assets()->std_requires_asset('propeller-checkout');

        $paymentController = new PaymentController();

        return $paymentController->payment_cancelled();
    }

    public function payment_expired()
    {

        $this->assets()->std_requires_asset('propeller-checkout');

        $paymentController = new PaymentController();

        return $paymentController->payment_expired();
    }

    public function authorization_confirmed()
    {

        $this->assets()->std_requires_asset('propeller-checkout');

        $paymentController = new PaymentController();

        return $paymentController->authorization_confirmed();
    }

    public function mini_shopping_cart()
    {
        $shoppingCartController = new ShoppingCartController();

        return $shoppingCartController->mini_shopping_cart();
    }

    public function mini_checkout_cart()
    {
        $shoppingCartController = new ShoppingCartController();

        return $shoppingCartController->mini_checkout_cart();
    }

    public function quotations()
    {
        $orderController = new OrderController();

        return $orderController->quotations();
    }

    public function orders()
    {
        $orderController = new OrderController();

        return $orderController->orders();
    }

    public function order_details()
    {

        $this->assets()->std_requires_asset('propeller-form-return-products');

        $orderController = new OrderController();

        return $orderController->order_details();
    }

    public function account_favorites()
    {
        $this->assets()->std_requires_asset(['propeller-favorites', 'propeller-pricerequest']);

        $favoritesController = new FavoriteController();

        return $favoritesController->account_favorites();
    }

    public function account_orderlist()
    {
        $userController = new UserController();

        return $userController->account_orderlist();
    }

    public function account_invoices()
    {
        $userController = new UserController();

        return $userController->account_invoices();
    }

    public function account_recent_invoices()
    {
        $userController = new UserController();

        return $userController->account_recent_invoices($userController);
    }
    public function account_addresses()
    {
        $addressController = new AddressController();

        return $addressController->account_addresses();
    }

    public function account_prices()
    {
        $userController = new UserController();

        return $userController->account_prices();
    }

    public function contact_companies()
    {
        $userController = new UserController();

        return $userController->contact_companies();
    }

    public function mini_account()
    {
        $userController = new UserController();

        return $userController->mini_account();
    }

    public function favorites_menu()
    {
        $favoritesController = new FavoriteController();

        return $favoritesController->favorites_menu();
    }

    public function account_recent_favorites()
    {
        $favoritesController = new FavoriteController();

        return $favoritesController->recent_favorite_lists($favoritesController);
    }

    public function my_account()
    {
        $userController = new UserController();

        return $userController->my_account();
    }

    public function account_mobile()
    {
        $userController = new UserController();

        return $userController->account_mobile();
    }

    public function account_details()
    {
        $userController = new UserController();

        return $userController->account_details();
    }

    public function account_details_no_dashboard()
    {
        $userController = new UserController();

        return $userController->account_no_dashboard();
    }

    public function account_details_section()
    {
        $userController = new UserController();

        return $userController->account_details_section($userController);
    }

    public function account_menu()
    {
        $userController = new UserController();

        ob_start();
        $userController->account_menu($userController);
        return ob_get_clean();
    }

    public function account_company_name()
    {
        $userController = new UserController();

        return $userController->account_company_name($userController);
    }

    public function account_recent_orders()
    {
        $orderController = new OrderController();

        return $orderController->account_recent_orders($orderController);
    }

    public function account_recent_quotations()
    {
        $orderController = new OrderController();

        return $orderController->account_recent_quotations($orderController);
    }

    public function newsletter_subscription()
    {
        $userController = new UserController();

        return $userController->newsletter_subscription();
    }

    public function login_page()
    {

        $this->assets()->std_requires_asset(['propeller-login', 'propeller-cart']);

        $userController = new UserController();

        return $userController->login_page();
    }

    public function login_form()
    {

        $this->assets()->std_requires_asset('propeller-login');

        $userController = new UserController();

        return $userController->login_form();
    }

    public function forgot_password_form()
    {

        $this->assets()->std_requires_asset('propeller-register');

        $userController = new UserController();

        return $userController->forgot_password_form();
    }

    public function reset_password_form()
    {
        $userController = new UserController();

        return $userController->reset_password_form();
    }

    public function register_form()
    {

        $this->assets()->std_requires_asset('propeller-checkout-forms');
        $this->assets()->std_requires_asset('propeller-register');

        $userController = new UserController();

        return $userController->register_form();
    }

    public function machines()
    {
        $this->assets()->std_requires_asset(['propeller-filters', 'propeller-paginator', 'propeller-product', 'propeller-spareparts']);

        $machinesController = new MachineController();

        return $machinesController->machine_listing();
    }

    public function price_request()
    {
        $this->assets()->std_requires_asset(['propeller-pricerequest']);

        $pricerequestController = new PricerequestController();

        return $pricerequestController->price_request();
    }

    public function purchase_authorizations()
    {
        $this->assets()->std_requires_asset(['propeller-user']);

        $userController = new UserController();

        return $userController->purchase_authorizations();
    }

    public function purchase_authorization_requests()
    {
        $this->assets()->std_requires_asset(['propeller-user']);

        $userController = new UserController();

        return $userController->purchase_authorization_requests();
    }

    public function purchase_authorization_thank_you()
    {
        $this->assets()->std_requires_asset(['propeller-user']);

        $shoppingCartController = new ShoppingCartController();

        return $shoppingCartController->purchase_authorization_thank_you();
    }

    public function purchase_authorizations_short_list()
    {
        $userController = new UserController();

        return $userController->purchase_authorizations_short_list($userController);
    }

    public function sso_sign_in()
    {
        $this->assets()->std_requires_asset(['propeller-user']);

        $userController = new UserController();

        return $userController->sso_sign_in();
    }
}
