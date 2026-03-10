<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Enum\MediaImagesType;
use Propeller\Includes\Enum\MediaType;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\ProductClass;
use Propeller\Includes\Object\Category;
use Propeller\Includes\Object\Cluster;
use Propeller\Includes\Object\FilterArray;
use Propeller\Includes\Object\Product;
use Propeller\Includes\Query\Media;
use Propeller\PropellerHelper;
use Propeller\PropellerUtils;
use stdClass;

class CategoryController extends BaseController
{

    protected $type = 'category';
    static $category_slug;
    protected $base_catalog_id;

    protected $data;
    protected $products;
    protected $attributes;
    protected $filters;
    protected $category;

    protected $model;
    public $title;
    public $pagename;

    const FILTERS_FLASH_KEY = 'filters';

    public $offset_arr = [12, 24, 48];
    public $sort_arr = [];
    public $sort_order = [];

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->load_model('category');

        // $this->sort_arr = [
        //     "LAST_MODIFIED_AT" => __('Date changed', 'propeller-ecommerce-v2'),
        //     "CREATED_AT" => __('Date created', 'propeller-ecommerce-v2'),
        //     "NAME" => __('Name', 'propeller-ecommerce-v2'),
        //     "PRICE" => __('Price', 'propeller-ecommerce-v2'),
        //     "RELEVANCE" => __('Relevance', 'propeller-ecommerce-v2'),
        //     "SKU" => __('SKU', 'propeller-ecommerce-v2'),
        //     "SUPPLIER_CODE" => __('Supplier code', 'propeller-ecommerce-v2'),
        // ];

        // $this->sort_order = [
        //     "ASC" => __('Asc', 'propeller-ecommerce-v2'),
        //     "DESC" => __('Desc', 'propeller-ecommerce-v2'),
        // ];

        self::$category_slug = PageController::get_slug(PageType::CATEGORY_PAGE);

