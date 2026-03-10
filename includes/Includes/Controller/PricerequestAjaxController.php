<?php
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class PricerequestAjaxController extends BaseAjaxController {
    protected $pricerequest_controller;

    public function __construct() {
        parent::__construct();

        $this->pricerequest_controller = new PricerequestController();
    }

    public function add() {
        $this->init_ajax();

        $response = new stdClass();
        
        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $product = new stdClass();
            $product->id            = $data['id'];
            $product->code          = $data['code'];
            $product->name          = $data['name'];
            $product->quantity      = $data['quantity'];
            $product->minquantity   = $data['minquantity'];
            $product->unit          = $data['unit'];

            $response = $this->pricerequest_controller->add($product);
        }
        else {
            $response->message       = __("Security check failed", "propeller-ecommerce-v2");
            $response->error = true;
            $response->success = false;
        }

        die(json_encode($response));
    }
    
    public function remove() {
        $this->init_ajax();

        $response = new stdClass();
        
        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $product = new stdClass();
            $product->code = $data['code'];
            
            $response = $this->pricerequest_controller->remove($product);
        }
        else {
            $response->message       = __("Security check failed", "propeller-ecommerce-v2");
            $response->error = true;
            $response->success = false;
        }

        die(json_encode($response));
    }

    public function send_price_request() {
        $this->init_ajax();

        $response = new stdClass();
        
        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $response = $this->pricerequest_controller->send_price_request($data);
        }
        else {
            $response->message       = __("Security check failed", "propeller-ecommerce-v2");
            $response->error = true;
            $response->success = false;
        }

        die(json_encode($response));
    }
}