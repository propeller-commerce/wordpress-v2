<?php

declare(strict_types=1);

namespace Propeller\Includes\Extra\Ga;

if (! defined('ABSPATH')) exit;

use Propeller\Includes\Enum\ProductClass;
use Propeller\Includes\Object\Category;
use Propeller\Includes\Object\Cluster;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

class PropellerGa4
{
    private static ?self $instance = null;

    protected mixed $data = [];
    protected array $products = [];
    protected mixed $category = null;
    protected mixed $order = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public function __construct()
    {
        if (!defined('PROPELLER_USE_GA4'))
            $this->getGa4Data();

        if (defined('PROPELLER_USE_GA4') && PROPELLER_USE_GA4) {
            add_action('wp_head', [$this, 'renderHeadScripts'], 1);
            add_action('wp_body_open', [$this, 'renderBodyScripts'], 1);
        }
    }

    public function bootstrap(): void
    {
        self::instance();
    }

    /**
     * Get GA4 configuration data from database
     */
    public function getGa4Data(): ?object
    {
        global $table_prefix, $wpdb;

        $behavior_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i", $table_prefix . PROPELLER_BEHAVIOR_TABLE));

        $ga4_data = null;

        if ($behavior_result && $behavior_result->use_ga4 && $behavior_result->ga4_tracking && !empty($behavior_result->ga4_key)) {
            if (!defined('PROPELLER_USE_GA4'))
                define('PROPELLER_USE_GA4', true);

            if (!defined('PROPELLER_GA4_TRACKING'))
                define('PROPELLER_GA4_TRACKING', $behavior_result->ga4_tracking == 1 ? true : false);

            $ga4_data = (object) [
                'ga4_key' => $behavior_result->ga4_key,
                'gtm_key' => !empty($behavior_result->gtm_key) ? $behavior_result->gtm_key : null
            ];
        }

        return $ga4_data;
    }

    /**
     * Check if GA4 is enabled
     */
    public function isGa4Enabled(): bool
    {
        return $this->getGa4Data() !== null;
    }

    /**
     * Check if GTM is enabled
     */
    public function isGtmEnabled(): bool
    {
        $ga4_data = $this->getGa4Data();
        return $ga4_data !== null && !empty($ga4_data->gtm_key);
    }

    /**
     * Get GA4 key
     */
    public function getGa4Key(): ?string
    {
        $ga4_data = $this->getGa4Data();
        return $ga4_data ? (string)$ga4_data->ga4_key : null;
    }

    /**
     * Get GTM key
     */
    public function getGtmKey(): ?string
    {
        $ga4_data = $this->getGa4Data();
        return ($ga4_data && !empty($ga4_data->gtm_key)) ? (string)$ga4_data->gtm_key : null;
    }

    /**
     * Render GTM head scripts
     */
    public function renderHeadScripts(): void
    {
        if ($this->isGtmEnabled()) {    // GTM head script
            $gtm_key = esc_js((string)$this->getGtmKey());

?><!-- Google Tag Manager HEAD -->
            <script>
                (function(w, d, s, l, i) {
                    w[l] = w[l] || [];
                    w[l].push({
                        'gtm.start': new Date().getTime(),
                        event: 'gtm.js'
                    });
                    var f = d.getElementsByTagName(s)[0],
                        j = d.createElement(s),
                        dl = l != 'dataLayer' ? '&l=' + l : '';
                    j.async = true;
                    j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                    f.parentNode.insertBefore(j, f);
                })(window, document, 'script', 'dataLayer', '<?php echo esc_js($gtm_key); ?>');
            </script>
            <!-- End Google Tag Manager --><?php
                                        }
                                    }

                                    /**
                                     * Render GA4 & GTM body scripts
                                     */
                                    public function renderBodyScripts(): void
                                    {   // GTM body noscript
                                        if ($this->isGtmEnabled()) {
                                            $gtm_key = esc_js((string)$this->getGtmKey());

                                            apply_filters('propel_gtm_noscript', $gtm_key);
                                        }

                                        if ($this->isGa4Enabled()) {    // GA4 body script
                                            $ga4_key = esc_js((string)$this->getGa4Key());

                                            $src = "https://www.googletagmanager.com/gtag/js?id=" . esc_js($ga4_key);

                                            // 1. Enqueue the external Google script
                                            wp_enqueue_script('google-gtag', $src, array(), null, false);

                                            /* <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js( $ga4_key ); ?>"></script> */
                                            apply_filters('propel_gtm_init', $ga4_key);
                                        }
                                    }

