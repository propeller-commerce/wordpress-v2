<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

class PricerequestController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function price_request()
    {
        add_action('wp_footer', function () {
            apply_filters('propel_price_request_cluster_form', 0);
        });

        $products = $this->get_products();

        require $this->load_template('templates', '/propeller-price-request.php');
    }

    public function price_request_cluster_form()
    {
        require $this->load_template('templates', '/propeller-price-request-cluster-form.php');
    }


    public function get_products()
    {
        if (!SessionController::has(PROPELLER_PRICE_REQUEST))
            SessionController::set(PROPELLER_PRICE_REQUEST, []);

        return SessionController::get(PROPELLER_PRICE_REQUEST);
    }

    public function add($product)
    {
        $response = new stdClass();

        if (!$this->check($product)) {
            $this->add_product($product);

            $response->success = true;
            $response->error = false;
            $response->message = __('Product is added in your price request list. Please go to your account menu to send your price request list.', 'propeller-ecommerce-v2');
        } else {
            $response->success = false;
            $response->error = true;
            $response->message = __('Product already exists in price request list', 'propeller-ecommerce-v2');
        }

        return $response;
    }

    public function remove($product)
    {
        $response = new stdClass();

        if ($this->check($product)) {
            $this->remove_product($product);

            $response->success = true;
            $response->error = false;
            $response->message = __('Product removed from price request list', 'propeller-ecommerce-v2');
        } else {
            $response->success = false;
            $response->error = true;
            // $response->message = __('Product doesn\'t exists in price request list', 'propeller-ecommerce-v2');
        }

        return $response;
    }

    public function send_price_request($data)
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

        $user_data = SessionController::get(PROPELLER_USER_DATA);
        $user_email = $user_data->email;

        require $this->load_template('emails', '/propeller-price-request-template.php');

        $email_content = ob_get_contents();
        ob_end_clean();

        $response = new stdClass();

        $response->success = wp_mail($user_email, __('Price request', 'propeller-ecommerce-v2'), $email_content, implode("\r\n", $headers));
        // $response->postprocess->success = mail($args['return_email'], __('Return request', 'propeller-ecommerce-v2'), $email_content, implode("\r\n", $headers));

        $msg = __('Price request sent. We will contact you.', 'propeller-ecommerce-v2');

        if (!$response->success) {
            $err = debug_wpmail($response->success);

            if (count($err))
                $msg = $err[0];
        } else
            SessionController::remove(PROPELLER_PRICE_REQUEST);

        $response->request_email = $user_email;
        $response->message = $msg;

        return $response;
    }

    private function check($product)
    {
        $products = $this->get_products();

        $found = array_filter($products, function ($obj) use ($product) {
            return $obj->code == $product->code;
        });

        return count($found) > 0;
    }

    private function set_products($products)
    {
        SessionController::set(PROPELLER_PRICE_REQUEST, $products);
    }

    private function add_product($product)
    {
        $products = $this->get_products();

        $products[] = $product;

        $this->set_products($products);
    }

    private function remove_product($product)
    {
        $products = $this->get_products();

        $arr = array_filter($products, function ($obj) use ($product) {
            return $obj->code !== $product->code;
        });

        $this->set_products($arr);
    }
}
