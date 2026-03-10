<?php

namespace Propeller\Includes\Controller;

if (! defined('ABSPATH')) exit;

use Propeller\Includes\Enum\MediaImagesType;
use Propeller\Includes\Enum\MediaType;
use Propeller\Includes\Enum\OrderItemClass;
use Propeller\Includes\Enum\OrderStatus;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\Order;
use Propeller\Includes\Object\Product;
use Propeller\Includes\Query\Media;
use Propeller\Propeller;
use Propeller\PropellerHelper;
use Propeller\PropellerUtils;
use stdClass;

class OrderController extends BaseController
{
    protected $type = 'order';
    protected $model;

    public $current_sort_field = 'CREATED_AT';
    public $current_sort_order = 'DESC';
    public mixed $orders;
    public mixed $data;
    public mixed $order;


    public function __construct()
    {
        parent::__construct();

        $this->model = $this->load_model('order');

        add_action('template_redirect', [$this, 'download_order_pdf']);
    }

    public function orders_table($orders, $data, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-orders-table.php');
    }

    public function orders_table_header($orders)
    {
        $current_sort_field = 'ordernumber';
        $current_sort_order = 'DESC';

        require $this->load_template('partials', '/user/propeller-account-orders-table-header.php');
    }

    public function orders_table_list($orders, $data, $obj)
    {

        $this->assets()->std_requires_asset('propeller-account-paginator');

        require $this->load_template('partials', '/user/propeller-account-orders-table-list.php');
    }

    public function orders_table_list_item($order, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-orders-table-list-item.php');
    }

    public function orders_table_list_paging($data, $obj)
    {

        $this->assets()->std_requires_asset('propeller-account-paginator');

        require $this->load_template('partials', '/user/propeller-account-orders-table-list-paging.php');
    }

    public function quotations_table($orders, $data, $obj)
    {
        $current_sort_field = 'ordernumber';
        $current_sort_order = 'DESC';

        require $this->load_template('partials', '/user/propeller-account-quotations-table.php');
    }

    public function quotations_table_header($orders)
    {
        $current_sort_field = 'ordernumber';
        $current_sort_order = 'DESC';

        require $this->load_template('partials', '/user/propeller-account-quotations-table-header.php');
    }

    public function quotations_table_list($orders, $data, $obj)
    {

        $this->assets()->std_requires_asset('propeller-account-paginator');

        require $this->load_template('partials', '/user/propeller-account-quotations-table-list.php');
    }

    public function quotations_table_list_item($order, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-quotations-table-list-item.php');
    }

    public function quotations_table_list_paging($data, $obj)
    {

        $this->assets()->std_requires_asset('propeller-account-paginator');

        require $this->load_template('partials', '/user/propeller-account-quotations-table-list-paging.php');
    }

    public function order_details_back_button($obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-back-button.php');
    }