                                    public function setData(mixed $data): void
                                    {
                                        $this->data = $data;
                                    }

                                    public function getData(): mixed
                                    {
                                        return $this->data;
                                    }

                                    public function printJson(string $_data_type): object
                                    {
                                        return (object) [
                                            'category' => $this->category,
                                            'products' => $this->products
                                        ];
                                    }

                                    public function toJson(string $data_type): mixed
                                    {
                                        if (!PROPELLER_USE_GA4 || !PROPELLER_GA4_TRACKING)
                                            return '{}';

                                        if ($this->getData()) {
                                            switch ($data_type) {
                                                case 'product':
                                                    // parse product
                                                    if (isset($this->getData()->productId)) {
                                                        $this->products = $this->mapProduct($this->getData());

                                                        $product_data = [
                                                            'currency' => PropellerHelper::currency_abbr(),
                                                            'value' => isset($this->products[0]->price) ? $this->products[0]->price : 0,
                                                            'items' => $this->products
                                                        ];

                                                        if (isset($this->products[0]->price))
                                                            $product_data['value'] = (float) $this->products[0]->price;

                                                        return (object) [
                                                            'event' => 'view_item',
                                                            'ecommerce' => (object) $product_data
                                                        ];
                                                    }
                                                    break;
                                                case 'cluster':
                                                    // parse product
                                                    if (isset($this->getData()->clusterId)) {
                                                        $this->products = $this->mapCluster($this->getData());

                                                        $product_data = [
                                                            'currency' => PropellerHelper::currency_abbr(),
                                                            'value' => isset($this->products[0]->price) ? $this->products[0]->price : 0,
                                                            'items' => $this->products
                                                        ];

                                                        if (isset($this->products[0]->price))
                                                            $product_data['value'] = (float) $this->products[0]->price;

                                                        return (object) [
                                                            'event' => 'view_item',
                                                            'ecommerce' => (object) $product_data
                                                        ];
                                                    }
                                                    break;
                                                case 'category':
                                                    // parse category
                                                    if (isset($this->getData()->categoryId) && isset($this->getData()->products->items)) {
                                                        $this->category = $this->mapCategory($this->getData());
                                                        $this->products = $this->mapProducts($this->getData()->products);

                                                        return (object) [
                                                            'event' => 'view_item_list',
                                                            'ecommerce' => (object) [
                                                                'items' => $this->products,
                                                                'item_list_name' => $this->category->name,
                                                                'item_list_id' => $this->category->id
                                                            ]
                                                        ];
                                                    }

                                                    break;
                                                case 'view_search':
                                                    // parse search results
                                                    if (isset($this->getData()->items)) {
                                                        $term = wp_unslash(get_query_var('term'));
                                                        // $this->category = $this->mapCategory($this->getData());
                                                        $this->products = $this->mapProducts($this->getData());

                                                        return (object) [
                                                            'event' => 'view_item_list',
                                                            'ecommerce' => (object) [
                                                                'items' => $this->products,
                                                                'item_list_name' => 'Search Results: ' . $term,
                                                                'item_list_id' => 'search_results'
                                                            ]
                                                        ];
                                                    }

                                                    break;
                                                case 'view_brand':
                                                    // parse search results
                                                    if (isset($this->getData()->items)) {
                                                        $manufacturer = get_query_var('manufacturer');
                                                        // $this->category = $this->mapCategory($this->getData());
                                                        $this->products = $this->mapProducts($this->getData());

                                                        return (object) [
                                                            'event' => 'view_item_list',
                                                            'ecommerce' => (object) [
                                                                'items' => $this->products,
                                                                'item_list_name' => 'Brand Results: ' . $manufacturer,
                                                                'item_list_id' => 'brand_results'
                                                            ]
                                                        ];
                                                    }

                                                    break;
                                                case 'cart':
                                                    // parse cart
                                                    if (isset($this->getData()->items) && is_array($this->getData()->items) && !empty($this->getData()->items)) {
                                                        $this->products = $this->mapCartItems($this->getData()->items);

                                                        return (object) [
                                                            'event' => 'view_cart',
                                                            'ecommerce' => (object) [
                                                                'currency' => PropellerHelper::currency_abbr(),
                                                                'value' => $this->getData()->total->totalGross,
                                                                'items' => $this->products
                                                            ]
                                                        ];
                                                    }
                                                    break;
                                                case 'add_to_cart':
                                                    // parse add to cart
                                                    if (isset($this->getData()->added_item) && isset($this->getData()->added_item->product)) {
                                                        $added_product = $this->mapCartItem([$this->getData()->added_item]);

                                                        if (!empty($added_product)) {
                                                            $cart_total = 0;

                                                            $index = 0;
                                                            foreach ($added_product as $product) {
                                                                if (isset($this->getData()->added_item->added_quantity))
                                                                    $cart_total += $product->price * ($this->getData()->added_item->added_quantity ?? 1);

                                                                if (isset($this->getData()->added_item->updated_quantity))
                                                                    $cart_total += $product->price * ($this->getData()->added_item->updated_quantity ?? 1);

                                                                $index++;
                                                            }

                                                            return (object) [
                                                                'event' => 'add_to_cart',
                                                                'ecommerce' => (object) [
                                                                    'currency' => PropellerHelper::currency_abbr(),
                                                                    'value' => $cart_total,
                                                                    'items' => $added_product
                                                                ]
                                                            ];
                                                        }
                                                    }
                                                case 'remove_from_cart':
                                                    // parse remove from cart
                                                    if (isset($this->getData()->removed_item) && isset($this->getData()->removed_item->product)) {
                                                        $removed_product = $this->mapCartItem([$this->getData()->removed_item]);

                                                        if (!empty($removed_product)) {
                                                            $cart_total = 0;
                                                            foreach ($removed_product as $product) {
                                                                if (isset($product->price))
                                                                    $cart_total += $product->price * ($product->updated_quantity ?? $product->quantity);
                                                            }

                                                            return (object) [
                                                                'event' => 'remove_from_cart',
                                                                'ecommerce' => (object) [
                                                                    'currency' => PropellerHelper::currency_abbr(),
                                                                    'value' => $cart_total,
                                                                    'items' => $removed_product
                                                                ]
                                                            ];
                                                        }
                                                    }
                                                    break;
                                                case 'search':
                                                    // parse search
                                                    if (isset($this->getData()->term)) {
                                                        return (object) [
                                                            'event' => 'search',
                                                            'ecommerce' => (object) [
                                                                'search_term' => $this->getData()->term
                                                            ]
                                                        ];
                                                    }
                                                    break;
                                                case 'brand':
                                                    // parse brand
                                                    if (isset($this->getData()->manufacturer)) {
                                                        return (object) [
                                                            'event' => 'search',
                                                            'ecommerce' => (object) [
                                                                'search_term' => $this->getData()->manufacturer
                                                            ]
                                                        ];
                                                    }
                                                    break;
                                                case 'begin_checkout':
                                                    // parse begin checkout
                                                    if (isset($this->getData()->items) && is_array($this->getData()->items) && !empty($this->getData()->items)) {
                                                        $this->products = $this->mapCartItems($this->getData()->items);

                                                        return (object) [
                                                            'event' => 'begin_checkout',
                                                            'ecommerce' => (object) [
                                                                'currency' => PropellerHelper::currency_abbr(),
                                                                'value' => $this->getData()->total->totalGross,
                                                                'coupon' => isset($this->getData()->actionCode) ? $this->getData()->actionCode : null,
                                                                'items' => $this->products
                                                            ]
                                                        ];
                                                    }
                                                case 'add_to_wishlist':
                                                    // parse add to wishlist
                                                    if (is_array($this->getData()) && !empty($this->getData())) {
                                                        $this->products = $this->mapProducts((object) ['items' => $this->getData()]);

                                                        return (object) [
                                                            'event' => 'add_to_wishlist',
                                                            'ecommerce' => (object) [
                                                                'currency' => PropellerHelper::currency_abbr(),
                                                                'value' => isset($this->products[0]->price) ? $this->products[0]->price : 0,
                                                                'items' => $this->products
                                                            ]
                                                        ];
                                                    }
                                                    break;
                                                case 'login':
                                                    // parse login
                                                    if (isset($this->getData()->method)) {
                                                        return (object) [
                                                            'event' => 'login',
                                                            'method' => $this->getData()->method
                                                        ];
                                                    }
                                                    break;
                                                case 'sign_up':
                                                    // parse sign up
                                                    if (isset($this->getData()->method)) {
                                                        return (object) [
                                                            'event' => 'sign_up',
                                                            'method' => $this->getData()->method
                                                        ];
                                                    }
                                                    break;
                                                case 'add_payment_info':
                                                    // parse add payment info
                                                    if (isset($this->getData()->items) && is_array($this->getData()->items) && !empty($this->getData()->items)) {
                                                        $this->products = $this->mapCartItems($this->getData()->items);

                                                        return (object) [
                                                            'event' => 'add_payment_info',
                                                            'ecommerce' => (object) [
                                                                'currency' => PropellerHelper::currency_abbr(),
                                                                'value' => $this->getData()->total->totalGross ?? 0,
                                                                'payment_type' => isset($this->getData()->paymentData->method) ? $this->getData()->paymentData->method : null,
                                                                'coupon' => isset($this->getData()->actionCode) ? $this->getData()->actionCode : null,
                                                                'items' => $this->products
                                                            ]
                                                        ];
                                                    }
                                                    break;
                                                case 'add_shipping_info':
                                                    // parse add shipping info
                                                    if (isset($this->getData()->items) && is_array($this->getData()->items) && !empty($this->getData()->items)) {
                                                        $this->products = $this->mapCartItems($this->getData()->items);

                                                        return (object) [
                                                            'event' => 'add_shipping_info',
                                                            'ecommerce' => (object) [
                                                                'currency' => PropellerHelper::currency_abbr(),
                                                                'value' => $this->getData()->total->totalGross,
                                                                'shipping_tier' => isset($this->getData()->postageData->method) ? $this->getData()->postageData->method : null,
                                                                'coupon' => isset($this->getData()->actionCode) ? $this->getData()->actionCode : null,
                                                                'items' => $this->products
                                                            ]
                                                        ];
                                                    }
                                                    break;
                                                case 'purchase':
                                                    // parse purchase
                                                    if (isset($this->getData()->items) && is_array($this->getData()->items) && !empty($this->getData()->items) && isset($this->getData()->id)) {
                                                        $this->products = $this->mapOrderItems($this->getData()->items);

                                                        return (object) [
                                                            'event' => 'purchase',
                                                            'ecommerce' => (object) [
                                                                'currency' => $this->getData()->currency ?? PropellerHelper::currency_abbr(),
                                                                'value' => $this->getData()->total->gross,
                                                                'transaction_id' => $this->getData()->id,
                                                                'tax' => $this->getData()->total->tax ?? 0,
                                                                'shipping' => $this->getData()->postageData->gross ?? 0,
                                                                'items' => $this->products
                                                            ]
                                                        ];
                                                    }
                                                default:
                                                    // do nothing
                                                    break;
                                            }
                                        }

                                        return wp_json_encode('{}', JSON_UNESCAPED_SLASHES);
                                    }

