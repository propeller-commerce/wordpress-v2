<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Enum\MediaImagesType;
use Propeller\Includes\Enum\MediaType;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\ProductClass;
use Propeller\Includes\Object\Cluster;
use Propeller\Includes\Object\FilterArray;
use Propeller\Includes\Object\Product;
use Propeller\Includes\Query\Media;
use Propeller\PropellerHelper;
use Propeller\PropellerUtils;
use stdClass;
use TRP_Translate_Press;

class ProductController extends BaseController
{
    protected $type = 'product';
    static $product_slug;
    public $product;
    public $attributes = [];

    public $offset_arr = [12, 24, 48];
    public $sort_arr = [];

    public $sort_order = [];

    protected $model;
    public $pagename;
    public $slug;
    public mixed $data;
    public mixed $products;
    public mixed $filters;
    public mixed $search_categories;

    public array $shortcode_atts = [
        "type" => '',                           // custom predefined values, like "recently_viewed", etc
        "manufacturers" => '',                   // [String!]
        "supplier" => '',                       // [String!]
        "brand" => '',                          // [String!]
        "categoryId" => 0,                      // Int
        "class" => '',                          // ProductClass: product/cluster
        "tag" => '',                            // [String!]
        "page" => 1,                            // Int = 1 = 1
        "offset" => 12,                         // Int = 12 = 12
        "languagege" => '',
        "attribute" => '',                      // [TextFilterInput!]
        "statuses" => "A,P,T,S",                // [ProductStatus!] = [ "A" ] = [A]
        "hidden" => false,                      // Boolean
        "sort" => '',                           // [SortInput!]
        "ids" => 0,                             // [Int!]
        "productIds" => 0,                      // [Int!]
        "clusterIds" => 0,                      // [Int!]
    ];

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->load_model('product');

        // $this->sort_arr = [
        //     "dateChanged" => __('Date changed', 'propeller-ecommerce-v2'),
        //     "dateCreated" => __('Date created', 'propeller-ecommerce-v2'),
        //     "name" => __('Name', 'propeller-ecommerce-v2'),
        //     "price" => __('Price', 'propeller-ecommerce-v2'),
        //     "relevance" => __('Relevance', 'propeller-ecommerce-v2'),
        //     "sku" => __('SKU', 'propeller-ecommerce-v2'),
        //     "supplierCode" => __('Supplier code', 'propeller-ecommerce-v2'),
        // ];

        // $this->sort_order = [
        //     "asc" => __('Asc', 'propeller-ecommerce-v2'),
        //     "desc" => __('Desc', 'propeller-ecommerce-v2'),
        // ];

