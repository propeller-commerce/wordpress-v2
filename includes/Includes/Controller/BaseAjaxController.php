<?php
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Propeller;
use Propeller\PropellerUtils;

class BaseAjaxController {
    protected $reCaptcha_url = PROPELLER_GRECAPTCHA_VERIFY_URL;

    public function __construct() { 
		
	}

	/**
	 * Init Propeller in AJAX calls
	 *
	 * @param void
	 *
	 * @return void
	 */
    public function init_ajax() {
		// SessionController::start();
		UserController::set_default_tax_zone();

		$this->load_text_domain();
		$this->init_filters();

		$this->init_custom_hooks();
    }

	/**
	 * Ends Propeller in AJAX calls
	 *
	 * @param void
	 *
	 * @return void
	 */
    public function end_ajax() {
		// SessionController::set(PROPELLER_QUERY_REQUEST, []);
    }

	/**
	 * Loads text domain for AJAX calls
	 *
	 * @param void
	 *
	 * @return void
	 */
    public function load_text_domain() {
		if (function_exists('load_propel_mu_textdomain'))
            load_propel_mu_textdomain();
        // else 
        //     load_propel_textdomain();
    }

	/**
	 * Reinit Propeller filters for AJAX calls
	 *
	 * @param void
	 *
	 * @return void
	 */
    public function init_filters() {
		$prop = new Propeller();
        $prop->reinit_filters();
    }

	/**
	 * Reinit Propeller custom hooks for AJAX calls
	 *
	 * @param void
	 *
	 * @return void
	 */
    public function init_custom_hooks() {
		if (defined('PROPELLER_PLUGIN_EXTEND_DIR')) {
			// if (file_exists(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-ajax.php')) {
			// 	require_once( PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-ajax.php' );
			// }

			if (file_exists( PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-filters.php')) {
				require_once( PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-filters.php');
			}
		}
    }

    /**
	 * Sanitize frontend input
	 *
	 * @param $data
	 *
	 * @return array
	 */
    public function sanitize($data) {
		return PropellerUtils::sanitize($data);
    }

    /**
	 * Check the front-end request
	 *
	 * @param $nonce_data_key
	 * @param string $nonce_action_key
	 *
	 * @return false|int
	 */
	protected function validate_form_request( $nonce_data_key, $nonce_action_key = PROPELLER_NONCE_KEY_FRONTEND ) {
		$nonce = isset( $_REQUEST[ $nonce_data_key ] ) ? sanitize_text_field($_REQUEST[ $nonce_data_key ]) : false;

		if ( false === $nonce ) {
			return false;
		}

		return (defined('DOING_AJAX') && DOING_AJAX) && wp_verify_nonce( $nonce, $nonce_action_key );
	}

    /**
     * Validate Google reCaptcha
     * @param $data
     *
     * @return bool;
     */
	protected function validate_recaptcha( $data ) {
		if ( ! isset( $data['rc_token'] ) ) {
			return false;
		}

		$response = file_get_contents(
            "$this->reCaptcha_url?secret=" . PROPELLER_RECAPTCHA_SECRET . "&response=" . $data['rc_token'] . "&remoteip=" . $_SERVER['REMOTE_ADDR']
        );

        // use json_decode to extract json response
        $response = json_decode($response);

		if(!isset($response->success)) {
			return false;
		}

		if ( $response->success === false ) {
			return false;
		}

		if ( $response->success && $response->score < PROPELLER_RECAPTCHA_MIN_SCORE ) {
			return false;
		}

		return true;
	}
}