                                    protected function mapCategory(object $data): object
                                    {
                                        $category = new Category($data);

                                        return (object) [
                                            'id' => $category->categoryId ?? null,
                                            'name' => $category->name[0]->value ?? null
                                        ];
                                    }

                                    protected function mapProducts(object $items): array
                                    {
                                        if (!is_array($items->items) || empty($items->items))
                                            return [];

                                        $products = [];

                                        $page = $items->page ?? 1;
                                        $offset = $items->offset ?? 12;
                                        $product_index = 1;

                                        foreach ($items->items as $item) {
                                            $product = null;
                                            $product_data = null;

                                            if ($item->class == ProductClass::Product) {
                                                $product = new Product($item);

                                                $product_data = [
                                                    // 'id' => $product->productId ?? null,
                                                    'item_name' => $product->get_name() ?? null,
                                                    'item_id' => $product->sku ?? null,
                                                    'item_brand' => $product->manufacturer ?? null,
                                                    'currency' => PropellerHelper::currency_abbr() ?? null,
                                                    'quantity' => $product->minimumQuantity ?? 1,
                                                    'index' => ($page - 1) * $offset + $product_index++,
                                                ];
                                            } else if ($item->class == ProductClass::Cluster) {
                                                $product = new Cluster($item, false);

                                                $product_data = [
                                                    // 'id' => $product->productId ?? null,
                                                    'item_name' => $product->get_name() ?? null,
                                                    'item_id' => $product->defaultProduct?->sku ?? null,
                                                    'item_brand' => $product->defaultProduct?->manufacturer ?? null,
                                                    'currency' => PropellerHelper::currency_abbr() ?? null,
                                                    'quantity' => $product->defaultProduct?->minimumQuantity ?? 1,
                                                    'index' => ($page - 1) * $offset + $product_index++,
                                                ];
                                            }

                                            if ($product === null || $product_data === null)
                                                continue;

                                            // Add category information
                                            $categories = $this->mapProductCategories($item);
                                            $product_data = array_merge($product_data, $categories);

                                            if ($item->class == ProductClass::Product) {
                                                if (!$product->is_price_on_request() && !PROPELLER_WP_SEMICLOSED_PORTAL && !PROPELLER_WP_CLOSED_PORTAL)
                                                    $product_data['price'] = $product->price->gross ?? 0;
                                            } else if ($item->class == ProductClass::Cluster) {
                                                if (!$product->defaultProduct?->is_price_on_request() && !PROPELLER_WP_SEMICLOSED_PORTAL && !PROPELLER_WP_CLOSED_PORTAL)
                                                    $product_data['price'] = $product->defaultProduct?->price->gross ?? 0;
                                            }

                                            $products[] = (object) $product_data;
                                        }

                                        return $products;
                                    }

