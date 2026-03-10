<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\Cluster;
use Propeller\Includes\Object\Product;
use Propeller\Propeller;
use stdClass;

class FavoriteAjaxController extends BaseAjaxController
{
    protected $favorite_obj;

    public function __construct()
    {
        parent::__construct();

        $this->favorite_obj = new FavoriteController();
    }

    public function create_favorite_list()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->favorite_obj->create_favorite_list($data);

        // Refresh the favorites lists in session after creating a new list
        if (isset($response->id)) {
            SessionController::set(PROPELLER_USER_FAV_LISTS, $this->favorite_obj->get_favorites_lists());
        }

        $return = new stdClass();
        $return->object = 'Favorites';
        $return->postprocess = new stdClass();
        $return->postprocess->reload = isset($response->id);

        die(json_encode($return));
    }

    public function rename_favorite_list()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->favorite_obj->rename_favorite_list($data);

        $return = new stdClass();
        $return->object = 'Favorites';
        $return->postprocess = new stdClass();

        if (is_object($response) && isset($response->id)) {
            // Refresh the favorites lists in session after renaming
            SessionController::set(PROPELLER_USER_FAV_LISTS, $this->favorite_obj->get_favorites_lists());

            $return->success = true;
            $return->postprocess->success = true;
            $return->postprocess->message = __('Favorite list updated', 'propeller-ecommerce-v2');
            $return->postprocess->reload = false;
            $return->postprocess->change_list_name = true;
            $return->postprocess->list_name = $data['name'];
        } else {
            $return->success = false;
            $return->postprocess->success = false;
            $return->postprocess->message = __('An error ocurred updating this favorite list', 'propeller-ecommerce-v2');
        }

        die(json_encode($return));
    }

    public function delete_favorite_list()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->favorite_obj->delete_favorite_list($data);

        // Refresh the favorites lists in session after deleting a list
        if ($response === true) {
            SessionController::set(PROPELLER_USER_FAV_LISTS, $this->favorite_obj->get_favorites_lists());
        }

        $return = new stdClass();

        $return->success = $response;
        $return->message = $response === true ? __("Favorite list deleted", 'propeller-ecommerce-v2') : $response;

        if ($response)
            $return->redirect_url = home_url('/' . PageController::get_slug(PageType::FAVORITES_PAGE));

        die(json_encode($return));
    }

    public function add_favorite()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $update_list = isset($data['update_list']) && (int) $data['update_list'] == 1;

        $response = $this->favorite_obj->add_favorite($data, $update_list);

        die(json_encode($response));
    }

    public function delete_favorite()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $response = $this->favorite_obj->delete_favorite($data);

        $return = new stdClass();

        if (is_object($response)) {
            $index = 0;
            $return->message = '';
            $return->class = $data['class'];

            if (is_array($data['product_id']) && array_key_exists('product_id', $data) && count($data['product_id']) == 1)
                $return->id = $data['product_id'][0];

            if (is_array($data['cluster_id']) && array_key_exists('cluster_id', $data) && count($data['cluster_id']) == 1)
                $return->id = $data['cluster_id'][0];

            foreach ($data['list_id'] as $list_id) {
                $response_key = "favRemoveList_$index";
                $single_response = $response->$response_key;

                $found = array_filter(SessionController::get(PROPELLER_USER_FAV_LISTS)->items, function ($fav_list) use ($list_id) {
                    return $fav_list->id == $list_id;
                });

                $list = current($found);

                if (isset($single_response->id))
                    $return->message .= __('Favorite item removed from', 'propeller-ecommerce-v2') . ' ' . $list->name . '<br />';
                else
                    $return->message .= __('An error ocurred removing this favorite item from', 'propeller-ecommerce-v2') . ' ' . $list->name . '<br />';
            }

            $return->success = true;
            $return->removed = true;
        } else {
            $return->success = false;
            $return->removed = false;
            $return->message = __('An error ocurred removing this favorite item', 'propeller-ecommerce-v2');
        }

        die(json_encode($return));
    }

    public function reload_favorite_modal()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $content = $this->favorite_obj->reload_favorite_modal($data);

        $return = new stdClass();

        if (!empty($content)) {
            $return->success = true;
            $return->content = $content;
        } else {
            $return->success = false;
            $return->content = $content;
        }

        die(json_encode($return));
    }

    public function get_favorite_list_page()
    {
        $this->init_ajax();

        $data = $this->sanitize($_POST);

        $page = isset($data['ppage']) ? (int) $data['ppage'] : 1;
        $items_per_page = isset($data['offset']) ? (int) $data['offset'] : 12;
        $list_id = isset($data['list_id']) ? $data['list_id'] : '';

        if (empty($list_id)) {
            $return = new stdClass();
            $return->success = false;
            $return->message = __('Invalid list ID', 'propeller-ecommerce-v2');
            die(json_encode($return));
        }


        $favorite_list_initial = $this->favorite_obj->get_favorite_list($list_id, 1, 1);

        if (!$favorite_list_initial) {
            $return = new stdClass();
            $return->success = false;
            $return->message = __('Favorite list not found', 'propeller-ecommerce-v2');
            die(json_encode($return));
        }

        $products_total = $favorite_list_initial->products->itemsFound ?? 0;
        $clusters_total = $favorite_list_initial->clusters->itemsFound ?? 0;
        $total_items = $products_total + $clusters_total;

        $start_item = (($page - 1) * $items_per_page) + 1; // 1-indexed
        $end_item = min($page * $items_per_page, $total_items);


        $products_to_fetch = 0;
        $clusters_to_fetch = 0;
        $products_start = 0;
        $clusters_start = 0;

        if ($start_item <= $products_total) {
            // We need some products
            $products_start = $start_item;
            $products_end = min($end_item, $products_total);
            $products_to_fetch = $products_end - $products_start + 1;
        }

        if ($end_item > $products_total) {
            // We need some clusters
            $clusters_start = max($start_item - $products_total, 1);
            $clusters_end = $end_item - $products_total;
            $clusters_to_fetch = $clusters_end - $clusters_start + 1;
        }


        $all_items = [];

        if ($products_to_fetch > 0) {
            $products_page = ceil($products_start / $items_per_page);
            $favorite_list_products = $this->favorite_obj->get_favorite_list($list_id, $products_page, $items_per_page);

            if (isset($favorite_list_products->products->items)) {
                $products_offset_in_page = ($products_start - 1) % $items_per_page;
                $products_slice = array_slice($favorite_list_products->products->items, $products_offset_in_page, $products_to_fetch);

                foreach ($products_slice as $item) {
                    $all_items[] = new Product($item);
                }
            }
        }

        if ($clusters_to_fetch > 0) {
            $clusters_page = ceil($clusters_start / $items_per_page);
            $favorite_list_clusters = $this->favorite_obj->get_favorite_list($list_id, $clusters_page, $items_per_page);

            if (isset($favorite_list_clusters->clusters->items)) {
                $clusters_offset_in_page = ($clusters_start - 1) % $items_per_page;
                $clusters_slice = array_slice($favorite_list_clusters->clusters->items, $clusters_offset_in_page, $clusters_to_fetch);

                foreach ($clusters_slice as $item) {
                    $all_items[] = new Cluster($item, false);
                }
            }
        }


        ob_start();

        if (sizeof($all_items)) {
            $controller = new FavoriteController();
            $controller->data = new stdClass();
            $controller->data->id = $list_id;

            foreach ($all_items as $product) {
                apply_filters('propel_account_favorites_' . strtolower($product->class) . '_card', $product, $controller);
            }
        } else {
            echo '<h5>' . esc_html(__("No favorite items in this list", 'propeller-ecommerce-v2')) . '</h5>';
        }

        $products_content = ob_get_clean();


        $pagination_data = (object)[
            'total_items' => $total_items,
            'offset' => $items_per_page,
            'current_page' => $page,
            'list_id' => $list_id
        ];

        $favorite_controller = new FavoriteController();
        ob_start();
        $favorite_controller->account_favorites_single_list_paging($pagination_data, $favorite_controller);
        $pagination_content = ob_get_clean();

        $return = new stdClass();
        $return->success = true;
        $return->content = $products_content;
        $return->pagination = $pagination_content;
        $return->total_items = $total_items;
        $return->current_page = $pagination_data->current_page;
        $return->total_pages = $total_items > 0 ? ceil($total_items / $items_per_page) : 1;
        $return->products_total = $products_total;
        $return->clusters_total = $clusters_total;

        die(json_encode($return));
    }
}