        self::$product_slug = PageController::get_slug(PageType::PRODUCT_PAGE);
    }

    /*

        Product actions

    */
    public function product_price($product, $quantity = 1, $is_bulk = false)
    {
        // require $this->load_template('partials', '/product/propeller-product-price.php');
    }

    public function cluster_price($product, $quantity = 1, $is_bulk = false)
    {
        // require $this->load_template('partials', '/product/propeller-cluster-price.php');
    }


    /* 
    
        Product filters 
    
    */
    public function product_gecommerce($product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-product-gecommerce.php');
    }

    public function product_gallery($product, $obj)
    {
        $lazy_load_images = defined('PROPELLER_LAZYLOAD_IMAGES') && PROPELLER_LAZYLOAD_IMAGES;

        require $this->load_template('partials', '/product/propeller-product-gallery.php');
    }

    public function product_name($product)
    {
        require $this->load_template('partials', '/product/propeller-product-name.php');
    }

    public function product_meta($product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-product-meta.php');
    }

    public function product_name_mobile($product)
    {
        require $this->load_template('partials', '/product/propeller-product-name-mobile.php');
    }

    public function product_meta_mobile($product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-product-meta-mobile.php');
    }

    public function product_desc_media($product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-product-desc-media.php');
    }

    public function product_price_details($product)
    {
        require $this->load_template('partials', '/product/propeller-product-price-details.php');
    }

    public function product_stock($product)
    {
        require $this->load_template('partials', '/product/propeller-product-stock.php');
    }

    public function product_short_desc($product)
    {
        require $this->load_template('partials', '/product/propeller-product-short-desc.php');
    }

    public function product_bulk_prices($product)
    {
        if (!is_null($product->bulkPrices) && is_array($product->bulkPrices) && count($product->bulkPrices)) {
            usort($product->bulkPrices, function ($first, $second) {
                return $first->discount->quantityFrom <=> $second->discount->quantityFrom;
            });

            for ($i = 0; $i < count($product->bulkPrices); $i++) {
                $product->bulkPrices[$i]->from = $product->bulkPrices[$i]->discount->quantityFrom;

                if ($i < count($product->bulkPrices) - 1)
                    $product->bulkPrices[$i]->to = $product->bulkPrices[$i + 1]->discount->quantityFrom - 1;
            }
        }

        require $this->load_template('partials', '/product/propeller-product-bulk-prices.php');
    }

    public function product_bundles($product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-product-bundles.php');
    }

    public function product_crossupsells($product, $obj, $type)
    {
        require $this->load_template('partials', '/product/propeller-product-crossupsells.php');
    }

    public function product_crossupsell_card($crossupsell, $obj)
    {
        $lazy_load_images = defined('PROPELLER_LAZYLOAD_IMAGES') && PROPELLER_LAZYLOAD_IMAGES;

        require $this->load_template('partials', '/product/propeller-product-card-crossupsell.php');
    }

    public function product_crossupsells_ajax_items($product, $obj, $type)
    {
        require $this->load_template('partials', '/product/propeller-product-crossupsells-ajax-items.php');
    }

    public function product_add_favorite($product, $obj)
    {
        $found = null;

        if (isset($product->favoriteLists) && $product->favoriteLists->itemsFound > 0) {
            foreach ($product->favoriteLists->items as $fav_list) {
                $fav_list_id = $fav_list->id;

                $found = array_filter(SessionController::get(PROPELLER_USER_FAV_LISTS)->items, function ($obj) use ($fav_list_id) {
                    return $obj->id == $fav_list_id;
                });
            }
        }

        require $this->load_template('partials', '/product/propeller-product-add-favorite.php');
    }

    public function product_add_to_basket($product)
    {
        require $this->load_template('partials', '/product/propeller-product-add-to-basket.php');
    }

    public function product_add_to_price_request($product)
    {
        require $this->load_template('partials', '/product/propeller-product-add-to-price-request.php');
    }

    public function product_description($product)
    {
        require $this->load_template('partials', '/product/propeller-product-description.php');
    }

    public function product_downloads($product)
    {
        require $this->load_template('partials', '/product/propeller-product-downloads.php');
    }

    public function product_videos($product)
    {
        require $this->load_template('partials', '/product/propeller-product-videos.php');
    }

    public function product_specifications($product)
    {
        require $this->load_template('partials', '/product/propeller-product-specifications.php');
    }

    public function product_downloads_content($product)
    {
        require $this->load_template('partials', '/product/propeller-product-downloads-content.php');
    }

    public function product_videos_content($product)
    {
        require $this->load_template('partials', '/product/propeller-product-videos-content.php');
    }

    public function product_specifications_content($product)
    {
        require $this->load_template('partials', '/product/propeller-product-specifications-content.php');
    }

    public function product_specifications_rows($product)
    {
        require $this->load_template('partials', '/product/propeller-product-specifications-rows.php');
    }

    public function product_surcharges($product)
    {
        require $this->load_template('partials', '/product/propeller-product-surcharges.php');
    }

    public function cluster_name($cluster)
    {
        require $this->load_template('partials', '/product/propeller-cluster-name.php');
    }

    public function cluster_name_mobile($cluster)
    {
        require $this->load_template('partials', '/product/propeller-cluster-name-mobile.php');
    }

    public function cluster_product_name($cluster_product)
    {
        require $this->load_template('partials', '/product/propeller-cluster-product-name.php');
    }

    public function cluster_product_name_mobile($cluster_product)
    {
        require $this->load_template('partials', '/product/propeller-cluster-product-name-mobile.php');
    }

    public function cluster_meta($cluster, $cluster_product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-meta.php');
    }

    public function cluster_meta_mobile($cluster, $cluster_product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-meta-mobile.php');
    }

    public function cluster_gallery($cluster_product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-gallery.php');
    }

    public function cluster_desc_media($cluster, $cluster_product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-desc-media.php');
    }

    public function cluster_description($cluster)
    {
        require $this->load_template('partials', '/product/propeller-cluster-description.php');
    }

    public function cluster_short_description($cluster)
    {
        require $this->load_template('partials', '/product/propeller-cluster-short-desc.php');
    }

    public function cluster_specifications($cluster_product)
    {
        require $this->load_template('partials', '/product/propeller-cluster-specifications.php');
    }

    public function cluster_downloads($cluster_product)
    {
        require $this->load_template('partials', '/product/propeller-cluster-downloads.php');
    }

    public function cluster_videos($cluster_product)
    {
        require $this->load_template('partials', '/product/propeller-cluster-videos.php');
    }

    public function cluster_price_details($cluster, $cluster_product, $obj)
    {
        $cluster->get_price();

        require $this->load_template('partials', '/product/propeller-cluster-price-details.php');
    }

    public function cluster_stock($cluster_product)
    {
        require $this->load_template('partials', '/product/propeller-cluster-stock.php');
    }

    public function cluster_gecommerce($cluster, $cluster_product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-gecommerce.php');
    }

    public function cluster_bundles($cluster_product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-bundles.php');
    }

    public function cluster_crossupsells($cluster, $obj)
    {

        require $this->load_template('partials', '/product/propeller-cluster-crossupsells.php');
    }

    public function cluster_crossupsell_card($crossupsell, $obj)
    {
        $lazy_load_images = defined('PROPELLER_LAZYLOAD_IMAGES') && PROPELLER_LAZYLOAD_IMAGES;

        require $this->load_template('partials', '/product/propeller-cluster-card-crossupsell.php');
    }

    public function cluster_crossupsells_ajax_items($crosupsells, $obj, $type)
    {
        require $this->load_template('partials', '/product/propeller-cluster-crossupsells-ajax-items.php');
    }

    public function cluster_options($cluster, $cluster_product, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-options-' . $cluster->cluster_type . '.php');
    }

    public function cluster_add_favorite($cluster, $obj)
    {
        $found = null;

        if (isset($cluster->favoriteLists) && $cluster->favoriteLists->itemsFound > 0) {
            foreach ($cluster->favoriteLists->items as $fav_list) {
                $fav_list_id = $fav_list->id;

                $found = array_filter(SessionController::get(PROPELLER_USER_FAV_LISTS)->items, function ($obj) use ($fav_list_id) {
                    return $obj->id == $fav_list_id;
                });
            }
        }

        require $this->load_template('partials', '/product/propeller-cluster-add-favorite.php');
    }

    public function cluster_config($cluster, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-config.php');
    }

    public function cluster_config_dropdown($option, $cluster, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-config-dropdown.php');
    }

    public function cluster_config_radio($option, $cluster, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-config-radio.php');
    }

    public function cluster_config_image($option, $cluster, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-config-image.php');
    }

    public function cluster_config_color($option, $cluster, $obj)
    {
        require $this->load_template('partials', '/product/propeller-cluster-config-color.php');
    }

    public function cluster_config_option($option, $cluster, $obj)
    {
        $request_data = $this->sanitize($_REQUEST);

        require $this->load_template('partials', '/product/propeller-cluster-config-option.php');
        add_action('wp_footer', function () use ($option, $cluster, $obj) {
            require $this->load_template('partials', '/product/propeller-cluster-config-option-description.php');
        });
    }


    public function product_card($product, $obj)
    {
        $lazy_load_images = defined('PROPELLER_LAZYLOAD_IMAGES') && PROPELLER_LAZYLOAD_IMAGES;

        require $this->load_template('partials', '/product/propeller-product-card.php');
    }

    public function product_card_placecholder($obj)
    {
        require $this->load_template('partials', '/product/propeller-product-card-loading-placeholder.php');
    }

    public function cluster_card($product, $obj)
    {
        $lazy_load_images = defined('PROPELLER_LAZYLOAD_IMAGES') && PROPELLER_LAZYLOAD_IMAGES;

        $price_incl_vat = $product->defaultProduct->price->net;
        $price_excl_vat = $product->defaultProduct->price->gross;

        foreach ($product->options as $option) {
            $option->required = $option->isRequired == 'Y' ? true : false;

            if ($option->required) {
                $default_option_product = $option->defaultProduct->productId;

                $found = array_filter($option->products, function ($obj) use ($default_option_product) {
                    return $obj->productId == $default_option_product;
                });

                if (count($found)) {
                    $selected_product = current($found);

                    $price_incl_vat += $selected_product->price->net;
                    $price_excl_vat += $selected_product->price->gross;
                }
            }
        }

        $product->defaultProduct->price->net = $price_incl_vat;
        $product->defaultProduct->price->gross = $price_excl_vat;

        require $this->load_template('partials', '/product/propeller-cluster-card.php');
    }

    public function crossupsells_ajax($obj, $type)
    {
        require $this->load_template('partials', '/product/propeller-crossupsells-ajax.php');
    }


    /* 
    
        Product shortcodes 
    
    */
    public function product_details($is_ajax = false)
    {
        if (null === get_query_var('slug') || empty(get_query_var('slug')))
            return;

        global $propel, $wp_query;

        $productId = null;

        if (PROPELLER_ID_IN_URL) {
            if (isset($wp_query->query_vars) && isset($wp_query->query_vars['obid']) && is_numeric($wp_query->query_vars['obid']))
                $productId = (int) $wp_query->query_vars['obid'];
        }

        $data = isset($propel['data'])
            ? $propel['data']
            : $this->get_product(get_query_var('slug'), $productId);

        $this->product = new Product($data);

        $this->product->crossupsells = $this->get_crossupsell_types($this->product);

        $this->slug = get_query_var('slug');

        // set GA4 data
        if ($this->analytics)
            $this->analytics->setData($data);

        ob_start();

        require $this->load_template('templates', '/propeller-product-details.php');

        if ($this->analytics)
            apply_filters('propel_ga4_fire_event', 'product');

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    private function get_crossupsell_types($product)
    {
        $types = new stdClass();

        $types->from = [];
        $types->to = [];

        if (isset($product->crossupsellsFrom) && $product->crossupsellsFrom->itemsFound > 0) {
            foreach ($product->crossupsellsFrom->items as $cu) {
                if ($cu->productTo && $cu->productTo->class == ProductClass::Product)
                    $types->from[$cu->type][] = $cu->productTo;
                else
                    $types->from[$cu->type][] = $cu->clusterTo;
            }
        }

        return $types;
    }

    public function cluster_details($slug = '', $cluster_id = 0, $price_only = false)
    {
        global $propel, $wp_query;

        $data = null;

        if (!wp_doing_ajax() && (null === get_query_var('slug') || empty(get_query_var('slug'))))
            return;

        if (PROPELLER_ID_IN_URL) {
            if (isset($wp_query->query_vars) && isset($wp_query->query_vars['obid']) && is_numeric($wp_query->query_vars['obid']))
                $cluster_id = (int) $wp_query->query_vars['obid'];
        }

        if (isset($propel['data']))
            $data = $propel['data'];

        if ($cluster_id > 0 && !$data) {
            $cluster_transient = PROPELLER_VIEWING_CLUSTER . '_' . $cluster_id . '_' . PROPELLER_LANG;

            if (false === ($data = CacheController::get($cluster_transient))) {
                $data = $this->get_cluster(get_query_var('slug'), $cluster_id, []);

                CacheController::set($cluster_transient, $data, 30 * MINUTE_IN_SECONDS);
            }
        }

        if ($data) {
            $this->product = new Cluster($data);

            $this->slug = $this->product->slug[0]->value;

            $fav_lists = $this->load_cluster_favorite_lists($this->product->clusterId);

            if (!is_null($fav_lists))
                $this->product->favoriteLists = $fav_lists->favoriteLists;

            $this->product->crossupsells = $this->get_crossupsell_types($this->product);

            ob_start();

            if ($price_only)
                echo wp_kses_data(apply_filters('propel_cluster_price_details', $this->product, $this->product->defaultProduct, $this));
            else {
                // set GA4 data
                if ($this->analytics)
                    $this->analytics->setData($this->product);

                require $this->load_template('templates', '/propeller-cluster-details.php');

                if ($this->analytics)
                    apply_filters('propel_ga4_fire_event', 'cluster');
            }

            $content = ob_get_contents();

            ob_end_clean();

            $this->slug = $slug;

            return $content;
        }

        return '';
    }

    private function load_cluster_favorite_lists($cluster_id)
    {
        if (UserController::is_propeller_logged_in()) {
            $type = 'cluster';

            $gql = $this->model->get_cluster_favorite_lists(
                ['cluster_id' => (int) $cluster_id, 'language' => PROPELLER_LANG],
            );

            return $this->query($gql, $type);
        }

        return null;
    }

    public function load_crossupsells($slug, $obid, $class, $crossupsell_type)
    {
        $crossupsell_type = strtolower($crossupsell_type);

        $data = $class == ProductClass::Product
            ? $this->load_product_crossupsells($slug, $obid, $crossupsell_type)
            : $this->load_cluster_crossupsells($slug, $obid, $crossupsell_type);

        if (is_array(count($data->$crossupsell_type)) && count($data->$crossupsell_type)) {
            for ($i = 0; $i < count($data->$crossupsell_type); $i++) {
                if ($data->$crossupsell_type[$i]->item->isHidden == 'Y' || !count($data->$crossupsell_type[$i]->item->slug))
                    unset($data->$crossupsell_type[$i]);
            }
        }

        ob_start();

        apply_filters('propel_' . $class . '_crossupsells_ajax_items', $data, $this, $crossupsell_type);

        $content = ob_get_clean();

        ob_end_clean();

        return $content;
    }

    private function load_product_crossupsells($slug, $id, $crossupsell_type)
    {
        $type = 'product';

        $gql = $this->model->crossupsells(
            is_numeric($id) ?
                ['productId' => $id, 'language' => PROPELLER_LANG] :
                ['slug' => $slug, 'language' => PROPELLER_LANG],
            Media::get([
                'name' => MediaImagesType::MEDIUM,
                'offset' => 1
            ], MediaType::IMAGES),
            PROPELLER_LANG,
            $crossupsell_type,
            $type
        );

        return $this->query($gql, $type);
    }

    private function load_cluster_crossupsells($slug, $id, $crossupsell_type)
    {
        $type = 'cluster';

        $gql = $this->model->crossupsells(
            is_numeric($id) ?
                ['clusterId' => $id, 'language' => PROPELLER_LANG] :
                ['slug' => $slug, 'language' => PROPELLER_LANG],
            Media::get([
                'name' => MediaImagesType::MEDIUM,
                'offset' => 1
            ], MediaType::IMAGES),
            PROPELLER_LANG,
            $crossupsell_type,
            $type
        );

        return $this->query($gql, $type);
    }

    public function load_specifications($product_id, $page = 1, $offset = 1000)
    {
        $type = 'product';

        $gql = $this->model->specifications($product_id, [
            'attributeDescription' => [
                'isPublic' => true,
            ],
            'page' => (int) $page,
            'offset' => (int) $offset
        ]);

        $product = new Product($this->query($gql, $type));

        $product->current_page = $page;
        $product->items_per_page = $offset;

        ob_start();

        $page == 1
            ? apply_filters('propel_product_specifications_content', $product)
            : apply_filters('propel_product_specifications_rows', $product);

        $content = ob_get_clean();

        ob_end_clean();

        return $content;
    }

    public function load_downloads($product_id)
    {
        $type = 'product';

        $gql = $this->model->downloads($product_id, Media::get([], MediaType::DOCUMENTS));

        $product = new Product($this->query($gql, $type));

        ob_start();

        apply_filters('propel_product_downloads_content', $product);

        $content = ob_get_clean();

        ob_end_clean();

        return $content;
    }

    public function load_videos($product_id)
    {
        $type = 'product';

        $gql = $this->model->videos($product_id, Media::get([], MediaType::VIDEOS));

        $product = new Product($this->query($gql, $type));

        ob_start();

        apply_filters('propel_product_videos_content', $product);

        $content = ob_get_clean();

        ob_end_clean();

        return $content;
    }

    public function search_products()
    {

        static $search_id = null;
        if (is_null($search_id)) {
            $search_id = 0;
        }
        $search_id++;

        ob_start();

        require $this->load_template('partials', '/other/propeller-product-search.php');

        return ob_get_clean();
    }

    public function search($applied_filters = [], $is_ajax = false)
    {
        global $propel;

        if (!$applied_filters || !sizeof($applied_filters))
            $applied_filters = PropellerUtils::sanitize($_REQUEST);

        $applied_filters['language'] = PROPELLER_LANG;

        $filters_applied = $this->process_filters($applied_filters);
        $qry_params = $this->build_search_arguments($applied_filters);

        $qry_params = array_merge($filters_applied, $qry_params);

        $term = isset($applied_filters['term']) ? $applied_filters['term'] : get_query_var('term');
        $term = wp_unslash($term);
        $term = urldecode($term);

        if (!empty($term))
            $qry_params['term'] = $term;

        if (!isset($qry_params['language']))
            $qry_params['language'] = PROPELLER_LANG;

        $sort_params = isset($applied_filters['sortInputs']) && !empty($applied_filters['sortInputs'])
            ? explode(',', $applied_filters['sortInputs'])
            : 'RELEVANCE,' . PROPELLER_DEFAULT_SORT_DIRECTION;

        $style = isset($applied_filters['view']) ? $applied_filters['view'] : 'blocks';

        $this->data = isset($propel['data'])
            ? $propel['data']
            : $this->get_products($qry_params);

        $this->products = [];

        foreach ($this->data->items as $product) {
            if (!count($product->slug) || ($product->class == ProductClass::Product && $product->status == 'N')) {
                $this->data->itemsFound--;
                continue;
            }

            if ($product->class == ProductClass::Product)
                $this->products[] = new Product($product);
            if ($product->class == ProductClass::Cluster) {
                // $this->purge_cluster($product);

                if (is_null($product->defaultProduct))
                    continue;

                $this->products[] = new Cluster($product, false);
            }
        }

        $this->attributes = [];
        if ($this->data->filters)
            $this->attributes = new FilterArray($this->data->filters);

        $this->filters = new FilterController($this->attributes, [$this->data->minPrice, $this->data->maxPrice]);
        $this->filters->set_slug($term);
        $this->filters->set_action("do_search");
        $this->filters->set_prop('term');
        $this->filters->set_liststyle($style);

        $this->search_categories = [];
        foreach ($this->products as $product) {
            if (isset($product->category) && is_object($product->category)) {
                if (!isset($this->search_categories[$product->category->categoryId])) {
                    $cat = new stdClass();
                    $cat->id = $product->category->categoryId;
                    $cat->items = 1;

                    $this->search_categories[$product->category->categoryId] = $cat;
                } else {
                    $cat = $this->search_categories[$product->category->categoryId];
                    $cat->items++;
                }
            }
        }

        $this->pagename = PageController::get_slug(PageType::SEARCH_PAGE);

        $paging_data = $this->data;
        $do_action = "do_search";
        $prop_name = "term";
        $prop_value = $term;

        ob_start();

        // set GA4 data
        if ($this->analytics)
            $this->analytics->setData($this->data);

        if ($is_ajax) {
            $response = new stdClass();

            apply_filters('propel_category_grid', $this, $this->products, $paging_data, $sort_params, $prop_name, $prop_value, $do_action);
            $response->content = ob_get_clean();

            ob_start();
            apply_filters('propel_category_filters', $this->filters);
            $filters_content = ob_get_clean();

            ob_start();
            apply_filters('propel_category_menu', $this->data);
            $categories_content = ob_get_clean();

            $cat_ga4 = '';
            if ($this->analytics) {
                ob_start();

                apply_filters('propel_ga4_fire_event', 'view_search');
                apply_filters('propel_ga4_print_data', 'view_search');

                $cat_ga4 = ob_get_clean();
            }

            $response->filters = $filters_content;
            $response->categories = $categories_content . $cat_ga4;

            return $response;
        } else {
            $sort = $sort_params;

            require $this->load_template('templates', '/propeller-search-results.php');

            if ($this->analytics) {
                $this->analytics->setData((object) ['term' => $term]);
                apply_filters('propel_ga4_fire_event', 'search');

                $this->analytics->setData($this->data);
                apply_filters('propel_ga4_fire_event', 'view_search');
                apply_filters('propel_ga4_print_data', 'view_search');
            }
        }

        return ob_get_clean();
    }

    public function global_product_search($applied_filters = [], $is_ajax = false)
    {
        if (!$applied_filters || !sizeof($applied_filters))
            $applied_filters = PropellerUtils::sanitize($_REQUEST);

        $term = isset($applied_filters['term']) ? $applied_filters['term'] : get_query_var('term');
        $term = wp_unslash($term);

        // $filters_applied = $this->process_filters($applied_filters);
        $qry_params = $this->build_search_arguments($applied_filters);

        $term = str_replace('"', '\"', urldecode($term));
        $qry_params['term'] = $term;

        $qry_params['offset'] = PROPELLER_SEARCH_SUGGESTIONS;
        $qry_params['page'] = 1;

        $results = $this->global_search_products($qry_params);

        return $results;
    }

    public function brand($applied_filters = [], $is_ajax = false)
    {
        global $propel;

        if (!$applied_filters || !sizeof($applied_filters))
            $applied_filters = PropellerUtils::sanitize($_REQUEST);

        $applied_filters['language'] = PROPELLER_LANG;

        $filters_applied = $this->process_filters($applied_filters);
        $qry_params = $this->build_search_arguments($applied_filters);

        $qry_params = array_merge($filters_applied, $qry_params);

        $term = isset($applied_filters['manufacturers']) ? $applied_filters['manufacturers'] : get_query_var('manufacturers');
        $term = wp_unslash($term);
        $term = urldecode($term);

        if (!empty($term))
            $qry_params['manufacturers'] = [$term];

        if (!isset($qry_params['language']))
            $qry_params['language'] = PROPELLER_LANG;

        $sort_params = isset($applied_filters['sortInputs']) && !empty($applied_filters['sortInputs'])
            ? explode(',', $applied_filters['sortInputs'])
            : PROPELLER_DEFAULT_SORT_FIELD . ',' . PROPELLER_DEFAULT_SORT_DIRECTION;

        $style = isset($applied_filters['view']) ? $applied_filters['view'] : 'blocks';

        $this->data = isset($propel['data'])
            ? $propel['data']
            : $this->get_products($qry_params);

        $this->products = [];

        foreach ($this->data->items as $product) {
            if (!count($product->slug) || ($product->class == ProductClass::Product && $product->status == 'N')) {
                $this->data->itemsFound--;
                continue;
            }

            if ($product->class == ProductClass::Product)
                $this->products[] = new Product($product);
            if ($product->class == ProductClass::Cluster)
                $this->products[] = new Cluster($product, false);
        }

        $this->attributes = [];
        if ($this->data->filters)
            $this->attributes = new FilterArray($this->data->filters);

        $this->filters = new FilterController($this->attributes, [$this->data->minPrice, $this->data->maxPrice]);
        $this->filters->set_slug($term);
        $this->filters->set_prop('manufacturers');
        $this->filters->set_action("do_brand");
        $this->filters->set_liststyle($style);

        $this->search_categories = [];
        foreach ($this->products as $product) {
            if (!isset($this->search_categories[$product->category->categoryId])) {
                $cat = new stdClass();
                $cat->id = $product->category->categoryId;
                $cat->items = 1;

                $this->search_categories[$product->category->categoryId] = $cat;
            } else {
                $cat = $this->search_categories[$product->category->categoryId];
                $cat->items++;
            }
        }

        $this->pagename = PageController::get_slug(PageType::BRAND_PAGE);

        $paging_data = $this->data;
        $do_action = "do_brand";
        $prop_name = "manufacturers";
        $prop_value = $term;

        ob_start();

        // set GA4 data
        if ($this->analytics)
            $this->analytics->setData($this->data);

        if ($is_ajax) {
            $response = new stdClass();

            apply_filters('propel_category_grid', $this, $this->products, $paging_data, $sort_params, $prop_name, $prop_value, $do_action);
            $response->content = ob_get_clean();

            $cat_ga4 = '';
            if ($this->analytics) {
                ob_start();

                apply_filters('propel_ga4_fire_event', 'view_brand');
                apply_filters('propel_ga4_print_data', 'view_brand');

                $cat_ga4 = ob_get_clean();
            }

            ob_start();
            apply_filters('propel_category_filters', $this->filters);
            $filters_content = ob_get_clean();

            $filters_content .= $cat_ga4;

            $response->filters = $filters_content;

            return $response;
        } else {
            require $this->load_template('templates', '/propeller-brand-listing.php');

            if ($this->analytics) {
                $this->analytics->setData((object) ['manufacturer' => get_query_var('manufacturer')]);
                apply_filters('propel_ga4_fire_event', 'brand');

                $this->analytics->setData($this->data);
                apply_filters('propel_ga4_fire_event', 'view_brand');
                apply_filters('propel_ga4_print_data', 'view_brand');
            }
        }

        return ob_get_clean();
    }

    public function brand_listing_content($applied_filters = [], $is_ajax = false)
    {
        ob_start();

        if (!$applied_filters || !sizeof($applied_filters)) {
            $applied_filters = PropellerUtils::sanitize($_REQUEST);
        }

        $filters_applied = $this->process_filters($applied_filters);
        $qry_params = $this->build_search_arguments($filters_applied);

        $term = isset($applied_filters['manufacturers']) ? $applied_filters['manufacturers'] : get_query_var('manufacturers');
        $term = wp_unslash($term);

        if (!empty($term))
            $qry_params['manufacturers'] = [$term];

        require $this->load_template('partials', '/other/propeller-brand-listing-content.php');

        return ob_get_clean();
    }

    public function product_slider($atts = [], $content = null)
    {
        $this->shortcode_atts['language'] = PROPELLER_LANG;

        $arguments = [];

        foreach ($this->shortcode_atts as $key => $val) {
            if (isset($atts[strtolower($key)]) && !empty($atts[strtolower($key)]))
                $arguments[$key] = $atts[strtolower($key)];
            else
                $atts[$key] = $val;
        }

        // var_dump($atts);

        $data_attrs = [];

        foreach ($atts as $key => $value) {
            if (!empty($value))
                $data_attrs[$key] = $value;
        }

        $slider_id = PropellerHelper::random_string();

        $slider_template = $this->load_template('partials', '/other/propeller-product-slider.php');

        $do_search = true;

        if (isset($arguments['type']) && $arguments['type'] != '') {
            switch ($arguments['type']) {
                case 'recently_viewed':
                    $slider_template = $this->load_template('partials', '/other/propeller-recent-slider.php');

                    $do_search = false;

                    add_action('wp_enqueue_scripts', function () {
                        wp_enqueue_style('slick_theme_css', $this->assets_dir . '/css/lib/slick-theme.css', array(), null, 'all');
                        wp_enqueue_style('slick_css', $this->assets_dir . '/css/lib/slick.css', array(), null, 'all');
                    });

                    break;
                default:
                    break;
            }
        }

        $no_results = false;
        $products = [];

        if (isset($arguments['productIds']) && !empty($arguments['productIds']) && is_string($arguments['productIds']) && str_contains($arguments['productIds'], ','))
            $arguments['productIds'] = explode(',', $arguments['productIds']);

        if (isset($arguments['clusterIds']) && !empty($arguments['clusterIds']) && is_string($arguments['clusterIds']) && str_contains($arguments['clusterIds'], ','))
            $arguments['clusterIds'] = explode(',', $arguments['clusterIds']);

        if ($do_search) {
            $qry_params = $this->build_search_arguments($arguments);

            $products_data = $this->get_slider_products($qry_params);

            foreach ($products_data->items as $product) {
                if (!count($product->slug))
                    continue;

                if ($product->class == ProductClass::Product)
                    $products[] = new Product($product);
                if ($product->class == ProductClass::Cluster)
                    $products[] = new Cluster($product, false);
            }
        } else
            $no_results = true;

        ob_start();

        require $slider_template;

        return ob_get_clean();
    }

    public function load_slider_products($arguments)
    {
        $products = [];

        $qry_params = $this->build_search_arguments($arguments);

        $products_data = $this->get_slider_products($qry_params);

        foreach ($products_data->items as $product) {
            if (!count($product->slug) || ($product->class == ProductClass::Product && $product->status == 'N'))
                continue;

            if ($product->class == ProductClass::Product)
                $products[] = new Product($product);
            if ($product->class == ProductClass::Cluster)
                $products[] = new Cluster($product, false);
        }

        ob_start();

        require $this->load_template('partials', '/other/propeller-product-slider-items.php');

        $content = ob_get_clean();

        return $content;
    }

    public function get_recently_viewed_products($arguments)
    {
        $products = [];

        $qry_params = $this->build_search_arguments($arguments);

        $products_data = $this->get_slider_products($qry_params);

        foreach ($products_data->items as $product) {
            if (!count($product->slug) || ($product->class == ProductClass::Product && $product->status == 'N'))
                continue;

            if ($product->class == ProductClass::Product)
                $products[] = new Product($product);
            if ($product->class == ProductClass::Cluster)
                $products[] = new Cluster($product, false);
        }

        ob_start();

        require $this->load_template('partials', '/other/propeller-recent-viewed-products.php');

        $content = ob_get_clean();

        return $content;
    }

    /**
     * Temporary function, checks if /product/$slug is product or cluster
     */
    private function check_product($slug, $productId = null)
    {
        $class = null;

        $product_gql = $this->model->check_product(
            $productId
                ? ['productId' => (int) $productId, 'language' => PROPELLER_LANG]
                : ['slug' => $slug, 'language' => PROPELLER_LANG]
        );

        $product_check_response = $this->query($product_gql, 'product', false, false);

        if (isset($product_check_response->class))
            $class = $product_check_response->class;
        else {
            $cluster_gql = $this->model->check_cluster(
                $productId
                    ? ['clusterId' => (int) $productId, 'language' => PROPELLER_LANG]
                    : ['slug' => $slug, 'language' => PROPELLER_LANG]
            );

            $cluster_check_response = $this->query($cluster_gql, 'cluster', false, false);

            if (isset($cluster_check_response->class))
                $class = $cluster_check_response->class;
        }

        return $class;
    }

    private function check_product_language($slug, $productId = null, $type = 'product')
    {
        $language = PROPELLER_LANG;

        $gql = $this->model->check_product_language($slug, $productId, $type, $language);

        $response = $this->query($gql, $type);

        $return = new stdClass();

        if (is_object($response) && count($response->slugs)) {
            $found = array_filter($response->slugs, function ($obj) use ($language) {
                return strtolower($obj->language) == strtolower($language);
            });

            if (!count($found)) {
                $return->exists = false;


                $default_lang = PROPELLER_DEFAULT_LOCALE;

                if (strpos($default_lang, '_'))
                    $default_lang = explode('_', $default_lang)[1];

                $default_found = array_filter($response->slugs, function ($obj) use ($default_lang) {
                    return strtolower($obj->language) == strtolower($default_lang);
                });

                if (count($default_found))
                    $return->languages = [current($default_found)];
                else
                    $return->languages = $response->slugs;
            } else
                $return->exists = true;
        } else {
            $return->exists = false;
        }

        return $return;
    }

    public function get_product_default_lang_url($language, $original_url)
    {
        if (class_exists('TRP_Translate_Press')) {
            $trp = TRP_Translate_Press::get_trp_instance();
            $url_converter = $trp->get_component('url_converter');

            // It's recommended to keep third parameter of the get_url_for_language() an empty string.
            $url = $url_converter->get_url_for_language($language, $original_url, '');

            return $url;
        }

        return null;
    }

    public function get_product($slug, $productId = null, $args = [])
    {
        $product_data = $this->check_product_language($slug, $productId, 'product');

        if ($product_data->exists) {
            $gql = $this->model->get_product(
                $productId
                    ? ['productId' => (int) $productId, 'language' => PROPELLER_LANG]
                    : ['slug' => $slug, 'language' => PROPELLER_LANG],
                Media::get([
                    'name' => MediaImagesType::LARGE
                ], MediaType::IMAGES),
                [],
                PROPELLER_LANG
            );

            // var_dump($gql->query);
            // var_dump(json_encode($gql->variables));

            $product_data = $this->query($gql, $this->type);

            return $product_data;
        } else {
            return $product_data;
        }
    }

    public function get_cluster($slug, $clusterId = null, $args = [])
    {
        $type = 'cluster';
        $data = null;
        $cluster_data = $this->check_product_language($slug, $clusterId, 'cluster');

        if ($clusterId) {
            $cluster_transient = PROPELLER_VIEWING_CLUSTER . '_' . $clusterId . '_' . PROPELLER_LANG;

            $clusterId = (int) $clusterId;

            if (false !== ($data = CacheController::get($cluster_transient))) {
                $data->exists = $cluster_data->exists;

                $fav_lists = $this->load_cluster_favorite_lists($clusterId);

                if (!is_null($fav_lists))
                    $data->favoriteLists = $fav_lists->favoriteLists;

                return $data;
            }
        }

        if ($cluster_data->exists) {
            $attrs_gql = $this->model->get_cluster_attributes($clusterId
                ? ['clusterId' => (int) $clusterId, 'language' => PROPELLER_LANG]
                : ['slug' => $slug, 'language' => PROPELLER_LANG], PROPELLER_LANG);

            $attr_data = $this->query($attrs_gql, $type);

            $attr_names = [];
            $attr_offset = 12;

            if (isset($attr_data->config) && isset($attr_data->config->settings) && count($attr_data->config->settings)) {
                foreach ($attr_data->config->settings as $dd)
                    $attr_names[] = $dd->name;
            }

            $attr_offset = count($attr_names);

            $gql = $this->model->get_cluster(
                $clusterId
                    ? ['clusterId' => (int) $clusterId, 'language' => PROPELLER_LANG]
                    : ['slug' => $slug, 'language' => PROPELLER_LANG],
                count($attr_names)
                    ? [
                        'offset' => $attr_offset,
                        'attributeDescription' => [
                            'names' => $attr_names
                        ]
                    ]
                    : [],
                Media::get([
                    'name' => MediaImagesType::LARGE
                ], MediaType::IMAGES),
                PROPELLER_LANG
            );

            // var_dump($gql->query);
            // var_dump(json_encode($gql->variables));

            // die;

            $data = $this->query($gql, $type);

            $data->exists = $cluster_data->exists;

            $this->preserve_cluster($clusterId, $data);

            return $data;
        } else {
            return $cluster_data;
        }
    }

    public function get_products($qry_params, $is_ajax = false)
    {
        $type = 'category';

        if ($is_ajax)
            $qry_params = $this->build_search_arguments($qry_params);

        if (!isset($qry_params['language']))
            $qry_params['language'] = PROPELLER_LANG;
        if (!isset($qry_params['page']))
            $qry_params['page'] = 1;
        if (!isset($qry_params['offset']))
            $qry_params['offset'] = intval(PROPELLER_DEFAULT_OFFSET);
        if (!isset($qry_params['statuses']))
            $qry_params['statuses'] = ["A", "P", "T", "S"];
        if (!isset($qry_params['hidden']))
            $qry_params['hidden'] = false;

        if (UserController::is_propeller_logged_in()) {
            if (UserController::is_contact() && SessionController::has(PROPELLER_CONTACT_COMPANY_ID)) {
                $qry_params['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
                $qry_params['userId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
            } else if (UserController::is_customer())
                $qry_params['userId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
        }

        $qry_params['searchFields'] = [
            [
                'fieldNames' => [
                    "NAME",
                    "KEYWORDS",
                    "SKU",
                    "CUSTOM_KEYWORDS"
                ],
                'boost' => 5
            ], 
            [
                'fieldNames' => [
                    "DESCRIPTION",
                    "MANUFACTURER",
                    "MANUFACTURER_CODE",
                    "EAN_CODE",
                    "BAR_CODE",
                    "CLUSTER_ID",
                    "CUSTOM_KEYWORDS",
                    "PRODUCT_ID",
                    "SHORT_DESCRIPTION",
                    "SUPPLIER",
                    "SUPPLIER_CODE"
                ],
                'boost' => 1
            ]
        ];

        $images_fragment = Media::get([
            'name' => MediaImagesType::MEDIUM,
            'offset' => 1
        ], MediaType::IMAGES);

        $filters_args = [
            'isSearchable' => true
        ];

        $gql = $this->model->get_products(
            $qry_params,
            $filters_args,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($gql);
        // var_dump(json_encode($gql->variables));

        return $this->query($gql, $type)->products;
    }

    public function get_slider_products($qry_params, $is_ajax = false)
    {
        $type = 'products';

        if (!isset($qry_params['language']))
            $qry_params['language'] = PROPELLER_LANG;
        if (!isset($qry_params['statuses']))
            $qry_params['statuses'] = ["A", "P", "T", "S"];
        if (!isset($qry_params['hidden']))
            $qry_params['hidden'] = false;

        if (UserController::is_propeller_logged_in()) {
            if (UserController::is_contact() && SessionController::has(PROPELLER_CONTACT_COMPANY_ID)) {
                $qry_params['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
                $qry_params['userId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
            } else if (UserController::is_customer())
                $qry_params['userId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
        }
        // else 
        //     $qry_params['userId'] = intval(PROPELLER_ANONYMOUS_USER);

        $images_fragment = Media::get([
            'name' => MediaImagesType::MEDIUM,
            'offset' => 1
        ], MediaType::IMAGES);

        $gql = $this->model->get_slider_products(
            $qry_params,
            $images_fragment,
            PROPELLER_LANG
        );

        return $this->query($gql, $type);
    }

    public function global_search_products($qry_params, $is_ajax = false)
    {
        $type = 'category';

        $qry_params = $this->build_search_arguments($qry_params);

        if (!isset($qry_params['language']))
            $qry_params['language'] = PROPELLER_LANG;
        if (!isset($qry_params['statuses']))
            $qry_params['statuses'] = ["A", "P", "T", "S"];
        if (!isset($qry_params['hidden']))
            $qry_params['hidden'] = false;
        if (!isset($qry_params['sortInputs']))
            $qry_params['sortInputs'] = [
                'field' => 'RELEVANCE',
                'order' => PROPELLER_DEFAULT_SORT_DIRECTION
            ];

        if (UserController::is_propeller_logged_in()) {
            if (UserController::is_contact() && SessionController::has(PROPELLER_CONTACT_COMPANY_ID)) {
                $qry_params['companyId'] = SessionController::get(PROPELLER_CONTACT_COMPANY_ID);
                $qry_params['userId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
            } else if (UserController::is_customer())
                $qry_params['userId'] = SessionController::get(PROPELLER_USER_DATA)->userId;
        }
        
        $qry_params['searchFields'] = [
            [
                'fieldNames' => [
                    "NAME",
                    "KEYWORDS",
                    "SKU",
                    "CUSTOM_KEYWORDS"
                ],
                'boost' => 5
            ], 
            [
                'fieldNames' => [
                    "DESCRIPTION",
                    "MANUFACTURER",
                    "MANUFACTURER_CODE",
                    "EAN_CODE",
                    "BAR_CODE",
                    "CLUSTER_ID",
                    "CUSTOM_KEYWORDS",
                    "PRODUCT_ID",
                    "SHORT_DESCRIPTION",
                    "SUPPLIER",
                    "SUPPLIER_CODE"
                ],
                'boost' => 1
            ]
        ];

        $images_fragment = Media::get([
            'name' => MediaImagesType::SMALL,
            'offset' => 1
        ], MediaType::IMAGES);

        $searchGql = $this->model->global_search_products(
            $qry_params,
            $images_fragment,
            PROPELLER_LANG
        );

        // var_dump($searchGql->query);
        // var_dump(json_encode($searchGql->variables));

        $response = $this->query($searchGql, $type)->products;

        return $response;
    }

    public function preserve_recently_viewed($class_with_id)
    {
        $cookie = $this->get_cookie(PROPELLER_RECENT_PRODS_COOKIE);
        $products = [];

        if ($cookie)
            $products = explode(',', $cookie);

        if (!in_array($class_with_id, $products))
            array_unshift($products, $class_with_id);

        if (sizeof($products) > 12)
            array_pop($products);

        $this->set_cookie(PROPELLER_RECENT_PRODS_COOKIE, implode(',', $products));
    }

    public function preserve_cluster($clusterId, $data)
    {
        $cluster_transient = PROPELLER_VIEWING_CLUSTER . '_' . $clusterId . '_' . PROPELLER_LANG;

        if (false === CacheController::get($cluster_transient))
            CacheController::set($cluster_transient, $data, 30 * MINUTE_IN_SECONDS);
    }

    public function get_product_codes($product_ids, $is_quickorder = false)
    {
        $gql = $this->model->get_product_codes($product_ids);

        return $this->query($gql, 'products');
    }
}

/*

$total_products = count($product->products);
        $unavailable_products = 0;
        $not_orderable_products = 0;

        foreach ($product->products as $cluster_product) {
            if ($cluster_product->status == ProductStatus::N)
                $unavailable_products++;
            if ($cluster_product->orderable == 'N')
                $not_orderable_products++;
        }

        var_dump(count($product->products));
        var_dump($total_products);
        var_dump($unavailable_products);
        var_dump($not_orderable_products);

        $cluster_unavailable = ($unavailable_products + $not_orderable_products) >= $total_products;

*/