                                    protected function mapProduct(object $item): array
                                    {
                                        $products = [];

                                        $page = $item->page ?? 1;
                                        $offset = $item->offset ?? 12;
                                        $product_index = 1;

                                        $product = new Product($item);

                                        $product_data = [
                                            // 'id' => $product->productId ?? null,
                                            'item_name' => $product->get_name() ?? null,
                                            'item_id' => $product->sku ?? null,
                                            'item_brand' => $product->manufacturer ?? null,
                                            'currency' => PropellerHelper::currency_abbr() ?? null,
                                            'quantity' => $product->minimumQuantity ?? 1,
                                            'index' => ($page - 1) * $offset + $product_index++,
                                        ];

                                        $categories = $this->mapProductCategories($item);
                                        $product_data = array_merge($product_data, $categories);

                                        if (!$product->is_price_on_request() && !PROPELLER_WP_SEMICLOSED_PORTAL && !PROPELLER_WP_CLOSED_PORTAL)
                                            $product_data['price'] = $product->price->gross ?? 0;

                                        $products[] = (object) $product_data;

                                        return $products;
                                    }

                                    protected function mapCluster(object $item): array
                                    {
                                        $products = [];

                                        $page = $item->page ?? 1;
                                        $offset = $item->offset ?? 12;
                                        $product_index = 1;

                                        $cluster = new Cluster($item, false);

                                        $product_data = [
                                            // 'id' => $product->productId ?? null,
                                            'item_name' => $cluster->defaultProduct?->get_name() ?? null,
                                            'item_id' => $cluster->defaultProduct?->sku ?? null,
                                            'item_brand' => $cluster->defaultProduct?->manufacturer ?? null,
                                            'currency' => PropellerHelper::currency_abbr() ?? null,
                                            'quantity' => $cluster->defaultProduct?->minimumQuantity ?? 1,
                                            'index' => ($page - 1) * $offset + $product_index++,
                                        ];

                                        $categories = $this->mapProductCategories($item);
                                        $product_data = array_merge($product_data, $categories);

                                        // $cluster->get_price();

                                        if (!$cluster->defaultProduct?->is_price_on_request() && !PROPELLER_WP_SEMICLOSED_PORTAL && !PROPELLER_WP_CLOSED_PORTAL)
                                            $product_data['price'] = $cluster->defaultProduct?->price->gross ?? 0;

                                        $products[] = (object) $product_data;

                                        return $products;
                                    }

