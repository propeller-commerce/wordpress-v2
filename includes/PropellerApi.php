<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Exception;
use Propeller\Includes\Controller\SessionController;
use Softonic\GraphQL\ClientBuilder;

class PropellerApi
{
    protected $client;
    protected $endpoint;
    protected $key;
    protected $order_key;
    protected $omit_access_token = false;

    protected $execution_time;

    protected $order_key_types = [
        'order',
        'orderSetStatus',
        'passwordResetLink',
        'triggerQuoteSendRequest',
        'triggerOrderSendConfirm',
        'paymentCreate',
        'paymentUpdate',
        'authenticationCreate',
        'contactCreateAccount',
        'contactDeleteAccount',
        'triggerPasswordSendResetEmailEvent',
        'triggerPasswordSendInitEmailEvent',
        'contactRegister',
        'contacts',
        'magicTokenCreate'
    ];


    public function __construct()
    {
        if (defined('PROPELLER_API_URL') && defined('PROPELLER_API_KEY') && defined('PROPELLER_ORDER_API_KEY')) {
            $this->endpoint = PROPELLER_API_URL;
            $this->key = PROPELLER_API_KEY;
            $this->order_key = PROPELLER_ORDER_API_KEY;
        } else {
            $this->get_credentials();

            if (empty($this->endpoint) || empty($this->key))
                return;
        }
    }

    protected function buildClient($type)
    {
        $this->client = ClientBuilder::build($this->endpoint, [
            'verify' => false,
            'connect_timeout' => 60,
            'timeout' => 60,
            'headers' => $this->buildHeaders($type)
        ]);
    }

    public function omit_access_token($omit)
    {
        $this->omit_access_token = $omit;
    }

    protected function buildHeaders($type)
    {
        $headers = [
            'apikey' => $this->key
        ];

        $mutations = $this->order_key_types;
        if (defined('PROPELLER_PLUGIN_EXTEND_DIR') && file_exists(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-mutations.php')) {
            $custom_mutations = include(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-mutations.php');

            $mutations = array_merge($mutations, $custom_mutations);
        }

        if (in_array($type, $mutations))
            $headers['apikey'] = $this->order_key;

        if (SessionController::has(PROPELLER_ACCESS_TOKEN) && SessionController::get(PROPELLER_ACCESS_TOKEN) && !$this->omit_access_token)
            $headers['Authorization'] = 'Bearer ' . SessionController::get(PROPELLER_ACCESS_TOKEN);

        // Used only if SSO is used for resetting claims
        if (SessionController::has(PROPELLER_ID_TOKEN) && SessionController::get(PROPELLER_ID_TOKEN))
            $headers['Authorization'] = 'Bearer ' . SessionController::get(PROPELLER_ID_TOKEN);

        return $headers;
    }

    protected function query($gql, $type, $display_error = true)
    {
        if (empty($this->endpoint) || empty($this->key))
            return;

        $this->buildClient($type);

        try {
            /* rate limiter for API requests. */
            // Rate defined in PROPELLER_MAX_API_ATTEMPTS

            // preg_match('/(mutation|query)\s*(\w+)\s*/', $gql->query, $matches);

            // if (!SessionController::has(PROPELLER_QUERY_REQUEST))
            // SessionController::set(PROPELLER_QUERY_REQUEST, []);

            // if (is_array(SessionController::get(PROPELLER_QUERY_REQUEST)) && defined('PROPELLER_MAX_API_ATTEMPTS')) {
            //     $counts = array_count_values(SessionController::get(PROPELLER_QUERY_REQUEST));

            //     if (isset($counts[$matches[2]]) && $counts[$matches[2]] == intval(PROPELLER_MAX_API_ATTEMPTS)) {
            //         SessionController::set(PROPELLER_QUERY_REQUEST, []);
            //         propel_log($matches[2] . ' caused too many request attempts. Dying...');
            //         die(__('We cannot process your request. Feel free to contact us.', 'propeller-ecommerce-v2'));
            //     }
            // } 

            // $requests_arr = SessionController::get(PROPELLER_QUERY_REQUEST);
            // $requests_arr[] = $matches[2];
            // SessionController::set(PROPELLER_QUERY_REQUEST, $requests_arr);

            /* ----------- rate limiter for API requests. ------------- */

            // ob_start();
            // debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            // $backtrace = ob_get_clean();

            // query_log($matches[2]);
            // query_log("\n" . print_r($backtrace, true));
            // query_log("\n" . print_r($requests_arr, true));


            $response = $this->client->query($gql->query, $gql->variables);

            if ($response->hasErrors()) {
                // Returns an array with all the errors found.
                if ($display_error) {
                    ob_start();
                    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                    $backtrace = ob_get_clean();

                    $error_log_msg = "\n" . print_r($response->getErrors(), true) . "\r\n";
                    $error_log_msg .= $gql->query . "\r\n";
                    $error_log_msg .= print_r($gql->variables, true) . "\r\n";
                    $error_log_msg .= json_encode($gql->variables) . "\r\n";
                    $error_log_msg .= print_r($backtrace, true) . "\r\n";
                    propel_log($error_log_msg . "\r\n");
                }

                if (!$type)
                    return json_decode(json_encode($response->getData()), false);
                else if (isset($response->getData()[$type])) {
                    $data = json_decode(json_encode($response->getData()), false);

                    return $data->$type;
                } else
                    return $response->getErrors();
            } else {
                // Returns an array with all the data returned by the GraphQL server.
                $data = json_decode(json_encode($response->getData()), false);

                if (!$type)
                    return $data;

                return $data->$type;
            }
        } catch (Exception $ex) {
            ob_start();
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $backtrace = ob_get_clean();

            $error_log_msg = "\n" . $ex->getMessage() . "\r\n";
            $error_log_msg .= $gql->query . "\r\n";
            $error_log_msg .= print_r($gql->variables, true) . "\r\n";
            $error_log_msg .= json_encode($gql->variables) . "\r\n";
            $error_log_msg .= print_r($backtrace, true) . "\r\n";

            propel_log($error_log_msg . "\r\n");

            return $ex->getMessage();
        }
    }

    protected function get_credentials()
    {
        global $wpdb;

        $settings_tbl = $wpdb->prefix . PROPELLER_SETTINGS_TABLE;

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM %i WHERE `id` = %d", $settings_tbl, 1)
        );

        if (sizeof($results)) {
            $this->endpoint = $results[0]->api_url;
            $this->key = $results[0]->api_key;

            if (!defined('PROPELLER_API_URL'))
                define('PROPELLER_API_URL', $results[0]->api_url);

            if (!defined('PROPELLER_API_KEY'))
                define('PROPELLER_API_KEY', $results[0]->api_key);

            if (!defined('PROPELLER_ORDER_API_KEY'))
                define('PROPELLER_ORDER_API_KEY', $results[0]->order_api_key);

            if (!defined('PROPELLER_ANONYMOUS_USER'))
                define('PROPELLER_ANONYMOUS_USER', (int) $results[0]->anonymous_user);

            if (!defined('PROPELLER_SITE_ID'))
                define('PROPELLER_SITE_ID', (int) $results[0]->site_id);

            if (!defined('PROPELLER_BASE_CATALOG'))
                define('PROPELLER_BASE_CATALOG', (int) $results[0]->catalog_root);

            if (!defined('PROPELLER_DEFAULT_LOCALE'))
                define('PROPELLER_DEFAULT_LOCALE', $results[0]->default_locale);
        }
    }
}
