<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Exception;
use Propeller\Includes\Enum\AddressTypeCart;
use Propeller\PropellerHelper;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\AddressType;
use Propeller\Includes\Enum\MediaImagesType;
use Propeller\Includes\Enum\MediaType;
use Propeller\Includes\Enum\OrderStatus;
use Propeller\Includes\Enum\OrderType;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\PaymentStatuses;
use Propeller\Includes\Enum\UserTypes;
use Propeller\Includes\Object\Cluster;
use Propeller\Includes\Object\Product;
use Propeller\Includes\Query\Media;
use Propeller\Includes\Trait\CxmlTrait;
use Propeller\Includes\Trait\OCITrait;
use Propeller\Propeller;
use Propeller\PropellerUtils;
use stdClass;

class ShoppingCartController extends BaseController
{
    use OCITrait;
    use CxmlTrait;

    protected $type = 'cart';
    protected $cart_id;
    protected $cart = null;
    protected $items;
    protected $response;
    protected $object_name = 'Checkout';
    protected $warehouses = null;

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->load_model('shoppingCart');

        add_filter('template_redirect', [$this, 'clear_carts_request']);
        add_filter('template_redirect', [$this, 'oci_results']);
        add_filter('template_redirect', [$this, 'cxml_results']);
    }

    public function oci_results()
    {
        global $wp;

        if ((isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'oci_results') || (isset($wp->query_vars['name']) && $wp->query_vars['name'] == 'oci_results')) {
            var_dump($_REQUEST);
            wp_die();
        }
    }

    public function cxml_results()
    {
        global $wp;

        if ((isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'cxml_results') || (isset($wp->query_vars['name']) && $wp->query_vars['name'] == 'cxml_results')) {
            echo '<pre>';
            echo wp_kses_post(htmlspecialchars_decode(urldecode(stripslashes($_REQUEST['cxml-urlencoded']))));
            echo '</pre>';
            wp_die();
        }
    }

    public function clear_carts_request()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'clear_carts') !== false) {
            $responses = [];

            $responses[] = $this->clear_carts();

            var_dump($responses);

            die;
        }
    }

    public function init_cart($cart_start = false)
    {
        if (SessionController::has(PROPELLER_CART)) {
            if (!$this->check_cart(SessionController::get(PROPELLER_CART)->cartId)) {
                $this->clear_cart(true);
                $this->start();
            } else
                $this->init_postprocess(SessionController::get(PROPELLER_CART));
        } else {
            if (UserController::is_propeller_logged_in() && !SessionController::has(PROPELLER_CART))
                $this->start();

            if (!UserController::is_propeller_logged_in() && $cart_start)
                $this->start();
        }
    }

    /**
     * 
     * Shopping cart filters
     * 
     */
    public function shopping_cart_title($cart)
    {
        require $this->load_template('partials', '/cart/propeller-shopping-cart-title.php');
    }

    public function shopping_cart_info($cart, $obj)
    {
        require $this->load_template('partials', '/cart/propeller-shopping-cart-info.php');
    }

    public function shopping_cart_table_header($cart, $obj)
    {
        require $this->load_template('partials', '/cart/propeller-shopping-cart-table-header.php');
    }

    public function shopping_cart_table_items($cart, $obj)
    {
        require $this->load_template('partials', '/cart/propeller-shopping-cart-table-items.php');
    }

    public function shopping_cart_table_product_item($item, $cart, $obj)
    {
        require $obj->load_template('partials', '/cart/propeller-shopping-cart-product-item.php');
    }

    public function shopping_cart_table_product_item_crossupsells($product, $cart, $obj)
    {
        require $obj->load_template('partials', '/cart/propeller-shopping-cart-product-item-crossupsells.php');
    }

    public function shopping_cart_table_bundle_item($item, $cart, $obj)
    {
        require $obj->load_template('partials', '/cart/propeller-shopping-cart-bundle-item.php');
    }

    public function shopping_cart_bonus_items($cart, $obj)
    {
        require $obj->load_template('partials', '/cart/propeller-shopping-cart-bonus-items.php');
    }

    public function shopping_cart_bonus_items_title($cart, $obj)
    {
        require $obj->load_template('partials', '/cart/propeller-shopping-cart-bonus-items-title.php');
    }

    public function shopping_cart_bonus_item($bonusItem, $cart, $obj)
    {
        require $obj->load_template('partials', '/cart/propeller-shopping-cart-bonus-item.php');
    }

    public function shopping_cart_action_code($cart, $obj)
    {
        require $obj->load_template('partials', '/cart/propeller-shopping-cart-action-code.php');
    }

    public function shopping_cart_voucher($cart, $obj)
    {

        $this->assets()->std_requires_asset('propeller-action-tooltip');

        require $this->load_template('partials', '/cart/propeller-shopping-cart-voucher.php');
    }

    public function shopping_cart_order_type($cart, $obj)
    {
        $order_types = $this->get_order_types();

        if (!SessionController::has(PROPELLER_ORDER_TYPE))
            $this->change_order_type($this->get_order_types()->items[OrderType::REGULAR]->value, false);

        require $this->load_template('partials', '/cart/propeller-shopping-cart-order-type.php');
    }

    public function shopping_cart_totals($cart, $obj)
    {
        require $this->load_template('partials', '/cart/propeller-shopping-cart-totals.php');
    }

    public function shopping_cart_totals_with_items($cart, $obj)
    {
        require $this->load_template('partials', '/cart/propeller-shopping-cart-totals-with-items.php');
    }

    public function shopping_cart_buttons($cart, $obj)
    {
        require $this->load_template('partials', '/cart/propeller-shopping-cart-buttons.php');
    }

    public function shopping_cart_invoice_address_form($invoice_address, $cart, $obj)
    {
        $countries = propel_get_countries();

        require $this->load_template('partials', '/checkout/propeller-cart-invoice-address-form.php');
    }

    public function shopping_cart_delivery_address_form($delivery_address, $cart, $obj)
    {
        $countries = propel_get_countries();

        require $this->load_template('partials', '/checkout/propeller-cart-delivery-address-form.php');
    }

    /*
        Checkout reusable filters  
    */
    public function checkout_step_1_info($cart, $obj)
    {
        $countries = propel_get_countries();

        $invoice_address = $obj->get_invoice_address();

        require $this->load_template('partials', '/checkout/propeller-checkout-step-1-info.php');
    }

    public function checkout_step_2_info($cart, $obj)
    {
        $countries = propel_get_countries();

        $delivery_address = $cart->deliveryAddress;

        require $this->load_template('partials', '/checkout/propeller-checkout-step-2-info.php');
    }

    public function checkout_step_3_info($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-checkout-step-3-info.php');
    }

    public function checkout_invoice_details($cart, $obj)
    {
        $countries = propel_get_countries();
        $can_edit_address = false;

        if (UserController::is_propeller_logged_in()) {
            if (PROPELLER_EDIT_ADDRESSES)
                $can_edit_address = true;
        } else if (PROPELLER_ANONYMOUS_ORDERS) {
            $can_edit_address = true;
        }

        $invoice_address = $cart->invoiceAddress;

        $address_controller = new AddressController();

        $address_controller->set_user();

        if (empty($cart->invoiceAddress->street)) {
            $invoice_address = PROPELLER_ANONYMOUS_ORDERS && !UserController::is_propeller_logged_in()
                ? $address_controller->get_address_obj(AddressType::INVOICE)
                : $address_controller->get_default_address(['type' => AddressType::INVOICE]);
        }

        if (!isset($invoice_address->id) && UserController::is_propeller_logged_in())
            $invoice_address = $this->get_address_with_id(AddressType::INVOICE, $cart->invoiceAddress);

        if (!UserController::is_propeller_logged_in())
            $invoice_address->id = '_guest';

        add_action('wp_footer', function () use ($invoice_address, $cart, $obj) {
            apply_filters('propel_shopping_cart_invoice_address_form', $invoice_address, $cart, $obj);
        });

        require $this->load_template('partials', '/checkout/propeller-checkout-invoice-details.php');
    }

    public function checkout_invoice_addresses($cart, $obj)
    {
        $countries = propel_get_countries();

        $can_edit_address = false;

        if (UserController::is_propeller_logged_in()) {
            if (PROPELLER_EDIT_ADDRESSES)
                $can_edit_address = true;
        } else if (PROPELLER_ANONYMOUS_ORDERS) {
            $can_edit_address = true;
        }

        $invoice_addresses = [];
        $address_controller = new AddressController();

        $address_controller->set_user();

        if (UserController::is_propeller_logged_in()) {
            $invoice_addresses = $address_controller->get_addresses(['type' => AddressType::INVOICE]);
        } else {
            $invoice_addresses[] = !empty($cart->invoiceAddress->street)
                ? $cart->invoiceAddress
                : $address_controller->get_address_obj(AddressType::INVOICE);
        }

        require $this->load_template('partials', '/checkout/propeller-checkout-invoice-addresses.php');
    }

    public function checkout_invoice_address($invoice_address, $cart, $obj)
    {
        $countries = propel_get_countries();

        $checked = '';
        $checked_label = '';

        $address_controller = new AddressController();

        $address_controller->set_user();

        if (!isset($invoice_address->id) && UserController::is_propeller_logged_in())
            $invoice_address = $this->get_address_with_id(AddressType::INVOICE, $cart->invoiceAddress);

        if (UserController::is_propeller_logged_in()) {
            if (SessionController::has(PROPELLER_DEFAULT_INVOICE_ADDRESS_CHANGED) && isset($invoice_address->id) && SessionController::get(PROPELLER_DEFAULT_INVOICE_ADDRESS_CHANGED) == $invoice_address->id) {
                $checked = 'checked="checked"';
                $checked_label = 'selected';
            } else if (isset($invoice_address->isDefault) && $invoice_address->isDefault == 'Y' && !SessionController::has(PROPELLER_DEFAULT_INVOICE_ADDRESS_CHANGED)) {
                $checked = 'checked="checked"';
                $checked_label = 'selected';
            }
        } else if (!UserController::is_propeller_logged_in()) {
            $invoice_address->id = 'anonymous';
            $checked = 'checked="checked"';
            $checked_label = 'selected';
        }

        add_action('wp_footer', function () use ($invoice_address, $cart, $obj) {
            apply_filters('propel_shopping_cart_invoice_address_form', $invoice_address, $cart, $obj);
        });

        require $this->load_template('partials', '/checkout/propeller-checkout-invoice-address.php');
    }

    public function checkout_delivery_addresses($cart, $obj)
    {
        $countries = propel_get_countries();

        $can_edit_address = false;

        if (UserController::is_propeller_logged_in()) {
            if (PROPELLER_EDIT_ADDRESSES)
                $can_edit_address = true;
        } else if (PROPELLER_ANONYMOUS_ORDERS) {
            $can_edit_address = true;
        }

        $delivery_addresses = [];
        $address_controller = new AddressController();

        $address_controller->set_user();

        if (UserController::is_propeller_logged_in()) {
            $delivery_addresses = $address_controller->get_addresses(['type' => AddressType::DELIVERY]);
        } else {
            $delivery_addresses[] = !empty($cart->deliveryAddress->street)
                ? $cart->deliveryAddress
                : $address_controller->get_address_obj(AddressType::DELIVERY);
        }

        require $this->load_template('partials', '/checkout/propeller-checkout-delivery-addresses.php');
    }

    public function checkout_delivery_address($delivery_address, $cart, $obj)
    {
        $countries = propel_get_countries();

        $checked = '';
        $checked_label = '';

        $address_controller = new AddressController();

        $address_controller->set_user();

        if (!isset($delivery_address->id) && UserController::is_propeller_logged_in())
            $delivery_address = $this->get_address_with_id(AddressType::DELIVERY, $cart->deliveryAddress);

        if (UserController::is_propeller_logged_in()) {
            if (SessionController::has(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED) && isset($delivery_address->id) && SessionController::get(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED) == $delivery_address->id) {
                $checked = 'checked="checked"';
                $checked_label = 'selected';
            } else if (isset($delivery_address->isDefault) && $delivery_address->isDefault == 'Y' && !SessionController::has(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED)) {
                $checked = 'checked="checked"';
                $checked_label = 'selected';
            }
        } else if (!UserController::is_propeller_logged_in()) {
            $delivery_address->id = 'anonymous';
            $checked = 'checked="checked"';
            $checked_label = 'selected';
        }

        add_action('wp_footer', function () use ($delivery_address, $cart, $obj) {
            apply_filters('propel_shopping_cart_delivery_address_form', $delivery_address, $cart, $obj);
        });

        require $this->load_template('partials', '/checkout/propeller-checkout-delivery-address.php');
    }

    public function get_address_with_id($address_type, $address_comparison)
    {
        $address_controller = new AddressController();

        $address_controller->set_user();

        $addresses = $address_controller->get_addresses_cart(['type' => $address_type]);

        foreach ($addresses as $addr) {
            $match = true;

            foreach ($address_comparison as $key => $val) {
                if (isset($addr->$key) && $address_comparison->$key != $addr->$key) {
                    $match = false;
                    break;
                }
            }

            if ($match)
                return $addr;
        }

        $address_comparison->id = 'rand_id';
        $address_comparison->isDefault = false;

        return $address_comparison;
    }

    public function checkout_delivery_address_new($cart, $obj)
    {
        $countries = propel_get_countries();

        $address_controller = new AddressController();

        $address_controller->set_user();

        $delivery_address = $address_controller->get_address_obj(AddressTypeCart::DELIVERY);

        if (!UserController::is_propeller_logged_in())
            $delivery_address->id = '_guest';

        add_action('wp_footer', function () use ($delivery_address, $cart, $obj) {
            apply_filters('propel_shopping_cart_delivery_address_form', $delivery_address, $cart, $obj);
        });

        require $this->load_template('partials', '/checkout/propeller-checkout-delivery-address-new.php');
    }

    public function checkout_pickup_locations($cart, $obj)
    {
        $orderconfirm_email = '';

        if (UserController::is_propeller_logged_in())
            $orderconfirm_email = SessionController::get(PROPELLER_SESSION)->email;

        require $this->load_template('partials', '/checkout/propeller-checkout-pickup-locations.php');
    }

    public function checkout_pickup_location($warehouse)
    {
        $countries = propel_get_countries();

        require $this->load_template('partials', '/checkout/propeller-checkout-pickup-location.php');
    }

    public function checkout_shipping_method($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-checkout-shipping-method.php');
    }

    public function checkout_paymethods($pay_methods, $cart, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-checkout-paymethods.php');
    }

    public function checkout_paymethod($payMethod, $cart, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-checkout-paymethod.php');
    }

    /*
        /Checkout reusable filters 
    */

    /*
        Checkout - regular 
    */

    public function checkout_regular_page_title($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-page-title.php');
    }

    public function checkout_regular_step_1_titles($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-1-titles.php');
    }

    public function checkout_regular_step_1_submit($cart, $obj, $slug)
    {
        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-1-submit.php');
    }

    public function checkout_regular_step_1_other_steps($cart, $obj)
    {
        $delivery_address = $cart->deliveryAddress;

        if (!isset($delivery_address->id) && UserController::is_propeller_logged_in())
            $delivery_address = $this->get_address_with_id(AddressType::DELIVERY, $cart->deliveryAddress);

        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-1-other-steps.php');
    }

    public function checkout_regular_step_2_titles($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-2-titles.php');
    }

    public function checkout_regular_step_2_form($cart, $obj, $slug)
    {
        $countries = propel_get_countries();

        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-2-form.php');
    }

    public function checkout_regular_step_2_submit($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-2-submit.php');
    }

    public function checkout_regular_step_2_other_steps($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-2-other-steps.php');
    }

    public function checkout_regular_step_3_titles($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-3-titles.php');
    }

    public function checkout_regular_step_3_form($cart, $obj, $slug)
    {
        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-3-form.php');
    }

    public function checkout_regular_step_3_submit($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/regular/propeller-checkout-regular-step-3-submit.php');
    }

    /*
        Checkout dropshipment
    */
    public function checkout_dropshipment_page_title($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/dropshipment/propeller-checkout-dropshipment-page-title.php');
    }

    public function checkout_dropshipment_step_3_titles($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/dropshipment/propeller-checkout-dropshipment-step-3-titles.php');
    }

    public function checkout_dropshipment_step_3_form($cart, $obj, $slug)
    {
        require $this->load_template('partials', '/checkout/dropshipment/propeller-checkout-dropshipment-step-3-form.php');
    }

    public function checkout_dropshipment_step_3_submit($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/dropshipment/propeller-checkout-dropshipment-step-3-submit.php');
    }

    /*
        Checkout summary
    */
    public function checkout_summary_form($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-checkout-summary-form.php');
    }

    public function checkout_summary_submit($cart, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-checkout-summary-submit.php');
    }

    public function purchase_authorization_thank_you()
    {
        global $propel;

        ob_start();

        $purchase_authorization_cart = $propel['cart'];

        require $this->load_template('partials', '/cart/propeller-purchase-authorization-thank-you.php');

        return ob_get_clean();
    }
    /**
     * Used for external price calculation in case custom/external prices are
     * being used. Call this filter in the ShoppingCartAjaxController
     */
    public function shopping_cart_get_item_price($dummy, $product_identifier, $quantity)
    {
        // by default return null since we're using Propeller prices
        return null;
    }

    public function ics_icp($address)
    {
        $icp = 'N';

        if (UserController::is_propeller_logged_in()) {
            $eu_countries = propel_get_countries('EUCountries.php');

            // if address has ICP set
            if (isset($address['icp'])) {
                // check if the delivery address country is in the array of EU countries and set the ICP of that address (Y or N)
                if (in_array($address['country'], array_keys($eu_countries)))
                    $icp = $address['icp'];
                else // if not in the EU then ICP is set to N
                    $icp = 'N';
            } else {
                // If address doesn't have ICP set if following conditions meet (probably a new delivery address): 
                // - user is contact 
                // - AND address type is delivery 
                // - AND order type is regular
                // = set ICP to N if delivery address country is in the EU and it's equal to the selected ICP country in the settings
                //   (plugin settings -> behavior -> "Shop home country")
                // = set ICP to Y in every other case
                if (
                    UserController::user()->get_type() == UserTypes::CONTACT &&
                    isset($address['type']) && $address['type'] == AddressTypeCart::DELIVERY &&
                    SessionController::has(PROPELLER_ORDER_TYPE) &&
                    SessionController::get(PROPELLER_ORDER_TYPE) == $this->get_order_types()->items[OrderType::REGULAR]->value
                ) {
                    $icp = in_array($address['country'], array_keys($eu_countries)) && $address['country'] == PROPELLER_ICP_COUNTRY ? 'N' : 'Y';
                }
            }
        }

        return $icp;
    }

    /**
     * 
     * FRONTEND HOOKS
     * 
     */
    public function mini_shopping_cart()
    {
        if (!SessionController::has('reinitialize_cart')) {
            if (!$this->cart)
                $this->init_cart();

            if (SessionController::has(PROPELLER_CART)) {
                if (!SessionController::has(PROPELLER_ORDER_TYPE) && !UserController::is_propeller_logged_in())
                    $this->change_order_type($this->get_order_types()->items[OrderType::REGULAR]->value, false);

                if (UserController::is_propeller_logged_in() && empty($this->cart->invoiceAddress->street))
                    $this->set_user_default_cart_address();
            }
        } else
            $this->clear_cart(true);

        ob_start();
        require $this->load_template('partials', '/cart/propeller-mini-shopping-cart.php');
        return ob_get_clean();
    }

    public function load_mini_cart()
    {
        if (UserController::is_propeller_logged_in()) {
            if (SessionController::has('reinitialize_cart') && SessionController::get('reinitialize_cart')) {

                SessionController::remove('reinitialize_cart');

                if (SessionController::has(PROPELLER_AUTHORIZER_CART_ID) && SessionController::get(PROPELLER_AUTHORIZER_CART_ID)) {
                    $cart_data = $this->get_user_cart(SessionController::get(PROPELLER_AUTHORIZER_CART_ID));

                    SessionController::remove(PROPELLER_AUTHORIZER_CART_ID);

                    if (is_object($cart_data))
                        $this->postprocess($cart_data);
                    else {
                        $this->init_cart(true);

                        $this->change_order_type(SessionController::get(PROPELLER_ORDER_TYPE));
                        $this->set_user_default_cart_address();
                    }
                } else {
                    $this->init_cart(true);

                    $this->change_order_type(SessionController::get(PROPELLER_ORDER_TYPE));
                    $this->set_user_default_cart_address();
                }
            } else
                if (!$this->cart)
                $this->init_cart();
        } else {
            if (SessionController::has('reinitialize_cart') && SessionController::get('reinitialize_cart'))
                SessionController::remove('reinitialize_cart');
        }


        $response = new stdClass();

        // ob_start();
        // require $this->load_template('partials', '/cart/propeller-mini-shopping-cart-content.php');

        $response->badge = (int) $this->get_items_count();
        $response->title = __('Shopping cart', 'propeller-ecommerce-v2');
        $response->currency = PropellerHelper::currency();
        if ($this->get_items_count() > 0) {
            if ($this->get_total_price() > 0)
                $response->totals = PropellerHelper::formatPrice($this->get_total_price());
            else
                $response->totals = PropellerHelper::formatPrice(0, 00);
        } else
            $response->totals = PropellerHelper::formatPrice(0, 00);


        // $response->content = ob_get_clean();
        $response->success = true;

        return $response;
    }

    public function mini_checkout_cart()
    {
        if (!$this->cart)
            $this->init_cart();

        ob_start();
        require $this->load_template('partials', '/cart/propeller-mini-checkout-cart.php');
        return ob_get_clean();
    }

    public function validate_checkout()
    {
        global $wp;

        if (!$this->cart)
            $this->init_cart();

        $slug = isset($wp->query_vars['slug']) ? $wp->query_vars['slug'] : get_query_var('slug');
        if (empty($slug))
            $slug = '1';


        $redirect = new stdClass();

        $redirect->do_redirect = false;
        $redirect->url = '';

        // empty cart, redirect to index?
        if ($this->get_items_count() == 0 || $this->cart->total->totalNet < 0) {
            $redirect->do_redirect = true;
            $redirect->url = home_url('/' . PageController::get_slug(PageType::SHOPPING_CART_PAGE));

            return $redirect;
        }

        $checkout_slug = PageController::get_slug(PageType::CHECKOUT_PAGE);

        if (!UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS) {
            if (SessionController::get(PROPELLER_ORDER_TYPE) != $this->get_order_types()->items[OrderType::REGULAR]->value && SessionController::get(PROPELLER_ORDER_TYPE) != $this->get_order_types()->items[OrderType::PICKUP]->value) {
                $redirect->do_redirect = true;
                $redirect->url = home_url();
            } else if ($slug == '2' && empty($this->cart->invoiceAddress->street)) {
                $redirect->do_redirect = true;
                $redirect->url = home_url('/' . $checkout_slug);
            } else if ($slug == '3') {
                if (empty($this->cart->invoiceAddress->street)) {
                    $redirect->do_redirect = true;
                    $redirect->url = home_url('/' . $checkout_slug);
                } else if (empty($this->cart->deliveryAddress->street)) {
                    $redirect->do_redirect = true;
                    $redirect->url = home_url('/' . $checkout_slug . '/2');
                }
            }
        }

        return $redirect;
    }

    public function checkout()
    {
        global $wp;

        if (!$this->cart)
            $this->init_cart();

        ob_start();

        $slug = isset($wp->query_vars['slug']) ? $wp->query_vars['slug'] : get_query_var('slug');
        $has_slug = empty($slug) ? false : true;

        if (empty($slug)) {
            $slug = '1';

            // if there is an invoice address set, skip step 1
            if (!empty($this->get_invoice_address()->street) && $slug == '1')
                $slug = '2';

            // if there is a delivery address set, skip step 2
            if (!empty($this->get_delivery_address()->street) && $slug == '2') {
                if (SessionController::get(PROPELLER_ORDER_TYPE) === $this->get_order_types()->items[OrderType::DROPSHIPMENT]->value)
                    $slug = '2';
                else
                    $slug = '3';
            }
        }

        $order_type = strtolower(SessionController::get(PROPELLER_ORDER_TYPE));

        if (!$order_type || empty($order_type))
            $order_type = strtolower($this->get_order_types()->items[OrderType::REGULAR]->value);

        $warehouse_obj = new WarehouseController();

        if ((int) $slug > 1)
            $this->check_order_type();

        $this->warehouses = new stdClass();

        if ($slug == '2') {
            $this->warehouses = $warehouse_obj->get_warehouses([
                'isActive' => true,
                'isPickupLocation' => true
            ]);
        }

        $checkout_page = $this->load_template('partials', "/checkout/$order_type/propeller-checkout-step-$slug.php");

        if (file_exists($checkout_page)) {
            require $checkout_page;

            if ($this->analytics && (!$has_slug || (int)$slug === 1)) {
                $this->analytics->setData($this->cart);

                apply_filters('propel_ga4_fire_event', 'begin_checkout');
            }

            return ob_get_clean();
        }
    }

    public function checkout_summary()
    {
        if (!$this->cart)
            $this->init_cart();

        ob_start();

        require $this->load_template('partials', '/checkout/propeller-checkout-summary.php');
        return ob_get_clean();
    }

    public function quick_add_to_basket()
    {
        if (!$this->cart)
            $this->init_cart();

        $error          = null;
        $products       = [];
        $missing_codes  = [];

        $subtotal       = 0;
        $exclbtw        = 0;
        $total          = 0;
        $total_quantity = 0;

        if ($this->is_post_request()) {
            if (!$this->validate_form_request('_wpnonce', PROPELLER_NONCE_KEY_FRONTEND)) {
                $error = __('Permission denied!', 'propeller-ecommerce-v2');
            } else {

                $values = $this->parse_excel();

                if (!is_array($values) || count($values) === 0) {
                    $error = __('Error uploading Excel file', 'propeller-ecommerce-v2');
                } else {

                    $productController = new ProductController();

                    $skus = [];
                    foreach ($values as $key => $val) {
                        $skus[] = $val['code'];
                        $missing_codes[] = $val['code'];
                    }

                    $products_search = $productController->get_products(['skus' => $skus, 'offset' => count($skus)], true);

                    if (isset($products_search->items)) {
                        // sort items as in excel 
                        $sorted_products = [];

                        foreach ($values as $key => $val) {
                            $product_code = $val['code'];

                            $sort_found = array_filter($products_search->items, function ($p) use ($product_code) {
                                return $p->sku == $product_code;
                            });

                            if (count($sort_found))
                                $sorted_products[] = current($sort_found);
                        }

                        $products_search->items = array_reverse($sorted_products);

                        foreach ($products_search->items as $item) {
                            if ($item->priceData->display == Product::PRICE_ON_REQUEST)
                                continue;

                            if ($item->orderable == 'N')
                                continue;

                            $net_price = $this->get_quick_item_price($item->price);
                            $quantity  = $this->get_quick_item_quantity($values, $item->sku);

                            $quick_item            = new stdClass();
                            $quick_item->code      = $item->sku;
                            $quick_item->id        = $item->productId;
                            $quick_item->name      = $item->name[0]->value;
                            $quick_item->net_price = $net_price;
                            $quick_item->quantity  = $quantity;
                            $quick_item->total     = $net_price * $quantity;

                            $total_quantity += $quantity;
                            $products[] = $quick_item;

                            $total += $quick_item->total;

                            if (in_array($item->sku, $missing_codes)) {
                                $index = array_search($item->sku, $missing_codes);

                                if ($index !== false)
                                    unset($missing_codes[$index]);
                            }
                        }
                    }

                    $exclbtw  = PropellerHelper::percentage(21, $total);
                    $subtotal = $total - $exclbtw;

                    $products = array_reverse($products);
                }
            }
        }

        ob_start();

        require $this->load_template('templates', '/propeller-quick-add-to-basket.php');

        return ob_get_clean();
    }

    protected function get_quick_item_price($price)
    {
        if ($price->discount)
            return $price->discount->value;

        return $price->gross;
    }

    protected function get_quick_item_quantity($values, $sku)
    {
        foreach ($values as $vals) {
            if ($vals['code'] == $sku)
                return (int) $vals['quantity'];
        }

        return 0;
    }

    protected function parse_excel()
    {
        $values = [];

        $action = !empty($_POST['action']) ? sanitize_text_field($_POST['action']) : null;

        if ($action === 'upload_excel_file' && !empty($_FILES['attachment']['tmp_name'])) {
            $file = wp_upload_bits($_FILES['attachment']['name'], null, file_get_contents($_FILES['attachment']['tmp_name']));

            if ($file['error'] != false) {
                return [];
            }

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadEmptyCells(false);
            $reader->setReadDataOnly(true);

            $spreadsheet = $reader->load($file['file']);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            if (is_array($sheetData)) {
                $index = 0;
                foreach ($sheetData as $data) {
                    if ($index > 1) {
                        $values[] = [
                            'code' => trim($data['A']),
                            'quantity' => (int) trim($data['B'])
                        ];
                    }

                    $index++;
                }
            }
        }

        return $values;
    }

    public function checkout_thank_you()
    {
        global $propel;

        ob_start();

        $orderController = new OrderController();

        $order = isset($propel['order'])
            ? $propel['order']
            : $orderController->get_order_minimal((int) $_GET['order_id']);

        if (isset($order) && is_object($order)) {
            if (
                $order->paymentData->status == PaymentStatuses::FAILED ||
                $order->paymentData->status == PaymentStatuses::CANCELLED ||
                $order->paymentData->status == PaymentStatuses::EXPIRED
            ) {
                wp_safe_redirect(home_url('/' . PageController::get_slug(PageType::PAYMENT_FAILED_PAGE)) . '?order_id=' . $_GET['order_id']);
                die;
            }
        }

        require $this->load_template('partials', '/checkout/propeller-checkout-thank-you.php');

        return ob_get_clean();
    }

    public function payment_failed()
    {
        ob_start();

        $orderController = new OrderController();

        $order = isset($propel['order'])
            ? $propel['order']
            : $orderController->get_order((int) $_GET['order_id']);

        require $this->load_template('partials', '/checkout/propeller-checkout-payment-failed.php');

        return ob_get_clean();
    }

    public function shopping_cart()
    {
        if (!$this->cart)
            $this->init_cart();

        $items = $this->get_items();

        // set GA4 data
        if ($this->analytics)
            $this->analytics->setData($this->cart);

        $this->assets()->std_requires_asset('propeller-action-tooltip');

        ob_start();

        require $this->load_template('templates', '/propeller-shopping-cart.php');

        if ($this->analytics)
            apply_filters('propel_ga4_fire_event', 'cart');

        return ob_get_clean();
    }

    public function start()
    {
        if (!defined('PROPELLER_SITE_ID'))
            Propeller::register_settings();

        $type = 'cartStart';

        $cart_params = [
            'language' => PROPELLER_LANG
        ];

        if (UserController::is_propeller_logged_in()) {
            if (UserController::is_contact()) {
                $cart_params['contactId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
                $cart_params['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
            } else if (UserController::is_customer())
                $cart_params['customerId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
        }

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->cart_start(
            $cart_params,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($gql);
        // die;

        $cartData = $this->query($gql, $type);

        // var_dump($cartData);
        // die;

        if (is_object($cartData)) {
            SessionController::set(PROPELLER_CART_INITIALIZED, true);

            $this->start_postprocess($cartData);

            if (UserController::is_propeller_logged_in()) {
                try {
                    $this->set_user_default_cart_address();
                } catch (Exception $ex) {
                }
            }
        }
    }

    public function set_user()
    {
        $type = 'cartSetUser';

        if (UserController::is_contact()) {
            $params['contactId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
            $params['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
        } else if (UserController::is_customer()) {
            $params['customerId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
        }

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->set_user(
            $this->cart_id,
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        SessionController::set(PROPELLER_CART_USER_SET, true);

        $this->postprocess($cartData);

        return $cartData;
    }

    public function add_item($quantity, $product_id, $cluster_id = null, $notes = '', $price = null, $child_items = [])
    {
        $this->init_cart(true);

        $type = 'cartAddItem';

        $params = [
            'quantity' => $quantity > 0 ? $quantity : 1,
            'notes' => $notes,
            'productId' => $product_id
        ];

        if ($cluster_id)
            $params['clusterId'] = $cluster_id;

        if (is_array($child_items) && count($child_items)) {
            $params['childItems'] = [];

            foreach ($child_items as $item) {
                if ((int) $item > 0) {
                    $params['childItems'][] = [
                        "productId" => (int) $item
                    ];
                }
            }
        }

        if ($price)
            $params['price'] = $price;

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->add_item(
            $this->cart_id,
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($gql->query);
        // var_dump(json_encode($gql->variables));
        // die;

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        $added_item = null;

        if (is_object($cartData)) {
            $this->postprocess($cartData);

            $this->response->product_id = $product_id;

            if ($cluster_id)
                $this->response->cluster_id = $cluster_id;

            $cart_items = array_reverse($this->get_items());

            $added_item = $this->get_item_by_product_id($product_id);

            foreach ($cart_items as $c_item) {
                if ($cluster_id) {
                    if ($c_item->clusterId == $cluster_id && $c_item->productId == $product_id) {
                        $added_item = $c_item;
                        break;
                    }
                } else {
                    if ($c_item->productId == $product_id) {
                        $added_item = $c_item;
                        break;
                    }
                }
            }

            // set GA4 data
            if ($this->analytics) {
                $cartData->added_item = $added_item;
                $cartData->added_item->added_quantity = $quantity;

                $this->analytics->setData($cartData);
            }


            $postprocess->error         = false;
            $postprocess->badge         = $this->get_items_count();
            $postprocess->message       = $added_item->product->name[0]->value . " added to cart";
            $postprocess->totals        = $this->get_totals();
            $postprocess->taxLevels     = $this->get_tax_levels();
            $postprocess->postageData   = $this->get_postage_data();
            $postprocess->show_modal    = true;
            $postprocess->item          = $added_item;

            if ($this->analytics) {
                ob_start();
                apply_filters('propel_ga4_fire_event', 'add_to_cart');
                $postprocess->analytics = ob_get_clean();
            }
        } else {
            $this->response = new stdClass();

            $postprocess->error = true;
            $postprocess->message = $cartData[0]['message'];
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function add_item_bundle($quantity, $bundle_id, $notes = '')
    {
        $this->init_cart(true);

        $type = 'cartAddBundle';

        $params = [
            'quantity' => $quantity > 0 ? $quantity : 1,
            'notes' => $notes,
            'bundleId' => $bundle_id
        ];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->add_item_bundle(
            $this->cart_id,
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $this->postprocess($cartData);

            $added_item = $this->get_item_by_bundle_id($bundle_id);

            $postprocess->badge         = $this->get_items_count();
            $postprocess->message       = __("Bundle added to cart", 'propeller-ecommerce-v2');
            $postprocess->totals        = $this->get_totals();
            $postprocess->taxLevels     = $this->get_tax_levels();
            $postprocess->postageData   = $this->get_postage_data();
            $postprocess->show_bundle   = true;
            $postprocess->error         = false;
            $postprocess->item          = $added_item;
        } else {
            $postprocess->message = $cartData;
            $postprocess->error = true;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function update_item($item_id, $quantity, $notes = '', $price = null)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartUpdateItem';

        $params = [
            'quantity' => $quantity,
            'notes' => $notes,
        ];

        if (is_numeric($price))
            $params['price'] = $price;

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->update_item(
            $this->cart_id,
            $item_id,
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            // get the updated item before the new cart is storred in session 
            // to preserve the previous price in case of bulk prices
            $updated_item = array_filter($this->cart->items, function ($item) use ($item_id) {
                return $item->id == $item_id;
            });

            $this->postprocess($cartData);

            $postprocess->badge = $this->get_items_count();
            $postprocess->message = __("Item updated", 'propeller-ecommerce-v2');
            $postprocess->totals = $this->get_totals();
            $postprocess->items = $this->get_items();
            $postprocess->postageData = $this->get_postage_data();
            $postprocess->error = false;
            $postprocess->taxLevels = $this->get_tax_levels();

            $request_data = PropellerUtils::sanitize($_REQUEST);
            $prev_quantity = (int) $request_data['prev_quantity'];
            $updated_quantity = $quantity;

            // set GA4 data
            if ($this->analytics && count($updated_item)) {
                $updated_item = current($updated_item);

                if ($prev_quantity == $quantity)
                    $updated_quantity == $quantity;
                else if ($prev_quantity > $quantity) {
                    $updated_quantity = $prev_quantity - $quantity;

                    $cartData->removed_item = $updated_item;
                    $cartData->removed_item->updated_quantity = $updated_quantity;
                } else {
                    $updated_quantity = $quantity - $prev_quantity;
                    $cartData->added_item = $updated_item;
                    $cartData->added_item->updated_quantity = $updated_quantity;
                }

                $this->analytics->setData($cartData);
            }

            ob_start();
            require $this->load_template('templates', '/propeller-shopping-cart.php');
            $postprocess->content = ob_get_clean();

            if ($this->analytics) {
                $fire_action = $prev_quantity > $quantity ? 'remove_from_cart' : 'add_to_cart';

                ob_start();
                apply_filters('propel_ga4_fire_event', $fire_action);
                $postprocess->analytics = ob_get_clean();
            }
        } else {
            $this->response = new stdClass();

            $postprocess->message = $cartData;
            $postprocess->error = true;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function update_items($item_id, $quantity, $notes, $items)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartUpdateItems';

        $params = [
            'cartId' => $this->cart_id,
            'items' => []
        ];

        foreach ($items as $arr_item_id => $item_price) {
            $item = [
                'notes' => $notes,
                'itemId' => $arr_item_id
            ];

            if ($arr_item_id == $item_id)
                $item['quantity'] = $quantity;

            if ($item_price)
                $item['price'] = $item_price;

            $params['items'][] = $item;
        }

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->update_items(
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $this->postprocess($cartData);

            $postprocess->badge = $this->get_items_count();
            $postprocess->message = __("Item updated", 'propeller-ecommerce-v2');
            $postprocess->totals = $this->get_totals();
            $postprocess->items = $this->get_items();
            $postprocess->postageData = $this->get_postage_data();
            $postprocess->taxLevels = $this->get_tax_levels();
            $postprocess->error = false;

            ob_start();
            require $this->load_template('templates', '/propeller-shopping-cart.php');
            $postprocess->content = ob_get_clean();
        } else {
            $this->response = new stdClass();

            $postprocess->message = $cartData;
            $postprocess->error = true;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function delete_item($item_id)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartDeleteItem';

        $params = [
            'itemId' => $item_id
        ];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->delete_item(
            $this->cart_id,
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $removed_item = $this->get_item_by_id($item_id);

            $this->postprocess($cartData);

            $postprocess->badge = $this->get_items_count();
            $postprocess->message = __("Item removed from cart", 'propeller-ecommerce-v2');
            $postprocess->remove = $item_id;
            $postprocess->totals = $this->get_totals();
            $postprocess->postageData = $this->get_postage_data();
            $postprocess->taxLevels = $this->get_tax_levels();
            $postprocess->error = false;

            if ($this->analytics) {
                $cartData->removed_item = $removed_item;
                $this->analytics->setData($cartData);

                ob_start();
                apply_filters('propel_ga4_fire_event', 'remove_from_cart');
                $postprocess->analytics = ob_get_clean();
            }

            ob_start();
            require $this->load_template('templates', '/propeller-shopping-cart.php');
            $postprocess->content = ob_get_clean();
        } else {
            $postprocess->message = $cartData;
            $postprocess->error = true;
        }

        if (!$this->response)
            $this->response = new stdClass();

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function update($payment_data, $postage_data, $notes, $reference, $extra3, $extra4)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartUpdate';
        $payment_data_args = [
            'method' => $payment_data['method']
        ];

        if (isset($payment_data['status']))
            $payment_data_args['status'] = $payment_data['status'];

        if (isset($payment_data['price']))
            $payment_data_args['price'] = $payment_data['price'];

        $postage_data_args = [
            'partialDeliveryAllowed' => $postage_data['partialDeliveryAllowed'],
        ];

        if (isset($postage_data['method']))
            $postage_data_args['method'] = $postage_data['method'];

        if (isset($postage_data['requestDate']))
            $postage_data_args['requestDate'] = $postage_data['requestDate'];

        if (isset($postage_data['price']))
            $postage_data_args['price'] = $postage_data['price'];

        if (isset($postage_data['carrier']))
            $postage_data_args['carrier'] = (string) $postage_data['carrier'];

        if (isset($postage_data['pickUpLocationId']))
            $postage_data_args['pickUpLocationId'] = $postage_data['pickUpLocationId'];

        $params = [
            'paymentData' => $payment_data_args,
            'postageData' => $postage_data_args,
            'notes' => $notes,
            'reference' => $reference,
            'extra3' => $extra3,
            'extra4' => $extra4,
            'language' => PROPELLER_LANG
        ];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->update(
            $this->cart_id,
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($gql);
        // var_dump(json_encode($gql->variables));

        $cartData = $this->query($gql, $type);

        $this->postprocess($cartData);

        return $this->response;
    }

    public function update_address($address_data)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartUpdateAddress';

        if (UserController::is_propeller_logged_in() && UserController::is_contact() && $address_data['type'] == AddressTypeCart::DELIVERY) {
            $addressController = new AddressController();
            $addressController->set_user(SessionController::get(PROPELLER_USER_DATA));

            $default_delivery_address = $addressController->get_default_address(AddressType::DELIVERY);

            if ($address_data['email'] == $default_delivery_address->email)
                $address_data['email'] = SessionController::get(PROPELLER_USER_DATA)->email;
        }

        $params = $this->format_address_params($address_data);

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->cart_update_address(
            $this->cart_id,
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $this->postprocess($cartData);

            $postprocess->success = true;
            $postprocess->message = __("Address updated", 'propeller-ecommerce-v2');

            if (isset($address_data['next_step']))
                $postprocess->redirect = esc_url_raw(home_url("/" . PageController::get_slug(PageType::CHECKOUT_PAGE) . "/" . $address_data['next_step'] . '/'));
            else
                $postprocess->reload = true;
        } else {
            $postprocess->success = false;
            $postprocess->message = $cartData;
        }

        if (!$this->response)
            $this->response = new stdClass();

        $this->response->object = $this->object_name;

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function action_code($coupon_code)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartAddActionCode';

        $raw_params = [
            "actionCode" => $coupon_code
        ];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->action_code(
            $this->cart_id,
            $raw_params,
            $images_fragment,
            PROPELLER_LANG
        );
        // var_dump($gql);
        // var_dump(json_encode($gql->variables));

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $this->postprocess($cartData);

            $postprocess->badge = $this->get_items_count();
            $postprocess->message = __("Added action code to cart", 'propeller-ecommerce-v2');
            $postprocess->totals = $this->get_totals();
            $postprocess->postageData = $this->get_postage_data();
            $postprocess->taxLevels = $this->get_tax_levels();
            $postprocess->reload = true;
        } else {
            $postprocess->message = __("This action code is not found", 'propeller-ecommerce-v2');
            $postprocess->error = true;
        }

        if (!$this->response)
            $this->response = new stdClass();

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function remove_action_code($action_code)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartRemoveActionCode';

        $raw_params = [
            "cartId" => $this->cart_id,
            "actionCode" => $action_code
        ];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->remove_action_code(
            $raw_params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $this->postprocess($cartData);

            $postprocess->badge = $this->get_items_count();
            $postprocess->message = __("Action code removed", 'propeller-ecommerce-v2');
            $postprocess->totals = $this->get_totals();
            $postprocess->postageData = $this->get_postage_data();
            $postprocess->taxLevels = $this->get_tax_levels();
            $postprocess->reload = true;
        } else {
            $postprocess->message = $cartData;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function voucher_code($voucher_code)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartAddVoucherCode';

        $raw_params = [
            "cartId" => $this->cart_id,
            "voucherCode" => $voucher_code
        ];

        $gql = $this->model->voucher_code(
            $raw_params,
            Media::get([
                'name' => MediaImagesType::SMALL,
                'offset' => 1
            ], MediaType::IMAGES),
            [],
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $this->postprocess($cartData);

            $postprocess->badge = $this->get_items_count();
            $postprocess->message = __("Added voucher code to cart", 'propeller-ecommerce-v2');
            $postprocess->totals = $this->get_totals();
            $postprocess->postageData = $this->get_postage_data();
            $postprocess->taxLevels = $this->get_tax_levels();
            $postprocess->reload = true;
        } else {
            $postprocess->message = __("This voucher code is not found", 'propeller-ecommerce-v2');
            $postprocess->error = true;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function remove_voucher_code($voucher_code)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartRemoveVoucherCode';

        $raw_params = [
            "cartId" => $this->cart_id,
            "voucherCode" => $voucher_code
        ];

        $gql = $this->model->remove_voucher_code(
            $raw_params,
            Media::get([
                'name' => MediaImagesType::SMALL,
                'offset' => 1
            ], MediaType::IMAGES),
            [],
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $this->postprocess($cartData);

            $postprocess->badge = $this->get_items_count();
            $postprocess->message = __("Voucher code removed", 'propeller-ecommerce-v2');
            $postprocess->totals = $this->get_totals();
            $postprocess->postageData = $this->get_postage_data();
            $postprocess->taxLevels = $this->get_tax_levels();
            $postprocess->reload = true;
        } else {
            $postprocess->message = $cartData;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    /**
     * regular, quick, dropshipment
     */
    public function change_order_type($order_type, $update_addresses = true)
    {
        if (!$this->cart)
            $this->init_cart();

        $type = 'cartUpdate';

        $params = [
            'postageData' => [
                'method' => $order_type,
            ]
        ];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->update(
            $this->cart_id,
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $this->postprocess($cartData);

            SessionController::set(PROPELLER_ORDER_TYPE, $order_type);

            SessionController::set(PROPELLER_ORDER_STATUS_TYPE, OrderStatus::ORDER_STATUS_NEW);

            if ($update_addresses) {
                $this->set_user_default_cart_address();
                SessionController::set(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED, false);
            }

            $postprocess->message = __("Order type changed", 'propeller-ecommerce-v2');
            $postprocess->reload = true;
        } else {
            $postprocess->message = $cartData;
        }

        if (!is_object($this->response))
            $this->response = new stdClass();

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function replenish($items)
    {
        if (!$this->cart)
            $this->init_cart();

        $products = [];
        $clusters = [];

        $prices = $this->get_items_prices($items, true);

        $bulk_cart_input = [
            'cartId' => $this->cart_id,
            'items' => []
        ];

        foreach ($items as $item) {
            $chunks = explode('-', $item);

            $cluster_id = null;
            $product_id = null;

            if (!str_contains($chunks[0], '|'))
                $product_id = (int) $chunks[0];
            else {
                $prod_chunks = explode('|', $chunks[0]);

                $product_id = (int) $prod_chunks[0];
                $cluster_id = (int) $prod_chunks[1];
            }

            $quantity = 1;
            $child_items = [];

            if (str_contains($chunks[1], '^')) {
                $quantity_children_chunks = explode('^', $chunks[1]);

                $quantity = (int) $quantity_children_chunks[0];
                $child_items = explode('|', $quantity_children_chunks[1]);
            } else {
                $quantity = (int) $chunks[1];
            }

            $price = isset($prices[$product_id]) ? $prices[$product_id] : null;

            $item = [
                'productId' => $product_id,
                'quantity' => $quantity
            ];

            if ($price)
                $item['price'] = $price;

            if ($cluster_id) {
                $clusters[] = $cluster_id;
                $item['clusterId'] = $cluster_id;
            }

            if (count($child_items)) {
                foreach ($child_items as $child) {
                    $item['childItems'][] = [
                        'productId' => (int) $child,
                        'quantity' => $quantity
                    ];
                }
            }

            $products[] = $product_id;

            $bulk_cart_input['items'][] = $item;
        }

        $gql = $this->model->bulk_cart_items($bulk_cart_input);

        $response = $this->query($gql, 'cartItemBulk');

        $items_content = [];
        $items_messages = [];

        if (is_object($response) && isset($response->total) && $response->total > 0) {
            $cart_data = $this->get_user_cart($this->cart_id);

            $this->postprocess($cart_data);

            foreach ($products as $product_id) {
                $added_item = $this->get_item_by_product_id($product_id);

                if ($added_item) {
                    $items_messages[]    = '<span class="text-success">' . $added_item->product->get_name() . " added to cart</span>";

                    ob_start();
                    require $this->load_template('partials', '/cart/propeller-shopping-cart-popup-item.php');

                    $item_content = ob_get_clean();
                    $items_content[] = $item_content;
                }
            }
        } else {
            $has_errors = true;
            $items_messages[] = __('An error occurred while processing your request', 'propeller-ecommerce-v2');
        }

        $postprocess = new stdClass();

        $postprocess->content       = implode('', $items_content);
        $postprocess->message       = implode('<br />', $items_messages);
        $postprocess->badge         = $this->get_items_count();
        $postprocess->totals        = $this->get_totals();
        $postprocess->taxLevels     = $this->get_tax_levels();
        $postprocess->postageData   = $this->get_postage_data();
        $postprocess->show_modal    = !$has_errors;
        $postprocess->show_quick_modal = !$has_errors;
        $postprocess->error         = $has_errors;

        $this->response = new stdClass();
        $this->response->postprocess = $postprocess;
        $this->response->object = "QuickOrder";

        return $this->response;
    }

    public function add_item_replenish($quantity, $product_id, $notes = '', $price = null, $child_items = [])
    {
        $raw_params_arr = [
            'quantity' => (int) $quantity,
            'notes' => $notes,
            'productId' => (int) $product_id
        ];

        if (is_array($child_items) && count($child_items)) {
            $raw_params_arr['childItems'] = [];

            foreach ($child_items as $item)
                $raw_params_arr['childItems'] = ['productId' => $item];
        }

        if ($price)
            $raw_params_arr['price'] = $price;

        $gql = new stdClass();
        $gql->query = $this->model->add_item_replenish($product_id);
        $gql->variables = $raw_params_arr;

        return $gql;
    }

    private function get_items_prices($items)
    {
        $prices = [];

        $productController = new ProductController();

        $product_ids = [];
        $product_qty = [];

        foreach ($items as $item) {
            $chunks = explode('-', $item);
            $product_ids[] = (int) $chunks[0];
            $product_qty[$chunks[0]] = $chunks[1];
        }

        $procucts_codes = $productController->get_product_codes($product_ids);

        foreach ($product_ids as $id) {
            $product = array_filter($procucts_codes->items, function ($pcode) use ($id) {
                return $pcode->productId == $id;
            });

            if (count($product)) {
                $product = current($product);

                $price = apply_filters('propel_shopping_cart_get_item_price', 'dummy', $product->sku, (float) $product_qty[$id]);

                $prices[$id] = $price;
            }
        }

        return $prices;
    }

    public function set_order_status($data)
    {
        SessionController::set(PROPELLER_ORDER_STATUS_TYPE, $data['order_status']);
    }

    public function check_order_type()
    {
        if (!$this->cart)
            $this->init_cart();

        $selected_order_type = $this->get_postage_data()->method;
        $order_types = $this->get_shipping_methods();

        $available_order_types = [];

        foreach ($order_types->items as $order_type)
            $available_order_types[] = $order_type->value;

        if (
            !SessionController::has(PROPELLER_ORDER_TYPE) ||
            (SessionController::has(PROPELLER_ORDER_TYPE) && $selected_order_type != SessionController::get(PROPELLER_ORDER_TYPE)) ||
            !in_array($selected_order_type, $available_order_types)
        )
            $this->change_order_type($this->get_order_types()->items[OrderType::REGULAR]->value, false);
    }

    public function load_item_crossupsells($product_id, $crossupsell_types)
    {
        if (!$this->cart)
            $this->init_cart();

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->load_item_crossupsells(
            [
                'product_id' => $product_id,
                'crossupsells_input' => [
                    'types' => $crossupsell_types
                ]
            ],
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($gql->query);
        // var_dump(json_encode($gql->variables));

        $product = $this->query($gql, 'product');

        $response = new stdClass();

        ob_start();
        apply_filters('propel_shopping_cart_table_product_item_crossupsells', $product, $this->cart, $this);
        $crossupsells_content = ob_get_clean();

        $response->content = $crossupsells_content;

        return $response;
    }

    public function process($order_data)
    {
        if (!$this->cart)
            $this->init_cart();

        $default_paymethods = explode(',', PROPELLER_ONACCOUNT_PAYMENTS);
        $is_request = false;

        $will_do_payment = false;

        $payment_controller = new PaymentController();

        $payMethod = !empty($_POST['payMethod']) ? sanitize_text_field($_POST['payMethod']) : (count($default_paymethods) ? $default_paymethods[0] : PROPELLER_DEFAULT_PAYMETHOD);

        $paymentData = $this->get_payment_data();
        $postageData = $this->get_postage_data();

        $payment_data_arr = [
            'method' => $paymentData->method
        ];

        if ($paymentData->price > 0)
            $payment_data_arr['price'] = $paymentData->price;
        if ($paymentData->status)
            $payment_data_arr['status'] = $paymentData->status;

        $postage_data_arr = [
            'partialDeliveryAllowed' => $postageData->partialDeliveryAllowed,
            'method' => $postageData->method
        ];

        if ($postageData->requestDate)
            $postage_data_arr['requestDate'] = $postageData->requestDate;
        if ($postageData->price > 0)
            $postage_data_arr['price'] = $postageData->price;
        if ($postageData->carrier)
            $postage_data_arr['carrier'] = $postageData->carrier;

        if ($order_data['status'] == OrderStatus::ORDER_STATUS_NEW) {
            // $payment = $this->get_payment_data();
            // $payment->method = $order_data['payMethod'];

            $this->update(
                $payment_data_arr,
                $postage_data_arr,
                $order_data['notes'],
                $order_data['reference'],
                $this->cart->extra3,
                $this->cart->extra4
            );
        } else if ($order_data['status'] == OrderStatus::ORDER_STATUS_REQUEST) {
            $this->update(
                $payment_data_arr,
                $postage_data_arr,
                $order_data['notes'],
                $order_data['reference'],
                $this->cart->extra3,
                $this->cart->extra4
            );

            $is_request = true;
        }

        // var_dump($this->cart);
        // die;

        if (!in_array($payMethod, $default_paymethods) && $payment_controller->has_providers() && !$is_request)
            $order_data['status'] = OrderStatus::ORDER_STATUS_UNFINISHED;

        $type = 'cartProcess';

        $params = [
            'orderStatus' => $order_data['status'],
            'language' => PROPELLER_LANG
        ];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->process(
            $this->cart_id,
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        // var_dump($cartData);

        $postprocess = new stdClass();

        if (is_object($cartData)) {
            $payment_controller = new PaymentController();
            $redirect_url = $this->buildUrl('', PageController::get_slug(PageType::THANK_YOU_PAGE)) . '?order_id=' . $cartData->cartOrderId;

            if (!in_array($payMethod, $default_paymethods) && $payment_controller->has_providers() && !$is_request) {

                $payment_args = [
                    "user_id" => UserController::is_propeller_logged_in() && SessionController::has(PROPELLER_USER_ID) ? SessionController::get(PROPELLER_USER_ID) : PROPELLER_ANONYMOUS_USER,
                    "method" => $payMethod,
                    "order_id" => $cartData->cartOrderId,
                    "amount" => sprintf("%.2F", number_format($cartData->order->total->net, 2, '.', '')),
                    "currency" => "EUR",    // TODO: check how to make this dynamic
                    "redirect_url" => $this->buildUrl(PageController::get_slug(PageType::PAYMENT_CHECK_PAGE), '') . '?order_id=' . $cartData->cartOrderId,
                    "cancel_url" => $this->buildUrl(PageController::get_slug(PageType::PAYMENT_CANCELLED_PAGE), '') . '?order_id=' . $cartData->cartOrderId,
                    "description" => "Order $cartData->cartOrderId " . (isset(SessionController::get(PROPELLER_USER_DATA)->debtorId) && !empty(SessionController::get(PROPELLER_USER_DATA)->debtorId) ? "| " . SessionController::get(PROPELLER_USER_DATA)->debtorId : "") . " - payment",
                    "user_data" => UserController::is_propeller_logged_in() ? SessionController::get(PROPELLER_USER_DATA) : null,
                    "cart" => SessionController::get(PROPELLER_CART),
                    'locale' => $this->get_locale(),
                    'object' => $this
                ];

                $payment_response = $payment_controller->create($payment_args);

                if (isset($payment_response->error) && $payment_response->error) {
                    $this->response = new stdClass();

                    $this->response->postprocess = $payment_response;

                    return $this->response;
                }

                $redirect_url = $payment_response->payment_data['checkout_url'];

                $will_do_payment = true;
            } else {
                $orderController = new OrderController();
                $order_data = $cartData->order;

                if ($order_data->status == OrderStatus::ORDER_STATUS_NEW) {
                    $orderController->change_status([
                        'order_id' => $cartData->cartOrderId,
                        'status' => $order_data->status,
                        'add_pdf' => true,
                        'payStatus' => 'UNKNOWN',
                        'send_email' => true,
                        'delete_cart' => true
                    ], false);
                } else if ($order_data->status == OrderStatus::ORDER_STATUS_REQUEST) {
                    $orderController->change_status([
                        'order_id' => $cartData->cartOrderId,
                        'status' => $order_data->status,
                        'add_pdf' => true,
                        'payStatus' => 'UNKNOWN',
                        'send_email' => true,
                        'delete_cart' => true
                    ], false, true);
                } else {
                    $orderController->change_status([
                        'order_id' => $cartData->cartOrderId,
                        'status' => $order_data->status,
                        'add_pdf' => false,
                        'payStatus' => 'UNKNOWN',
                        'send_email' => false,
                        'delete_cart' => true
                    ], false);
                }
            }

            $this->response = new stdClass();

            $this->response->order = $cartData->order;
            $this->response->cartOrderId = $cartData->cartOrderId;

            FlashController::add(PROPELLER_ORDER_PLACED, $cartData->cartOrderId);

            if ($will_do_payment)
                $postprocess->message = __("Proceed to payment", 'propeller-ecommerce-v2');
            else {
                if ($order_data->status == OrderStatus::ORDER_STATUS_REQUEST)
                    $postprocess->message = __("Quote request processed", 'propeller-ecommerce-v2');
                else
                    $postprocess->message = __("Order processed", 'propeller-ecommerce-v2');
            }


            $postprocess->redirect = esc_url_raw($redirect_url);
        } else {
            $postprocess->message = $cartData;
        }

        $this->response->postprocess = $postprocess;

        return $this->response;
    }

    public function clear_cart($hard = false)
    {
        // clean up previous cart data
        SessionController::remove(PROPELLER_CART);
        SessionController::remove(PROPELLER_CART_ID);
        SessionController::remove(PROPELLER_CART_INITIALIZED);
        SessionController::remove(PROPELLER_CART_USER_SET);
        // SessionController::remove(PROPELLER_ORDER_TYPE);

        SessionController::set(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED, false);
        SessionController::set(PROPELLER_ORDER_STATUS_TYPE, OrderStatus::ORDER_STATUS_NEW);
    }

    public function init_postprocess($cart)
    {
        // SessionController::set(PROPELLER_CART, $cart);
        SessionController::set(PROPELLER_CART_ID, $cart->cartId);
        // SessionController::set(PROPELLER_USER_ID, $cart->userId);

        $this->cart = $cart;
        $this->cart_id = $cart->cartId;

        $items = [];
        foreach ($this->cart->items as $item) {
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

        $this->cart->items = $items;
    }

    private function check_cart($cartId)
    {
        $type = 'cart';

        $gql = $this->model->check_cart($cartId);

        $cartData = $this->query($gql, $type);

        $cart_exists = isset($cartData->cartId);

        if (!$cart_exists) {
            if (function_exists('propel_log')) {
                $userData = SessionController::get(PROPELLER_USER_DATA);
                propel_log("$cartId not found for user $userData->firstName $userData->lastName");
            }
        }

        return $cart_exists;
    }

    private function start_postprocess($cart)
    {
        if (!is_object($cart)) {
            if ($this->response)
                unset($this->response);

            $this->response = new stdClass();

            $this->response->error = $cart;
            return;
        }

        SessionController::set(PROPELLER_CART, $cart);
        SessionController::set(PROPELLER_CART_ID, $cart->cartId);

        $this->cart = $cart;
        $this->cart_id = $cart->cartId;
    }

    public function get_paymethods()
    {
        if (!$this->cart)
            $this->init_cart();

        if ($this->cart->payMethods && is_array($this->cart->payMethods))
            return $this->cart->payMethods;

        return [];
        // $type = 'payMethods';

        // $params = [
        //     'offset' => 12
        // ];

        // $gql = $this->model->get_paymethods($params);

        // $paymethods = $this->query($gql, $type);

        // return $paymethods;
    }

    protected function postprocess($cart)
    {
        // start the response fresh after every call
        if ($this->response)
            unset($this->response);

        $this->response = new stdClass();

        if (!is_object($cart)) {
            $this->response->error = $cart;
            return;
        }

        if (isset($cart->response)) {
            $this->response = new stdClass();

            $this->response->data = $cart->response->data;

            if (is_array($cart->response->messages)) {
                $this->response->api_messages = [];

                foreach ($cart->response->messages as $msg) {
                    $this->response->api_messages[] = $msg;
                }
            }


            if (isset($cart->response->error)) {
                $this->response->errors = [];

                foreach ($cart->response->errors as $err) {
                    $this->response->errors[] = $err;
                }
            }

            unset($cart->response);
        }

        SessionController::set(PROPELLER_CART_ID, $cart->cartId);

        $this->cart = $cart;
        $this->cart_id = $cart->cartId;

        $items = [];

        foreach ($this->cart->items as $item) {
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

            $item->product = $product;
            $items[] = $item;
        }

        $this->cart->items = $items;

        SessionController::set(PROPELLER_CART, $this->cart);
    }

    public function set_user_default_cart_address()
    {
        if (!UserController::is_propeller_logged_in())
            return;

        $address_controller = new AddressController();
        $address_controller->set_user();

        $invoiceAddress = $address_controller->get_default_address(AddressType::INVOICE);
        $deliveryAddress = $address_controller->get_default_address(AddressType::DELIVERY);

        // Fallback to get the first address of type that is not default ?!?!?!
        // $all_addreses = [];
        // if (!$invoiceAddress || !$deliveryAddress)
        //     $all_addreses = $address_controller->get_addresses();

        // if (!$invoiceAddress)
        //     $invoiceAddress = $this->get_first_address_of_type($all_addreses, AddressType::INVOICE);

        // if (!$deliveryAddress)
        //     $deliveryAddress = $this->get_first_address_of_type($all_addreses, AddressType::DELIVERY);


        try {
            if (is_object($invoiceAddress)) {
                $invoiceAddress->type = AddressTypeCart::INVOICE;

                $this->update_address((array) $invoiceAddress);
            }
        } catch (Exception $ex) {
            propel_log("Error updating cart invoice address: " . $ex->getMessage());
        }

        try {
            if (is_object($deliveryAddress)) {
                $deliveryAddress->type = AddressTypeCart::DELIVERY;

                $this->update_address((array) $deliveryAddress);
            }
        } catch (Exception $ex) {
            propel_log("Error updating cart delivery address: " . $ex->getMessage());
        }
    }

    private function get_first_address_of_type($addresses, $type)
    {
        foreach ($addresses as $addr) {
            if ($addr->type == $type)
                return $addr;
        }

        return null;
    }

    protected function format_address_params($args)
    {
        $params = [];

        if (isset($args['city']) && !empty($args['city']))
            $params['city'] = str_replace('"', '\"', $args['city']);

        if (isset($args['code']) && !empty($args['code']))
            $params['code'] = $args['code'];

        if (isset($args['company']) && !empty($args['company']))
            $params['company'] = str_replace('"', '\"', $args['company']);

        if (isset($args['country']) && !empty($args['country']))
            $params['country'] = str_replace('"', '\"', $args['country']);

        if (isset($args['email']) && !empty($args['email']))
            $params['email'] = $args['email'];

        if (isset($args['firstName']) && !empty($args['firstName']))
            $params['firstName'] = str_replace('"', '\"', $args['firstName']);
        else {
            if ($this->cart->postageData->method == $this->get_order_types()->items[OrderType::PICKUP]->value)
                $params['firstName'] = "/";
            else
                $params['firstName'] = SessionController::get(PROPELLER_USER_DATA)->firstName;
        }

        if (isset($args['lastName']) && !empty($args['lastName']))
            $params['lastName'] = str_replace('"', '\"', $args['lastName']);
        else {
            if ($this->cart->postageData->method == $this->get_order_types()->items[OrderType::PICKUP]->value)
                $params['lastName'] = "/";
            else
                $params['lastName'] = SessionController::get(PROPELLER_USER_DATA)->lastName;
        }

        if (isset($args['middleName']) && !empty($args['middleName']))
            $params['middleName'] = str_replace('"', '\"', $args['middleName']);

        $params['gender'] = (isset($args['gender']) && !empty($args['gender'])) ? $args['gender'] : "U";

        if (isset($args['notes']) && !empty($args['notes']))
            $params['notes'] = str_replace('"', '\"', $args['notes']);

        if (isset($args['number']) && !empty($args['number']))
            $params['number'] = strval($args['number']);

        if (isset($args['numberExtension']) && !empty($args['numberExtension']))
            $params['numberExtension'] = strval($args['numberExtension']);

        if (isset($args['postalCode']) && !empty($args['postalCode']))
            $params['postalCode'] = strval($args['postalCode']);

        if (isset($args['region']) && !empty($args['region']))
            $params['region'] = strval($args['region']);

        if (isset($args['street']) && !empty($args['street']))
            $params['street'] = str_replace('"', '\"', $args['street']);

        $params['icp'] = apply_filters('propel_ics_icp', $args);

        if (isset($args['phone']) && !empty($args['phone']))
            $params['phone'] = strval($args['phone']);

        $params['type'] = isset($args['type']) ? $args['type'] : AddressTypeCart::DELIVERY;

        return $params;
    }

    public function init_user_cart($is_login = false)
    {
        $result = new stdClass();
        $result->error = false;
        $result->message = "";

        $guest_cart = null;

        if ($is_login) {
            if (SessionController::has(PROPELLER_CART) && isset(SessionController::get(PROPELLER_CART)->customerId) && SessionController::get(PROPELLER_CART)->customerId == PROPELLER_ANONYMOUS_USER) {
                $guest_cart = SessionController::get(PROPELLER_CART);
                $delete_cart_data = $this->delete_cart($guest_cart->cartId);

                if (!$delete_cart_data)
                    propel_log('Previous cart could not be deleted:' . isset($guest_cart->cartId) ? $guest_cart->cartId : "");
            }
        }

        $params = [];

        if (UserController::is_contact()) {
            $params['contactIds'] = [SessionController::get(PROPELLER_USER_DATA)->userId];
            $params['companyIds'] = [SessionController::get(PROPELLER_CONTACT_COMPANY_ID)];
        } else if (UserController::is_customer())
            $params['customerIds'] = [SessionController::get(PROPELLER_USER_DATA)->userId];

        $params['statuses'] = [
            'OPEN'
        ];

        $params['page'] = 1;
        $params['offset'] = 1000;

        $cartUserData = $this->get_carts($params);

        if ($cartUserData) {
            if ($guest_cart) {
                $merge_result = $this->merge_guest_cart($guest_cart, SessionController::get(PROPELLER_CART)->cartId);

                if ($merge_result->error) {
                    $result->error = true;

                    if (!$merge_result)
                        propel_log('Some cart items could not be merged: ' . SessionController::get(PROPELLER_CART)->cartId);
                }
            }

            // $this->update_cart_prices(SessionController::get(PROPELLER_CART));

            if (empty($this->cart->invoiceAddress->street)) {
                try {
                    $this->set_user_default_cart_address();
                } catch (Exception $ex) {
                }
            }
        } else {
            $result = $this->start();
        }

        return $result;
    }

    public function get_carts($params, $return_carts = false)
    {
        $type = 'carts';

        $gql = $this->model->get_carts(
            $params
        );

        $cartData = $this->query($gql, $type);

        if ($return_carts)
            return $cartData;

        if (is_object($cartData) && $cartData->itemsFound > 0) {
            // foreach ($cartData->items as $c) {
            //     $delres = $this->delete_cart($c->cartId);

            //     var_dump("deleting cart {$c->cartId}");
            //     var_dump($delres);
            // }

            // die;

            $this->clear_cart();

            $user_cart = $this->get_user_cart($cartData->items[$cartData->itemsFound - 1]->cartId);

            $this->start_postprocess($user_cart);

            SessionController::set(PROPELLER_CART_INITIALIZED, true);

            return true;
        } else {
            $this->start();

            return true;
        }

        return false;
    }

    public function get_user_cart($cart_id)
    {
        $type = 'cart';

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $params = [
            'cartId' => $cart_id
        ];

        $gql = $this->model->get_user_cart(
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        $cartData = $this->query($gql, $type);

        return $cartData;
    }

    public function delete_cart($cart_id)
    {
        if (!$cart_id || empty($cart_id))
            return true;

        $type = 'cartDelete';

        $params = [
            'cart_id' => $cart_id
        ];

        $gql = $this->model->delete_cart($params);

        return $this->query($gql, $type);
    }

    public function clear_carts()
    {
        $params = [];

        if (UserController::is_contact()) {
            $params['contactIds'] = [SessionController::get(PROPELLER_USER_DATA)->userId];
            $params['companyIds'] = [SessionController::get(PROPELLER_CONTACT_COMPANY_ID)];
        } else if (UserController::is_customer())
            $params['customerIds'] = [SessionController::get(PROPELLER_USER_DATA)->userId];

        $cartsData = $this->get_carts($params, true);

        $responses = [];

        if (is_object($cartsData)) {
            foreach ($cartsData->items as $cart)
                $responses[] = $this->delete_cart($cart->cartId);
        }

        return $responses;
    }

    public function merge_guest_cart($old_cart, $new_cart_id)
    {
        $result = new stdClass();
        $result->error = false;
        $result->messages = [];

        if (!$old_cart)
            return $result;

        if (count($old_cart->items)) {
            $merge_responses = [];

            foreach ($old_cart->items as $item) {
                if (is_null($item->bundleId) || empty($item->bundleId)) {
                    $child_items = [];

                    if (count($item->childItems)) {
                        foreach ($item->childItems as $child)
                            $child_items[] = $child->productId;
                    }

                    $merge_responses[] = $this->add_item($item->quantity, $item->product->productId, $item->clusterId, $item->notes, null, $child_items);
                } else
                    $merge_responses[] = $this->add_item_bundle($item->quantity, $item->bundleId, $item->notes, null, []);
            }

            foreach ($merge_responses as $response) {
                if ($response->postprocess->error) {
                    $result->messages[] = $response->postprocess->message;
                    $result->error = true;
                }
            }
        }

        return $result;
    }

    public function purchase_authorizations($args)
    {
        $type = 'carts';

        $gql = $this->model->get_purchase_authorizations($args);

        $purchase_authorizations = $this->query($gql, $type);

        if (is_object($purchase_authorizations) && isset($purchase_authorizations->itemsFound) && $purchase_authorizations->itemsFound > 0)
            return $purchase_authorizations;

        return null;
    }

    public function accept_purchase_authorization_request($cart_id, $contact_id)
    {
        $type = 'cartAcceptPurchaseAuthorizationRequest';

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $args = [
            'cart_id' => $cart_id,
            'input' => [
                'contactId' => $contact_id
            ]
        ];

        $gql = $this->model->accept_purchase_authorization_request($args, $images_fragment, PROPELLER_LANG);

        $cart_data = $this->query($gql, $type);

        if (is_object($cart_data)) {
            SessionController::set(PROPELLER_CART_INITIALIZED, true);

            $this->start_postprocess($cart_data);

            // if (UserController::is_propeller_logged_in()) {
            //     try {
            //         $this->set_user_default_cart_address();
            //     } catch (Exception $ex) {
            //     }
            // }
            return true;
        }

        return false;
    }

    public function submit_purchase_request($cart_id)
    {
        $type = 'cartRequestPurchaseAuthorization';

        $args = [
            'cart_id' => $cart_id
        ];

        $gql = $this->model->submit_purchase_request($args);

        $cart_data = $this->query($gql, $type);

        if (is_object($cart_data) && isset($cart_data->cartId) && $cart_data->cartId == $cart_id)
            return true;

        return false;
    }

    /**
     * 
     *  GETTERS
     * 
     */
    public function get_cart()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart;
    }

    public function get_items()
    {
        $items = [];

        if (!$this->cart) $this->init_cart();

        if (isset($this->cart->items))
            $items = $this->cart->items;

        return $items;
    }

    public function get_item_by_product_id($product_id)
    {
        if (!$this->cart) $this->init_cart();

        $item = array_filter($this->get_items(), function ($item) use ($product_id) {
            return $item->productId == $product_id;
        });

        if (count($item)) {
            $item = current($item);

            $item->product = new Product($item->product);

            return $item;
        }

        return null;
    }

    public function get_item_by_id($item_id)
    {
        if (!$this->cart) $this->init_cart();

        foreach ($this->get_items() as $item) {
            if ($item->id == $item_id) {
                return $item;
            }
        }

        return null;
    }

    public function get_item_by_bundle_id($bundle_id)
    {
        if (!$this->cart) $this->init_cart();

        foreach ($this->get_items() as $item) {
            if ($item->bundleId == $bundle_id)
                return $item;
        }

        return null;
    }
    public function get_items_count()
    {
        if (!$this->cart) $this->init_cart();

        $count = 0;

        foreach ($this->get_items() as $item)
            $count += $item->quantity;

        return $count;
    }

    public function get_payment_data()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->paymentData;
    }

    public function get_postage_data()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->postageData;
    }

    public function get_totals()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->total;
    }

    public function get_total_price()
    {
        if (!$this->cart) $this->init_cart();

        if (!$this->cart || !$this->cart->total)
            return 0.00;

        return $this->cart->total->totalNet;
    }

    public function get_date_created()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->dateCreated;
    }

    public function get_notes()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->notes;
    }

    public function get_carrier()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->carriers;
    }

    public function get_action_code()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->actionCode;
    }

    public function get_invoice_address()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->invoiceAddress;
    }

    public function get_delivery_address()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->deliveryAddress;
    }

    public function get_tax_levels()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->taxLevels;
    }

    public function get_pay_methods()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->payMethods;
    }

    public function get_carriers()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->carriers;
    }

    public function get_reference()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->reference;
    }

    public function get_extra3()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->extra3;
    }

    public function get_extra4()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->extra4;
    }

    public function get_request_date()
    {
        if (!$this->cart) $this->init_cart();

        return $this->get_postage_data()->requestDate;
    }

    public function is_anonymous_cart()
    {
        if (!$this->cart) $this->init_cart();

        return $this->cart->contactId == PROPELLER_ANONYMOUS_USER;
    }

    public function get_carriers_temp()
    {
        $gql = $this->model->get_carriers_temp(20);

        $carriers = $this->query($gql, 'carriers');

        return $carriers;
    }

    public function get_shipping_methods()
    {
        if (false === ($result = CacheController::get('propeller_SYSTEM_SHIPPING_METHOD'))) {
            $valsets = new ValuesetController();

            $result = $valsets->get_valueset(['names' => ['SYSTEM_SHIPPING_METHOD']]);

            CacheController::set('propeller_SYSTEM_SHIPPING_METHOD', $result, WEEK_IN_SECONDS);
        }

        return is_object($result) ? $result : null;
    }

    public function get_order_types()
    {
        return $this->get_shipping_methods();
    }
}