                                    public function mapCartItems(array $items): array
                                    {
                                        if (empty($items))
                                            return [];

                                        $products = [];
                                        $product_index = 1;

                                        foreach ($items as $item) {
                                            $item_quantity = $item->quantity ?? 1;

                                            if (!is_object($item->product->cluster)) {
                                                $product = new Product($item->product);

                                                $product_data = [
                                                    // 'id' => $product->productId ?? null,
                                                    'item_name' => $product->get_name() ?? null,
                                                    'item_id' => $product->sku ?? null,
                                                    'item_brand' => $product->manufacturer ?? null,
                                                    'currency' => PropellerHelper::currency_abbr() ?? null,
                                                    'quantity' => $item_quantity,
                                                    'price' => $item->sum ?? 0,
                                                    'index' => $product_index++,
                                                ];

                                                // Add category information
                                                $categories = $this->mapProductCategories($item->product);
                                                $product_data = array_merge($product_data, $categories);

                                                $products[] = (object) $product_data;
                                            } else {
                                                $cluster = new Cluster($item->product->cluster, false);

                                                $product_data = [
                                                    // 'id' => $product->productId ?? null,
                                                    'item_name' => $item->product->get_name() ?? null,
                                                    'item_id' => $item->product->sku ?? null,
                                                    'item_brand' => $item->product->manufacturer ?? null,
                                                    'currency' => PropellerHelper::currency_abbr() ?? null,
                                                    'quantity' => $item_quantity,
                                                    'price' => $item->price ?? 0,
                                                    'index' => $product_index++,
                                                ];

                                                $categories = $this->mapProductCategories($item);
                                                $product_data = array_merge($product_data, $categories);

                                                $products[] = (object) $product_data;

                                                if (is_array($item->childItems) && count($item->childItems) > 0) {
                                                    foreach ($item->childItems as $childItem) {
                                                        $child_product = new Product($childItem->product);

                                                        $child_product_data = [
                                                            // 'id' => $child_product->productId ?? null,
                                                            'item_name' => $child_product->get_name() ?? null,
                                                            'item_id' => $child_product->sku ?? null,
                                                            'item_brand' => $child_product->manufacturer ?? null,
                                                            'currency' => PropellerHelper::currency_abbr() ?? null,
                                                            'quantity' => $childItem->quantity ?? 1,
                                                            'price' => $childItem->price ?? 0,
                                                            'item_variant' => $child_product->get_name() ?? null,
                                                            'index' => $product_index++,
                                                        ];

                                                        // Add category information
                                                        $child_categories = $this->mapProductCategories($childItem->product);
                                                        $child_product_data = array_merge($child_product_data, $child_categories);

                                                        $products[] = (object) $child_product_data;
                                                    }
                                                }
                                            }
                                        }

                                        return $products;
                                    }

