<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

$ref = 'Propeller\Custom\Includes\Controller\UserAjaxController';

$AjaxUser = class_exists($ref, true)
    ? new $ref()
    : new Propeller\Includes\Controller\UserAjaxController();

add_filter('query_vars', 'user_query_vars');

add_action('wp_ajax_propel_load_mini_account', array($AjaxUser, 'load_mini_account'));
add_action('wp_ajax_nopriv_propel_load_mini_account', array($AjaxUser, 'load_mini_account'));

add_action('wp_ajax_do_login', array($AjaxUser, 'do_login'));
add_action('wp_ajax_nopriv_do_login', array($AjaxUser, 'do_login'));

add_action('wp_ajax_do_register', array($AjaxUser, 'do_register'));
add_action('wp_ajax_nopriv_do_register', array($AjaxUser, 'do_register'));

add_action('wp_ajax_user_prices', array($AjaxUser, 'user_prices'));
add_action('wp_ajax_nopriv_user_prices', array($AjaxUser, 'user_prices'));

add_action('wp_ajax_forgot_password', array($AjaxUser, 'forgot_password'));
add_action('wp_ajax_nopriv_forgot_password', array($AjaxUser, 'forgot_password'));

add_action('wp_ajax_company_switch', array($AjaxUser, 'company_switch'));
add_action('wp_ajax_nopriv_company_switch', array($AjaxUser, 'company_switch'));

add_action('wp_ajax_purchase_authorizations', array($AjaxUser, 'purchase_authorizations'));
add_action('wp_ajax_nopriv_purchase_authorizations', array($AjaxUser, 'purchase_authorizations'));

add_action('wp_ajax_preview_authorization_request', array($AjaxUser, 'preview_authorization_request'));
add_action('wp_ajax_nopriv_preview_authorization_request', array($AjaxUser, 'preview_authorization_request'));

add_action('wp_ajax_delete_authorization_request', array($AjaxUser, 'delete_authorization_request'));
add_action('wp_ajax_nopriv_delete_authorization_request', array($AjaxUser, 'delete_authorization_request'));

add_action('wp_ajax_accept_authorization_request', array($AjaxUser, 'accept_authorization_request'));
add_action('wp_ajax_nopriv_accept_authorization_request', array($AjaxUser, 'accept_authorization_request'));

add_action('wp_ajax_create_purchase_authorization_config', array($AjaxUser, 'create_purchase_authorization_config'));
add_action('wp_ajax_nopriv_create_purchase_authorization_config', array($AjaxUser, 'create_purchase_authorization_config'));

add_action('wp_ajax_update_purchase_authorization_config', array($AjaxUser, 'update_purchase_authorization_config'));
add_action('wp_ajax_nopriv_update_purchase_authorization_config', array($AjaxUser, 'update_purchase_authorization_config'));

add_action('wp_ajax_delete_purchase_authorization_config', array($AjaxUser, 'delete_purchase_authorization_config'));
add_action('wp_ajax_nopriv_delete_purchase_authorization_config', array($AjaxUser, 'delete_purchase_authorization_config'));

add_action('wp_ajax_add_contact_to_company', array($AjaxUser, 'add_contact_to_company'));
add_action('wp_ajax_nopriv_add_contact_to_company', array($AjaxUser, 'add_contact_to_company'));

add_action('wp_ajax_create_contact_login', array($AjaxUser, 'create_contact_login'));
add_action('wp_ajax_nopriv_create_contact_login', array($AjaxUser, 'create_contact_login'));

add_action('wp_ajax_delete_contact_login', array($AjaxUser, 'delete_contact_login'));
add_action('wp_ajax_nopriv_delete_contact_login', array($AjaxUser, 'delete_contact_login'));

function user_query_vars($qvars)
{
    $qvars[] = 'action';

    return $qvars;
}
