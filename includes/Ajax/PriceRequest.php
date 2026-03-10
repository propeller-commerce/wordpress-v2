<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

$ref = 'Propeller\Custom\Includes\Controller\PricerequestAjaxController';

$AjaxPriceRequest = class_exists($ref, true) 
                    ? new $ref()
                    : new Propeller\Includes\Controller\PricerequestAjaxController();

add_filter('query_vars', 'pr_query_vars');

add_action('wp_ajax_propel_add_pr_product', array($AjaxPriceRequest, 'add'));
add_action('wp_ajax_nopriv_propel_add_pr_product', array($AjaxPriceRequest, 'add'));

add_action('wp_ajax_propel_remove_pr_product', array($AjaxPriceRequest, 'remove'));
add_action('wp_ajax_nopriv_propel_remove_pr_product', array($AjaxPriceRequest, 'remove'));

add_action('wp_ajax_propel_do_price_request', array($AjaxPriceRequest, 'send_price_request'));
add_action('wp_ajax_nopriv_propel_do_price_request', array($AjaxPriceRequest, 'send_price_request'));


function pr_query_vars($qvars) {
    $qvars[] = 'action';
    
    return $qvars;
}