                                    public function mapCartItem(array $items): array
                                    {
                                        if (empty($items))
                                            return [];

                                        $products = [];
                                        $product_index = 1;

                                        foreach ($items as $item) {
                                            $item_quantity = $item->quantity ?? 1;

                                            if (isset($item->added_quantity))
                                                $item_quantity = $item->added_quantity;
                                            if (isset($item->updated_quantity))
                                                $item_quantity = $item->updated_quantity;

                                            $product = new Product($item->product);

                                            if (!is_object($item->product->cluster)) {
                                                $product_data = [
                                                    // 'id' => $product->productId ?? null,
                                                    'item_name' => $product->get_name() ?? null,
                                                    'item_id' => $product->sku ?? null,
                                                    'item_brand' => $product->manufacturer ?? null,
                                                    'currency' => PropellerHelper::currency_abbr() ?? null,
                                                    'quantity' => $item_quantity,
                                                    'price' => $item->sum ?? 0,
                                                    'index' => $product_index++,
                                                ];

                                                // Add category information
                                                $categories = $this->mapProductCategories($item->product);
                                                $product_data = array_merge($product_data, $categories);

                                                $products[] = (object) $product_data;
                                            } else {
                                                $product_data = [
                                                    // 'id' => $product->productId ?? null,
                                                    'item_name' => $item->product->get_name() ?? null,
                                                    'item_id' => $item->product->sku ?? null,
                                                    'item_brand' => $item->product->manufacturer ?? null,
                                                    'currency' => PropellerHelper::currency_abbr() ?? null,
                                                    'quantity' => $item_quantity,
                                                    'price' => $item->price ?? 0,
                                                    'index' => $product_index++,
                                                ];

                                                $categories = $this->mapProductCategories($item);
                                                $product_data = array_merge($product_data, $categories);

                                                $products[] = (object) $product_data;

                                                if (is_array($item->childItems) && count($item->childItems) > 0) {
                                                    foreach ($item->childItems as $childItem) {
                                                        $child_product = new Product($childItem->product);

                                                        $child_product_data = [
                                                            // 'id' => $child_product->productId ?? null,
                                                            'item_name' => $child_product->get_name() ?? null,
                                                            'item_id' => $child_product->sku ?? null,
                                                            'item_brand' => $child_product->manufacturer ?? null,
                                                            'currency' => PropellerHelper::currency_abbr() ?? null,
                                                            'quantity' => $childItem->quantity ?? 1,
                                                            'price' => $childItem->price ?? 0,
                                                            'item_variant' => $child_product->get_name() ?? null,
                                                            'index' => $product_index++,
                                                        ];

                                                        // Add category information
                                                        $child_categories = $this->mapProductCategories($childItem->product);
                                                        $child_product_data = array_merge($child_product_data, $child_categories);

                                                        $products[] = (object) $child_product_data;
                                                    }
                                                }
                                            }
                                        }

                                        return $products;
                                    }

