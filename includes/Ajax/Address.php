<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

$ref = 'Propeller\Custom\Includes\Controller\AddressAjaxController';

$AjaxAddress = class_exists($ref, true) 
                    ? new $ref() 
                    : new Propeller\Includes\Controller\AddressAjaxController();

add_filter('query_vars', 'address_query_vars');

add_action('wp_ajax_add_address', array($AjaxAddress, 'create'));
add_action('wp_ajax_nopriv_add_address', array($AjaxAddress, 'create'));

add_action('wp_ajax_update_address', array($AjaxAddress, 'update'));
add_action('wp_ajax_nopriv_update_address', array($AjaxAddress, 'update'));

add_action('wp_ajax_delete_address', array($AjaxAddress, 'delete'));
add_action('wp_ajax_nopriv_delete_address', array($AjaxAddress, 'delete'));

add_action('wp_ajax_set_address_default', array($AjaxAddress, 'set_address_default'));
add_action('wp_ajax_nopriv_set_address_default', array($AjaxAddress, 'set_address_default'));

function address_query_vars($qvars) {
    $qvars[] = 'action';
    
    return $qvars;
}