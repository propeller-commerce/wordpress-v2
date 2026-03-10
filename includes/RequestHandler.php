<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\BaseController;
use Propeller\Includes\Controller\CategoryController;
use Propeller\Includes\Controller\FavoriteController;
use Propeller\Includes\Controller\MachineController;
use Propeller\Includes\Controller\OrderController;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\ProductController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\ShoppingCartController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\OrderStatus;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\PaymentStatuses;
use Propeller\Includes\Enum\ProductClass;

class RequestHandler
{

    public function process($query_vars)
    {
        global $propel;

        UserController::set_default_tax_zone();

        if (isset($query_vars['pagename'])) {
            $page_chunks = [];

            $page_chunks[0] = $query_vars['pagename'];

            if (strpos($query_vars['pagename'], '/') !== false)
                $page_chunks = explode('/', $query_vars['pagename']);

            switch ($page_chunks[0]) {
                case PageController::get_slug(PageType::CATEGORY_PAGE):
                    $ref = 'Propeller\Custom\Includes\Controller\CategoryController';

                    $categoryObj = class_exists($ref, true)
                        ? new $ref()
                        : new CategoryController();

                    $applied_filters = PropellerUtils::sanitize($_REQUEST);

                    if (!isset($applied_filters['sortInputs']))
                        $applied_filters['sortInputs'] = PROPELLER_DEFAULT_SORT_FIELD . ',' . PROPELLER_SECONDARY_SORT_FIELD . ',' . PROPELLER_DEFAULT_SORT_DIRECTION;

                    $filters_applied = $categoryObj->process_filters($applied_filters);

                    $qry_params = $categoryObj->build_search_arguments(array_merge($applied_filters, $filters_applied));

                    $slug = isset($applied_filters['slug']) ? $applied_filters['slug'] : $query_vars['slug'];
                    $categoryId = null;

                    if (isset($query_vars['obid']) && is_numeric($query_vars['obid']) && PROPELLER_ID_IN_URL)
                        $categoryId = (int) $query_vars['obid'];

                    $data = isset($propel['data']) && is_object($propel['data']) ? $propel['data'] : $categoryObj->get_catalog($slug, $categoryId, $qry_params);

                    if (is_object($data) && !$categoryId && !empty($slug)) {
                        $found = array_filter($data->slugs, function ($obj) {
                            return $obj->language == PROPELLER_LANG;
                        });

                        if (count($found)) {
                            $current_lang_slug = current($found);

                            if ($current_lang_slug->value != $slug) {
                                propel_log("Using slug from a different language: $slug instead $current_lang_slug->value (" . PROPELLER_LANG . ")");
                                $propel['error_404'] = 'Category';
                            }
                        }
                    }

                    if (!is_object($data)) {
                        propel_log(print_r($data, true));
                        $propel['error_404'] = 'Category';
                    } else {
                        if (!isset($propel['error_404'])) {
                            $propel['url_slugs'] = $data->slugs;

                            $propel['data'] = $data;
                            $propel['title'] = $data->name[0]->value . " | " .  get_bloginfo('name');
                            $propel['description'] = trim(preg_replace('/\s+/', ' ', $data->shortDescription[0]->value));

                            $propel['breadcrumbs'] = $this->pack_breadcrumbs($data, PageController::get_slug(PageType::CATEGORY_PAGE));

                            $propel['meta'] = $this->parse_meta($data);

                            if (isset($data->images) && is_array($data->images) && count($data->images) > 0)
                                $propel['meta']['image'] = $data->images[0]->url;

                            do_action('propel_category_seo', $data);
                        }
                    }

                    break;
                case PageController::get_slug(PageType::PRODUCT_PAGE):
                    if (!isset($query_vars['slug']) || empty($query_vars['slug'])) {
                        $propel['error_404'] = 'Product';
                    } else {
                        $ref = 'Propeller\Custom\Includes\Controller\ProductController';

                        $productObj = class_exists($ref, true)
                            ? new $ref()
                            : new ProductController();

                        $slug = $query_vars['slug'];
                        $productId = null;

                        if (isset($query_vars['obid']) && is_numeric($query_vars['obid']) && PROPELLER_ID_IN_URL)
                            $productId = (int) $query_vars['obid'];

                        $data = isset($propel['data']) && is_object($propel['data']) ? $propel['data'] : $productObj->get_product($slug, $productId, []);

                        if (isset($data->exists) && !$data->exists) {
                            propel_log(print_r($data, true));

                            if (isset($data->languages)) {
                                $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                                $default_lang_url = $productObj->get_product_default_lang_url(PROPELLER_DEFAULT_LOCALE, $current_url);

                                if ($default_lang_url) {
                                    wp_safe_redirect($default_lang_url);
                                    die;
                                } else {
                                    $propel['error_404'] = 'Product';
                                    propel_log($current_url . ' not found in default language ' . PROPELLER_DEFAULT_LOCALE);
                                }
                            } else {
                                $propel['error_404'] = 'Product';
                                propel_log($query_vars['slug'] . ' not found in default language ' . PROPELLER_DEFAULT_LOCALE);
                            }
                        } else {
                            if (isset($data->status) && $data->status == 'N')
                                $propel['error_404'] = 'Product';
                            else {
                                $productObj->preserve_recently_viewed($data->class . '-' . $data->urlId);

                                $propel['url_slugs'] = $data->slugs;

                                $propel['data'] = $data;

                                $propel['title'] = $data->name[0]->value . " | " .  get_bloginfo('name');
                                $propel['description'] = trim(preg_replace('/\s+/', ' ', $data->shortDescription[0]->value));

                                $propel['breadcrumbs'] = $this->pack_breadcrumbs($data, PageController::get_slug(PageType::PRODUCT_PAGE));

                                $propel['meta'] = $this->parse_meta($data);

                                if (
                                    isset($data->media->images->items) && is_array($data->media->images->items) && count($data->media->images->items) > 0 &&
                                    isset($data->media->images->items[0]->imageVariants) && is_array($data->media->images->items[0]->imageVariants) && count($data->media->images->items[0]->imageVariants) > 0
                                )
                                    $propel['meta']['image'] = $data->media->images->items[0]->imageVariants[0]->url;

                                do_action('propel_product_seo', $data);
                            }
                        }
                    }

                    break;
                case PageController::get_slug(PageType::CLUSTER_PAGE):
                    if (!isset($query_vars['slug']) || empty($query_vars['slug'])) {
                        $propel['error_404'] = 'Product';
                    } else {
                        $ref = 'Propeller\Custom\Includes\Controller\ProductController';

                        $productObj = class_exists($ref, true)
                            ? new $ref()
                            : new ProductController();

                        $slug = $query_vars['slug'];
                        $clusterId = null;

                        if (isset($query_vars['obid']) && is_numeric($query_vars['obid']) && PROPELLER_ID_IN_URL)
                            $clusterId = (int) $query_vars['obid'];

                        $data = isset($propel['data']) && is_object($propel['data']) ? $propel['data'] : $productObj->get_cluster($slug, $clusterId, []);

                        if (isset($data->exists) && !$data->exists) {
                            propel_log(print_r($data, true));

                            if (isset($data->languages)) {
                                $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                                $default_lang_url = $productObj->get_product_default_lang_url(PROPELLER_DEFAULT_LOCALE, $current_url);

                                if ($default_lang_url) {
                                    wp_safe_redirect($default_lang_url);
                                    die;
                                } else {
                                    $propel['error_404'] = 'Product';
                                    propel_log($current_url . ' not found in default language ' . PROPELLER_DEFAULT_LOCALE);
                                }
                            } else {
                                $propel['error_404'] = 'Product';
                                propel_log($query_vars['slug'] . ' not found in default language ' . PROPELLER_DEFAULT_LOCALE);
                            }
                        } else {
                            if (isset($data->status) && $data->status == 'N')
                                $propel['error_404'] = 'Product';
                            else {
                                if (is_null($data->defaultProduct)) {
                                    $propel['error_404'] = 'Product';
                                    propel_log($query_vars['slug'] . ' no default product, probably all not available');
                                } else {
                                    $productObj->preserve_recently_viewed($data->class . '-' . $data->urlId);

                                    $productObj->preserve_cluster($data->urlId, $data);

                                    $propel['url_slugs'] = $data->slugs;

                                    $propel['data'] = $data;

                                    $propel['title'] = $data->name[0]->value . " | " .  get_bloginfo('name');
                                    $propel['description'] = trim(preg_replace('/\s+/', ' ', $data->shortDescription[0]->value));

                                    $propel['breadcrumbs'] = $this->pack_breadcrumbs($data, PageController::get_slug(PageType::CLUSTER_PAGE));

                                    $propel['meta'] = $this->parse_meta($data);

                                    if (
                                        isset($data->defaultProduct->media->images->items) && is_array($data->defaultProduct->media->images->items) && count($data->defaultProduct->media->images->items) > 0 &&
                                        isset($data->defaultProduct->media->images->items[0]->imageVariants) && is_array($data->defaultProduct->media->images->items[0]->imageVariants) && count($data->defaultProduct->media->images->items[0]->imageVariants) > 0
                                    )
                                        $propel['meta']['image'] = $data->media->images->items[0]->imageVariants[0]->url;

                                    do_action('propel_product_seo', $data);
                                }
                            }
                        }
                    }

                    break;
                case PageController::get_slug(PageType::SEARCH_PAGE):
                    $ref = 'Propeller\Custom\Includes\Controller\ProductController';

                    $productObj = class_exists($ref, true)
                        ? new $ref()
                        : new ProductController();

                    $applied_filters = PropellerUtils::sanitize($_REQUEST);

                    if (!isset($applied_filters['sortInputs']))
                        $applied_filters['sortInputs'] = 'RELEVANCE,' . PROPELLER_DEFAULT_SORT_DIRECTION;

                    if (!isset($applied_filters['term']) && isset($query_vars['term']))
                        $applied_filters['term'] = $query_vars['term'];

                    $filters_applied = $productObj->process_filters($applied_filters);
                    $qry_params = $productObj->build_search_arguments(array_merge($applied_filters, $filters_applied));

                    $term = isset($applied_filters['term']) ? $applied_filters['term'] : $query_vars['term'];
                    $term = wp_unslash($term);
                    $term = urldecode($term);

                    if (!empty($term)) {
                        $term = str_replace('"', '\"', $term);
                        $qry_params['term'] = $term;
                    }

                    $data = isset($propel['data']) && is_object($propel['data']) ? $propel['data'] : $productObj->get_products($qry_params);

                    if (!is_object($data)) {
                        propel_log(print_r($data, true));
                        $propel['error_404'] = 'Page not found';
                    } else {
                        $propel['meta'] = [
                            'url' => $productObj->buildUrl(PageController::get_slug(PageType::SEARCH_PAGE), $term)
                        ];

                        $propel['data'] = $data;
                        $propel['title'] = 'Search "' . $term . '"';
                        $propel['description'] = 'Search "' . $term . '"';
                    }

                    break;
                case PageController::get_slug(PageType::BRAND_PAGE):
                    $ref = 'Propeller\Custom\Includes\Controller\ProductController';

                    $productObj = class_exists($ref, true)
                        ? new $ref()
                        : new ProductController();

                    $applied_filters = PropellerUtils::sanitize($_REQUEST);

                    if (!isset($applied_filters['sortInputs']))
                        $applied_filters['sortInputs'] = PROPELLER_DEFAULT_SORT_FIELD . ',' . PROPELLER_SECONDARY_SORT_FIELD . ',' . PROPELLER_DEFAULT_SORT_DIRECTION;

                    $filters_applied = $productObj->process_filters($applied_filters);
                    $qry_params = $productObj->build_search_arguments(array_merge($applied_filters, $filters_applied));

                    $term = isset($applied_filters['manufacturer']) ? $applied_filters['manufacturer'] : $query_vars['manufacturer'];
                    $term = urldecode($term);

                    if (!empty($term))
                        $qry_params['manufacturers'] = [$term];

                    $data = isset($propel['data']) && is_object($propel['data']) ? $propel['data'] : $productObj->get_products($qry_params);

                    if (!is_object($data)) {
                        propel_log(print_r($data, true));
                        $propel['error_404'] = 'Page not found';
                    } else {
                        $propel['meta'] = [
                            'url' => $productObj->buildUrl(PageController::get_slug(PageType::BRAND_PAGE), $term)
                        ];

                        $propel['data'] = $data;
                        $propel['title'] = 'Brand "' . $term . '"';
                        $propel['description'] = 'Brand "' . $term . '"';
                    }

                    break;
                case PageController::get_slug(PageType::MACHINES_PAGE):
                    if (!UserController::is_propeller_logged_in()) {
                        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                        SessionController::set('login_referrer', $current_url);
                        SessionController::set('register_referrer', $current_url);

                        $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE));

                        wp_safe_redirect($redirect_url);
                        die;
                    }

                    $ref = 'Propeller\Custom\Includes\Controller\MachineController';

                    $machineObj = class_exists($ref, true)
                        ? new $ref()
                        : new MachineController();

                    $applied_filters = PropellerUtils::sanitize($_REQUEST);

                    if (!isset($applied_filters['sortInputs']))
                        $applied_filters['sortInputs'] = PROPELLER_DEFAULT_SORT_FIELD . ',' . PROPELLER_DEFAULT_SORT_DIRECTION;

                    $filters_applied = $machineObj->process_filters($applied_filters);
                    $qry_params = $machineObj->build_search_arguments(array_merge($applied_filters, $filters_applied));

                    $slug = $query_vars['slug'];

                    if (is_array($slug))
                        $slug = $slug[count($slug) - 1];

                    $data = $data = isset($propel['data']) && is_object($propel['data'])
                        ? $propel['data']
                        : (!isset($query_vars['slug'])
                            ? $machineObj->get_installations($qry_params)
                            : $machineObj->get_machines($slug, $qry_params));

                    if (!is_object($data)) {
                        propel_log(print_r($data, true));
                        $propel['error_404'] = 'Page not found';
                    } else {
                        $propel['meta'] = [
                            'url' => $machineObj->buildUrl(PageController::get_slug(PageType::MACHINES_PAGE), '')
                        ];

                        $propel['url_slugs'] = $data->slugs;

                        $propel['data'] = $data;
                        $propel['title'] = $data->name[0]->value . " | " .  get_bloginfo('name');
                        $propel['description'] = $data->description[0]->value;

                        $propel['breadcrumbs'] = $this->pack_breadcrumbs($data, PageController::get_slug(PageType::MACHINES_PAGE));

                        $propel['meta'] = [
                            'title' => $data->name[0]->value . " | " .  get_bloginfo('name'),
                            'description' => wp_strip_all_tags(trim($data->description[0]->value)),
                            'type' => 'machine',
                            'url' => $machineObj->buildUrl(PageController::get_slug(PageType::MACHINES_PAGE), $data->slug[0]->value),
                            'locale' => get_locale()
                        ];

                        if (isset($data->images) && is_array($data->images) && count($data->images) > 0)
                            $propel['meta']['image'] = $data->images[0]->url;
                    }

                    break;
                case PageController::get_slug(PageType::PAYMENT_CHECK_PAGE):
                    if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
                        $propel['error_404'] = 'Page not found';
                    } else {
                        $orderController = new OrderController();
                        $order = $orderController->get_order_minimal((int) $_GET['order_id']);

                        if (!is_object($order)) {
                            propel_log(print_r($order, true));
                            $propel['error_404'] = 'Page not found';
                        } else {
                            $redirect_url = home_url();

                            switch ($order->paymentData->status) {
                                case PaymentStatuses::FAILED:
                                    $redirect_url = home_url('/' . PageController::get_slug(PageType::PAYMENT_FAILED_PAGE)) . '?order_id=' . $_GET['order_id'];

                                    break;
                                case PaymentStatuses::CANCELLED:
                                    $redirect_url = home_url('/' . PageController::get_slug(PageType::PAYMENT_CANCELLED_PAGE)) . '?order_id=' . $_GET['order_id'];

                                    break;
                                case PaymentStatuses::AUTHORIZED:
                                    $redirect_url = home_url('/' . PageController::get_slug(PageType::PAYMENT_AUTHORIZATION_CONFIRMED_PAGE)) . '?order_id=' . $_GET['order_id'];

                                    break;
                                case PaymentStatuses::EXPIRED:
                                    $redirect_url = home_url('/' . PageController::get_slug(PageType::PAYMENT_EXPIRED_PAGE)) . '?order_id=' . $_GET['order_id'];

                                    break;
                                case PaymentStatuses::PENDING:
                                case PaymentStatuses::OPEN:
                                case PaymentStatuses::UNKNOWN:
                                case PaymentStatuses::EMPTY:
                                    $redirect_url = home_url('/' . PageController::get_slug(PageType::PAYMENT_PROCESSED_PAGE)) . '?order_id=' . $_GET['order_id'];

                                    break;
                                case PaymentStatuses::PAID:
                                    $redirect_url = home_url('/' . PageController::get_slug(PageType::THANK_YOU_PAGE)) . '?order_id=' . $_GET['order_id'];

                                    break;
                            }

                            wp_safe_redirect($redirect_url);
                            die;
                        }
                    }

