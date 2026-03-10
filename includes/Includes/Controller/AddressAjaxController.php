<?php 

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class AddressAjaxController extends BaseAjaxController {
    protected $address;
    protected $object_name = 'Address';

    public function __construct() {
        parent::__construct();
        
        $this->address = new AddressController();
    }

    public function create() {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            unset($data['action']); // remove action from the params

            $this->address->set_user_data(SessionController::get(PROPELLER_USER_DATA));
            $this->address->set_user_type(UserController::get_type());
    
            $address_response = $this->address->add_address($data);
            $postprocess = new stdClass();
            $response = new stdClass();
    
            if (!is_object($address_response)) {
                $message = $address_response;
    
                $postprocess->status = false;
                $postprocess->reload = false;
                $postprocess->error = true;
                $postprocess->message = $message;
            }   
            else {
                $postprocess->message = __('Address created', 'propeller-ecommerce-v2');
                $postprocess->status = isset($address_response->id);
                $postprocess->reload = true;
                $postprocess->error = null;
            }
    
            $response->postprocess = $postprocess;
        }
        else {
            $postprocess->status = false;
            $postprocess->reload = false;
            $postprocess->error = true;
            $postprocess->message = __("Security check failed", "propeller-ecommerce-v2");

            $response->postprocess = $postprocess;
        }
        
        $response->object = $this->object_name;
        
        die(json_encode($response));
    }

    public function update() {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
	        $data = $this->sanitize($_POST);

            unset($data['action']); // remove action from the params
    
            $this->address->set_user_data(SessionController::get(PROPELLER_USER_DATA));
            $this->address->set_user_type(UserController::get_type());

            $address_response = $this->address->update_address($data);
            $postprocess = new stdClass();
            $response = new stdClass();
    
            if ($address_response == true) {
                $message = __('Address updated', 'propeller-ecommerce-v2');
    
                $postprocess->status = true;
                $postprocess->reload = true;
                $postprocess->error = false;
                $postprocess->message = $message;
            }   
            else {
                $postprocess->status = false;
                $postprocess->reload = false;
                $postprocess->error = true;
            }
            
            $response->postprocess = $postprocess;
        }
        else {
            $postprocess->status = false;
            $postprocess->reload = false;
            $postprocess->error = true;
            $postprocess->message = __("Security check failed", "propeller-ecommerce-v2");

            $response->postprocess = $postprocess;
        }
        
        $response->object = $this->object_name;
        
        die(json_encode($response));
    }

    public function delete() {
        $this->init_ajax();

	    $data = $this->sanitize($_POST);

        unset($data['action']); // remove action from the params

        $this->address->set_user_data(SessionController::get(PROPELLER_USER_DATA));
        $this->address->set_user_type(UserController::get_type());

        $address_response = $this->address->delete_address($data);
        $postprocess = new stdClass();
        $response = new stdClass();

        if ($address_response == true) {
            $message = __('Address deleted', 'propeller-ecommerce-v2');

            $postprocess->status = true;
            $postprocess->reload = true;
            $postprocess->error = false;
            $postprocess->message = $message;
        }   
        else {
            $postprocess->status = false;
            $postprocess->reload = false;
            $postprocess->error = true;
        }

        $response->postprocess = $postprocess;
        $response->object = $this->object_name;


        die(json_encode($response));
    }

    public function set_address_default() {
        $this->init_ajax();
        
	    $data = $this->sanitize($_POST);
    
        unset($data['action']); // remove action from the params

        $this->address->set_user_data(SessionController::get(PROPELLER_USER_DATA));
        $this->address->set_user_type(UserController::get_type());

        $address_response = $this->address->update_address($data);
        $postprocess = new stdClass();
        $response = new stdClass();

        if (isset($address_response->id)) {
            $message = __('Address set as default', 'propeller-ecommerce-v2');

            $postprocess->status = true;
            $postprocess->reload = true;
            $postprocess->error = false;
            $postprocess->message = $message;
        }   
        else {
            $postprocess->status = false;
            $postprocess->reload = false;
            $postprocess->error = true;
        }
        
        $response->postprocess = $postprocess;
        
        $response->object = $this->object_name;
        
        die(json_encode($response));
    }
}