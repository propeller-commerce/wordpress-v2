<?php

namespace Propeller\Includes\Controller;

if (! defined('ABSPATH')) exit;

use Exception;
use Propeller\Includes\Enum\AddressType;
use Propeller\Includes\Enum\AddressTypeCart;
use Propeller\Includes\Enum\CrossupsellTypes;
use Propeller\Includes\Enum\OrderType;
use Propeller\Includes\Enum\PageType;
use stdClass;

class ShoppingCartAjaxController extends BaseAjaxController
{
    protected $shoppingCart;

    public function __construct()
    {
        parent::__construct();

        $this->shoppingCart = new ShoppingCartController();
    }

    public function load_mini_cart()
    {
        $this->init_ajax();

        $response = $this->shoppingCart->load_mini_cart();

        die(json_encode($response));
    }

    public function cart_add_item()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            // Clamp quantity to 32-bit signed integer max to prevent API errors
            if (isset($data['quantity']) && intval($data['quantity']) > 999999)
                $data['quantity'] = 999999;

            $price = apply_filters('propel_shopping_cart_get_item_price', 'dummy', $data['product_id'], (int) $data['quantity']);

            $child_items = [];

            if (isset($data['options']) && !empty($data['options']) && trim($data['options']) != ',')
                $child_items = explode(',', $data['options']);

            $response = $this->shoppingCart->add_item(
                (int) $data['quantity'],
                $data['product_id'],
                isset($data['cluster_id']) ? $data['cluster_id'] : null,
                (isset($data['notes']) ? $data['notes'] : ''),
                $price,
                $child_items
            );

            $response->modal = "add-to-basket-modal";
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function cart_add_items_bulk()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            if (isset($data['product_id']) && is_array($data['product_id']) && count($data['product_id'])) {
                $responses = [];
                $child_items = [];

                foreach ($data['product_id'] as $index => $product_id) {
                    $price = apply_filters('propel_shopping_cart_get_item_price', 'dummy', $product_id, (int) $data['quantity'][$index]);

                    if (isset($data['quantity'][$index])) {
                        $responses[] = $this->shoppingCart->add_item(
                            (int) $data['quantity'][$index],
                            $product_id,
                            (isset($data['notes'][$index]) ? $data['notes'][$index] : ''),
                            $price,
                            $child_items
                        );
                    }
                }

                $has_errors = false;

                foreach ($responses as $response) {
                    if ($response->postprocess->error) {
                        $items_messages[] = '<span class="text-danger">' . $response->postprocess->message . '</span>';
                        $has_errors = true;
                    } else {
                        $added_item = $this->shoppingCart->get_item_by_product_id($response->postprocess->item->productId);

                        $items_messages[]    = '<span class="text-success">' . $added_item->product->get_name() . " added to cart</span>";

                        ob_start();
                        require $this->shoppingCart->load_template('partials', '/cart/propeller-shopping-cart-popup-item.php');

                        $item_content = ob_get_clean();
                        $items_content[] = $item_content;
                    }
                }

                $postprocess->content       = implode('', $items_content);
                $postprocess->message       = implode('<br />', $items_messages);
                $postprocess->badge         = $this->shoppingCart->get_items_count();
                $postprocess->totals        = $this->shoppingCart->get_totals();
                $postprocess->taxLevels     = $this->shoppingCart->get_tax_levels();
                $postprocess->postageData   = $this->shoppingCart->get_postage_data();
                $postprocess->show_modal    = !$has_errors;
                $postprocess->error         = $has_errors;
            }

            $response->postprocess = $postprocess;

            $response->modal = "add-to-basket-modal";
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        $response->object = "Cart";