                    break;
                case PageController::get_slug(PageType::THANK_YOU_PAGE):
                case PageController::get_slug(PageType::PAYMENT_FAILED_PAGE):
                case PageController::get_slug(PageType::PAYMENT_EXPIRED_PAGE):
                case PageController::get_slug(PageType::PAYMENT_CANCELLED_PAGE):
                case PageController::get_slug(PageType::PAYMENT_PROCESSED_PAGE):
                case PageController::get_slug(PageType::PAYMENT_AUTHORIZATION_CONFIRMED_PAGE):
                    $do_redirect = true;

                    if (PROPELLER_ANONYMOUS_ORDERS) {
                        $do_redirect = false;

                        if (!UserController::is_propeller_logged_in() && !SessionController::has('anonymous_order_id'))
                            SessionController::set('anonymous_order_id', $_GET['order_id']);
                    }

                    if (!UserController::is_propeller_logged_in() && $do_redirect) {
                        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                        SessionController::set('login_referrer', $current_url);
                        SessionController::set('register_referrer', $current_url);

                        $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE));

                        wp_safe_redirect($redirect_url);
                        die;
                    }

                    if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
                        $propel['error_404'] = 'Page not found';
                    } else if (!UserController::is_propeller_logged_in() && $do_redirect) {
                        $propel['error_403'] = 'Accesss denied';
                    } else {
                        $orderController = new OrderController();
                        $order = $orderController->get_order_minimal((int) $_GET['order_id']);

                        if (!is_object($order)) {
                            propel_log(print_r($order, true));
                            $propel['error_404'] = 'Page not found';
                        } else {
                            if (UserController::is_propeller_logged_in() && SessionController::get(PROPELLER_USER_DATA)->userId != $order->userId)
                                $propel['error_403'] = 'Accesss denied';
                            else if (
                                !UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS && SessionController::has('anonymous_order_id') &&
                                ($_GET['order_id'] != SessionController::get('anonymous_order_id') || $order->userId != PROPELLER_ANONYMOUS_USER)
                            )
                                $propel['error_403'] = 'Accesss denied';
                            else {
                                $default_paymethods = explode(',', PROPELLER_ONACCOUNT_PAYMENTS);

                                $reinitialize = false;

                                if ((in_array($order->paymentData->method, $default_paymethods) && $order->paymentData->status == PaymentStatuses::UNKNOWN) ||  // for on-account payments
                                    (!in_array($order->paymentData->method, $default_paymethods) && $order->paymentData->status == PaymentStatuses::PAID)
                                ) {    // for 3rd party payment providers (mollie, etc)
                                    if (UserController::is_propeller_logged_in()) {
                                        if (UserController::is_contact()) {
                                            $reinitialize = $order->userId == SessionController::get(PROPELLER_USER_DATA)->userId &&
                                                $order->companyId == SessionController::get(PROPELLER_CONTACT_COMPANY_ID) &&
                                                $order->cartId == SessionController::get(PROPELLER_CART)->cartId;
                                        } else if (UserController::is_customer()) {
                                            $reinitialize = $order->userId == SessionController::get(PROPELLER_USER_DATA)->userId &&
                                                $order->cartId == SessionController::get(PROPELLER_CART)->cartId;
                                        }
                                    }
                                }

                                if ($reinitialize) {
                                    if ((!SessionController::has('reinitialize_cart') || !SessionController::get('reinitialize_cart')) && UserController::is_propeller_logged_in())
                                        SessionController::set('reinitialize_cart', true);
                                }

                                $propel['order'] = $order;
                            }
                        }
                    }

                    break;
                case PageController::get_slug(PageType::PURCHASE_AUTHORIZATION_THANK_YOU):
                    if (!UserController::is_propeller_logged_in()) {
                        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                        SessionController::set('login_referrer', $current_url);
                        SessionController::set('register_referrer', $current_url);

                        $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE));

                        wp_safe_redirect($redirect_url);
                        die;
                    }

                    $ref = 'Propeller\Custom\Includes\Controller\ShoppingCartController';

                    $shoppingCartController = class_exists($ref, true)
                        ? new $ref()
                        : new ShoppingCartController();

                    if (!isset($propel['cart'])) {
                        if (isset($query_vars['slug']) && !empty($query_vars['slug'])) {
                            $cart = $shoppingCartController->get_user_cart($query_vars['slug']);

                            if (SessionController::get(PROPELLER_USER_DATA)->userId != $cart->contactId)
                                $propel['error_403'] = 'Accesss denied';
                            else {
                                if (is_object($cart)) {
                                    $propel['cart'] = $cart;

                                    $shoppingCartController->submit_purchase_request($query_vars['slug']);

                                    SessionController::remove(PROPELLER_CART);
                                    SessionController::remove(PROPELLER_CART_ID);
                                    SessionController::set(PROPELLER_CART_INITIALIZED, false);
                                    SessionController::set(PROPELLER_CART_USER_SET, false);

                                    if ((!SessionController::has('reinitialize_cart') || !SessionController::get('reinitialize_cart')) && UserController::is_propeller_logged_in())
                                        SessionController::set('reinitialize_cart', true);
                                }
                            }
                        }
                    }

                    break;
                case PageController::get_slug(PageType::ORDER_DETAILS_PAGE):
                    if (!isset($_REQUEST['order_id']) || empty($_REQUEST['order_id']) || !is_numeric($_REQUEST['order_id'])) {
                        $propel['error_404'] = 'Page not found';
                    } else {
                        if (!UserController::is_propeller_logged_in()) {
                            $propel['error_403'] = 'Accesss denied';
                        } else {
                            $ref = 'Propeller\Custom\Includes\Controller\OrderController';

                            $orderController = class_exists($ref, true)
                                ? new $ref()
                                : new OrderController();

                            $data = isset($propel['data']) && is_object($propel['data']) ? $propel['data'] : $orderController->get_order((int) sanitize_text_field($_REQUEST['order_id']));

                            if (!is_object($data)) {
                                propel_log(print_r($data, true));
                                $propel['error_404'] = 'Page not found';
                            } else {
                                if (SessionController::get(PROPELLER_USER_DATA)->userId != $data->userId)
                                    $propel['error_404'] = 'Page not found';
                                else
                                    $propel['order'] = $data;
                            }
                        }
                    }

                    break;
                case PageController::get_slug(PageType::CHECKOUT_PAGE):
                case PageController::get_slug(PageType::CHECKOUT_SUMMARY_PAGE):
                    $do_redirect = true;

                    if (PROPELLER_ANONYMOUS_ORDERS) {
                        $do_redirect = false;

                        if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) != OrderStatus::ORDER_STATUS_NEW)
                            $do_redirect = true;
                    }

                    if (!UserController::is_propeller_logged_in() && $do_redirect) {
                        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                        SessionController::set('login_referrer', $current_url);
                        SessionController::set('register_referrer', $current_url);

                        $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE));

                        wp_safe_redirect($redirect_url);
                        die;
                    }

                    $ref = 'Propeller\Custom\Includes\Controller\ShoppingCartController';

                    $cartController = class_exists($ref, true)
                        ? new $ref()
                        : new ShoppingCartController();

                    $validate = $cartController->validate_checkout();

                    if ($validate->do_redirect) {
                        wp_safe_redirect($validate->url);

                        die;
                    }

                    break;
                case PageController::get_slug(PageType::FAVORITES_PAGE):
                    if (!UserController::is_propeller_logged_in()) {
                        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                        SessionController::set('login_referrer', $current_url);
                        SessionController::set('register_referrer', $current_url);

                        $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE));

                        wp_safe_redirect($redirect_url);
                        die;
                    }

                    $ref = 'Propeller\Custom\Includes\Controller\FavoriteController';

                    $favorite_obj = class_exists($ref, true)
                        ? new $ref()
                        : new FavoriteController();

                    $data = isset($propel['data']) && is_object($propel['data'])
                        ? $propel['data']
                        : ((!isset($query_vars['slug']) || empty($query_vars['slug']))
                            ? $favorite_obj->get_favorites_lists()
                            : $favorite_obj->get_favorite_list($query_vars['slug']));

                    if (!is_object($data)) {
                        propel_log(print_r($data, true));
                        $propel['error_404'] = 'Favorites';
                    } else {
                        $propel['data'] = $data;
                    }

                    break;
                case PageController::get_slug(PageType::QUICK_ORDER_PAGE):
                    if (!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL) {
                        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                        SessionController::set('login_referrer', $current_url);
                        SessionController::set('register_referrer', $current_url);

                        $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE));

                        wp_safe_redirect($redirect_url);
                        die;
                    }

                    break;
                case PageController::get_slug(PageType::MY_ACCOUNT_PAGE):
                case PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE):
                case PageController::get_slug(PageType::ADDRESSES_PAGE):
                case PageController::get_slug(PageType::ORDERS_PAGE):
                case PageController::get_slug(PageType::ORDER_DETAILS_PAGE):
                case PageController::get_slug(PageType::INVOICES_PAGE):
                case PageController::get_slug(PageType::ORDERLIST_PAGE):
                case PageController::get_slug(PageType::QUOTATIONS_PAGE):
                case PageController::get_slug(PageType::QUOTATION_DETAILS_PAGE):
                case PageController::get_slug(PageType::ACCOUNT_DETAILS_PAGE):
                case PageController::get_slug(PageType::PRODUCT_REQUEST_PAGE):
                case PageController::get_slug(PageType::PURCHASE_AUTHORIZATIONS_PAGE):
                    if (!UserController::is_propeller_logged_in()) {
                        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                        SessionController::set('login_referrer', $current_url);
                        SessionController::set('register_referrer', $current_url);

                        $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE));

                        wp_safe_redirect($redirect_url);
                        die;
                    }

                    break;
                default:
                    break;
            }

            if (count($page_chunks) > 1) {
                switch ($page_chunks[1]) {
                    case PageController::get_slug(PageType::MY_ACCOUNT_PAGE):
                    case PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE):
                    case PageController::get_slug(PageType::ADDRESSES_PAGE):
                    case PageController::get_slug(PageType::ORDERS_PAGE):
                    case PageController::get_slug(PageType::ORDER_DETAILS_PAGE):
                    case PageController::get_slug(PageType::INVOICES_PAGE):
                    case PageController::get_slug(PageType::ORDERLIST_PAGE):
                    case PageController::get_slug(PageType::QUOTATIONS_PAGE):
                    case PageController::get_slug(PageType::ACCOUNT_DETAILS_PAGE):
                    case PageController::get_slug(PageType::FAVORITES_PAGE):
                    case PageController::get_slug(PageType::CHECKOUT_PAGE):
                    case PageController::get_slug(PageType::CHECKOUT_SUMMARY_PAGE):
                    case PageController::get_slug(PageType::PRODUCT_REQUEST_PAGE):
                        if (!UserController::is_propeller_logged_in()) {
                            $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                            SessionController::set('login_referrer', $current_url);
                            SessionController::set('register_referrer', $current_url);

                            $redirect_url = home_url('/' . PageController::get_slug(PageType::LOGIN_PAGE));

                            wp_safe_redirect($redirect_url);
                            die;
                        }

                        break;
                    default:
                        break;
                }
            }
        }
    }

    private function pack_breadcrumbs($data, $page_slug)
    {
        $obj = new BaseController();

        $bcrumbs = [];

        $index = 0;

        if (isset($data->categoryPath)) {
            foreach ($data->categoryPath as $path) {
                if ($index > 0) {
                    if (isset($path->slug) && count($path->slug)) {
                        $bcrumbs[] = [
                            $obj->buildUrl(PageController::get_slug(PageType::CATEGORY_PAGE), $path->slug[0]->value, $path->urlId),
                            $path->name[0]->value
                        ];
                    }
                }

                $index++;
            }
        }

        if (isset($path->slug) && count($path->slug)) {
            $bcrumbs[] = [
                $obj->buildUrl($page_slug, $data->slug[0]->value, $data->urlId),
                $data->name[0]->value
            ];
        }

        return $bcrumbs;
    }

    private function parse_meta($data)
    {
        $base = new BaseController();

        $is_product = isset($data->class) && $data->class == ProductClass::Product;
        $is_cluster = isset($data->class) && $data->class == ProductClass::Cluster;

        $base_slug = PageController::get_slug(PageType::CATEGORY_PAGE);

        if ($is_product)
            $base_slug = PageController::get_slug(PageType::PRODUCT_PAGE);
        else if ($is_cluster)
            $base_slug = PageController::get_slug(PageType::CLUSTER_PAGE);

        $meta_title = $data->name[0]->value . " | " .  get_bloginfo('name');
        $meta_description = wp_strip_all_tags(trim(preg_replace('/\s+/', ' ', $data->description[0]->value)));
        $meta_keywords = '';
        $meta_canonical = $base->buildUrl($base_slug, $data->slug[0]->value, $data->urlId);

        if (isset($data->metadataTitles) && is_array($data->metadataTitles) && count($data->metadataTitles)) {
            $found = array_filter($data->metadataTitles, function ($mt) {
                return $mt->language == PROPELLER_LANG;
            });

            if (count($found) && !empty(trim(current($found)->value)))
                $meta_title = current($found)->value;
        }

        if (isset($data->metadataDescriptions) && is_array($data->metadataDescriptions) && count($data->metadataDescriptions)) {
            $found = array_filter($data->metadataDescriptions, function ($mt) {
                return $mt->language == PROPELLER_LANG;
            });

            if (count($found) && !empty(trim(current($found)->value)))
                $meta_description = current($found)->value;
        }

        if (isset($data->metadataKeywords) && is_array($data->metadataKeywords) && count($data->metadataKeywords)) {
            $found = array_filter($data->metadataKeywords, function ($mt) {
                return $mt->language == PROPELLER_LANG;
            });

            if (count($found) && !empty(trim(current($found)->value)))
                $meta_keywords = current($found)->value;
        }

        if (isset($data->metadataCanonicalUrls) && is_array($data->metadataCanonicalUrls) && count($data->metadataCanonicalUrls)) {
            $found = array_filter($data->metadataCanonicalUrls, function ($mt) {
                return $mt->language == PROPELLER_LANG;
            });

            if (count($found) && !empty(trim(current($found)->value)))
                $meta_canonical = current($found)->value;
        }

        $meta = [
            'title' => $meta_title,
            'description' => $meta_description,
            'type' => $is_cluster ? 'cluster' : ($is_product ? 'product' : 'category'),
            'url' => $base->buildUrl($base_slug, $data->slug[0]->value, $data->urlId),
            'locale' => get_locale(),
            'keywords' => $meta_keywords,
            'canonical' => $meta_canonical
        ];

        return $meta;
    }
}
