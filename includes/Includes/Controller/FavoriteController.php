<?php

namespace Propeller\Includes\Controller;

if (! defined('ABSPATH')) exit;

use Exception;
use Propeller\Includes\Enum\MediaImagesType;
use Propeller\Includes\Enum\MediaType;
use Propeller\Includes\Enum\UserTypes;
use Propeller\Includes\Object\Cluster;
use Propeller\Includes\Object\Product;
use Propeller\Includes\Query\Media;
use stdClass;

class FavoriteController extends BaseController
{
    public $data;
    public $products;
    protected $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->load_model('favorite');
    }

    /**
     * Filters
     */
    public function favorite_product_card($product, $obj)
    {
        $lazy_load_images = defined('PROPELLER_LAZYLOAD_IMAGES') && PROPELLER_LAZYLOAD_IMAGES;

        require $this->load_template('partials', '/user/propeller-account-favorites-product-card.php');
    }

    public function favorite_cluster_card($cluster, $obj)
    {
        $lazy_load_images = defined('PROPELLER_LAZYLOAD_IMAGES') && PROPELLER_LAZYLOAD_IMAGES;

        require $this->load_template('partials', '/user/propeller-account-favorites-cluster-card.php');
    }

    public function add_favorite_modal($product, $obj)
    {
        if (UserController::is_propeller_logged_in()) {
            if (!wp_doing_ajax()) {
                add_action('wp_footer', function () use ($product, $obj) {
                    // FIX: Only fetch favorites list once, reuse from session for all products
                    if (!SessionController::has(PROPELLER_USER_FAV_LISTS)) {
                        SessionController::set(PROPELLER_USER_FAV_LISTS, $this->get_favorites_lists());
                    }

                    $favorite_lists = SessionController::get(PROPELLER_USER_FAV_LISTS);

                    $found = null;

                    if (isset($product->favoriteLists) && $product->favoriteLists->itemsFound > 0) {
                        foreach ($product->favoriteLists->items as $fav_list) {
                            $fav_list_id = $fav_list->id;

                            $found = array_filter($favorite_lists->items, function ($obj) use ($fav_list_id) {
                                return $obj->id == $fav_list_id;
                            });
                        }
                    }

                    require $this->load_template('partials', '/user/propeller-account-favorites-modal.php');
                });
            } else {
                // FIX: Only fetch favorites list once, reuse from session for all products
                if (!SessionController::has(PROPELLER_USER_FAV_LISTS)) {
                    SessionController::set(PROPELLER_USER_FAV_LISTS, $this->get_favorites_lists());
                }

                $favorite_lists = SessionController::get(PROPELLER_USER_FAV_LISTS);

                $found = null;

                if (isset($product->favoriteLists) && $product->favoriteLists->itemsFound > 0) {
                    foreach ($product->favoriteLists->items as $fav_list) {
                        $fav_list_id = $fav_list->id;

                        $found = array_filter($favorite_lists->items, function ($obj) use ($fav_list_id) {
                            return $obj->id == $fav_list_id;
                        });
                    }
                }

                require $this->load_template('partials', '/user/propeller-account-favorites-modal.php');
            }
        }
    }

    public function remove_favorite_item_modal($list, $product, $obj)
    {
        // For AJAX calls (pagination), output modal immediately
        // For normal page loads, output in footer
        if (wp_doing_ajax()) {
            require $this->load_template('partials', '/user/propeller-account-favorites-remove-favorite-item-modal.php');
        } else {
            add_action('wp_footer', function () use ($list, $product, $obj) {
                require $this->load_template('partials', '/user/propeller-account-favorites-remove-favorite-item-modal.php');
            });
        }
    }

    public function remove_favorite_items_modal($list, $obj)
    {
        add_action('wp_footer', function () use ($list, $obj) {
            require $this->load_template('partials', '/user/propeller-account-favorites-bulk-remove-favorite-items-modal.php');
        });
    }

    public function new_favorite_list_modal($obj)
    {
        add_action('wp_footer', function () use ($obj) {
            require $this->load_template('partials', '/user/propeller-account-favorites-new-favorite-list-modal.php');
        });
    }

    public function delete_favorite_list_modal($obj)
    {
        add_action('wp_footer', function () use ($obj) {
            require $this->load_template('partials', '/user/propeller-account-favorites-delete-favorite-list-modal.php');
        });
    }

    public function rename_favorite_list_modal($obj)
    {
        add_action('wp_footer', function () use ($obj) {
            require $this->load_template('partials', '/user/propeller-account-favorites-rename-favorite-list-modal.php');
        });
    }

    public function add_to_favorite_list_modal($obj)
    {
        add_action('wp_footer', function () use ($obj) {
            require $this->load_template('partials', '/user/propeller-account-favorites-add-to-favorite-list-modal.php');
        });
    }

    public function list_row_item($list, $obj)
    {
        require $this->load_template('partials', '/user/propeller-account-favorites-lists-row-item.php');
    }

    public function reload_favorite_modal($data)
    {
        $content = '';

        if (UserController::is_propeller_logged_in()) {
            $productController = new ProductController();


            $class = isset($data['class']) ? strtolower($data['class']) : '';

            $product =  $class == 'product'
                ? $productController->get_product(null, $data['product_id'])
                : $productController->get_cluster(null, $data['cluster_id']);

            SessionController::set(PROPELLER_USER_FAV_LISTS, $this->get_favorites_lists());

            $favorite_lists = SessionController::get(PROPELLER_USER_FAV_LISTS);

            $found = null;

            if ($product->favoriteLists->itemsFound > 0) {
                foreach ($product->favoriteLists->items as $fav_list) {
                    $fav_list_id = $fav_list->id;

                    $found = array_filter($favorite_lists->items, function ($obj) use ($fav_list_id) {
                        return $obj->id == $fav_list_id;
                    });
                }
            }

            ob_start();

            require $this->load_template('partials', '/user/propeller-account-favorites-modal.php');

            $content = ob_get_clean();

            try {
                ob_end_clean();
            } catch (Exception $ex) {
            }
        }

        return $content;
    }

    public function account_favorites()
    {
        global $wp, $propel;

        $this->data = isset($propel['data'])
            ? $propel['data']
            : ((!isset($wp->query_vars['slug']) || empty(get_query_var('slug'))
                ? $this->get_favorites_lists()
                : $this->get_favorite_list(get_query_var('slug'), 1, 12, 1, 12))); // Fetch first page

        $this->products = [];

        if (isset($wp->query_vars['slug']) && !empty(get_query_var('slug'))) {

            $items_per_page = 12;
            $products_total = $this->data->products->itemsFound ?? 0;
            $clusters_total = $this->data->clusters->itemsFound ?? 0;


            $products_to_show = min($products_total, $items_per_page);
            $clusters_to_show = max(0, $items_per_page - $products_to_show);


            if (isset($this->data->products->items)) {
                $product_slice = array_slice($this->data->products->items, 0, $products_to_show);
                foreach ($product_slice as $item) {
                    $this->products[] = new Product($item);
                }
            }


            if ($clusters_to_show > 0 && isset($this->data->clusters->items)) {
                $cluster_slice = array_slice($this->data->clusters->items, 0, $clusters_to_show);
                foreach ($cluster_slice as $item) {
                    $this->products[] = new Cluster($item, false);
                }
            }
        }

        ob_start();

        if (!isset($wp->query_vars['slug']) || empty(get_query_var('slug')))
            require $this->load_template('partials', '/user/propeller-account-favorites-lists.php');
        else
            require $this->load_template('partials', '/user/propeller-account-favorites-single-list.php');

        return ob_get_clean();
    }

    public function account_favorites_single_list()
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-account-favorites-single-list.php');
        return ob_get_clean();
    }

    public function favorites_menu()
    {
        ob_start();
        require $this->load_template('partials', '/user/propeller-account-menu-favorites.php');
        return ob_get_clean();
    }

    public function recent_favorite_lists($obj)
    {
        if (!UserController::is_propeller_logged_in()) {
            return '';
        }

        $type = 'favoriteLists';

        $params = [
            'offset' => 100
        ];

        $user = $this->get_user();

        switch ($user->__typename) {
            case UserTypes::CUSTOMER:
                $params['customerId'] = $user->userId;
                break;
            case UserTypes::CONTACT:
                $params['contactId'] = $user->userId;
                break;
        }

        $gql = $this->model->get_favorites_lists($params);
        $result = $this->query($gql, $type);

        // Sort by createdAt descending and take only the first 3
        if (isset($result->items) && is_array($result->items)) {
            usort($result->items, function ($a, $b) {
                return strtotime($b->lastModifiedAt) - strtotime($a->lastModifiedAt);
            });
            $result->items = array_slice($result->items, 0, 3);
        }

        $this->data = $result;

        ob_start();
        require $this->load_template('partials', '/user/propeller-account-recent-favorites.php');
        return ob_get_clean();
    }

    public function get_favorites_lists()
    {
        $type = 'favoriteLists';

        $params = [
            'offset' => 100
        ];

        $user = $this->get_user();

        switch ($user->__typename) {
            case UserTypes::CUSTOMER:
                $params['customerId'] = $user->userId;
                break;
            case UserTypes::CONTACT:
                $params['contactId'] = $user->userId;
                // $params['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);                
                break;
        }

        $gql = $this->model->get_favorites_lists($params);

        return $this->query($gql, $type);
    }

    public function get_favorite_list($listId, $products_page = 1, $products_offset = 12, $clusters_page = 1, $clusters_offset = 12)
    {
        $type = 'favoriteList';

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->get_favorite_list(
            $listId,
            $images_fragment,
            PROPELLER_LANG,
            $products_page,
            $products_offset,
            $clusters_page,
            $clusters_offset
        );

        return $this->query($gql, $type);
    }

    public function create_favorite_list($data)
    {
        $type = 'favoriteListCreate';

        $params = [
            'name' => $data['name'],
            'isDefault' => isset($data['is_default']) ? true : false
        ];

        $user = $this->get_user();

        switch ($user->__typename) {
            case UserTypes::CUSTOMER:
                $params['customerId'] = $user->userId;

                break;
            case UserTypes::CONTACT:
                $params['contactId'] = $user->userId;
                // $params['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);

                break;
        }

        $gql = $this->model->create_favorites_list($params);

        return $this->query($gql, $type);
    }

    public function delete_favorite_list($data)
    {
        $type = 'favoriteListDelete';

        $gql = $this->model->delete_favorites_list($data['list_id']);

        return $this->query($gql, $type);
    }

    public function rename_favorite_list($data)
    {
        $type = 'favoriteListUpdate';

        $params['name'] = $data['name'];

        $list_params_arr['isDefault'] = isset($data['is_default']) ? true : false;

        $gql = $this->model->rename_favorite_list($data['list_id'], $params);

        return $this->query($gql, $type);
    }

    public function add_favorite($data, $update_list = false)
    {
        $type = 'favoriteListAddItems';

        $params = [];

        if (array_key_exists('product_id', $data))
            $params['productIds'] = $data['product_id'];

        if (array_key_exists('cluster_id', $data))
            $params['clusterIds'] = $data['cluster_id'];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->add_favorite(
            $data['list_id'],
            $params,
            $images_fragment,
            PROPELLER_LANG,
            $update_list
        );

        $response = $this->query($gql, $type);

        $return = new stdClass();

        if (is_object($response) && isset($response->id)) {
            $return->success = true;
            $return->message = __('Item added in favorites', 'propeller-ecommerce-v2');

            $return->class = isset($data['class']) ? strtolower($data['class']) : '';

            if (is_array($data['product_id']) && array_key_exists('product_id', $data) && count($data['product_id']) == 1)
                $return->id = $data['product_id'][0];

            if (is_array($data['cluster_id']) && array_key_exists('cluster_id', $data) && count($data['cluster_id']) == 1)
                $return->id = $data['cluster_id'][0];

            $products = [];

            foreach ($response->products->items as $item)
                $products[] = new Product($item);

            foreach ($response->clusters->items as $item)
                $products[] = new Cluster($item, false);

            if ($this->analytics) {
                $added_id = $return->id;

                $class = isset($data['class']) ? strtolower($data['class']) : '';

                $added_product = array_filter($products, function ($product) use ($class, $added_id) {
                    if ($class == 'product')
                        return $product->productId == (int) $added_id;
                    else if ($class == 'cluster')
                        return $product->clusterId == (int) $added_id;
                });

                $this->analytics->setData([$added_product ? current($added_product) : null]);

                ob_start();
                apply_filters('propel_ga4_fire_event', 'add_to_wishlist');
                $return->analytics = ob_get_clean();
            }

            if ($update_list) {
                $content = '';

                ob_start();
                if (sizeof($products)) {
                    $this->data = new stdClass();
                    $this->data->id = $data['list_id'];

                    foreach ($products as $product) {
                        apply_filters('propel_account_favorites_' . strtolower($product->class) . '_card', $product, $this);
                    }

                    $content .= ob_get_clean();
                } else {
                    echo wp_kses_post(__("No favorite items in this list", 'propeller-ecommerce-v2'));
                    $content .= ob_get_clean();
                }

                ob_end_clean();

                $return->content = $content;
                $return->total_count = ($response->products->itemsFound ?? 0) + ($response->clusters->itemsFound ?? 0);

                // Generate pagination HTML
                $pagination_data = (object)[
                    'total_items' => $return->total_count,
                    'offset' => 12,
                    'current_page' => 1,
                    'list_id' => $data['list_id']
                ];

                ob_start();
                $this->account_favorites_single_list_paging($pagination_data, $this);
                $return->pagination = ob_get_clean();
            }
        } else {
            $return->success = false;
            $return->message = __('An error ocurred adding this item to your favorites', 'propeller-ecommerce-v2');
        }

        return $return;
    }

    public function delete_favorite($data)
    {
        if (array_key_exists('product_id', $data))
            $params['productIds'] = $data['product_id'];

        if (array_key_exists('cluster_id', $data))
            $params['clusterIds'] = $data['cluster_id'];

        if (!is_array($data['list_id']))
            $data['list_id'] = [$data['list_id']];

        $gql = $this->model->delete_favorite_multiple_lists(
            $data['list_id'],
            $params
        );

        return $this->query($gql, null);
    }

    public function account_favorites_single_list_paging($data, $obj)
    {
        $total_items = $data->total_items;
        $offset = $data->offset ?? 12;
        $current_page = $data->current_page ?? 1;
        $total_pages = $total_items > 0 ? ceil($total_items / $offset) : 1;
        $list_id = $data->list_id;

        require $this->load_template('partials', '/user/propeller-account-favorites-single-list-paging.php');
    }

    private function get_user()
    {
        $user_obj = new UserController();
        return $user_obj->get_user();
    }
}