                                    public function mapOrderItems(array $items): array
                                    {
                                        if (empty($items))
                                            return [];

                                        $products = [];
                                        $product_index = 1;

                                        foreach ($items as $item) {
                                            $product_data = [
                                                // 'id' => $product->productId ?? null,
                                                'item_name' => $item->name ?? null,
                                                'item_id' => $item->sku ?? null,
                                                'item_brand' => $item->manufacturer ?? null,
                                                'currency' => PropellerHelper::currency_abbr() ?? null,
                                                'quantity' => $item->quantity,
                                                'price' => $item->price ?? 0,
                                                'index' => $product_index++,
                                            ];

                                            $categories = $this->mapProductCategories($item);
                                            $product_data = array_merge($product_data, $categories);

                                            $products[] = (object) $product_data;
                                        }

                                        return $products;
                                    }

                                    protected function mapProductCategories(mixed $item): array
                                    {
                                        $categories = [];

                                        // Check if categoryPath exists and is an array
                                        if (!isset($item->categoryPath) || !is_array($item->categoryPath)) {
                                            return $categories;
                                        }

                                        $category_index = 1;

                                        foreach ($item->categoryPath as $category) {
                                            // Skip root category with ID 17
                                            if (isset($category->categoryId) && $category->categoryId == 17) {
                                                continue;
                                            }

                                            // Get category name (prefer current language or fallback to first available)
                                            $category_name = null;
                                            if (isset($category->name) && is_array($category->name)) {
                                                // Try to find name for current language first
                                                $current_locale = get_locale();
                                                $current_lang = strtoupper(substr($current_locale, 0, 2));

                                                foreach ($category->name as $name) {
                                                    if (isset($name->language) && $name->language === $current_lang) {
                                                        $category_name = $name->value;
                                                        break;
                                                    }
                                                }

                                                // Fallback to first available name if current language not found
                                                if (!$category_name && !empty($category->name)) {
                                                    $category_name = $category->name[0]->value ?? null;
                                                }
                                            }

                                            if ($category_name) {
                                                if ($category_index === 1) {
                                                    $categories['item_category'] = $category_name;
                                                } else {
                                                    $categories['item_category' . $category_index] = $category_name;
                                                }
                                                $category_index++;
                                            }
                                        }

                                        return $categories;
                                    }
                                }