    public function order_details_title($order)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-title.php');
    }

    public function order_details_data($order, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-data.php');
    }

    public function order_details_attachments($order, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-attachments.php');
    }

    public function order_details_shipments($order)
    {
        add_action('wp_footer', function () use ($order) {
            apply_filters('propel_order_details_shipment_modal', $order);
        });

        require $this->load_template('partials', '/user/propeller-account-order-details-shipments.php');
    }

    public function order_details_shipment($shipment, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-shipment.php');
    }

    public function order_details_shipment_trackandtrace($carriers, $track_and_traces)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-shipment_trackandtrace.php');
    }

    public function order_details_shipment_modal($order)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-shipment-modal.php');
    }

    public function order_details_pdf($order)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-pdf.php');
    }

    public function order_details_returns($order)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-returns.php');
    }

    public function order_details_returns_form($order)
    {

        $this->assets()->std_requires_asset('propeller-order-details-return');

        require $this->load_template('partials', '/user/propeller-account-order-details-returns-form.php');
    }

    public function order_details_reorder($order)
    {
        $reorder_item_ids = [];

        $parents = [];

        foreach ($order->items as $item) {
            if ($item->class == 'product' && $item->isBonus == 'N' && $item->product->orderable == 'Y') {
                if ($item->parentOrderItemId) {
                    $parent = $item->parentOrderItemId;

                    if (!isset($parents[$parent]))
                        $parents[$parent] = [];

                    $found = array_filter($order->items, function ($i) use ($parent) {
                        return $i->id == $parent;
                    });

                    if ($found) {
                        if (!isset($parents[$parent][current($found)->product->productId]))
                            $parents[$parent][current($found)->product->productId] = '';

                        if (empty($parents[$parent][current($found)->product->productId]))
                            $parents[$parent][current($found)->product->productId] = $item->product->productId;
                        else
                            $parents[$parent][current($found)->product->productId] .= '|' . $item->product->productId;
                    }
                }
            }
        }

        foreach ($order->items as $item) {
            if ($item->class == 'product' && $item->isBonus == 'N' && $item->product->orderable == 'Y') {
                if (!$item->parentOrderItemId) {
                    $value = '';

                    if (!isset($item->product->cluster) || !$item->product->cluster)
                        $value = $item->productId . '-' . $item->quantity;
                    else
                        $value = $item->productId . '|' . $item->product->cluster->clusterId . '-' . $item->quantity;

                    if (isset($parents[$item->id]) && isset($parents[$item->id][$item->productId])) {
                        $value .= '^' . $parents[$item->id][$item->productId];
                    }

                    $reorder_item_ids[] = $value;
                }
            }
        }

        require $this->load_template('partials', '/user/propeller-account-order-details-reorder.php');
    }

    public function order_details_overview_headers($order)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-overview-headers.php');
    }

    public function order_details_overview_items($items, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-overview-items.php');
    }

    public function order_details_overview_item($item, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-overview-item.php');
    }

    public function order_details_overview_cluster_item($item, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-overview-cluster-item.php');
    }

    public function order_details_popup_cluster_item($item, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-popup-cluster-item.php');
    }

    public function order_details_overview_bonus_items($items, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-overview-bonus-items.php');
    }

    public function order_details_overview_bonus_item($item, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-overview-bonus-item.php');
    }

    public function order_details_totals($order, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-order-details-totals.php');
    }

    public function quote_details_back_button($obj)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-back-button.php');
    }

    public function quote_details_title($order)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-title.php');
    }

    public function quote_details_data($order)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-data.php');
    }

    public function quote_details_overview_headers($order)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-overview-headers.php');
    }

    public function quote_details_overview_items($items, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-overview-items.php');
    }

    public function quote_details_overview_item($item, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-overview-item.php');
    }

    public function quote_details_overview_cluster_item($item, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-overview-cluster-item.php');
    }

    public function quote_details_overview_bonus_items($items, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-overview-bonus-items.php');
    }

    public function quote_details_overview_bonus_item($item, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-overview-bonus-item.php');
    }

    public function quote_details_totals($order)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-totals.php');
    }

    public function quote_details_order($order)
    {
        require $this->load_template('partials', '/user/propeller-account-quote-details-order.php');
    }

    public function order_thank_you_headers($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-thank-you-headers.php');
    }

    public function order_failed_headers($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-failed-headers.php');
    }

    public function order_cancelled_headers($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-cancelled-headers.php');
    }

    public function order_expired_headers($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-expired-headers.php');
    }

    public function order_authorization_confirmed_headers($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-authorization-confirmed-headers.php');
    }

    public function order_processed_headers($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-processed-headers.php');
    }

    public function order_thank_you_billing_info($order, $obj)
    {
        $countries = propel_get_countries();

        require $this->load_template('partials', '/checkout/propeller-order-thank-you-billing-info.php');
    }

    public function order_thank_you_delivery_info($order, $obj)
    {
        $countries = propel_get_countries();

        require $this->load_template('partials', '/checkout/propeller-order-thank-you-delivery-info.php');
    }

    public function order_thank_you_shipping_info($order, $obj)
    {
        $countries = propel_get_countries();

        require $this->load_template('partials', '/checkout/propeller-order-thank-you-shipping-info.php');
    }

    public function order_thank_you_payment_info($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-thank-you-payment-info.php');
    }

    public function order_thank_you_items_title($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-thank-you-items-title.php');
    }

    public function order_thank_you_items($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-thank-you-items.php');
    }

    public function order_thank_you_summary_totals($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-thank-you-summary-totals.php');
    }

    public function purchase_authorization_thank_you($order, $obj)
    {
        require $this->load_template('partials', '/checkout/propeller-order-thank-you-summary-totals.php');
    }



    public function orders($is_ajax = false)
    {
        $_REQUEST = PropellerUtils::sanitize($_REQUEST);

        $order_args = [
            'status' => [
                OrderStatus::ORDER_STATUS_NEW,
                OrderStatus::ORDER_STATUS_CONFIRMED,
                OrderStatus::ORDER_STATUS_VALIDATED,
                OrderStatus::ORDER_STATUS_ARCHIVED
            ]
        ];

        $order_args['offset'] = (isset($_REQUEST['offset']) ? (int) $_REQUEST['offset'] : 12);

        $order_args['page'] = (isset($_REQUEST['ppage']) ? (int) $_REQUEST['ppage'] : (isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1));

        $order_args['channelIds'] = [PROPELLER_SITE_ID];
        // Handle sorting parameters
        $sort_field = isset($_REQUEST['sort_field']) ? $_REQUEST['sort_field'] : 'date';
        $sort_order = isset($_REQUEST['sort_order']) ? $_REQUEST['sort_order'] : 'DESC';

        // Validate sort field - map user-friendly names to GraphQL field names (use raw strings like ProductController)
        $field_mapping = [
            'date' => 'CREATED_AT',
            'ordernumber' => 'CREATED_AT', // Use CREATED_AT since ID field is not supported by GraphQL API
            'total' => 'TOTAL_GROSS',
            'status' => 'STATUS'
        ];

        $sort_field = isset($field_mapping[$sort_field]) ? $field_mapping[$sort_field] : 'CREATED_AT';

        // Validate sort order - use raw strings like ProductController
        $order_mapping = [
            'ASC' => 'ASC',
            'DESC' => 'DESC'
        ];

        $sort_order = isset($order_mapping[$sort_order]) ? $order_mapping[$sort_order] : 'DESC';

        $order_args['sortInputs'] = [
            'field' => $sort_field,
            'order' => $sort_order
        ];

        // Store current sorting for template use
        $this->current_sort_field = $sort_field;
        $this->current_sort_order = $sort_order;

        if (UserController::is_contact())
            $order_args['companyIds'] = [SessionController::get(PROPELLER_CONTACT_COMPANY_ID)];

        $this->data = $this->get_orders($order_args);

        $this->orders = [];

        foreach ($this->data->items as $order)
            $this->orders[] = new Order($order);

        ob_start();

        if ($is_ajax) {
            apply_filters('propel_account_orders_table_list', $this->orders, $this->data, $this);

            $response = new stdClass();
            $response->content = ob_get_clean();

            return $response;
        } else {
            require $this->load_template('partials', '/user/propeller-account-orders.php');
        }

        return ob_get_clean();
    }

    public function quotations($is_ajax = false)
    {
        ob_start();

        $_REQUEST = PropellerUtils::sanitize($_REQUEST);

        $order_args = [
            'status' => [
                OrderStatus::ORDER_STATUS_QUOTATION
            ]
        ];

        $order_args['offset'] = (isset($_REQUEST['offset']) ? (int) $_REQUEST['offset'] : 12);

        $order_args['page'] = (isset($_REQUEST['ppage']) ? (int) $_REQUEST['ppage'] : (isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1));

        $order_args['channelIds'] = [PROPELLER_SITE_ID];
        // Handle sorting parameters for quotations
        $sort_field = isset($_REQUEST['sort_field']) ? $_REQUEST['sort_field'] : 'date';
        $sort_order = isset($_REQUEST['sort_order']) ? $_REQUEST['sort_order'] : 'DESC';

        // Validate sort field - map user-friendly names to GraphQL field names for quotations
        $field_mapping = [
            'date' => 'CREATED_AT',
            'quotenumber' => 'CREATED_AT', // Use CREATED_AT since ID field is not supported by GraphQL API
            'total' => 'TOTAL_GROSS',
            'valid_until' => 'VALID_UNTIL'
        ];

        $sort_field = isset($field_mapping[$sort_field]) ? $field_mapping[$sort_field] : 'CREATED_AT';

        // Validate sort order - use raw strings like ProductController
        $order_mapping = [
            'ASC' => 'ASC',
            'DESC' => 'DESC'
        ];

        $sort_order = isset($order_mapping[$sort_order]) ? $order_mapping[$sort_order] : 'DESC';

        $order_args['sortInputs'] = [
            'field' => $sort_field,
            'order' => $sort_order
        ];

        // Store current sorting for template use
        $this->current_sort_field = $sort_field;
        $this->current_sort_order = $sort_order;

        if (UserController::is_contact())
            $order_args['companyIds'] = [SessionController::get(PROPELLER_CONTACT_COMPANY_ID)];

        $this->data = $this->get_orders($order_args);

        $this->orders = $this->data->items;

        if ($is_ajax) {
            apply_filters('propel_account_quotations_table_list', $this->orders, $this->data, $this);

            $response = new stdClass();
            $response->content = ob_get_clean();

            return $response;
        } else {
            require $this->load_template('partials', '/user/propeller-account-quotations.php');
        }

        return ob_get_clean();
    }

    public function order_details()
    {
        global $propel;

        $_REQUEST = PropellerUtils::sanitize($_REQUEST);

        $this->order = isset($propel['order'])
            ? $propel['order']
            : $this->get_order((int) $_REQUEST['order_id']);

        $this->order = new Order($this->order);

        ob_start();

        if ($this->order->status == 'QUOTATION')
            require $this->load_template('partials', '/user/propeller-account-quote-details.php');
        else
            require $this->load_template('partials', '/user/propeller-account-order-details.php');

        return ob_get_clean();
    }

    private function fix_order(&$order)
    {
        if (isset($order->items) && is_array($order->items)) {
            for ($i = 0; $i < count($order->items); $i++) {
                if (!$order->items[$i]->product)
                    continue;

                $order->items[$i]->product = new Product($order->items[$i]->product);

                if ($order->items[$i]->class == 'incentive') {
                    for ($j = 0; $j < count($order->items); $j++) {
                        if (
                            $order->items[$i]->sku == 'free' && $order->items[$i]->parentOrderItemId == $order->items[$j]->id &&
                            $order->items[$j]->isBonus == 'Y'
                        ) {
                            if (!isset($order->items[$i]->bonusitems))
                                $order->items[$i]->bonusitems = array();

                            $order->items[$i]->bonusitems[] = $order->items[$j];
                            unset($order->items[$j]);
                        }
                    }
                }
            }
        }
    }

    public function get_order_pdf_url($data)
    {
        $response = new stdClass();
        $response->success = false;

        $order_id = $data['order_id'];

        if (is_numeric($order_id)) {
            $user_gql = $this->model->get_pdf_order_user_id(['orderId' => (int) $order_id]);

            $user_id = $this->query($user_gql, 'order')->userId;

            if (SessionController::get(PROPELLER_USER_DATA)->userId == $user_id) {
                $pdf_data = $this->get_pdf((int) $order_id);

                if (!PropellerHelper::wp_filesys()->is_dir(PropellerHelper::get_uploads_dir() . '/invoices/'))
                    PropellerHelper::wp_filesys()->mkdir(PropellerHelper::get_uploads_dir() . '/invoices/');

                // PropellerHelper::download_pdf($pdf_data->fileName, PropellerHelper::get_uploads_dir() . '/invoices/' . $pdf_data->fileName, base64_decode($pdf_data->base64));
                $response->pdf_url = PropellerHelper::get_pdf_url(
                    $pdf_data->fileName,
                    PropellerHelper::get_uploads_dir() . '/invoices/' . $pdf_data->fileName,
                    PropellerHelper::get_uploads_url() . '/invoices/',
                    base64_decode($pdf_data->base64)
                );

                $response->filename = $pdf_data->fileName;
                $response->success = true;
            }
        }

        return $response;
    }

    public function delete_order_pdf($data)
    {
        $response = new stdClass();
        $response->success = false;

        $filename = $data['filename'];

        $filepath = PropellerHelper::get_uploads_dir() . '/invoices/' . $filename;

        if (PropellerHelper::wp_filesys()->exists($filepath))
            $response->success = PropellerHelper::wp_filesys()->delete($filepath);

        return $response;
    }

    public function download_order_pdf()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'order_pdf') !== false) {
            $download_chunks = explode('/', $_SERVER['REQUEST_URI']);

            $order_id = $download_chunks[count($download_chunks) - 2];

            if (is_numeric($order_id)) {
                $user_gql = $this->model->get_pdf_order_user_id(['orderId' => (int) $order_id]);

                $user_id = $this->query($user_gql, 'order')->userId;

                if (SessionController::get(PROPELLER_USER_DATA)->userId == $user_id) {
                    $pdf_data = $this->get_pdf((int) $order_id);

                    // Create invoices directory if it doesn't exist
                    if (!PropellerHelper::wp_filesys()->is_dir(PropellerHelper::get_uploads_dir() . '/invoices/'))
                        PropellerHelper::wp_filesys()->mkdir(PropellerHelper::get_uploads_dir() . '/invoices/');

                    // Use get_pdf_url to save the file and get the URL
                    $pdf_url = PropellerHelper::get_pdf_url(
                        $pdf_data->fileName,
                        PropellerHelper::get_uploads_dir() . '/invoices/' . $pdf_data->fileName,
                        PropellerHelper::get_uploads_url() . '/invoices/',
                        base64_decode($pdf_data->base64)
                    );

                    // Redirect to the generated URL
                    wp_safe_redirect($pdf_url);
                    wp_die();
                }
            }
        }
    }

    public function get_orders($args = [])
    {
        $type = 'orders';

        $args['userId'] = [SessionController::get(PROPELLER_USER_DATA)->userId];

        // Use provided sortInputs or default to CREATED_AT DESC
        if (!isset($args['sortInputs'])) {
            $args['sortInputs'] = [
                'field' => 'CREATED_AT',
                'order' => 'DESC'
            ];
        }

        $gql = $this->model->get_orders($args);

        $ordersData = $this->query($gql, $type);

        return $ordersData;
    }


    public function get_order($order_id)
    {
        $type = 'order';

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->get_order(
            $order_id,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($gql->query);
        // var_dump(json_encode($gql->variables));

        $orderData = $this->query($gql, $type);

        $this->fix_order($orderData);

        return $orderData;
    }

    public function get_order_minimal($order_id)
    {
        $type = 'order';

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->get_order_minimal(
            $order_id,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($gql->query);
        // var_dump(json_encode($gql->variables));

        $orderData = $this->query($gql, $type);

        $this->fix_order($orderData);

        return $orderData;
    }

    public function get_invoices($invoices_args = [])
    {
        $type = 'orders';

        $orders_args['userId'] = [SessionController::get(PROPELLER_USER_DATA)->userId];

        if (UserController::is_contact())
            $orders_args['companyIds'] = [SessionController::get(PROPELLER_CONTACT_COMPANY_ID)];

        $orders_args['sortInputs'] = [
            'field' => 'CREATED_AT',
            'order' => 'DESC'
        ];

        $orders_pages = 1;

        $gql = $this->model->get_invoices($orders_args, $invoices_args, PROPELLER_LANG);

        $invoicesData = $this->query($gql, $type);

        if ($invoicesData->itemsFound > 0 && $invoicesData->pages > 1) {
            $orders_pages = $invoicesData->pages;

            for ($page = 2; $page <= $orders_pages; $page++) {
                $orders_args['page'] = $page;

                $gql = $this->model->get_invoices($orders_args, $invoices_args, PROPELLER_LANG);

                $invoicesMoreData = $this->query($gql, $type);

                $invoicesData->items = array_merge($invoicesData->items, $invoicesMoreData->items);
            }
        }

        return $invoicesData;
    }

    public function get_pdf($order_id)
    {
        $type = 'orderGetPDF';

        $gql = $this->model->get_pdf(['orderId' => $order_id]);

        return $this->query($gql, $type);
    }

    public function order_cofirm_email($order_id)
    {
        $type = 'orderSendConfirmationEmail';

        $gql = $this->model->order_cofirm_email(['orderId' =>  $order_id]);

        return $this->query($gql, $type);
    }

    public function change_status($data, $return_response = false, $is_quote_request = false)
    {
        $type = 'orderSetStatus';

        $params = [
            'orderId' => $data['order_id'],
            'status' => $data['status'],
            'addPDFAttachment' => isset($data['add_pdf']) ? boolval($data['add_pdf']) : true,
            // 'sendOrderConfirmationEmail' => isset($data['send_email']) ? $data['send_email'] : true,
            'deleteCart' => isset($data['delete_cart']) ? boolval($data['delete_cart']) : true
        ];

        if (isset($data['payStatus']) && !empty(isset($data['payStatus'])))
            $params['payStatus'] = $data['payStatus'];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->change_status(
            $params,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($gql);
        // var_dump(json_encode($gql->variables));

        $orderData = $this->query($gql, $type);

        if (isset($data['send_email']) && boolval($data['send_email']) && is_object($orderData))
            $send_order_confirm = !$is_quote_request
                ? $this->send_order_confirm(['orderId' => $data['order_id'], 'language' => PROPELLER_LANG, 'channelId' => PROPELLER_SITE_ID])
                : $this->send_request_confirm(['orderId' => $data['order_id'], 'language' => PROPELLER_LANG, 'channelId' => PROPELLER_SITE_ID]);

        if ($return_response)
            return $orderData;

        $response = new stdClass();
        $postprocess = new stdClass();

        if (is_object($orderData)) {
            // $delete_cart = isset($data['delete_cart']) ? boolval($data['delete_cart']) : true;

            // if ($delete_cart) {
            //     $shoppingcart_controller = new ShoppingCartController();
            //     $cart = $shoppingcart_controller->get_cart();

            //     $shoppingcart_controller->delete_cart($cart->cartId);
            // }

            $postprocess = new stdClass();

            $response->status = true;

            FlashController::add(PROPELLER_ORDER_PLACED, (int) $data['order_id']);

            $postprocess->redirect = esc_url_raw(add_query_arg(['order_id' => $data['order_id']], $this->buildUrl(PageController::get_slug(PageType::THANK_YOU_PAGE), '')));
            $postprocess->status = true;

            $response->postprocess = $postprocess;
        } else {
            $response->status = true;
            $response->message = __('We were unable to process this request, please try again', 'propeller-ecommerce-v2');
            $postprocess->status = false;
            $postprocess->message = __('We were unable to process this request, please try again', 'propeller-ecommerce-v2');

            $response->postprocess = $postprocess;
        }

        $response->order_confirm_sent = $send_order_confirm;

        return $response;
    }

    public function send_order_confirm($args)
    {
        $type = 'triggerOrderSendConfirm';

        $gql = $this->model->send_order_confirm($args);

        return $this->query($gql, $type);
    }

    public function send_request_confirm($args)
    {
        $type = 'triggerQuoteSendRequest';

        $gql = $this->model->send_request_confirm($args);

        return $this->query($gql, $type);
    }

    public function return_request($args)
    {
        $cc = !empty(PROPELLER_CC_EMAIL) ? PROPELLER_CC_EMAIL : get_bloginfo('admin_email');
        $bcc = !empty(PROPELLER_BCC_EMAIL) ? PROPELLER_BCC_EMAIL : get_bloginfo('admin_email');

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . htmlspecialchars_decode(get_bloginfo('name')) . ' <' . get_bloginfo('admin_email') . '>',
            'Cc: ' . $cc,
            'Bcc: ' . $bcc,
            'X-Priority: 1',
            'X-Mailer: PHP/' . PHP_VERSION
        ];

        ob_start();

        require $this->load_template('emails', '/propeller-return-request-template.php');

        $email_content = ob_get_contents();
        ob_end_clean();

        $response = new stdClass();
        $response->postprocess = new stdClass();

        $response->object = 'Order';

        $response->postprocess->success = wp_mail($args['return_email'], __('Return request', 'propeller-ecommerce-v2'), $email_content, implode("\r\n", $headers));
        // $response->postprocess->success = mail($args['return_email'], __('Return request', 'propeller-ecommerce-v2'), $email_content, implode("\r\n", $headers));

        $msg = __('Return request sent. We will contact you.', 'propeller-ecommerce-v2');
        if (!$response->postprocess->success) {
            $err = debug_wpmail($response->postprocess->success);

            if (count($err))
                $msg = $err[0];
        }

        $response->postprocess->order_id = $args['return_order'];
        $response->postprocess->order_email = $args['return_email'];
        $response->postprocess->return_success = $response->postprocess->success;
        $response->postprocess->message = $msg;

        return $response;
    }

    public function get_secure_attachment_url($data)
    {
        $response = new stdClass();
        $response->success = false;

        $attachment_id = $data['attachment_id'];

        if (!empty($attachment_id)) {
            $gql = $this->model->get_order_attachment_query($attachment_id);

            $attachment_res = $this->query($gql, 'media');

            if (is_object($attachment_res)) {
                if (!defined('PROPELLER_API_KEY'))
                    Propeller::register_settings();

                $headers = [
                    'apikey' => PROPELLER_API_KEY,
                ];

                if (SessionController::has(PROPELLER_ACCESS_TOKEN) && SessionController::get(PROPELLER_ACCESS_TOKEN))
                    $headers['Authorization'] = 'Bearer ' . SessionController::get(PROPELLER_ACCESS_TOKEN);

                $remote_response = wp_remote_get($attachment_res->attachment->attachments[0]->originalUrl, [
                    'connect_timeout' => 60,
                    'timeout' => 60,
                    'headers' => $headers
                ]);

                if (!is_wp_error($remote_response)) {
                    $filename = basename(strtok(urldecode($attachment_res->attachment->attachments[0]->originalUrl), '?'));
                    $file_content = trim($remote_response['body']);

                    // Create attachments directory if it doesn't exist
                    if (!PropellerHelper::wp_filesys()->is_dir(PropellerHelper::get_uploads_dir() . '/attachments/'))
                        PropellerHelper::wp_filesys()->mkdir(PropellerHelper::get_uploads_dir() . '/attachments/');

                    // Use get_pdf_url to save the file and get the URL
                    $response->pdf_url = PropellerHelper::get_pdf_url(
                        $filename,
                        PropellerHelper::get_uploads_dir() . '/attachments/' . $filename,
                        PropellerHelper::get_uploads_url() . '/attachments/',
                        $file_content
                    );

                    $response->filename = $filename;
                    $response->success = true;
                } else {
                    $response->message = __("Unable to download attachment", "propeller-ecommerce-v2");
                }
            } else {
                $response->message = __("Attachment not found", "propeller-ecommerce-v2");
            }
        } else {
            $response->message = __("Invalid attachment ID", "propeller-ecommerce-v2");
        }

        return $response;
    }

    public function delete_attachment($data)
    {
        $response = new stdClass();
        $response->success = false;

        $filename = $data['filename'];

        if (!empty($filename)) {
            $filepath = PropellerHelper::get_uploads_dir() . '/attachments/' . $filename;

            if (PropellerHelper::wp_filesys()->exists($filepath))
                $response->success = PropellerHelper::wp_filesys()->delete($filepath);
        }

        return $response;
    }

    public function view_shipment_details($shipment_id)
    {
        $type = 'shipment';

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->get_shipment_details(
            $shipment_id,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($gql->query);
        // var_dump(json_encode($gql->variables));

        $shipmentData = $this->query($gql, $type);

        $response = new stdClass();

        $response->success = false;

        if (is_object($shipmentData)) {
            $response->success = true;

            $response->status = $shipmentData->status;
            ob_start();

            apply_filters('propel_order_details_shipment', $shipmentData, $this);
            $response->content = ob_get_clean();

            ob_end_clean();

            $carriers_ids = [];

            if (count($shipmentData->trackAndTraces)) {
                foreach ($shipmentData->trackAndTraces as $track_n_trace)
                    $carriers_ids[] = $track_n_trace->carrierId;
            }

            if (count($carriers_ids)) {
                $carriers_type = 'carriers';

                $carriers_gql = $this->model->order_shipment_track_and_trace([
                    'carriers_input' => [
                        'offset' => 20,
                        'ids' => $carriers_ids
                    ]
                ]);

                // var_dump($gql->query);
                // var_dump(json_encode($gql->variables));

                $carriersData = $this->query($carriers_gql, $carriers_type);

                if (is_object($carriersData) && $carriersData->itemsFound > 0) {
                    ob_start();

                    apply_filters('propel_order_details_shipment_trackandtrace', $carriersData->items, $shipmentData->trackAndTraces);
                    $response->track_n_trace = ob_get_clean();

                    ob_end_clean();
                }
            }
        } else
            $response->message = __('Failed to fetch shipment details', 'propeller-ecommerce-v2');

        return $response;
    }

    public function get_items_count($items)
    {
        $count = 0;

        foreach ($items as $item) {
            if ($item->class == OrderItemClass::SURCHARGE || $item->class == OrderItemClass::INCENTIVE)
                continue;

            $count += $item->quantity;
        }

        return $count;
    }

    public function account_recent_orders($obj)
    {
        $order_args = [
            'status' => [
                OrderStatus::ORDER_STATUS_NEW,
                OrderStatus::ORDER_STATUS_CONFIRMED,
                OrderStatus::ORDER_STATUS_VALIDATED
            ],
            'channelIds' => [PROPELLER_SITE_ID],
            'offset' => 3,
            'page' => 1,
            'sortInputs' => [
                'field' => 'CREATED_AT',
                'order' => 'DESC'
            ]
        ];

        if (UserController::is_contact()) {
            $order_args['companyIds'] = [SessionController::get(PROPELLER_CONTACT_COMPANY_ID)];
        }

        $orders_data = $this->get_orders($order_args);
        $orders = isset($orders_data->items) ? $orders_data->items : [];
        $orders = array_slice($orders, 0, 3);

        ob_start();
        require $this->load_template('partials', '/user/propeller-account-recent-orders.php');
        return ob_get_clean();
    }

    public function account_recent_quotations($obj)
    {
        $quotation_args = [
            'status' => [OrderStatus::ORDER_STATUS_QUOTATION],
            'channelIds' => [PROPELLER_SITE_ID],
            'offset' => 3,
            'page' => 1,
            'sortInputs' => [
                'field' => 'CREATED_AT',
                'order' => 'DESC'
            ]
        ];

        if (UserController::is_contact()) {
            $quotation_args['companyIds'] = [SessionController::get(PROPELLER_CONTACT_COMPANY_ID)];
        }

        $quotations_data = $this->get_orders($quotation_args);
        $quotations = isset($quotations_data->items) ? array_filter($quotations_data->items, function ($q) {
            return $q->public;
        }) : [];
        $quotations = array_slice($quotations, 0, 3);

        ob_start();
        require $this->load_template('partials', '/user/propeller-account-recent-quotations.php');
        return ob_get_clean();
    }
}