        die(json_encode($response));
    }

    public function cart_add_bundle()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $response = $this->shoppingCart->add_item_bundle(
                $data['quantity'],
                $data['bundle_id'],
                (isset($data['notes']) ? $data['notes'] : '')
            );

            $response->modal = "add-to-basket-modal";
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function cart_update_item()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            // Clamp quantity to 32-bit signed integer max to prevent API errors
            if (isset($data['quantity']) && intval($data['quantity']) > 999999)
                $data['quantity'] = 999999;

            $item = $this->shoppingCart->get_item_by_id($data['item_id']);

            // if has child items, it's a configurable cluster and update quantity for all child items
            if (is_null($item->bundleId) && isset($item->childItems) && is_array($item->childItems) && count($item->childItems)) {
                $cluster_items = [];

                $cluster_items[$data['item_id']] = apply_filters('propel_shopping_cart_get_item_price', 'dummy', $data['item_id'], (int) $data['quantity']);

                foreach ($item->childItems as $child_item) {
                    $cluster_items[$child_item->itemId] = apply_filters('propel_shopping_cart_get_item_price', 'dummy', $child_item->id, (int) $data['quantity']);

                    $response = $this->shoppingCart->update_item(
                        $data['item_id'],
                        $data['quantity'],
                        (isset($data['notes']) ? $data['notes'] : ''),
                        $cluster_items
                    );
                }
            } else {
                $price = apply_filters('propel_shopping_cart_get_item_price', 'dummy', $data['item_id'], (int) $data['quantity']);

                $response = $this->shoppingCart->update_item(
                    $data['item_id'],
                    $data['quantity'],
                    (isset($data['notes']) ? $data['notes'] : ''),
                    $price
                );
            }
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function cart_delete_item()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $response = $this->shoppingCart->delete_item(
                $data['item_id']
            );
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function cart_add_action_code()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            preg_match('/[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}/', $data['actionCode'], $result);

            $response = sizeof($result) > 0
                ? $this->shoppingCart->voucher_code($data['actionCode'])
                : $this->shoppingCart->action_code($data['actionCode']);
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function cart_remove_action_code()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);
            preg_match('/[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}/', $data['actionCode'], $result);

            $response = sizeof($result) > 0
                ? $this->shoppingCart->remove_voucher_code($data['actionCode'])
                : $this->shoppingCart->remove_action_code($data['actionCode']);
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function load_item_crossupsells()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $response = $this->shoppingCart->load_item_crossupsells((int) $data['product_id'], [
                CrossupsellTypes::ACCESSORIES
            ]);
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function cart_update()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $response = $this->shoppingCart->update(
                (array) $this->shoppingCart->get_payment_data(),
                (array) $this->shoppingCart->get_postage_data(),
                $this->shoppingCart->get_notes(),
                $this->shoppingCart->get_reference(),
                $this->shoppingCart->get_extra3(),
                $this->shoppingCart->get_extra4(),
                $this->shoppingCart->get_carriers()
            );
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        $response->object = 'Cart';

        die(json_encode($response));
    }

    public function cart_update_address()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $addr_response = null;

            $response = $this->shoppingCart->update_address($data);
            $address = new AddressController();

            $address->set_user();

            if ($data['type'] == AddressTypeCart::INVOICE) {
                if (isset($data['save_invoice_address']) && UserController::is_propeller_logged_in()) {
                    $data['type'] = AddressType::INVOICE;
                    $address->update_address($data);
                }


                if (isset($data['use_as_delivery_address']) && !UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS) {
                    $data['type'] = AddressTypeCart::DELIVERY;
                    $response = $this->shoppingCart->update_address($data);
                }
            }

            if (isset($data['update_delivery_address']) && UserController::is_propeller_logged_in()) {
                $data['type'] = AddressType::DELIVERY;
                $addr_response = $address->update_address($data);
            }


            if (isset($data['add_delivery_address']) && UserController::is_propeller_logged_in()) {
                $data['type'] = AddressType::DELIVERY;
                $addr_response = $address->add_address($data);
            }

            if (isset($addr_response->id))
                SessionController::set(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED, $addr_response->id);

            if (isset($data['subaction']) && $data['subaction'] == 'cart_update_delivery_address')
                SessionController::set(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED, true);
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;
            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function do_replenish()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_REQUEST);

            $response = $this->shoppingCart->replenish(explode(',', $data['items']));

            $response->modal = "add-to-basket-modal";
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;
            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function cart_change_order_type()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $response = $this->shoppingCart->change_order_type($data['order_type']);
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;
            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function cart_step_1()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            if (isset($data['invoice_address'])) {
                $addressController = new AddressController();
                $addressController->set_user();

                $invoice_addresses = $addressController->get_addresses(['type' => AddressType::INVOICE]);
                $selected_address_id = (int) $data['invoice_address'];

                $found = array_filter($invoice_addresses, function ($obj) use ($selected_address_id) {
                    return isset($obj->id) && $obj->id == $selected_address_id;
                });

                if (count($found)) {
                    current($found)->type = AddressTypeCart::INVOICE;
                    $response = $this->shoppingCart->update_address((array) current($found));

                    SessionController::set(PROPELLER_DEFAULT_INVOICE_ADDRESS_CHANGED, $selected_address_id);
                }
            }
            // $response = $this->shoppingCart->update_address($data);

            $postprocess = new stdClass();
            $postprocess->success = true;
            $postprocess->message = __("Proceeding to next step", 'propeller-ecommerce-v2');
            $postprocess->redirect = esc_url_raw(home_url("/" . PageController::get_slug(PageType::CHECKOUT_PAGE) . "/" . $data['next_step'] . '/'));

            $response->object = 'Cart';

            $response->postprocess = $postprocess;
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function cart_step_2()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $method = $data['method'];
            $addressController = new AddressController();
            $addressController->set_user();

            $this->shoppingCart->change_order_type($method, false);

            if (!UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS) {
                if ($method == $this->shoppingCart->get_order_types()->items[OrderType::PICKUP]->value) {
                    $pickup_address = $addressController->get_external_addresses(['id' => (int) $data['delivery_address']]);

                    if (is_object($pickup_address)) {
                        $pickup_address->type = AddressType::DELIVERY;
                        $pickup_address->email = $data['orderconfirm_email'];

                        $response = $this->shoppingCart->update_address((array) $pickup_address);

                        SessionController::set(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED, $pickup_address->id);
                    }
                } else {
                    $response = $this->shoppingCart->update_address($data);
                }
            } else {
                if (isset($data['add_delivery_address']) && $data['add_delivery_address'] == 'Y') {
                    $response = $this->shoppingCart->update_address($data);

                    if (isset($data['save_delivery_address'])) {
                        $add_response = $addressController->add_address($data);

                        if (isset($add_response->id))
                            SessionController::set(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED, $add_response->id);
                    }
                } else if (isset($data['delivery_address'])) {
                    if ($method == $this->shoppingCart->get_order_types()->items[OrderType::REGULAR]->value) {
                        $delivery_addresses = $addressController->get_addresses(['type' => AddressType::DELIVERY]);
                        $selected_address_id = (int) $data['delivery_address'];

                        $found = array_filter($delivery_addresses, function ($obj) use ($selected_address_id) {
                            return isset($obj->id) && $obj->id == $selected_address_id;
                        });

                        if (count($found)) {
                            current($found)->type = AddressTypeCart::DELIVERY;
                            $response = $this->shoppingCart->update_address((array) current($found));

                            SessionController::set(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED, $selected_address_id);
                        }
                    } else if ($method == $this->shoppingCart->get_order_types()->items[OrderType::PICKUP]->value) {
                        $pickup_address = $addressController->get_external_addresses(['id' => (int) $data['delivery_address']]);

                        if (is_object($pickup_address)) {
                            $pickup_address->type = AddressTypeCart::DELIVERY;
                            $pickup_address->email = $data['orderconfirm_email'];

                            $response = $this->shoppingCart->update_address((array) $pickup_address);

                            SessionController::set(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED, $pickup_address->id);
                        }
                    }
                }
            }

            $postprocess = new stdClass();
            $postprocess->success = true;
            $postprocess->message = __("Address updated", 'propeller-ecommerce-v2');
            $postprocess->redirect = esc_url_raw(home_url("/" . PageController::get_slug(PageType::CHECKOUT_PAGE) . "/" . $data['next_step'] . '/'));
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;
        }

        $response->object = 'Cart';

        $response->postprocess = $postprocess;

        die(json_encode($response));
    }

    public function cart_step_3()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $payment_data_arr = [
                'method' => $data['payMethod']
            ];

            $postage_data_arr = [
                'partialDeliveryAllowed' => isset($data['partialDeliveryAllowed']) ? $data['partialDeliveryAllowed'] : "N"
            ];

            if (isset($data['delivery_select']))
                $postage_data_arr['requestDate'] = $data['delivery_select'];
            if (isset($data['carrier']))
                $postage_data_arr['carrier'] = $data['carrier'];

            $cartupdate = $this->shoppingCart->update(
                $payment_data_arr,
                $postage_data_arr,
                $this->shoppingCart->get_notes(),
                $this->shoppingCart->get_reference(),
                $this->shoppingCart->get_extra3(),
                $this->shoppingCart->get_extra4()
            );

            if (!is_object($cartupdate)) {
                $response->message = $cartupdate;
            } else {
                $postprocess->success = true;
                $postprocess->message = __("Completed", 'propeller-ecommerce-v2');
                $postprocess->redirect = esc_url_raw($this->shoppingCart->buildUrl('', PageController::get_slug(PageType::CHECKOUT_SUMMARY_PAGE)));

                if ($this->shoppingCart->analytics) {
                    $this->shoppingCart->analytics->setData($this->shoppingCart->get_cart());

                    ob_start();

                    if (isset($data['payMethod']))
                        apply_filters('propel_ga4_fire_event', 'add_payment_info');

                    if (isset($data['carrier']))
                        apply_filters('propel_ga4_fire_event', 'add_shipping_info');

                    $postprocess->analytics = ob_get_clean();
                }
            }
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;
        }

        $response->object = 'Cart';

        $response->postprocess = $postprocess;

        die(json_encode($response));
    }

    public function cart_process()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_REQUEST);

            if (!UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS && SessionController::has('anonymous_order_id'))
                SessionController::remove('anonymous_order_id');

            $response = $this->shoppingCart->process($data);
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        $response->object = 'Checkout';

        die(json_encode($response));
    }

    public function cart_change_order_status()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $response = new stdClass();

            if (empty($this->shoppingCart->invoiceAddress->street)) {
                try {
                    $this->shoppingCart->set_user_default_cart_address();
                } catch (Exception $ex) {
                }
            }

            $this->shoppingCart->check_order_type();

            $this->shoppingCart->set_order_status($data);

            $response->success = true;
        } else {
            $postprocess->message       = __("Security check failed", "propeller-ecommerce-v2");
            $postprocess->error = true;
            $postprocess->success = false;

            $response->postprocess = $postprocess;
        }

        die(json_encode($response));
    }

    public function submit_purchase_request()
    {
        $this->init_ajax();

        $response = new stdClass();
        $response->success = false;
        $response->message = __('We were unable to submit you authorization request at this moment.', 'propeller-ecommerce-v2');

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $response->success = $this->shoppingCart->submit_purchase_request($data['cart_id']);

            if ($response->success) {
                $response->message = __('Your authorization request has been submitted successfully.', 'propeller-ecommerce-v2');
                $response->redirect = $this->shoppingCart->buildUrl('/' . PageController::get_slug(PageType::PURCHASE_AUTHORIZATION_THANK_YOU) . '/' . $data['cart_id'] . '/', '');
            }
        }

        die(json_encode($response));
    }

    public function clear_oci_cart()
    {
        $delete_cart = $this->shoppingCart->delete_cart($_REQUEST['cart_id']);

        die(json_encode(['success' => $delete_cart]));
    }
}
