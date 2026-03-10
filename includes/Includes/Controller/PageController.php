<?php
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class PageController extends BaseController {
    public function __construct() { 
        parent::__construct();
    }

    public static function create_pages() {
        global $wpdb, $propellerSluggablePages;

        $pages_result = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i", $wpdb->prefix . PROPELLER_PAGES_TABLE));

        $my_account_page_id = null;

        foreach ($pages_result as $index => $page) {
            if ($page->account_page_is_parent == 1)
                continue;

            if ($page->page_sluggable == 1)
                $propellerSluggablePages[$page->page_type] = $page->page_slug;

            // Check if the page already exists
            if(!self::pageExists($page->page_slug)) {
                $guid = site_url() . "/" . $page->page_slug . '/';

                $page_data  = array( 'post_title'     => $page->page_name . PROPELLER_PAGE_SUFFIX,
                                'post_type'      => 'page',
                                'post_name'      => $page->page_slug,
                                'post_content'   => "[$page->page_shortcode]",
                                'post_status'    => 'publish',
                                'comment_status' => 'closed',
                                'ping_status'    => 'closed',
                                'post_author'    => 1,
                                'menu_order'     => 0,
                                'guid'           => $guid);

                $page_id = wp_insert_post($page_data, FALSE);
                
                if ($page->is_my_account_page && !is_wp_error($page_id))
                    $my_account_page_id = $page_id;
            }
        }

        foreach ($pages_result as $page) {
            if ($page->account_page_is_parent == 0)
                continue;

            if ($page->page_sluggable == 1)
                $propellerSluggablePages[$page->page_type] = $page->page_slug;

            // Check if the page already exists
            if(!self::pageExists($page->page_slug)) {
                $guid = site_url() . "/" . $page->page_slug . '/';

                $page_data  = array( 'post_title'     => $page->page_name . PROPELLER_PAGE_SUFFIX,
                                'post_type'      => 'page',
                                'post_name'      => $page->page_slug,
                                'post_content'   => "[$page->page_shortcode]",
                                'post_status'    => 'publish',
                                'comment_status' => 'closed',
                                'ping_status'    => 'closed',
                                'post_author'    => 1,
                                'menu_order'     => 0,
                                'guid'           => $guid,
                                'posts_per_page'    => 1,
                                'no_found_rows'     => true);

                if ($page->account_page_is_parent == 1 && !empty($my_account_page_id))
                    $page_data['post_parent'] = $my_account_page_id;

                $page_id = wp_insert_post($page_data, FALSE);
            }
        }
    }

    public static function create_page($page) {
        // Check if the page already exists
        if(!self::pageExists($page->page_slug)) {
            $guid = site_url() . "/" . $page->page_slug . '/';

            $page_data  = array( 'post_title'     => $page->page_name . PROPELLER_PAGE_SUFFIX,
                            'post_type'      => 'page',
                            'post_name'      => $page->page_slug,
                            'post_content'   => "[$page->page_shortcode]",
                            'post_status'    => 'publish',
                            'comment_status' => 'closed',
                            'ping_status'    => 'closed',
                            'post_author'    => 1,
                            'menu_order'     => 0,
                            'guid'           => $guid);

            $page_id = wp_insert_post($page_data, FALSE);
        }
    }

    private static function pageExists($page_slug) {
        $page = get_page_by_path( $page_slug , OBJECT );
   
        if (isset($page))
            return true;
            
        return false;
    }

    public static function get_slug($page_type) {
        $page = self::get_page($page_type);

        if (!empty($page))
            return $page->page_slug;

        return '';
    }

    private static function get_page($page_type) {
        global $propellerPages;

        if (is_array($propellerPages) && count($propellerPages)) {
            foreach ($propellerPages as $index => $page) {
                if ($page->page_type == $page_type)
                    return $page;
            }
        }

        return null;
    }

    public static function insert_default_pages() {
        global $table_prefix, $wpdb;

        $tbl_pages      = $table_prefix . PROPELLER_PAGES_TABLE;

        $pages_queries = [];

        $columns = [
            'page_name',
            'page_slug',
            'page_sluggable',
            'page_shortcode',
            'page_type',
            'is_my_account_page',
            'account_page_is_parent'
        ];

        $pages_queries[] = "Catalog listing,category,1,product-listing,Category page,0,0";
        $pages_queries[] = "Product details,product,1,product-details,Product page,0,0";
        $pages_queries[] = "Cluster details,cluster,1,cluster-details,Cluster page,0,0";
        $pages_queries[] = "Brands page,brand,1,brand-listing,Brand page,0,0";
        $pages_queries[] = "Search,search,1,product-search,Search page,0,0";
        $pages_queries[] = "My Account,my-account-details,0,account-details,My account page,1,0";
        $pages_queries[] = "My Account Mobile,my-account,0,account-mobile,My account mobile page,1,0";
        $pages_queries[] = "My Orders,my-orders,0,account-orders,Orders page,0,1";
        $pages_queries[] = "My Order details,order-details,0,account-order-details,Order details page,0,1";
        $pages_queries[] = "My Quotations,my-quotations,0,account-quotations,Quotations page,0,1";
        $pages_queries[] = "My Quotation details,quote-details,0,account-order-details,Quotation details page,0,1";
        $pages_queries[] = "My Addresses,my-addresses,0,account-addresses,Addresses page,0,1";
        $pages_queries[] = "My Favorites,my-favorites,0,account-favorites,Favorites page,0,1";
        // $pages_queries[] = "My Ordelist,my-orderlist,0,account-orderlist,Orderlist page,0,1";
        $pages_queries[] = "My Invoices,my-invoices,0,account-invoices,Invoices page,0,1";
        $pages_queries[] = "Register,register,0,registration-form,Register page,0,0";
        $pages_queries[] = "Login,login,0,login-form,Login page,0,0";
        $pages_queries[] = "Forgot password,forgot-password,0,forgot-password-form,Forgot password page,0,0";
        $pages_queries[] = "Reset password,reset-password,0,reset-password-form,Reset password page,0,0";
        $pages_queries[] = "Quick Order,quick-order,0,quick-add-to-basket,Quick order page,0,0";
        $pages_queries[] = "Shopping Cart,shopping-cart,0,shopping-cart,Shopping cart page,0,0";
        $pages_queries[] = "Checkout,checkout,1,checkout,Checkout page,0,0";
        $pages_queries[] = "Checkout summary,checkout-summary,1,checkout-summary,Checkout summary page,0,0";
        $pages_queries[] = "Terms and conditions page,terms-conditions,0,menu,Terms & Conditions page,0,0";
        $pages_queries[] = "Machines,my-installations,1,machines,Machines page,0,0";
        $pages_queries[] = "Product price request,price-request,0,price-request,Price request page,0,0";
        $pages_queries[] = "Payment check,payment-check,0,menu,Payment check page,0,0";
        $pages_queries[] = "Thank you,thank-you,0,checkout-thank-you,Thank you page,0,0";
        $pages_queries[] = "Payment failed,payment-failed,0,payment-failed,Payment failed page,0,0";
        $pages_queries[] = "Payment being processed,payment-processed,0,payment-processed,Payment being processed page,0,0";
        $pages_queries[] = "Payment cancelled,payment-cancelled,0,payment-cancelled,Payment cancelled page,0,0";
        $pages_queries[] = "Authorization confirmed,authorization-confirmed,0,authorization-confirmed,Authorization confirmed page,0,0";
        $pages_queries[] = "Expired payment request,payment-expired,0,payment-expired,Expired payment request page,0,0";
        $pages_queries[] = "Purchase authorizations,purchase-authorizations,0,purchase-authorizations,Purchase authorizations page,0,1";
        $pages_queries[] = "Purchase authorization requests,purchase-authorization-requests,0,purchase-authorizations-requests,Purchase authorization requests page,0,1";
        $pages_queries[] = "Purchase authorization thank you,purchase-authorization-thank-you,1,purchase-authorization-thank-you,Purchase authorization thank you page,0,0";
        $pages_queries[] = "SSO login page,sign-in,0,sso-sign-in,SSO login page,0,0";

        foreach($pages_queries as $page_query) {
            $vals = explode(',', $page_query);

            $insert_arr = [];

            for ($i = 0; $i < count($columns); $i++) 
                $insert_arr[$columns[$i]] = $vals[$i];

            $wpdb->insert($tbl_pages, $insert_arr);
        }
            
    }

    public static function set_referrer($name, $uri) {
        SessionController::set($name, $uri);
    }

    public static function get_referrer($name) {
        if (SessionController::has($name)) {
            $val = SessionController::get($name);

            SessionController::remove($name);

            return $val;
        }
        
        return null;
    }
}