<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

$ref = 'Propeller\Custom\Includes\Controller\ProductAjaxController';

$AjaxProduct = class_exists($ref, true) 
                    ? new $ref()
                    : new Propeller\Includes\Controller\ProductAjaxController();

add_filter('query_vars', 'product_query_vars');

add_action('wp_ajax_search', array($AjaxProduct, 'search'));
add_action('wp_ajax_nopriv_search', array($AjaxProduct, 'search'));

add_action('wp_ajax_global_search', array($AjaxProduct, 'global_search'));
add_action('wp_ajax_nopriv_global_search', array($AjaxProduct, 'global_search'));

add_action('wp_ajax_do_search', array($AjaxProduct, 'do_search'));
add_action('wp_ajax_nopriv_do_search', array($AjaxProduct, 'do_search'));

add_action('wp_ajax_do_brand', array($AjaxProduct, 'do_brand'));
add_action('wp_ajax_nopriv_do_brand', array($AjaxProduct, 'do_brand'));

add_action('wp_ajax_quick_product_search', array($AjaxProduct, 'quick_product_search'));
add_action('wp_ajax_nopriv_quick_product_search', array($AjaxProduct, 'quick_product_search'));

add_action('wp_ajax_get_product', array($AjaxProduct, 'get_product'));
add_action('wp_ajax_nopriv_get_product', array($AjaxProduct, 'get_product'));

add_action('wp_ajax_update_cluster_content', array($AjaxProduct, 'update_cluster_content'));
add_action('wp_ajax_nopriv_update_cluster_content', array($AjaxProduct, 'update_cluster_content'));

add_action('wp_ajax_update_cluster_price', array($AjaxProduct, 'update_cluster_price'));
add_action('wp_ajax_nopriv_update_cluster_price', array($AjaxProduct, 'update_cluster_price'));

add_action('wp_ajax_get_recently_viewed_products', array($AjaxProduct, 'get_recently_viewed_products'));
add_action('wp_ajax_nopriv_get_recently_viewed_products', array($AjaxProduct, 'get_recently_viewed_products'));

add_action('wp_ajax_load_slider_products', array($AjaxProduct, 'load_slider_products'));
add_action('wp_ajax_nopriv_load_slider_products', array($AjaxProduct, 'load_slider_products'));

add_action('wp_ajax_load_crossupsells', array($AjaxProduct, 'load_crossupsells'));
add_action('wp_ajax_nopriv_load_crossupsells', array($AjaxProduct, 'load_crossupsells'));

add_action('wp_ajax_load_product_specifications', array($AjaxProduct, 'load_product_specifications'));
add_action('wp_ajax_nopriv_load_product_specifications', array($AjaxProduct, 'load_product_specifications'));

add_action('wp_ajax_load_product_downloads', array($AjaxProduct, 'load_product_downloads'));
add_action('wp_ajax_nopriv_load_product_downloads', array($AjaxProduct, 'load_product_downloads'));

add_action('wp_ajax_load_product_videos', array($AjaxProduct, 'load_product_videos'));
add_action('wp_ajax_nopriv_load_product_videos', array($AjaxProduct, 'load_product_videos'));

function product_query_vars($qvars) {
    $qvars[] = 'action';
    $qvars[] = 'page';
    $qvars[] = 'term';
    
    return $qvars;
}