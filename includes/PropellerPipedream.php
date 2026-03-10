<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

use stdClass;

/**
 * Pipedream handler
 * 
 * @author Propeller
 */
class PropellerPipedream {

	/**
	 * Pipedream endpoint
	 * 
	 * @var string
	 */
	protected $endpoint;
	
	/**
	 * Pipedream API Key
	 * 
	 * @var string
	 */
	protected $key;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->endpoint		= 'https://eojlqyl90pgaubb.m.pipedream.net';
		$this->key 			= 'CfUaFxq9ngQ6mpEjRTMDV7z5ocNVuc9S';
	}

	/**
	 * Build headers
	 * 
	 * @return array
	 */
	protected function headers() {
		return [ 
				'Content-Type' => 'application/json',
				'apikey' => $this->key,
			];
	}

	/**
	 * Make a POST request
	 * 
	 * @param array $args
	 * 
	 * @return object
	 */
	protected function post($args) {
		$return_obj = new stdClass();

		$response = wp_remote_post( 
			$this->endpoint, [ 
				'headers' => $this->headers(), 
				'body' => wp_json_encode( $args )
			]
		);
		
		if ( is_wp_error( $response ) ) {
			$return_obj->error = true;
			$return_obj->message = $response->get_error_message();
		} else {
			$return_obj->code = wp_remote_retrieve_response_code( $response );
			$return_obj->body = wp_remote_retrieve_body( $response );
			$return_obj->error = false;
		}

		return $return_obj;
	}

	/**
	 * Make a GET request
	 * 
	 * @param array $args
	 * 
	 * @return object
	 */
	protected function get($args) {
		$return_obj = new stdClass();

		$query_string = '';

		if (is_array($args) && count($args))
			$query_string = '?' . http_build_query($args);

		$response = wp_remote_get( $this->endpoint . $query_string, [
				'headers' => $this->headers()
			] 
		);

		if ( is_wp_error( $response ) ) {
			$return_obj->error = true;
			$return_obj->message = $response->get_error_message();
		} else {
			$return_obj->code = wp_remote_retrieve_response_code( $response );
			$return_obj->body = wp_remote_retrieve_body( $response );
			$return_obj->error = false;
		}

		return $return_obj;
	}
}