        if (defined('PROPELLER_BASE_CATALOG'))
            $this->base_catalog_id = PROPELLER_BASE_CATALOG;
    }

    /*
        Category filters
    */
    public function category_title($data)
    {
        require $this->load_template('partials', '/category/propeller-product-listing-title.php');
    }

    public function product_listing_pre_grid($data, $obj, $sort, $prop_name, $prop_value, $do_action, $obid)
    {
        switch ($obj->pagename) {
            case PageController::get_slug(PageType::CATEGORY_PAGE):
                require $this->load_template('partials', '/other/propeller-catalog-pre-grid.php');

                break;
            case PageController::get_slug(PageType::SEARCH_PAGE):
                require $this->load_template('partials', '/other/propeller-search-pre-grid.php');

                break;
            case PageController::get_slug(PageType::BRAND_PAGE):
                require $this->load_template('partials', '/other/propeller-brand-pre-grid.php');

                break;
            default:
                require $this->load_template('partials', '/other/propeller-catalog-pre-grid.php');

                break;
        }
    }

    public function selected_filters($obj)
    {
        $selected_filters = $obj->get_selected_filters($obj->filters->getFilters());

        $data = $this->sanitize($_REQUEST);

        if (
            isset($data['price_from']) && $data['price_from'] != '' && is_numeric($data['price_from']) &&
            isset($data['price_to']) && $data['price_to'] != '' && is_numeric($data['price_to'])
        ) {

            $price_filter = new stdClass();
            $price_filter->filter = new stdClass();
            $price_filter->filter->type = 'price';
            $price_filter->filter->searchId = 'price';

            $price_filter->value = PropellerHelper::currency() . PropellerHelper::formatPrice((int) $data['price_from']) . ' - ' . PropellerHelper::currency() . PropellerHelper::formatPrice((int) $data['price_to']);
            $price_filter->price_from = (int) $data['price_from'];
            $price_filter->price_to = (int) $data['price_to'];

            array_unshift($selected_filters, $price_filter);
        }

        require $this->load_template('partials', '/other/propeller-selected-filters.php');
    }

    public function category_menu($data)
    {
        $menucontroller = new MenuController();
        $menuItems = $menucontroller->getMenu();

        require $this->load_template('partials', '/category/propeller-product-listing-categories.php');
    }

    public function category_filters($filters)
    {
        require $this->load_template('partials', '/category/propeller-filter-container.php');
    }

    public function category_listing_grid($obj, $products, $paging_data, $sort, $prop_name, $prop_value, $do_action, $obid = null)
    {
        $display_class = isset($_REQUEST['view']) && !empty($_REQUEST['view']) ? sanitize_text_field($_REQUEST['view']) : 'blocks';

        require $this->load_template('partials', '/product/propeller-product-grid.php');
    }

    public function category_gecommerce_listing($products, $obj)
    {
        require $this->load_template('partials', '/category/propeller-gecommerce-listing.php');
    }

    public function category_listing_products($products, $obj)
    {
        require $this->load_template('partials', '/category/propeller-product-listing-products.php');
    }

    public function category_listing_pagination($paging_data, $prop_name, $prop_value, $do_action, $obid)
    {
        $prev = $paging_data->page - 1;
        $prev_disabled = false;

        if ($prev < 1) {
            $prev = 1;
            $prev_disabled = 'disabled';
        }

        $next = $paging_data->page + 1;
        $next_disabled = false;

        if ($next >= $paging_data->pages) {
            $next = $paging_data->pages;

            if ($paging_data->page == $next)
                $next_disabled = 'disabled';
        }

        require $this->load_template('partials', '/other/propeller-pagination.php');
    }

    public function category_listing_description($data)
    {
        require $this->load_template('partials', '/category/propeller-product-listing-description.php');
    }

    /*
        Category shortcodes
    */
    public function product_listing($applied_filters = [], $is_ajax = false)
    {
        global $propel, $wp_query;

        if (!$applied_filters || !sizeof($applied_filters))
            $applied_filters = PropellerUtils::sanitize($_REQUEST);

        $filters_applied = $this->process_filters($applied_filters);
        $qry_params = $this->build_search_arguments($applied_filters);

        $qry_params = array_merge($qry_params, $filters_applied);

        $sort = isset($applied_filters['sortInputs']) && !empty($applied_filters['sortInputs'])
            ? explode(',', $applied_filters['sortInputs'])
            : PROPELLER_DEFAULT_SORT_FIELD . ',' . PROPELLER_SECONDARY_SORT_FIELD . ',' . PROPELLER_DEFAULT_SORT_DIRECTION;

        $slug = isset($applied_filters['slug']) ? $applied_filters['slug'] : get_query_var('slug');

        $categoryId = null;

        if (PROPELLER_ID_IN_URL) {
            if (isset($wp_query->query_vars) && isset($wp_query->query_vars['obid']) && is_numeric($wp_query->query_vars['obid']))
                $categoryId = (int) $wp_query->query_vars['obid'];
            else if (isset($applied_filters['obid']) && is_numeric($applied_filters['obid']))
                $categoryId = (int) $applied_filters['obid'];
        }

        $style = isset($applied_filters['view']) ? $applied_filters['view'] : 'blocks';

        $this->data = isset($propel['data'])
            ? $propel['data']
            : $this->get_catalog($slug, $categoryId, $qry_params);

        $this->category = new Category($this->data);

        $this->products = [];

        $product_controller = new ProductController();

        foreach ($this->data->products->items as $product) {
            if (!count($product->slug)) {
                $this->data->products->itemsFound--;
                continue;
            }

            if ($product->class == ProductClass::Product)
                $this->products[] = new Product($product);
            if ($product->class == ProductClass::Cluster) {
                $cluster_product = new Cluster($product, false);

                if (is_null($cluster_product->defaultProduct))
                    continue;

                // $cluster_product->get_price();

                $this->products[] = $cluster_product;
            }
        }

        $this->attributes = [];
        if ($this->data->products->filters)
            $this->attributes = new FilterArray($this->data->products->filters);

        $this->filters = new FilterController($this->attributes, [$this->data->products->minPrice, $this->data->products->maxPrice]);
        $this->filters->set_slug($slug);
        $this->filters->set_action('do_filter');
        $this->filters->set_prop('slug');
        $this->filters->set_obid($categoryId);
        $this->filters->set_liststyle($style);

        $paging_data = $this->data->products;

        $this->pagename = PageController::get_slug(PageType::CATEGORY_PAGE);

        $do_action = "do_filter";
        $prop_name = "slug";
        $prop_value = $slug;
        $obid = $categoryId;

        $this->title = $this->data->name[0]->value;

        ob_start();

        // set GA4 data
        if ($this->analytics)
            $this->analytics->setData($this->data);

        if ($is_ajax) {
            $response = new stdClass();

            apply_filters('propel_category_title', $this->data);
            $cat_title = ob_get_clean();

            // ob_start();
            // apply_filters('propel_product_listing_pre_grid', $paging_data, $this, $sort, $prop_name, $prop_value, $do_action, $obid);
            // $cat_pre_grid = ob_get_clean();            

            ob_start();
            apply_filters('propel_category_grid', $this, $this->products, $paging_data, $sort, $prop_name, $prop_value, $do_action, $obid);
            $cat_grid = ob_get_clean();

            ob_start();
            apply_filters('propel_category_description', $this->data);
            $cat_desc = ob_get_clean();

            $cat_ga4 = '';
            if ($this->analytics) {
                ob_start();

                apply_filters('propel_ga4_fire_event', 'category');
                apply_filters('propel_ga4_print_data', 'category');

                $cat_ga4 = ob_get_clean();
            }

            $response->content = $cat_title . $cat_grid . $cat_desc . $cat_ga4;

            ob_start();
            apply_filters('propel_category_filters', $this->filters);
            $response->filters = ob_get_clean();

            ob_start();
            apply_filters('propel_category_menu', $this->data);
            $response->categories = ob_get_clean();

            ob_end_clean();

            return $response;
        } else {
            require $this->load_template('templates', '/propeller-product-listing.php');

            if ($this->analytics) {
                apply_filters('propel_ga4_fire_event', 'category');
                apply_filters('propel_ga4_print_data', 'category');
            }
                
        }

        return ob_get_clean();
    }

    public function get_catalog($idOrSlug = null, $categoryId = null, $product_args = [])
    {
        $category_args = [
            'categoryId' => !$categoryId ? $this->base_catalog_id : $categoryId,
            'slug' => $idOrSlug
        ];

        if (!isset($product_args['language']))
            $product_args['language'] = PROPELLER_LANG;

        if (!isset($product_args['hidden']))
            $product_args['hidden'] = false;

        if (!isset($product_args['offset']))
            $product_args['offset'] = intval(PROPELLER_DEFAULT_OFFSET);

        if (!isset($product_args['sortInputs']))
            $product_args['sortInputs'] = [[
                "field" => PROPELLER_DEFAULT_SORT_FIELD,
                "order" => PROPELLER_DEFAULT_SORT_DIRECTION
            ], [
                "field" => PROPELLER_SECONDARY_SORT_FIELD,
                "order" => PROPELLER_DEFAULT_SORT_DIRECTION
            ]];

        if (!isset($product_args['statuses']))
            $product_args['statuses'] = ["A", "P", "T", "S"];

        if (UserController::is_propeller_logged_in()) {
            if (UserController::is_contact() && SessionController::has(PROPELLER_CONTACT_COMPANY_ID)) {
                $product_args['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
                $product_args['userId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
            } else if (UserController::is_customer())
                $product_args['userId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
        }
        // else 
        //     $product_args['userId'] = intval(PROPELLER_ANONYMOUS_USER);

        $attributes_args = [];

        $images_fragment = Media::get([
            'name' => MediaImagesType::MEDIUM,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->get_catalog(
            $category_args,
            $product_args,
            $attributes_args,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump(json_encode($gql->variables));
        // var_dump($gql->query);


        return $this->query($gql, $this->type);
    }

    public function get_category($id)
    {
        $gql = $this->model->get_category(['categoryId' => $id], PROPELLER_LANG);

        return $this->query($gql, $this->type);
    }

    public function get_category_products($category_id, $language, $offset = PROPELLER_DEFAULT_OFFSET, $page = 1)
    {
        $gql = $this->model->get_category_products($category_id, $language, $offset, $page);

        return $this->query($gql, $this->type);
    }
}
