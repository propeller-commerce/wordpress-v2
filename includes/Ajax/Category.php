<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

$ref = 'Propeller\Custom\Includes\Controller\CategoryAjaxController';

$AjaxCategory = class_exists($ref, true) 
                    ? new $ref() 
                    : new Propeller\Includes\Controller\CategoryAjaxController();

add_filter('query_vars', 'category_query_vars');

add_action('wp_ajax_do_filter', array($AjaxCategory, 'do_filter'));
add_action('wp_ajax_nopriv_do_filter', array($AjaxCategory, 'do_filter'));

function category_query_vars($qvars) {
    $qvars[] = 'action';
    $qvars[] = 'page';
    
    return $qvars;
}