<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\PaymentStatuses;
use stdClass;

class PaymentController extends BaseController {
    public $providers = [];

    public function __construct() {
        parent::__construct();

        $this->providers = $this->get_active_payment_provider();
    }

    public function payment_success() {
        $order = $this->get_order((int) $_GET['order_id']);

        ob_start();

        if(isset($order) && is_object($order)) {
            if ($order->paymentData->status == PaymentStatuses::FAILED || 
                $order->paymentData->status == PaymentStatuses::CANCELLED ||
                $order->paymentData->status == PaymentStatuses::EXPIRED) {
                wp_safe_redirect(home_url('/' . PageController::get_slug(PageType::PAYMENT_FAILED_PAGE)) . '?order_id=' . $_GET['order_id']);
                die;
            } 
        }

        require $this->load_template('partials', '/payment/propeller-payment-success.php');
        
        if ($this->analytics) {
            $this->analytics->setData($order);

            apply_filters('propel_ga4_fire_event', 'purchase');
        }

        return ob_get_clean();
    }

    public function payment_failed() {
        $order = $this->get_order((int) $_GET['order_id']);
        
        ob_start();

        require $this->load_template('partials', '/payment/propeller-payment-failed.php');
        
        return ob_get_clean();
    }

    public function payment_processed() {
        $order = $this->get_order((int) $_GET['order_id']);
        
        ob_start();

        require $this->load_template('partials', '/payment/propeller-payment-processed.php');
        
        return ob_get_clean();
    }

    public function payment_cancelled() {
        $order = $this->get_order((int) $_GET['order_id']);
        
        ob_start();

        require $this->load_template('partials', '/payment/propeller-payment-cancelled.php');
        
        return ob_get_clean();
    }

    public function payment_expired() {
        $order = $this->get_order((int) $_GET['order_id']);
        
        ob_start();

        require $this->load_template('partials', '/payment/propeller-payment-expired.php');
        
        return ob_get_clean();
    }

    public function authorization_confirmed() {
        $order = $this->get_order((int) $_GET['order_id']);
        
        ob_start();

        require $this->load_template('partials', '/payment/propeller-payment-authorization-confirmed.php');
        
        return ob_get_clean();
    }

    private function get_order($order_id) {
        global $propel;

        $orderController = new OrderController();

        return isset($propel['order']) 
            ? $propel['order'] 
            : $orderController->get_order($order_id);
    }

    private function get_active_payment_provider() {
        $payment_providers = [];

        $plugins = array_filter(glob(ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'propeller-*-provider'), 'is_dir');

        if (is_array($plugins) && count($plugins)) {
            foreach ($plugins as $plg) {
                $name = basename($plg);

                if (in_array("$name/$name.php", (array) get_option('active_plugins', array()))) {
                    require_once $plg . DIRECTORY_SEPARATOR . $name . '.php';
                    
                    $provider = new stdClass();  
                    $provider->provider = get_provider();
                    $provider->name = $name;

                    $payment_providers[] = $provider;
                }
            }
        }
        
        return $payment_providers;
    }

    public function has_providers() {
        return sizeof($this->providers) > 0;
    }

    public function create($data) {    
        if ($this->has_providers()) {
            $payment = $this->providers[0]->provider->create($data);

            return $payment;
        }    
        
        return null;
    }
}