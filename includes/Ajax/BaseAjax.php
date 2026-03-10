<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

add_action('wp_ajax_propeller_get_nonce', 'get_nonce');
add_action('wp_ajax_nopriv_propeller_get_nonce', 'get_nonce');

function get_nonce() {
    wp_send_json_success(array(
        'nonce' => wp_create_nonce(PROPELLER_NONCE_KEY_FRONTEND)
    ));
    wp_die();
}