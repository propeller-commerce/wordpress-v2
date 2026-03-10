<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

$ref = 'Propeller\Custom\Includes\Controller\FavoriteAjaxController';

$AjaxFavorite = class_exists($ref, true)
    ? new $ref()
    : new Propeller\Includes\Controller\FavoriteAjaxController();

add_action('wp_ajax_create_favorite_list', array($AjaxFavorite, 'create_favorite_list'));
add_action('wp_ajax_nopriv_create_favorite_list', array($AjaxFavorite, 'create_favorite_list'));

add_action('wp_ajax_rename_favorite_list', array($AjaxFavorite, 'rename_favorite_list'));
add_action('wp_ajax_nopriv_rename_favorite_list', array($AjaxFavorite, 'rename_favorite_list'));

add_action('wp_ajax_delete_favorite_list', array($AjaxFavorite, 'delete_favorite_list'));
add_action('wp_ajax_nopriv_delete_favorite_list', array($AjaxFavorite, 'delete_favorite_list'));

add_action('wp_ajax_add_favorite', array($AjaxFavorite, 'add_favorite'));
add_action('wp_ajax_nopriv_add_favorite', array($AjaxFavorite, 'add_favorite'));

add_action('wp_ajax_delete_favorite', array($AjaxFavorite, 'delete_favorite'));
add_action('wp_ajax_nopriv_delete_favorite', array($AjaxFavorite, 'delete_favorite'));

add_action('wp_ajax_reload_favorite_modal', array($AjaxFavorite, 'reload_favorite_modal'));
add_action('wp_ajax_nopriv_reload_favorite_modal', array($AjaxFavorite, 'reload_favorite_modal'));

add_action('wp_ajax_get_favorite_list_page', array($AjaxFavorite, 'get_favorite_list_page'));
add_action('wp_ajax_nopriv_get_favorite_list_page', array($AjaxFavorite, 'get_favorite_list_page'));
