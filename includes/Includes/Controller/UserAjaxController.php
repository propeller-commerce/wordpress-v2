<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Propeller;
use Propeller\PropellerUtils;
use stdClass;

class UserAjaxController extends BaseAjaxController
{
    protected $user;
    protected $object_name = 'Login';

    public function __construct()
    {
        parent::__construct();

        $this->user = new UserController();
    }

    public function load_mini_account()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->user->load_mini_account($data);

        die(json_encode($response));
    }

    public function login($data, $skip_recaptcha = false)
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        $proceed = true;

        if ($this->validate_form_request('nonce')) {

            if (Propeller::use_recaptcha() && !$skip_recaptcha) {
                if (!$this->validate_recaptcha($data)) {
                    $postprocess->status = false;
                    $postprocess->reload = false;
                    $postprocess->error = true;
                    $postprocess->message = __("Security check failed (reCaptcha)", "propeller-ecommerce-v2") . " in login";

                    $proceed = false;
                }
            }

            if ($proceed) {
                $response = $this->user->login(
                    $data['user_mail'],
                    $data['user_password'],
                    '',
                    isset($data['referrer']) ? $data['referrer'] : ''
                );

                if (isset($response->error) && $response->error) {
                    if (isset($response->postprocess))
                        $postprocess = $response->postprocess;

                    $postprocess->status = false;
                    $postprocess->reload = false;
                    $postprocess->error = true;
                } else {
                    if (isset($response->postprocess))
                        $postprocess = $response->postprocess;

                    $postprocess->toast = true;
                    $postprocess->status = true;
                    $postprocess->reload = true;
                    $postprocess->error = null;
                }
            }
        } else {
            $postprocess->status = false;
            $postprocess->reload = false;
            $postprocess->error = true;
            $postprocess->message = __("Security check failed", "propeller-ecommerce-v2");
        }

        $response->postprocess = $postprocess;
        $response->object = $this->object_name;

        die(json_encode($response));
    }

    public function user_prices()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->user->user_prices(
            isset($data['active']) && (int) $data['active'] == 1 ? true : false,
        );

        do_action('propel_vat_switch', $response);

        die(json_encode($response));
    }

    public function do_login()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);
        $this->login($data);
    }

    public function do_register()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $proceed = true;

            if (Propeller::use_recaptcha()) {
                if (!$this->validate_recaptcha($data)) {
                    $postprocess->status = false;
                    $postprocess->reload = false;
                    $postprocess->error = true;
                    $postprocess->message = __("Security check failed (reCaptcha)", "propeller-ecommerce-v2") . " in register";

                    $response->postprocess = $postprocess;

                    $proceed = false;
                }
            }

            if ($proceed) {
                $response = $this->user->register_user($data);

                if (!$response->postprocess->error && PROPELLER_REGISTER_AUTOLOGIN) {
                    $data['user_mail'] = $data['email'];
                    $data['user_password'] = $data['password'];

                    $this->login($data, true);
                }
            }
        } else {
            $postprocess->status = false;
            $postprocess->reload = false;
            $postprocess->error = true;
            $postprocess->message = __("Security check failed", "propeller-ecommerce-v2");

            $response->postprocess = $postprocess;
        }

        $response->object = 'Register';

        die(json_encode($response));
    }

    public function forgot_password()
    {
        $this->init_ajax();

        $postprocess = new stdClass();
        $response = new stdClass();

        if ($this->validate_form_request('nonce')) {
            $data = $this->sanitize($_POST);

            $response = $this->user->forgot_password($data);
        } else {
            $postprocess->error = true;
            $postprocess->message = __("Security check failed", "propeller-ecommerce-v2");
            $response->postprocess = $postprocess;
        }

        $response->object = 'Register';

        die(json_encode($response));
    }

    public function company_switch()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->user->company_switch($data['company']);

        die(json_encode($response));
    }

    public function preview_authorization_request()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->user->preview_authorization_request($data['cart_id']);

        die(json_encode($response));
    }

    public function delete_authorization_request()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->user->delete_authorization_request($data['cart_id']);

        die(json_encode($response));
    }

    public function accept_authorization_request()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->user->accept_authorization_request($data['cart_id']);

        die(json_encode($response));
    }

    public function create_purchase_authorization_config()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->user->create_purchase_authorization_config($data);

        die(json_encode($response));
    }

    public function update_purchase_authorization_config()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->user->update_purchase_authorization_config($data);

        die(json_encode($response));
    }

    public function delete_purchase_authorization_config()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->user->delete_purchase_authorization_config($data);

        die(json_encode($response));
    }

    public function purchase_authorizations()
    {
        $this->init_ajax();

        $data = PropellerUtils::sanitize($_REQUEST);

        $offset = isset($data['offset']) ? (int) $data['offset'] : 12;
        $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;

        $response = $this->user->purchase_authorizations($page, $offset, true);

        die(json_encode($response));
    }

    public function add_contact_to_company()
    {
        $this->init_ajax();

        $data = PropellerUtils::sanitize($_REQUEST);

        $response = $this->user->add_contact_to_company($data);

        die(json_encode($response));
    }

    public function create_contact_login()
    {
        $this->init_ajax();

        $data = PropellerUtils::sanitize($_REQUEST);

        $response = $this->user->create_contact_login($data);

        die(json_encode($response));
    }

    public function delete_contact_login()
    {
        $this->init_ajax();

        $data = PropellerUtils::sanitize($_REQUEST);

        $response = $this->user->delete_contact_login($data);

        die(json_encode($response));
    }
}
