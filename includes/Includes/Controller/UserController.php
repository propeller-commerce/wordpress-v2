<?php

namespace Propeller\Includes\Controller;

if (! defined('ABSPATH')) exit;

use DateInterval;
use stdClass;
use DateTime;
use DateTimeZone;
use Propeller\Includes\Enum\AddressType;
use Propeller\Includes\Enum\CartStatus;
use Propeller\Includes\Enum\OrderStatus;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\PurchaseAuthorizationRoles;
use Propeller\Includes\Enum\UserTypes;
use Propeller\Includes\Object\Attribute as ObjectAttribute;
use Propeller\Includes\Object\Cluster;
use Propeller\Includes\Object\Order;
use Propeller\Includes\Object\Product;
use Propeller\Includes\Object\User;
use Propeller\Includes\Trait\CxmlTrait;
use Propeller\Includes\Trait\OCITrait;
use Propeller\Propeller;
use Propeller\PropellerHelper;

class UserController extends BaseController
{
    use OCITrait;
    use CxmlTrait;

    public $data;
    protected $type = 'user';
    protected $model;
    protected $AuthController;
    protected $ShoppingCart;
    protected $response;

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->load_model('user');

        $this->AuthController = new AuthController();
        $this->ShoppingCart = new ShoppingCartController();

        add_action('wp_logout', array($this, 'logout'), PHP_INT_MAX);
        add_action('logout_redirect', array($this, 'logout_redirect'), PHP_INT_MAX);

