<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class OrderAjaxController extends BaseAjaxController
{
    protected $order;
    protected $object_name = 'Order';

    public function __construct()
    {
        parent::__construct();

        $this->order = new OrderController();
    }

    public function change_order_status()
    {
        $this->init_ajax();

        $data = $this->sanitize($_REQUEST);

        $response = $this->order->change_status($data);

        $response->object = $this->object_name;

        die(json_encode($response));
    }

    public function get_orders()
    {
        $this->init_ajax();

        $response = $this->order->orders(true);

        die(json_encode($response));
    }

    public function get_quotes()
    {
        $this->init_ajax();

        $response = $this->order->quotations(true);

        die(json_encode($response));
    }

    public function return_request()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_REQUEST);

            $response = $this->order->return_request($data);
        } else {
            $postprocess->status = false;
            $postprocess->reload = false;
            $postprocess->error = true;
            $postprocess->message = __("Security check failed", "propeller-ecommerce-v2");

            $response->postprocess = $postprocess;
        }

        $response->object = $this->object_name;

        die(json_encode($response));
    }

    public function download_order_pdf()
    {
        $this->init_ajax();

        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_REQUEST);

            $response = $this->order->get_order_pdf_url($data);
        } else {
            $response->success = false;
            $response->message = __("Unable to download order confirmation PDF", "propeller-ecommerce-v2");
        }

        $response->object = $this->object_name;

        die(json_encode($response));
    }

    public function delete_order_pdf()
    {
        $this->init_ajax();

        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_REQUEST);

            $response = $this->order->delete_order_pdf($data);
        } else {
            $response->success = false;
            $response->message = __("Unable to delete order confirmation PDF", "propeller-ecommerce-v2");
        }

        $response->object = $this->object_name;

        die(json_encode($response));
    }

    public function download_secure_attachment()
    {
        $this->init_ajax();

        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_REQUEST);

            $response = $this->order->get_secure_attachment_url($data);
        } else {
            $response->success = false;
            $response->message = __("Unable to download secure attachment", "propeller-ecommerce-v2");
        }

        $response->object = $this->object_name;

        die(json_encode($response));
    }

    public function delete_attachment()
    {
        $this->init_ajax();

        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_REQUEST);

            $response = $this->order->delete_attachment($data);
        } else {
            $response->success = false;
            $response->message = __("Unable to delete attachment", "propeller-ecommerce-v2");
        }

        $response->object = $this->object_name;

        die(json_encode($response));
    }

    public function view_shipment_details()
    {
        $this->init_ajax();

        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_REQUEST);

            $response = $this->order->view_shipment_details($data['shipment']);
        } else {
            $response->success = false;
            $response->message = __("Unable to view shipment details", "propeller-ecommerce-v2");
        }

        die(json_encode($response));
    }
}