        if (defined('PROPELLER_USE_CXML') && PROPELLER_USE_CXML && defined('PROPELLER_CXML_CONTACT_ID') && !empty(PROPELLER_CXML_CONTACT_ID))
            add_action('template_redirect', array($this, 'cxml_punchout_login_request'), 100);
    }

    /*
        User filters
    */
    public function account_title($title)
    {
        require $this->load_template('partials', '/user/propeller-account-title.php');
    }

    public function account_menu($obj)
    {
        require $this->load_template('partials', '/user/propeller-account-sidemenu.php');
    }

    public function account_user_details_title($title)
    {
        require $this->load_template('partials', '/user/propeller-account-user-details-title.php');
    }

    public function account_user_details($user, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-user-details.php');
    }

    public function account_company_details($user)
    {
        require $this->load_template('partials', '/user/propeller-account-company-details.php');
    }

    public function account_pass_newsletter_title($title)
    {
        require $this->load_template('partials', '/user/propeller-account-pass-newsletter-title.php');
    }

    public function account_pass_newsletter($user)
    {
        require $this->load_template('partials', '/user/propeller-account-pass-newsletter.php');
    }

    public function account_addresses_title($title)
    {
        require $this->load_template('partials', '/user/propeller-account-addresses-title.php');
    }

    public function my_account()
    {
        require $this->load_template('partials', '/user/propeller-account-page.php');
    }

    public function account_mobile()
    {
        require $this->load_template('partials', '/user/propeller-account-mobile.php');
    }

    public function account_details()
    {
        require $this->load_template('partials', '/user/propeller-account-details.php');
    }

    public function account_details_section($obj)
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-account-details-section.php');
        return ob_get_clean();
    }

    public function account_company_name($obj)
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-account-company-name.php');
        return ob_get_clean();
    }

    public function account_no_dashboard()
    {
        require $this->load_template('partials', '/user/propeller-account-no-dashboard.php');
    }

    public function account_prices()
    {
        require $this->load_template('partials', '/other/propeller-prices-toggle.php');
    }

    public function account_orderlist()
    {
        require $this->load_template('partials', '/user/propeller-account-orderlist.php');
    }

    public function account_invoices()
    {
        $orderController = new OrderController();

        $data = $orderController->sanitize($_REQUEST);

        $invoices_args = [
            'page' => isset($data['page']) && is_numeric($data['page']) ? $data['page'] : 1,
            'offset' => isset($data['offset']) && is_numeric($data['offset']) ? $data['offset'] : 12,
            'tag' => [
                'language' => PROPELLER_LANG,
                'value' => "invoice"
            ]
        ];

        $orders = $orderController->get_invoices($invoices_args);
        $invoices = [];

        if ($orders->itemsFound > 0) {
            foreach ($orders->items as $order) {
                $order = new Order($order);

                if ($order->has_attachments()) {
                    $invoice = new stdClass();

                    $invoice->code = $order->id;
                    $invoice->attachments = $order->get_attachments();
                    $invoice->status = $order->paymentData->status;
                    $invoice->total = $order->total->net;

                    $invoices[] = $invoice;
                }
            }
        }

        require $this->load_template('partials', '/user/propeller-account-invoices.php');
    }

    public function order_invoice_item($invoice, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-invoice-item.php');
    }

    public function account_recent_invoices($obj)
    {
        $orderController = new OrderController();

        $invoices_args = [
            'page' => 1,
            'offset' => 3,
            'tag' => [
                'language' => PROPELLER_LANG,
                'value' => "invoice"
            ]
        ];

        $orders = $orderController->get_invoices($invoices_args);
        $invoices = [];

        if (isset($orders->itemsFound) && $orders->itemsFound > 0) {
            foreach ($orders->items as $order) {
                $order = new Order($order);

                if ($order->has_attachments()) {
                    $invoice = new stdClass();

                    $invoice->code = $order->id;
                    $invoice->attachments = $order->get_attachments();
                    $invoice->status = isset($order->paymentData->status) ? $order->paymentData->status : '';
                    $invoice->total = isset($order->total->net) ? $order->total->net : 0;

                    $invoices[] = $invoice;
                }
            }
        }

        ob_start();
        require $this->load_template('partials', '/user/propeller-account-recent-invoices.php');
        return ob_get_clean();
    }

    public function contact_companies()
    {
        if (self::is_propeller_logged_in()) {
            $user_data = SessionController::get(PROPELLER_USER_DATA);
            $user_type = $user_data->__typename;

            if ($user_type != UserTypes::CONTACT)
                return "";

            $companies = $user_data->companies->items;
            $default_company = $user_data->company;


            if (sizeof($companies) > 1 && SessionController::has(PROPELLER_CONTACT_COMPANY_ID) && SessionController::get(PROPELLER_CONTACT_COMPANY_ID)) {
                $found = array_filter($companies, function ($com) {
                    return $com->companyId == SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
                });

                $default_company = current($found);
            } else {
                SessionController::set(PROPELLER_CONTACT_COMPANY_ID, $default_company->companyId);
                SessionController::set(PROPELLER_CONTACT_COMPANY_NAME, $default_company->name);
            }

            add_action('wp_footer', function () use ($user_type, $companies) {
                apply_filters('propel_my_account_company_switch_modal', $user_type, $companies);
            });

            ob_start();
            require $this->load_template('partials', '/user/propeller-contact-companies.php');
            return ob_get_clean();
        }

        return "";
    }

    public function company_switch_modal($user_type, $companies)
    {
        require $this->load_template('partials', '/user/propeller-contact-companies-switch-modal.php');
    }

    public function mini_account()
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-mini-account.php');
        return ob_get_clean();
    }

    public function load_mini_account($data)
    {
        $response = new stdClass();

        ob_start();
        require $this->load_template('partials', '/user/propeller-mini-account-content.php');

        $response->title = UserController::is_propeller_logged_in() ? __('Welcome', 'propeller-ecommerce-v2') : __('Account', 'propeller-ecommerce-v2');
        $response->name = UserController::is_propeller_logged_in() ? SessionController::get(PROPELLER_USER_DATA)->firstName : __('Log in', 'propeller-ecommerce-v2');

        $response->content = ob_get_clean();
        $response->success = true;

        return $response;
    }

    public function edit_address()
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-account-edit-address.php');
        return ob_get_clean();
    }

    public function login_page()
    {
        $cart_has_items = $this->ShoppingCart->get_items_count() > 0;

        $show_guest_checkout = !UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS && !PROPELLER_WP_CLOSED_PORTAL && !PROPELLER_WP_SEMICLOSED_PORTAL && $cart_has_items;

        if ($show_guest_checkout && !SessionController::has('login_referrer') && !SessionController::has('register_referrer')) {
            $cart_url = home_url(PageController::get_slug(PageType::SHOPPING_CART_PAGE) . '/');

            SessionController::set('login_referrer', $cart_url);
            SessionController::set('register_referrer', $cart_url);
        }

        ob_start();
        require $this->load_template('templated', '/propeller-login-page.php');
        return ob_get_clean();
    }

    public function login_form()
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-login-form.php');
        return ob_get_clean();
    }

    public function forgot_password_form()
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-forgot-password-form.php');
        return ob_get_clean();
    }

    public function reset_password_form()
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-reset-password-form.php');
        return ob_get_clean();
    }

    public function register_form()
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-register-form.php');
        return ob_get_clean();
    }

    public function newsletter_subscription()
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-newsletter-subscription-form.php');
        return ob_get_clean();
    }

    public function purchase_authorizations($page = 1, $offset = 12, $is_ajax = false)
    {
        if (UserController::user()->is_authorization_manager()) {
            $purchase_authorizations_contacts = $this->get_purchase_authorization_contacts($page, $offset);

            if ($is_ajax) {
                $response = new stdClass();
                $response->success = true;

                ob_start();

                apply_filters('propel_account_purchase_authorizations_contacts_table_list', $purchase_authorizations_contacts->contacts->items, $purchase_authorizations_contacts->contacts, $this);

                $response->content = ob_get_clean();

                ob_end_clean();

                return $response;
            } else {
                require $this->load_template('partials', '/user/propeller-account-purchase-authorizations.php');
            }
        }
    }

    public function purchase_authorizations_contacts_table($purchase_authorizations_contacts, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-contacts-table.php');
    }

    public function purchase_authorizations_contacts_table_header($purchase_authorizations_contacts)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-contacts-table-header.php');
    }

    public function purchase_authorizations_contacts_table_list($contacts, $data, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-contacts-table-list.php');
    }

    public function purchase_authorizations_contacts_table_list_item($contact, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-contacts-table-list-item.php');
    }

    public function purchase_authorizations_contacts_table_list_paging($data, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-contacts-table-list-paging.php');
    }

    public function purchase_authorization_requests()
    {
        if (UserController::user()->is_authorization_manager()) {
            $purchase_authorizations = $this->get_purchase_authorizations();

            $obj = $this;

            add_action('wp_footer', function () use ($obj) {
                apply_filters('propel_account_purchase_authorization_modal', $obj);
            });

            require $this->load_template('partials', '/user/propeller-account-purchase-authorization-requests.php');
        }
    }

    public function purchase_authorizations_table($purchase_authorizations, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-table.php');
    }

    public function purchase_authorizations_table_header($purchase_authorizations)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-table-header.php');
    }

    public function purchase_authorizations_table_list($purchase_authorizations, $data, $obj)
    {

        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-table-list.php');
    }

    public function purchase_authorizations_table_list_item($purchase_authorization, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-table-list-item.php');
    }

    public function purchase_authorizations_table_list_paging($data, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorizations-table-list-paging.php');
    }

    public function purchase_authorization_modal($obj)
    {
        require $this->load_template('partials', '/user/propeller-account-purchase-authorization-modal.php');
    }

    public function purchase_authorizations_short_list($obj)
    {
        if (!UserController::is_propeller_logged_in()) {
            return '';
        }

        $user = $this->get_user();

        if (!$user->is_authorization_manager()) {
            return '';
        }

        $purchase_authorizations_data = $this->get_purchase_authorizations();

        $purchase_authorizations = [];
        if (is_object($purchase_authorizations_data) && isset($purchase_authorizations_data->items)) {
            $purchase_authorizations = array_slice($purchase_authorizations_data->items, 0, 3);
        }

        add_action('wp_footer', function () use ($obj) {
            apply_filters('propel_account_purchase_authorization_modal', $obj);
        });

        ob_start();
        require $this->load_template('partials', '/user/propeller-account-purchase-authorization-requests-recent.php');
        return ob_get_clean();
    }

    public function purchase_authorization_preview($cart, $obj)
    {
        $items_count = 0;

        foreach ($cart->items as $item)
            $items_count += $item->quantity;

        require $this->load_template('partials', '/user/propeller-account-purchase-authorization-preview.php');
    }

    public function cxml_setup_request($data)
    {
        require $this->load_template('partials', '/user/propeler-cxml-setup-request.php');
    }

    public function sso_sign_in()
    {
        return \Propeller\Includes\Extra\Sso\PropellerSso::instance()->renderSignIn();
    }


    public function start_session()
    {
        if (!self::is_propeller_logged_in()) {
            if (PROPELLER_USER_TRACK_ATTR != '') {
                $track_attrs = explode(',', PROPELLER_USER_TRACK_ATTR);

                foreach ($track_attrs as $track_attr) {
                    SessionController::remove($track_attr);
                }
            } else {
                SessionController::remove(PROPELLER_USER_ATTR_VALUE);
            }

            return null;
        }


        if (SessionController::has(PROPELLER_SESSION))
            return null;

        $type = 'startSession';

        $postprocess = new stdClass();
        $this->response = new stdClass();

        if (defined('PROPELLER_SITE_ID')) {
            $gql = $this->model->start_session(PROPELLER_SITE_ID);

            $sessionData = $this->query($gql, $type);

            if (is_object($sessionData)) {
                $this->postprocess_sesion($sessionData);

                $postprocess->dummy = 1;
            } else {
                $postprocess->message = $sessionData;
            }

            $this->response->postprocess = $postprocess;
        } else {
            $postprocess->dummy = 1;
            $this->response->postprocess = $postprocess;
        }

        return $this->response;
    }

    public function login($email, $password, $provider = '', $referrer = '')
    {
        $loginData = $this->AuthController->login($email, $password, $provider);

        $postprocess = new stdClass();
        $this->response = new stdClass();

        $fail_message = __('We are unable to process your information at this time. Please try again later.', 'propeller-ecommerce-v2');

        if (is_object($loginData)) {
            SessionController::regenerate_id();

            SessionController::start();

            $this->postprocess_sesion($loginData);

            if (!$loginData->session->isAnonymous) {
                $postprocess->is_logged_in = true;

                // fetch user data
                $userData = $this->get_viewer();

                if (is_object($userData)) {
                    $this->postprocess_user($userData);

                    $this->set_cookie(PROPELLER_USER_SESSION, $userData->userId, time() + (2 * DAY_IN_SECONDS));

                    if (isset($userData->trackAttributes))
                        $this->process_attributes($userData->trackAttributes, $userData);

                    if ($userData->get_type() == UserTypes::CONTACT)
                        $this->process_company_attributes($userData->company->trackAttributes);

                    $postprocess->redirect = $this->get_login_redirect($userData, $referrer);

                    // do wp login if setting is enabled
                    if (PROPELLER_WP_SESSIONS) {
                        $this->propeller_wp_login($userData);
                    }

                    $postprocess->message = __('Welcome ', 'propeller-ecommerce-v2') . ' ' . $userData->firstName;

                    $this->postprocess_login();

                    $this->ShoppingCart->init_user_cart(true);

                    SessionController::remove('login_referrer');

                    if ($this->analytics) {
                        $this->analytics->setData((object) ['method' => $provider != '' ? $provider : 'standard']);

                        ob_start();
                        apply_filters('propel_ga4_fire_event', 'login');
                        $postprocess->analytics = ob_get_clean();
                    }
                } else {
                    $this->response->error = true;

                    $postprocess->is_logged_in = false;
                    $postprocess->message_user = true;
                    $postprocess->message = $fail_message;
                }
            }
        } else {
            $this->response->error = true;

            $postprocess->message_user = true;
            $postprocess->is_logged_in = false;
            $postprocess->message = $fail_message;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function magic_token_login($magic_token, $oci_url = '', $generate_new_cart = false)
    {
        $loginData = $this->AuthController->magic_token_login($magic_token);

        $postprocess = new stdClass();
        $this->response = new stdClass();

        $fail_message = __('We are unable to process your information at this time. Please try again later.', 'propeller-ecommerce-v2');

        if (is_object($loginData)) {
            SessionController::regenerate_id();

            SessionController::start();

            $this->postprocess_sesion($loginData);

            if (!$loginData->session->isAnonymous) {
                $postprocess->is_logged_in = true;

                // fetch user data
                $userData = $this->get_viewer();

                if (is_object($userData)) {
                    if (!empty($oci_url) && !defined('PROPELLER_OCI_URL') && !SessionController::has('PROPELLER_OCI_URL')) {
                        define('PROPELLER_OCI_URL', $oci_url);
                        SessionController::set('PROPELLER_OCI_URL', $oci_url);
                    }

                    $this->postprocess_user($userData);

                    $this->set_cookie(PROPELLER_USER_SESSION, $userData->userId, time() + (2 * DAY_IN_SECONDS));

                    if (isset($userData->trackAttributes))
                        $this->process_attributes($userData->trackAttributes, $userData);

                    if ($userData->get_type() == UserTypes::CONTACT && isset($userData->company->trackAttributes))
                        $this->process_company_attributes($userData->company->trackAttributes);

                    $postprocess->redirect = home_url('/');

                    // do wp login if setting is enabled
                    if (PROPELLER_WP_SESSIONS) {
                        $this->propeller_wp_login($userData);
                    }

                    $postprocess->message = __('Welcome ', 'propeller-ecommerce-v2') . ' ' . $userData->firstName;

                    $this->postprocess_login();

                    if (!$generate_new_cart)
                        $this->ShoppingCart->init_user_cart(true);
                    else
                        $this->ShoppingCart->start();

                    SessionController::remove('login_referrer');
                } else {
                    $this->response->error = true;

                    $postprocess->is_logged_in = false;
                    $postprocess->message_user = true;
                    $postprocess->message = $fail_message;
                }
            }
        } else {
            $this->response->error = true;

            $postprocess->message_user = true;
            $postprocess->is_logged_in = false;
            $postprocess->message = $fail_message;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function firebase_sso_login()
    {
        $postprocess = new stdClass();
        $this->response = new stdClass();

        $fail_message = __('We are unable to process your information at this time. Please try again later.', 'propeller-ecommerce-v2');

        SessionController::regenerate_id();

        SessionController::start();

        $postprocess->is_logged_in = true;

        // fetch user data
        $userData = $this->get_viewer();

        if (is_object($userData)) {
            $session = new stdClass();
            $session->session = new stdClass();
            $session->session->isAnonymous = false;
            $session->session->accessToken = SessionController::get(PROPELLER_ACCESS_TOKEN);

            $this->postprocess_sesion($session);

            $this->postprocess_user($userData);

            $this->set_cookie(PROPELLER_USER_SESSION, $userData->userId, time() + (2 * DAY_IN_SECONDS));

            if (isset($userData->trackAttributes))
                $this->process_attributes($userData->trackAttributes, $userData);

            if ($userData->get_type() == UserTypes::CONTACT && isset($userData->company->trackAttributes))
                $this->process_company_attributes($userData->company->trackAttributes);

            $postprocess->redirect = home_url('/');

            // do wp login if setting is enabled
            if (PROPELLER_WP_SESSIONS) {
                $this->propeller_wp_login($userData);
            }

            $postprocess->message = __('Welcome ', 'propeller-ecommerce-v2') . ' ' . $userData->firstName;

            $this->postprocess_login();

            $this->ShoppingCart->init_user_cart(true);

            SessionController::remove('login_referrer');
        } else {
            $this->response->error = true;

            $postprocess->is_logged_in = false;
            $postprocess->message_user = true;
            $postprocess->message = $fail_message;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function magic_token_login_request()
    {
        global $wp;

        if ((isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'magic_token_login') || isset($wp->query_vars['name']) && $wp->query_vars['name'] == 'magic_token_login') {
            if (!isset($wp->query_vars['mtoken']) || empty($wp->query_vars['mtoken']))
                wp_die(esc_html(__('Magic token not provided or invalid', 'propeller-ecommerce-v2')), esc_html(__('Magic token error', 'propeller-ecommerce-v2')));

            $token_response = isset($wp->query_vars['HOOK_URL']) && !empty($wp->query_vars['HOOK_URL'])
                ? $this->magic_token_login($wp->query_vars['mtoken'], $wp->query_vars['HOOK_URL'], true)
                : $this->magic_token_login($wp->query_vars['mtoken']);

            if (isset($wp->query_vars['sid'])) {
                SessionController::set('sid', $wp->query_vars['sid']);
                SessionController::set('buyer_cookie', $wp->query_vars['buyer_cookie']);
                SessionController::set('cxml_from', $wp->query_vars['cxml_from']);
                SessionController::set('cxml_to', $wp->query_vars['cxml_to']);
                SessionController::set('is_cxml', true);
            }

            if (isset($token_response->postprocess->redirect)) {
                // map other URL params sent from magic token link concerning OCI (if any) 
                // and store them in session
                $oci_params = $this->mapUrlParamsForSession($_REQUEST);

                foreach ($oci_params as $name => $value)
                    SessionController::set($name, $value);

                wp_safe_redirect($token_response->postprocess->redirect);
            } else
                wp_safe_redirect(home_url('/'));
        }
    }

    public function cxml_punchout_login_request()
    {
        global $wp;

        if ((isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'cxml_auth') || isset($wp->query_vars['name']) && $wp->query_vars['name'] == 'cxml_auth') {
            if (!defined('PROPELLER_USE_CXML'))
                Propeller::register_behavior();

            if (PROPELLER_USE_CXML) {
                // Make sure this endpoint only handles POST requests
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    header('HTTP/1.1 405 Method Not Allowed');
                    header('Allow: POST');
                    exit('405 Method Not Allowed — this endpoint only accepts POST requests.');
                }

                $rawXml = file_get_contents('php://input'); // get POST XML from external ERP

                // Validate it's at least *probably* XML before parsing
                if (strpos(trim($rawXml), '<?xml') !== 0) {
                    error_log("Received data is not XML");
                    http_response_code(400);
                    exit("Invalid XML");
                }

                $contact_ids = [];
                if (defined('PROPELLER_CXML_CONTACT_ID') && !empty(PROPELLER_CXML_CONTACT_ID)) {
                    $tmp_contacts = explode(',', PROPELLER_CXML_CONTACT_ID);

                    foreach ($tmp_contacts as $tmp_contact) {
                        if (!empty(trim($tmp_contact)))
                            $contact_ids[] = intval(trim($tmp_contact));
                    }
                }

                $users = $this->get_contacts([
                    'contactIds' => $contact_ids
                ]);

                $xml_data = $this->handlePunchOutSetupRequest($rawXml, $users);

                header('Content-Type: text/xml; charset=UTF-8');
                http_response_code(200);

                apply_filters('propel_cxml_setup_request', $xml_data);

                // echo $response;

                exit;
            }
        }
    }

    public function create_magic_token($user = null, $one_time = true)
    {
        if (!$user)
            return null;

        $type = 'magicTokenCreate';

        $token_input = [
            'oneTimeUse' => $one_time
        ];

        if ($one_time) {
            $expiresAt = new DateTime('now', new DateTimeZone('UTC'));
            $expiresAt->add(new DateInterval('P1D'));
            $token_input['expiresAt'] = $expiresAt->format('Y-m-d\TH:i:s.000\Z');
        }

        $token_input['contactId'] = $user->userId;

        $gql = $this->model->create_magic_token([
            'magic_token_input' => $token_input
        ]);

        $magic_token_data = $this->query($gql, $type);

        return $magic_token_data;
    }

    public function get_login_redirect($userData, $referrer)
    {
        if (SessionController::has('login_referrer'))
            return SessionController::get('login_referrer');

        $default_lang = $this->get_default_lang();
        $user_lang = $userData->primaryLanguage;

        $slugs = $this->get_slugs();

        $found = array_filter($slugs, function ($slug) use ($user_lang) {
            return $slug == strtolower($user_lang);
        });

        if (count($found)) {
            $found_locale = array_keys($found)[0];

            if ($default_lang == $found_locale)
                return $referrer != '' ? $referrer : home_url();
            else {
                if (!class_exists('TRP_Translate_Press'))
                    return $referrer != '' ? $referrer : home_url();
                else {
                    $trp = \TRP_Translate_Press::get_trp_instance();
                    $url_converter = $trp->get_component('url_converter');

                    $url = $referrer != '' ? $referrer : home_url();

                    return esc_url($url_converter->get_url_for_language($found_locale, $url, ''));
                }
            }
        } else
            return $referrer != '' ? $referrer : home_url();
    }

    protected function postprocess_login()
    {
        // $this->ShoppingCart->change_order_type(OrderType::REGULAR, false);

        // set user specific prices upon login
        SessionController::set(PROPELLER_SPECIFIC_PRICES, PROPELLER_DEFAULT_INCL_VAT);

        // is default delivery address changed
        SessionController::set(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED, false);

        // preserve order status (NEW, REQUEST, etc)
        SessionController::set(PROPELLER_ORDER_STATUS_TYPE, OrderStatus::ORDER_STATUS_NEW);

        // get user's favorite lists
        if (!SessionController::has(PROPELLER_USER_FAV_LISTS)) {
            $favorites_obj = new FavoriteController();
            SessionController::set(PROPELLER_USER_FAV_LISTS, $favorites_obj->get_favorites_lists());
        }
        // delete menu transient/s
        // CacheController::delete(CacheController::PROPELLER_MENU_TRANSIENT);
    }

    protected function propeller_wp_login($userData)
    {
        $email = $userData->email;
        $response = [];

        $user = get_user_by("email", $email);
        if (!$user)
            $user = get_user_by("login", $email);

        if (!$user) {
            $user_name = explode('@', $email)[0];
            $userdata = array(
                'user_login' => $email,
                'user_pass'  => wp_generate_password(10, false),
                'user_email' => $email,
                'user_nicename' => $userData->firstName . ' ' . $userData->lastName,
                'user_name' => $user_name,
                'first_name' => $userData->firstName
            );

            $user_id = wp_insert_user($userdata);

            if (!is_wp_error($user_id))
                $user = get_user_by("email", $email);
        }

        if (!is_wp_error($user)) {
            wp_clear_auth_cookie();
            wp_set_current_user($user->ID); // Set the current user detail
            wp_set_auth_cookie($user->ID); // Set auth details in cookie
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }

        return $response['success'];
    }

    public function is_access_token_valid()
    {
        if (SessionController::has(PROPELLER_EXPIRATION_TIME)) {
            $expiration_date = strtotime(SessionController::get(PROPELLER_EXPIRATION_TIME));
            $now_date = time();

            $diff = $now_date < $expiration_date;

            return $diff;
        }

        return false;
    }

    public function refresh_access_token()
    {
        if (!$this->is_access_token_valid() && SessionController::has(PROPELLER_REFRESH_TOKEN)) {
            $refreshData = $this->AuthController->refresh();

            if (is_object($refreshData)) {
                $this->postprocess_refresh_token($refreshData);
            }
        }
    }

    public static function get_type()
    {
        return UserController::user()->get_type();
    }

    public function logout($redirect = true)
    {
        SessionController::remove_session_cookie();

        $this->remove_cookie(PROPELLER_USER_SESSION);

        if (PROPELLER_USER_TRACK_ATTR != '') {
            $track_attrs = explode(',', PROPELLER_USER_TRACK_ATTR);

            foreach ($track_attrs as $track_attr) {
                SessionController::remove($track_attr);
            }
        } else {
            SessionController::remove(PROPELLER_USER_ATTR_VALUE);
        }

        SessionController::regenerate_id(true);

        SessionController::end();

        if ($redirect) {
            wp_safe_redirect($this->logout_redirect());

            die;
        }
    }

    public function logout_redirect()
    {
        $redirect_url = home_url();

        if (PROPELLER_WP_CLOSED_PORTAL)
            $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE));

        return $redirect_url;
    }

    public function register_user($args)
    {
        $registration_response = null;

        switch ($args['user_type']) {
            case UserTypes::CONTACT:
                $registration_response = $this->register_contact($args);

                break;
            case UserTypes::CUSTOMER:
                $registration_response = $this->register_customer($args);

                break;
            default:
                $registration_response = $this->register_contact($args);

                break;
        }

        $this->response = new stdClass();

        $postprocess = new stdClass();
        $postprocess->message = "";

        if (is_object($registration_response) && isset($registration_response->session) && $registration_response->session !== null) {
            $registration_response->session->isAnonymous = false;

            $this->postprocess_sesion($registration_response);

            $postprocess->message .= __('Registration successful', 'propeller-ecommerce-v2');

            $user_data = $args['user_type'] == UserTypes::CONTACT
                ? $registration_response->contact
                : $registration_response->customer;

            // Preserve addresses
            $addressController = new AddressController();
            $addressController->set_user_data($user_data);
            $addressController->set_user_type($user_data->__typename);
            $addressController->set_is_registration(true);

            // Check what can be filled in from the user data
            if (!isset($args['invoice_address']['gender']))
                $args['invoice_address']['gender'] = $args['gender'];
            if (!isset($args['invoice_address']['email']))
                $args['invoice_address']['email'] = $args['email'];
            if (!isset($args['invoice_address']['phone']))
                $args['invoice_address']['phone'] = $args['phone'];
            if (!isset($args['invoice_address']['firstName']))
                $args['invoice_address']['firstName'] = $args['firstName'];
            if (!isset($args['invoice_address']['lastName']))
                $args['invoice_address']['lastName'] = $args['lastName'];
            if (!isset($args['invoice_address']['middleName']))
                $args['invoice_address']['middleName'] = $args['middleName'];
            $args['invoice_address']['isDefault'] = 'Y';

            $address_user_id = $user_data->__typename == UserTypes::CUSTOMER
                ? $user_data->userId
                : $user_data->company->companyId;

            $addressResult = $addressController->add_address($args['invoice_address'], $address_user_id);
            if (!is_object($addressResult))
                $postprocess->message .= '<br />' . __('Failed to create invoice address.', 'propeller-ecommerce-v2');

            if (isset($args['save_delivery_address'])) {
                $args['invoice_address']['type'] = AddressType::DELIVERY;
                $addressResult = $addressController->add_address($args['invoice_address'], $address_user_id);

                if (!is_object($addressResult))
                    $postprocess->message .= '<br />' . __('Failed to create delivery address.', 'propeller-ecommerce-v2');
            } else {
                if (!isset($args['delivery_address']['gender']))
                    $args['delivery_address']['gender'] = $args['gender'];
                if (!isset($args['delivery_address']['email']))
                    $args['delivery_address']['email'] = $args['email'];
                if (!isset($args['delivery_address']['phone']))
                    $args['delivery_address']['phone'] = $args['phone'];
                if (!isset($args['delivery_address']['firstName']))
                    $args['delivery_address']['firstName'] = $args['firstName'];
                if (!isset($args['delivery_address']['lastName']))
                    $args['delivery_address']['lastName'] = $args['lastName'];
                if (!isset($args['delivery_address']['middleName']))
                    $args['delivery_address']['middleName'] = $args['middleName'];
                $args['delivery_address']['isDefault'] = 'Y';

                $addressResult = $addressController->add_address($args['delivery_address'], $address_user_id);

                if (!is_object($addressResult))
                    $postprocess->message .= '<br />' . __('Failed to create delivery address.', 'propeller-ecommerce-v2');
            }

            do_action('propeller_after_user_register', $user_data);

            $postprocess->is_registered = true;
            $redirect_url = $this->buildUrl('', PageController::get_slug(PageType::LOGIN_PAGE));

            if (isset($args['referrer']))
                $redirect_url = $args['referrer'];
            else if (SessionController::has('register_referrer'))
                $redirect_url = SessionController::get('register_referrer');

            $postprocess->redirect = esc_url_raw($redirect_url);

            $this->send_registration_email($user_data);

            $postprocess->error = false;

            SessionController::remove('register_referrer');

            if ($this->analytics) {
                $this->analytics->setData((object) ['method' => 'standard']);

                ob_start();
                apply_filters('propel_ga4_fire_event', 'sign_up');
                $postprocess->analytics = ob_get_clean();
            }
        } else {
            $postprocess->error = true;
            $postprocess->message .= __('We were unable to process your registration data. Please try again later or contact us for assistance.', 'propeller-ecommerce-v2');
        }


        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    private function postprocess_registration($registration_data) {}

    private function register_contact($args)
    {
        $type = 'contactRegister';

        $companyController = new CompanyController();

        $company_data = [
            'name' => $args['company_name'],
            'taxNumber' => strval($args['taxNumber']),
            'cocNumber' => strval($args['cocNumber']),
            'parentId' => $args['parentId']
        ];

        $company_response = $companyController->create($company_data);

        if (is_object($company_response))
            $args['parentId'] = $company_response->companyId;
        else
            return $company_response;

        $params = [];

        $params['firstName'] = strval($args['firstName']);
        $params['middleName'] = strval($args['middleName']);
        $params['lastName'] = strval($args['lastName']);
        $params['primaryLanguage'] = PROPELLER_LANG;
        $params['gender'] = isset($args['gender']) ? $args['gender'] : 'U';

        if (isset($args['company']) && !empty($args['company']))
            $params['company'] = strval($args['company']);

        if (isset($args['email']) && !empty($args['email']))
            $params['email'] = strval(strtolower($args['email']));

        if (isset($args['homepage']) && !empty($args['homepage']))
            $params['homepage'] = strval($args['homepage']);

        // if (isset($args['cocNumber']) && !empty($args['cocNumber'])) 
        //     $params['cocNumber'] = $args['cocNumber'];

        if (isset($args['phone']) && !empty($args['phone']))
            $params['phone'] = strval($args['phone']);

        if (isset($args['mobile']) && !empty($args['mobile']))
            $params['mobile'] = strval($args['mobile']);

        if (isset($args['dateOfBirth']) && !empty($args['dateOfBirth']))
            $params['dateOfBirth'] = strval($args['dateOfBirth']);

        if (isset($args['password']) && !empty($args['password']))
            $params['password'] = strval($args['password']);

        $params['parentId'] = isset($args['parentId']) ? $args['parentId'] : 0;

        $gql = $this->model->contact_create($params);

        $userData = $this->query($gql, $type);

        if (is_object($userData))
            $userData->company = $company_response;

        return new User($userData);
    }

    private function register_customer($args)
    {
        $type = 'customerRegister';

        $params = [];

        $params['firstName'] = strval($args['firstName']);
        $params['middleName'] = strval($args['middleName']);
        $params['lastName'] = strval($args['lastName']);
        $params['primaryLanguage'] = PROPELLER_LANG;
        $params['gender'] = isset($args['gender']) ? $args['gender'] : 'U';

        if (isset($args['company']) && !empty($args['company']))
            $params['company'] = strval($args['company']);

        if (isset($args['email']) && !empty($args['email']))
            $params['email'] = strval(strtolower($args['email']));

        if (isset($args['homepage']) && !empty($args['homepage']))
            $params['homepage'] = strval($args['homepage']);

        // if (isset($args['cocNumber']) && !empty($args['cocNumber'])) 
        //     $params['cocNumber'] = $args['cocNumber'];

        if (isset($args['phone']) && !empty($args['phone']))
            $params['phone'] = strval($args['phone']);

        if (isset($args['mobile']) && !empty($args['mobile']))
            $params['mobile'] = strval($args['mobile']);

        if (isset($args['dateOfBirth']) && !empty($args['dateOfBirth']))
            $params['dateOfBirth'] = strval($args['dateOfBirth']);

        if (isset($args['password']) && !empty($args['password']))
            $params['password'] = strval($args['password']);

        // $params['parentId'] = isset($args['parentId']) ? $args['parentId'] : 0;

        $gql = $this->model->customer_create($params);

        $user_data = $this->query($gql, $type);

        return new User($user_data);
    }

    public function forgot_password($args)
    {
        $type = 'triggerPasswordSendResetEmailEvent';

        $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE) . '/');
        // $redirect_url = 'https://playground2.dev.wp-propel.com/login/';

        $params = [
            'email' => $args['user_mail'],
            'redirectUrl' => $redirect_url,
            'language' => PROPELLER_LANG,
            'channelId' => PROPELLER_SITE_ID
        ];

        $gql = $this->model->trigger_password_reset($params);

        $reset_link_response = $this->query($gql, $type, true);

        $response = new stdClass();
        $response->postprocess = new stdClass();
        $response->email_sent = $reset_link_response;
        $response->postprocess->message = __("If this email address is known to us we will send you a password reset email.", "propeller-ecommerce-v2");
        $response->postprocess->error = false;

        return $response;
    }

    public function trigger_password_init($args)
    {
        $type = 'triggerPasswordSendInitEmailEvent';

        $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE) . '/');
        // $redirect_url = 'https://playground2.dev.wp-propel.com/login/';

        $params = [
            'email' => $args['user_mail'],
            'redirectUrl' => $redirect_url,
            'language' => PROPELLER_LANG
        ];

        $gql = $this->model->trigger_password_init($params);

        $reset_link_response = $this->query($gql, $type, true);

        $response = new stdClass();
        $response->postprocess = new stdClass();
        $response->email_sent = $reset_link_response;
        $response->postprocess->message = __("If this email address is known to us we will send you a password reset email.", "propeller-ecommerce-v2");
        $response->postprocess->error = false;

        return $response;
    }

    private function send_registration_email($user_data)
    {
        if ($user_data->__typename == UserTypes::CUSTOMER)
            $this->send_customer_welcome([
                'customerId' => intval($user_data->userId),
                'language' => PROPELLER_LANG,
                'channelId' => PROPELLER_SITE_ID
            ]);
        else
            $this->send_contact_welcome([
                'contactId' => intval($user_data->userId),
                'language' => PROPELLER_LANG,
                'channelId' => PROPELLER_SITE_ID
            ]);

        /*
        $login_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE) . '/');

        $cc = !empty(PROPELLER_CC_EMAIL) ? PROPELLER_CC_EMAIL : get_bloginfo('admin_email');
        $bcc = !empty(PROPELLER_BCC_EMAIL) ? PROPELLER_BCC_EMAIL : get_bloginfo('admin_email');
        
        $subject = sprintf(__('Welcome to %s', 'propeller-ecommerce-v2'), get_bloginfo('name'));

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
            'Cc: ' . $cc,
            'Bcc: ' . $bcc,
            'X-Priority: 1',
            'X-Mailer: PHP/' . PHP_VERSION
        ];

        ob_start();

        require $this->load_template('emails', '/propeller-registration-template.php');

        $body = ob_get_clean();
        ob_end_clean();

        wp_mail($user_data->email, $subject, $body, implode("\r\n", $headers));
        */
    }

    public function send_customer_welcome($args)
    {
        $type = 'triggerCustomerSendWelcomeEmailEvent';

        $gql = $this->model->trigger_customer_welcome($args);

        return $this->query($gql, $type);
    }

    public function send_contact_welcome($args)
    {
        $type = 'triggerContactSendWelcomeEmailEvent';

        $gql = $this->model->trigger_contact_welcome($args);

        return $this->query($gql, $type);
    }

    public static function is_propeller_logged_in()
    {
        $user = new User();

        return $user->is_propeller_logged_in();
    }

    public function get_viewer()
    {
        $type = 'viewer';

        $gql = $this->model->viewer();

        $userData = $this->query($gql, $type);

        if (is_object($userData)) {
            $this->postprocess_user($userData);

            return new User($userData);
        }

        return null;
    }

    public function get_user($userId = null)
    {
        if (SessionController::has(PROPELLER_USER_DATA) && !$userId)
            return new User(SessionController::get(PROPELLER_USER_DATA));

        $type = 'user';

        $gql = $this->model->get_user_data(['user_id' => $userId ? $userId : SessionController::get(PROPELLER_USER_DATA)->userId]);

        $userData = $this->query($gql, $type);

        if (is_object($userData)) {
            // $this->postprocess_user($userData);

            return new User($userData);
        }

        return null;
    }

    public function get_contacts($args = [])
    {
        $type = 'contacts';

        $gql = $this->model->get_contacts($args);

        $usersData = $this->query($gql, $type);

        if (is_object($usersData) && isset($usersData->itemsFound) && $usersData->itemsFound > 0) {
            $contacts = [];

            foreach ($usersData->items as $userData) {
                $contacts[] = new User($userData);
            }

            return $contacts;
        }

        return [];
    }

    public function create_purchase_authorization_config($args)
    {
        $type = 'purchaseAuthorizationConfigCreate';

        $return = new stdClass();
        $return->success = false;
        $return->message = __('Failed to create an authorization settings for this contact', 'propeller-ecommerce-v2');

        $user = $this->get_user();

        if (!$user->is_authorization_manager()) {
            $return->message = __('You don\'t have sufficient rights to create an authorization settings for this contact', 'propeller-ecommerce-v2');
            return $return;
        }

        $data = [
            'contactId' => intval($args['contact_id']),
            'companyId' => intval($user->get_current_company()->companyId),
            'purchaseRole' => $args['purchase_authorization_role'],
        ];

        if ($args['purchase_authorization_role'] == PurchaseAuthorizationRoles::PURCHASER)
            $data['authorizationLimit'] = floatval($args['limit']);

        $gql = $this->model->create_purchase_authorization_config($data);

        $response = $this->query($gql, $type);

        if (is_object($response) && isset($response->id)) {
            $return->success = true;
            $return->message = __('Authorization settings successfully created', 'propeller-ecommerce-v2');

            $purchase_authorizations_contacts = $this->get_purchase_authorization_contacts($page = 1, $offset = 12);

            ob_start();

            apply_filters('propel_account_purchase_authorizations_contacts_table_list', $purchase_authorizations_contacts->contacts->items, $purchase_authorizations_contacts->contacts, $this);

            $return->content = ob_get_clean();

            ob_end_clean();
        }

        return $return;
    }

    public function update_purchase_authorization_config($args)
    {
        $type = 'purchaseAuthorizationConfigUpdate';

        $return = new stdClass();
        $return->success = false;
        $return->message = __('Failed to update an authorization settings for this contact', 'propeller-ecommerce-v2');

        $user = $this->get_user();

        if (!$user->is_authorization_manager()) {
            $return->message = __('You don\'t have sufficient rights to update an authorization settings for this contact', 'propeller-ecommerce-v2');
            return $return;
        }

        $data = [
            'id' => $args['purchase_autorization_id'],
            'purchaseRole' => $args['purchase_authorization_role'],
        ];

        if ($args['purchase_authorization_role'] == PurchaseAuthorizationRoles::PURCHASER)
            $data['authorizationLimit'] = floatval($args['limit']);

        $gql = $this->model->update_purchase_authorization_config($data);

        $response = $this->query($gql, $type);

        if (is_object($response) && isset($response->id)) {
            $return->success = true;
            $return->message = __('Authorization settings successfully updated', 'propeller-ecommerce-v2');

            $purchase_authorizations_contacts = $this->get_purchase_authorization_contacts($page = 1, $offset = 12);

            ob_start();

            apply_filters('propel_account_purchase_authorizations_contacts_table_list', $purchase_authorizations_contacts->contacts->items, $purchase_authorizations_contacts->contacts, $this);

            $return->content = ob_get_clean();

            ob_end_clean();
        }

        return $return;
    }

    public function delete_purchase_authorization_config($args)
    {
        $type = 'purchaseAuthorizationConfigDelete';

        $return = new stdClass();
        $return->success = false;
        $return->message = __('Failed to delete an authorization settings for this contact', 'propeller-ecommerce-v2');

        $user = $this->get_user();

        if (!$user->is_authorization_manager()) {
            $return->message = __('You don\'t have sufficient rights to delete an authorization settings for this contact', 'propeller-ecommerce-v2');
            return $return;
        }

        $gql = $this->model->delete_purchase_authorization_config([
            'id' => $args['purchase_autorization_id']
        ]);

        $response = $this->query($gql, $type);

        if (is_bool($response) && $response) {
            $return->success = true;
            $return->message = __('Authorization settings successfully deleted', 'propeller-ecommerce-v2');

            $purchase_authorizations_contacts = $this->get_purchase_authorization_contacts($page = 1, $offset = 12);

            ob_start();

            apply_filters('propel_account_purchase_authorizations_contacts_table_list', $purchase_authorizations_contacts->contacts->items, $purchase_authorizations_contacts->contacts, $this);

            $return->content = ob_get_clean();

            ob_end_clean();
        }

        return $return;
    }

    public function get_purchase_authorization_contacts($page = 1, $offset = 12)
    {
        $contacts = null;
        $type = 'company';

        $user = $this->get_user();

        if (!$user->is_authorization_manager())
            return $contacts;

        $gql = $this->model->purchase_authorizations_contacts(
            $user->get_current_company()->companyId,
            ['companyIds' => [$user->get_current_company()->companyId]],
            ['page' => $page, 'offset' => $offset]
        );

        // var_dump($gql->query);
        // var_dump(json_encode($gql->variables));

        $contacts = $this->query($gql, $type);

        return $contacts;
    }

    public function get_purchase_authorizations()
    {
        $purchase_authorizations = null;

        $user = $this->get_user();

        if (!$user->is_authorization_manager())
            return $purchase_authorizations;

        $purchase_authorizations = $this->ShoppingCart->purchase_authorizations([
            'companyIds' => [$user->get_current_company()->companyId],
            'statuses' => [CartStatus::PENDING_PURCHASE_AUTHORIZATION]
        ]);

        return $purchase_authorizations;
    }

    public function preview_authorization_request($cart_id)
    {
        $cart_content = $this->ShoppingCart->get_user_cart($cart_id);

        foreach ($cart_content->items as $item) {
            $product = new Product($item->product);

            if (is_object($item->product->cluster))
                $product->cluster = new Cluster($item->product->cluster, false);

            $child_items = [];

            if ($item->childItems && count($item->childItems)) {
                foreach ($item->childItems as $child_item) {
                    $child_item->product = new Product($child_item->product);

                    $child_items[] = $child_item;
                }

                $item->childItems = $child_items;
            }

            if (!empty($item->product->crossupsells)) {
                $crossupsells = [];

                foreach ($item->product->crossupsells as $crossupsell) {
                    $crossupsell_product = new Product($crossupsell->product);

                    $crossupsell->product = $crossupsell_product;
                    $crossupsells[] = $crossupsell;
                }

                $item->product->crossupsells = $crossupsells;
            }

            $item->product = $product;
            $items[] = $item;
        }

        $response = new stdClass();

        ob_start();
        $cart_contents = apply_filters('propel_account_purchase_authorization_preview', $cart_content, $this);

        $response->content = ob_get_clean();
        $response->cart_id = $cart_id;

        $response->success = true;

        return $response;
    }

    public function delete_authorization_request($cart_id)
    {
        $cart_response = $this->ShoppingCart->delete_cart($cart_id);

        $response = new stdClass();
        $response->cart_id = $cart_id;
        $response->success = $cart_response;

        return $response;
    }

    public function accept_authorization_request($cart_id)
    {
        SessionController::set(PROPELLER_AUTHORIZER_CART_ID, SessionController::get(PROPELLER_CART)->cartId);

        $cart_response = $this->ShoppingCart->accept_purchase_authorization_request($cart_id, self::user()->userId);

        $response = new stdClass();
        $response->success = $cart_response;
        $response->message = __('There was an error accepting this authorization request.', 'propeller-ecommerce-v2');

        if ($cart_response) {
            $response->message = __("Authorization request taken over", 'propeller-ecommerce-v2');
            $response->redirect = $this->buildUrl('/' . PageController::get_slug(PageType::SHOPPING_CART_PAGE) . '/', '');
        }

        return $response;
    }

    public static function user($userId = null)
    {
        $obj = new self();

        return $obj->get_user($userId);
    }

    public function get_addresses()
    {
        return $this->get_user() ? $this->get_user()->get_addresses() : [];
    }

    public function get_default_address($address_type)
    {
        $addresses = $this->get_addresses();

        $found = array_filter($addresses, function ($obj) use ($address_type) {
            return $obj->isDefault == 'Y' && $obj->type == $address_type;
        });

        if (count($found))
            return current($found);

        return null;
    }

    public function company_switch($companyId)
    {
        SessionController::set(PROPELLER_CONTACT_COMPANY_ID, intval($companyId));
        SessionController::remove(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED);

        $found = array_filter(SessionController::get(PROPELLER_USER_DATA)->companies->items, function ($cmp) {
            return $cmp->companyId == SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
        });

        SessionController::set(PROPELLER_CONTACT_COMPANY_NAME, current($found)->name);

        $this->switch_attributes(current($found)->trackAttributes);

        $this->clear_menu();

        if (SessionController::has('reinitialize_cart') && SessionController::get('reinitialize_cart'))
            SessionController::remove('reinitialize_cart');

        $this->ShoppingCart->init_user_cart();

        $return = new stdClass();
        $return->success = true;
        $return->reload = true;

        return $return;
    }

    public function add_contact_to_company($args)
    {
        $type = 'contactRegister';

        $return = new stdClass();
        $return->success = false;
        $return->message = __('Failed to add contact to this company', 'propeller-ecommerce-v2');
        $return->object = 'User';
        $return->postprocess = new stdClass();
        $return->postprocess->reload = false;
        $return->postprocess->success = false;
        $return->postprocess->message = __('Failed to add contact to this company', 'propeller-ecommerce-v2');
        $user = $this->get_user();

        if (!PROPELLER_PAC_ADD_CONTACTS || !$user->is_authorization_manager()) {
            $return->message = __('You don\'t have sufficient rights to add a contact to this company', 'propeller-ecommerce-v2');
            return $return;
        }

        $params = [];
        $params['parentId'] = intval($args['parentId']);
        $params['firstName'] = strval($args['firstName']);
        $params['middleName'] = strval($args['middleName']);
        $params['lastName'] = strval($args['lastName']);
        $params['primaryLanguage'] = PROPELLER_LANG;
        $params['gender'] = isset($args['gender']) ? $args['gender'] : 'U';
        $params['email'] = strval(strtolower($args['email']));

        if (isset($args['phone']) && !empty($args['phone']))
            $params['phone'] = strval($args['phone']);

        $params['password'] = PropellerHelper::random_string();

        $gql = $this->model->contact_create($params);

        $response = $this->query($gql, $type);

        if (is_object($response)) {
            // $this->trigger_password_init(['user_mail' => $params['email']]);
            $this->omit_access_token(true);

            $this->forgot_password(['user_mail' => $params['email']]);

            $this->omit_access_token(false);

            $return->success = true;
            $return->message = __('Contact successfully added to the company', 'propeller-ecommerce-v2');
            $return->reload = true;

            $return->postprocess->reload = true;
            $return->postprocess->success = true;
            $return->postprocess->message = __('Contact successfully added to the company', 'propeller-ecommerce-v2');
        }

        return $return;
    }

    public function create_contact_login($args)
    {
        $authController = new AuthController();

        $return = new stdClass();
        $return->success = false;
        $return->message = __('Failed to create contact authentication', 'propeller-ecommerce-v2');
        $return->object = 'User';
        $return->postprocess = new stdClass();
        $return->postprocess->reload = false;
        $return->postprocess->success = false;
        $return->postprocess->message = __('Failed to create contact authentication', 'propeller-ecommerce-v2');

        $gql = $this->model->create_contact_account([
            'id' => $args['contact_id'],
            'input' => [
                'password' => PropellerHelper::random_string()
            ]
        ]);

        $response = $this->query($gql, 'contactCreateAccount');

        if (is_object($response)) {
            $this->omit_access_token(true);

            $this->forgot_password(['user_mail' => $args['email']]);

            $this->omit_access_token(false);

            $return->success = true;
            $return->message = __('Contact successfully authenticated', 'propeller-ecommerce-v2');
            $return->reload = true;

            $return->postprocess->reload = true;
            $return->postprocess->success = true;
            $return->postprocess->message = __('Contact successfully authenticated', 'propeller-ecommerce-v2');
        }

        return $return;
    }

    public function delete_contact_login($args)
    {
        $authController = new AuthController();

        $return = new stdClass();
        $return->success = false;
        $return->message = __('Failed to delete contact authentication', 'propeller-ecommerce-v2');
        $return->object = 'User';
        $return->postprocess = new stdClass();
        $return->postprocess->reload = false;
        $return->postprocess->success = false;
        $return->postprocess->message = __('Failed to delete contact authentication', 'propeller-ecommerce-v2');

        $gql = $this->model->delete_contact_account([
            'id' => $args['contact_id']
        ]);

        $response = $this->query($gql, 'contactDeleteAccount');

        if ($response === true) {
            $return->success = true;
            $return->message = __('Contact successfully deleted', 'propeller-ecommerce-v2');
            $return->reload = true;

            $return->postprocess->reload = true;
            $return->postprocess->success = true;
            $return->postprocess->message = __('Contact successfully deleted', 'propeller-ecommerce-v2');
        }

        return $return;
    }

    public function assign_to_pricesheet($args)
    {
        $return = new stdClass();
        $return->success = false;
        $return->message = __('Failed to assign user to pricesheet', 'propeller-ecommerce-v2');
        $return->object = 'User';
        $return->postprocess = new stdClass();
        $return->postprocess->reload = false;
        $return->postprocess->success = false;
        $return->postprocess->message = __('Failed to assign user to pricesheet', 'propeller-ecommerce-v2');

        $gql = $this->model->assign_to_pricesheet($args);

        $response = $this->query($gql, 'pricesheetAssign');

        if (is_object($response) && isset($response->id)) {
            $return->success = true;
            $return->message = __('Contact successfully assigned to pricesheet', 'propeller-ecommerce-v2');
            $return->reload = true;

            $return->postprocess->reload = true;
            $return->postprocess->success = true;
            $return->postprocess->message = __('Contact successfully assigned to pricesheet', 'propeller-ecommerce-v2');
        }

        return $return;
    }

    protected function switch_attributes($company_attributes)
    {
        $user_track_attrs = [];
        $company_track_attrs = [];

        if (defined('PROPELLER_USER_TRACK_ATTR') && !empty(PROPELLER_USER_TRACK_ATTR))
            $user_track_attrs = explode(',', PROPELLER_USER_TRACK_ATTR);

        if (defined('PROPELLER_COMPANY_TRACK_ATTR') && !empty(PROPELLER_COMPANY_TRACK_ATTR))
            $company_track_attrs = explode(',', PROPELLER_COMPANY_TRACK_ATTR);

        foreach ($company_track_attrs as $company_attr) {
            $found_in_user = [];

            if (isset(SessionController::get(PROPELLER_USER_DATA)->trackAttributes) && SessionController::get(PROPELLER_USER_DATA)->trackAttributes->itemsFound > 0) {
                $found_in_user = array_filter(SessionController::get(PROPELLER_USER_DATA)->trackAttributes->items, function ($obj) use ($company_attr) {
                    return $obj->attributeDescription->name == $company_attr;
                });
            }

            if (!in_array($company_attr, $user_track_attrs) || !count($found_in_user))
                SessionController::remove($company_attr);
        }

        $this->process_company_attributes($company_attributes);
    }

    public static function set_default_tax_zone()
    {
        if (!defined('PROPELLER_DEFAULT_TAXZONE')) {
            if (!UserController::is_propeller_logged_in()) {
                define('PROPELLER_DEFAULT_TAXZONE', PROPELLER_ICP_COUNTRY);
            } else {
                $address_controller = new AddressController();
                $address_controller->set_user(SessionController::get(PROPELLER_USER_DATA));

                $default_delivery_address = $address_controller->get_session_address(AddressType::DELIVERY);

                if ($default_delivery_address)
                    define('PROPELLER_DEFAULT_TAXZONE', $default_delivery_address->country);
                else
                    define('PROPELLER_DEFAULT_TAXZONE', PROPELLER_ICP_COUNTRY);
            }
        }
    }

    public static function get_company()
    {
        $user_data = new User(SessionController::get(PROPELLER_USER_DATA));

        $companies = $user_data->companies->items;
        $default_company = $user_data->company;

        if (sizeof($companies) > 1 && SessionController::has(PROPELLER_CONTACT_COMPANY_ID) && SessionController::get(PROPELLER_CONTACT_COMPANY_ID)) {
            $found = array_filter($companies, function ($com) {
                return $com->companyId == SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
            });

            return current($found);
        } else
            return $default_company;
    }

    public static function is_contact()
    {
        return SessionController::get(PROPELLER_USER_DATA)->__typename == UserTypes::CONTACT;
    }

    public static function is_customer()
    {
        return SessionController::get(PROPELLER_USER_DATA)->__typename == UserTypes::CUSTOMER;
    }

    protected function process_attributes($attributes)
    {
        if (defined('PROPELLER_USER_TRACK_ATTR') && !empty(PROPELLER_USER_TRACK_ATTR)) {
            $track_attrs = explode(',', PROPELLER_USER_TRACK_ATTR);

            foreach ($track_attrs as $track_attr) {
                $found = array_filter($attributes->items, function ($obj) use ($track_attr) {
                    return $obj->attributeDescription->name == $track_attr;
                });

                if (count($found)) {
                    $attribute = new ObjectAttribute(current($found));

                    SessionController::set($track_attr, $attribute->get_value());
                }
            }
        }
    }

    public function process_company_attributes($attributes)
    {
        // Check if attributes and items exist before processing
        if (!$attributes || !isset($attributes->items) || !is_array($attributes->items)) {
            return;
        }

        if (defined('PROPELLER_COMPANY_TRACK_ATTR') && !empty(PROPELLER_COMPANY_TRACK_ATTR)) {
            $track_attrs = explode(',', PROPELLER_COMPANY_TRACK_ATTR);

            foreach ($track_attrs as $track_attr) {
                $found = array_filter($attributes->items, function ($obj) use ($track_attr) {
                    return $obj->attributeDescription->name == $track_attr;
                    return isset($obj->attributeDescription->name) && $obj->attributeDescription->name == $track_attr;
                });

                if (count($found)) {
                    $attribute = new ObjectAttribute(current($found));

                    /* 
                        skip company attributes with same name as contact attributes.
                        since contact is on the lowest level in the hierarchy, contact attributes
                        has greater priority
                    */
                    if (!SessionController::has($track_attr))
                        SessionController::set($track_attr, $attribute->get_value());
                }
            }
        }
    }

    public function user_prices($user_specific_prices)
    {
        SessionController::set(PROPELLER_SPECIFIC_PRICES, $user_specific_prices == 1 ? true : false);

        $response = new stdClass();
        $response->success = true;
        $response->reload = true;

        return $response;
    }

    protected function postprocess_user($user)
    {
        SessionController::set(PROPELLER_USER_ID, $user->userId);
        SessionController::set(PROPELLER_USER_DATA, $user);

        if ($user->__typename == UserTypes::CONTACT) {
            SessionController::set(PROPELLER_CONTACT_COMPANY_ID, $user->company->companyId);
            SessionController::set(PROPELLER_CONTACT_COMPANY_NAME, $user->company->name);
        }
    }

    protected function postprocess_sesion($session)
    {
        SessionController::set(PROPELLER_SESSION, $session->session);

        if (isset($session->session->accessToken))
            SessionController::set(PROPELLER_ACCESS_TOKEN, $session->session->accessToken);

        if (isset($session->session->refreshToken))
            SessionController::set(PROPELLER_REFRESH_TOKEN, $session->session->refreshToken);

        if (isset($session->session->expirationTime))
            SessionController::set(PROPELLER_EXPIRATION_TIME, $session->session->expirationTime);
    }

    protected function postprocess_refresh_token($refresh)
    {
        SessionController::get(PROPELLER_USER_DATA);

        SessionController::set(PROPELLER_ACCESS_TOKEN, $refresh->access_token);
        SessionController::set(PROPELLER_REFRESH_TOKEN, $refresh->refresh_token);

        // update the expiration datetime
        $now_date = new DateTime('NOW');
        $now_date->add(new DateInterval('PT' . $refresh->expires_in . 'S'));
        SessionController::set(PROPELLER_EXPIRATION_TIME, $now_date->format('c'));
    }
}
