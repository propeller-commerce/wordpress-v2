<?php

namespace Propeller;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\BaseController;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\ProductClass;
use Propeller\Includes\Model\SitemapModel;
use stdClass;
use TRP_Translate_Press;

class PropellerSitemap extends BaseController {
	protected $cron_jobs;
    protected $model;

    protected $brands = [];

    protected $products_count;
    protected $categories_count;
    protected $brands_count;

    protected $products_offset = 100;
    protected $products_pages = 1;

    public $sitemap_files = [];

    protected $is_yoast_active = false;
    protected $is_trp_active = false;
    
    public function __construct() {
        parent::__construct();

        $this->model = new SitemapModel();

        $this->cron_jobs = [
            'propel_sitemap_cron' => 'build_sitemap'
        ];
    }

    public function yoast_active() {
        include_once(ABSPATH.'wp-admin/includes/plugin.php');

        return is_plugin_active('wordpress-seo/wp-seo.php') && class_exists('WPSEO_Options');

        // return (in_array('wordpress-seo/wp-seo.php', apply_filters('active_plugins', get_option('active_plugins'))) && class_exists('WPSEO_Options'));
    }

    public function trp_active() {
        include_once(ABSPATH.'wp-admin/includes/plugin.php');

        return is_plugin_active('translatepress-multilingual/index.php') && class_exists('TRP_Translate_Press');

        // return (in_array('translatepress-multilingual/index.php', apply_filters('active_plugins', get_option('active_plugins'))) && class_exists('TRP_Translate_Press'));
    }

    public function register_actions() {
        foreach ($this->cron_jobs as $name => $job) {
            add_action($name, [$this, $job]);
        }

        if ($this->yoast_active())
            add_filter('wpseo_sitemap_index', [$this, 'add_to_yoast_sitemap']);
    }

    public function build_sitemap() {
        if (!defined('ABSPATH'))
            define( 'ABSPATH', __DIR__ . '/../../../' );

        if (!defined('DB_NAME'))
            require_once ABSPATH . 'wp-config.php';

        global $table_prefix, $wpdb, $propel;
        
        if ($this->is_sitemap_valid()) {
            $files = $this->get_files();

            if (!$this->yoast_active())
                $this->build_sitemap_index($files);

            $return = new stdClass();

            $return->success = true;
            $return->message = __('Sitemap files are generated 1.', 'propeller-ecommerce-v2');
            $return->reload = true;
            $return->page = 'propeller';
            $return->tab = 'sitemap';

            die(json_encode($return));
        }

        set_time_limit(100000);

        if (!defined('PROPELLER_CATALOG_DEPTH'))
            include  __DIR__ . '/../constants.php';

        $propel_settings = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i", $table_prefix . PROPELLER_SETTINGS_TABLE));
        $behavior_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i", $table_prefix . PROPELLER_BEHAVIOR_TABLE));

        $base_catalog_id = (int) $propel_settings->catalog_root;
        $use_ids_in_urls = isset($behavior_result->ids_in_urls) ? $behavior_result->ids_in_urls == 1 : false;
        
        $catalog_depth = PROPELLER_CATALOG_DEPTH;
        
        $languages = $this->get_languages();

        $sitemap_files = [];
        // date_default_timezone_set('UTC');

        if (isset($languages->languages) && is_array($languages->languages) && count($languages->languages)) {
            foreach ($languages->languages as $locale => $url) {
                if (strpos($url, '_') !== false)
                    $url = explode('_', $url)[0];

                $language = strtoupper($url);
                $avoid_lang_prefix = $url == $languages->avoid_lang_slug;
                
                $sitemap_products = [];
                $product_sitemap_files_count = 0;
                $sitemap_products[$product_sitemap_files_count] = '';
                
                $this->products_count = 1;
                $this->products_pages = 1;

                // Build product sitemap
                $this->build_product_sitemap(
                    PageController::get_slug(PageType::PRODUCT_PAGE), 
                    $language, 
                    $avoid_lang_prefix, 
                    $use_ids_in_urls,
                    $sitemap_products, 
                    $product_sitemap_files_count);

                for ($i = 0; $i <= $product_sitemap_files_count; $i++) {
                    $file = ABSPATH . "propel-" . $this->get_multisite_blog_name() . "sitemap-products-$url-" . ($i + 1) . ".xml";
                    // $products_sitemap = fopen($file, "w");
            
                    $sitemap_content = $this->get_sitemap_start();                    
                    $sitemap_content .= $sitemap_products[$i];
                    $sitemap_content .= $this->get_sitemap_end();
        
                    PropellerHelper::wp_filesys()->put_contents($file, $sitemap_content);
                    // fwrite($products_sitemap, $sitemap_content);
                    // fclose($products_sitemap);

                    $file_data = new stdClass();
                    $file_data->file = $file;
                    $file_data->lastmod = gmdate(DATE_ATOM);

                    $sitemap_files[] = $file_data;
                }      

                // Build catalog sitemap
                $catalog = $this->get_menu_structure($base_catalog_id, $language, $catalog_depth);

                $sitemap_categories = [];
                $categories_sitemap_files_count = 0;
                $sitemap_categories[$categories_sitemap_files_count] = '';
                $this->categories_count = 1;

                $this->build_catalog_sitemap(
                    $catalog->categories,
                    PageController::get_slug(PageType::CATEGORY_PAGE), 
                    $language, 
                    $avoid_lang_prefix, 
                    $use_ids_in_urls,
                    $sitemap_categories, 
                    $categories_sitemap_files_count);

                for ($i = 0; $i <= $categories_sitemap_files_count; $i++) {
                    $file = ABSPATH . "propel-" . $this->get_multisite_blog_name() . "sitemap-catalog-$url-" . ($i + 1) . ".xml";

                    // $catalog_sitemap = fopen($file, "w");
            
                    $sitemap_content = $this->get_sitemap_start();
                    
                    $sitemap_content .= $sitemap_categories[$i];

                    $sitemap_content .= $this->get_sitemap_end();
        
                    PropellerHelper::wp_filesys()->put_contents($file, $sitemap_content);
                    // fwrite($catalog_sitemap, $sitemap_content);
                    // fclose($catalog_sitemap);

                    $file_data = new stdClass();
                    $file_data->file = $file;
                    $file_data->lastmod = gmdate(DATE_ATOM);

                    $sitemap_files[] = $file_data;
                }      

                // Build brands sitemap                
                $sitemap_brands = [];
                $brands_sitemap_files_count = 0;
                $sitemap_brands[$brands_sitemap_files_count] = '';
                $this->brands_count = 1;

                $this->build_brands_sitemap(
                    $this->brands,
                    PageController::get_slug(PageType::BRAND_PAGE), 
                    $language, 
                    $avoid_lang_prefix,
                    $sitemap_brands, 
                    $brands_sitemap_files_count);

                for ($i = 0; $i <= $brands_sitemap_files_count; $i++) {
                    $file = ABSPATH . "propel-" . $this->get_multisite_blog_name() . "sitemap-brands-$url-" . ($i + 1) . ".xml";

                    // $brands_sitemap = fopen($file, "w");
            
                    $sitemap_content = $this->get_sitemap_start();
                    
                    $sitemap_content .= $sitemap_brands[$i];

                    $sitemap_content .= $this->get_sitemap_end();
        
                    PropellerHelper::wp_filesys()->put_contents($file, $sitemap_content);
                    // fwrite($brands_sitemap, $sitemap_content);
                    // fclose($brands_sitemap);

                    $file_data = new stdClass();
                    $file_data->file = $file;
                    $file_data->lastmod = gmdate(DATE_ATOM);

                    $sitemap_files[] = $file_data;
                }    
                
                // if (!$this->yoast_active())
                //     $sitemap_files[] = $this->build_content_sitemap($locale, $url, $avoid_lang_prefix);
            }
        }
        
        $this->sitemap_files = $sitemap_files;
        $builder_files = [];

        foreach ($sitemap_files as $sitemap_file) {
            $file = new stdClass();

            $file->url = home_url('/' . basename($sitemap_file->file));
            $file->lastmod = $sitemap_file->lastmod;

            $propel['sitemap_files'][] = $file;
            $builder_files[] = $file;
        }
        
        if (!$this->yoast_active())
            $this->build_sitemap_index($builder_files);

        $return = new stdClass();

        $return->success = true;
        $return->message = __('Sitemap files are generated 2.', 'propeller-ecommerce-v2');
        $return->reload = true;
        $return->page = 'propeller';
        $return->tab = 'sitemap';

        die(json_encode($return));
    }

    private function build_product_sitemap($page_slug, $language, $avoid_lang_prefix, $use_ids_in_urls, &$sitemap_products, &$product_sitemap_files_count) {
        for ($i = 1; $i <= $this->products_pages; $i++) {
            $products = $this->get_products($language, $this->products_offset, $i);

            if (!is_object($products))
                break;

            if ($this->products_pages == 1)
                $this->products_pages = $products->pages;

            foreach ($products->items as $product) {
                if (isset($product->slug) && is_array($product->slug) && count($product->slug) && $product->hidden == 'N' && 
                    ($product->class == ProductClass::Product || $product->class == ProductClass::Cluster)) {
                        
                    // Skip products with empty slugs to avoid double slashes in URLs
                    if (empty($product->slug[0]->value)) {
                        continue;
                    }
                        
                    $url_chunks = [];
    
                    if (!$avoid_lang_prefix)
                        $url_chunks[] = strtolower($language);
    
                    $url_chunks[] = $product->class == ProductClass::Cluster ? PageController::get_slug(PageType::CLUSTER_PAGE) : $page_slug;

                    if ($use_ids_in_urls)
                        $url_chunks[] = $product->urlId;
    
                    $url_chunks[] = $product->slug[0]->value;
                    
                    $url = home_url(implode('/', $url_chunks) . '/');
    
                    $content = "\t" . '<url>' . "\n";
                    $content .= "\t\t" . '<loc>' . $url . '</loc>' . "\n";
    
                    if (isset($product->dateChanged) && !empty($product->dateChanged))
                        $content .= "\t\t" . '<lastmod>' . explode('T', $product->dateChanged)[0] . '</lastmod>' . "\n";
                    
                    $content .= "\t" . '</url>' . "\n";
    
                    $sitemap_products[$product_sitemap_files_count] .= $content;
    
                    if (isset($product->manufacturer) && !empty($product->manufacturer) && !in_array($product->manufacturer, $this->brands))
                        $this->brands[] = $product->manufacturer;
    
                    $this->products_count++;

                    if ($this->products_count > 0 && $this->products_count % PROPELLER_SITEMAP_MAX_ITEMS == 0) {
                        $product_sitemap_files_count++;
    
                        $sitemap_products[$product_sitemap_files_count] = '';
                    }
                }
            }
        }

        $this->products_pages = 1;
    }

    private function build_catalog_sitemap($categories, $page_slug, $language, $avoid_lang_prefix, $use_ids_in_urls, &$sitemap_categories, &$categories_sitemap_files_count) {
        if (isset($categories) && is_array($categories) && count($categories)) {
            foreach ($categories as $category) {
                if (isset($category->slug) && is_array($category->slug) && count($category->slug)) {
                    // Skip categories with empty slugs to avoid double slashes in URLs
                    if (empty($category->slug[0]->value)) {
                        // Still process subcategories even if parent has empty slug
                        if (isset($category->categories) && is_array($category->categories) && count($category->categories)) {
                            $this->build_catalog_sitemap(
                                $category->categories,
                                $page_slug,
                                $language,
                                $avoid_lang_prefix,
                                $use_ids_in_urls,
                                $sitemap_categories,
                                $categories_sitemap_files_count
                            );
                        }
                        continue;
                    }
                    
                    $url_chunks = [];
    
                    if (!$avoid_lang_prefix)
                        $url_chunks[] = strtolower($language);;
    
                    $url_chunks[] = $page_slug;

                    if ($use_ids_in_urls)
                        $url_chunks[] = $category->urlId;
    
                    $url_chunks[] = $category->slug[0]->value;
                    
                    $url = home_url(implode('/', $url_chunks) . '/');
    
                    $content = "\t" . '<url>' . "\n";
                    $content .= "\t\t" . '<loc>' . $url . '</loc>' . "\n";
                    $content .= "\t" . '</url>' . "\n";
    
                    $sitemap_categories[$categories_sitemap_files_count] .= $content;
    
                    $this->categories_count++;

                    if ($this->categories_count > 0 && $this->categories_count % PROPELLER_SITEMAP_MAX_ITEMS == 0) {
                        $categories_sitemap_files_count++;
    
                        $sitemap_categories[$categories_sitemap_files_count] = '';
                    }

                    if (isset($category->categories) && is_array($category->categories) && count($category->categories))
                        $this->build_catalog_sitemap(
                            $category->categories,
                            $page_slug, 
                            $language, 
                            $avoid_lang_prefix, 
                            $use_ids_in_urls,
                            $sitemap_categories, 
                            $categories_sitemap_files_count);
                }
            }
        }
    }

    private function build_brands_sitemap($brands, $page_slug, $language, $avoid_lang_prefix, &$sitemap_brands, &$brands_sitemap_files_count) {
        if (is_array($brands) && count($brands)) {
            foreach ($brands as $brand) {
                $url_chunks = [];

                if (!$avoid_lang_prefix)
                    $url_chunks[] = strtolower($language);

                $url_chunks[] = $page_slug;
                $url_chunks[] = urlencode($brand);
                
                $url = home_url(implode('/', $url_chunks) . '/');

                $content = "\t" . '<url>' . "\n";
                $content .= "\t\t" . '<loc>' . $url . '</loc>' . "\n";
                $content .= "\t" . '</url>' . "\n";

                $sitemap_brands[$brands_sitemap_files_count] .= $content;

                $this->brands_count++;

                if ($this->brands_count > 0 && $this->brands_count % PROPELLER_SITEMAP_MAX_ITEMS == 0) {
                    $brands_sitemap_files_count++;

                    $sitemap_brands[$brands_sitemap_files_count] = '';
                }
            }
        }
    }

    // public function build_content_sitemap($locale, $language, $avoid_lang_prefix) {
    //     $posts = get_posts(array(
    //         'numberposts' => -1,
    //         'orderby' => 'modified',
    //         'post_type' => array('post','page'),
    //         'order' => 'DESC'
    //     ));

    //     $sitemap_content = $this->get_sitemap_start();

    //     foreach($posts as $post) {
    //         setup_postdata($post);
    //         $postdate = explode(" ", $post->post_modified);

    //         $post_url = get_permalink($post->ID);

    //         if (!$avoid_lang_prefix && $this->trp_active())
    //             $this->get_post_url_in_language($locale, $post_url);

    //         $sitemap_content .= "\t" . '<url>' . "\n";
    //         $sitemap_content .= "\t\t" . '<loc>'. $post_url .'</loc>' . "\n";
    //         $sitemap_content .= "\t\t" . '<lastmod>'. $postdate[0] .'</lastmod>' . "\n";
    //         $sitemap_content .= "\t" . '</url>' . "\n";
    //     }

    //     $sitemap_content .= '</urlset>';
        
    //     $file = ABSPATH . "sitemap-content-$language.xml";
    //     $fp = fopen($file, 'w');
        
    //     fwrite($fp, $sitemap_content);
    //     fclose($fp);

    //     $file_data = new stdClass();
    //     $file_data->file = $file;
    //     $file_data->lastmod = gmdate(DATE_ATOM);

    //     return $file_data;
    // }

    // private function get_post_url_in_language($locale, $post_url) {
    //     $trp = TRP_Translate_Press::get_trp_instance();
    //     $url_converter = $trp->get_component( 'url_converter' );

    //     return $url_converter->get_url_for_language($locale, $post_url, '');
    // }

    private function get_menu_structure($base_catalog_id, $language, $depth = 3) {
        $gql = $this->model->get_menu_structure($base_catalog_id, $language, $depth);
        
        return $this->query($gql, 'category');
    }

    private function get_products($language, $offset = 12, $page = 1) {
        $gql = $this->model->get_products($language, $offset, $page);
        
        return $this->query($gql, 'products');
    }

    private function get_sitemap_start() {
        $sitemap_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap_content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        return $sitemap_content;
    }

    private function get_sitemap_end() {
        $sitemap_content = '</urlset>' . "\n";

        return $sitemap_content;
    }

    private function build_sitemap_index($files) {
        $sitemap_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap_content .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        if (is_array($files) && count($files)) {
            foreach ($files as $sitemap_file) {
                $sitemap_content .= "\t" . '<sitemap>' . "\n";
                $sitemap_content .= "\t\t" . '<loc>' . $sitemap_file->url . '</loc>' . "\n";
                $sitemap_content .= "\t\t" . '<lastmod>' . $sitemap_file->lastmod . '</lastmod>' . "\n";
                $sitemap_content .= "\t" . '</sitemap>' . "\n";
            }
        }

        $sitemap_content .= '</sitemapindex>';

        $file = ABSPATH . $this->get_multisite_blog_name() . "sitemap.xml";

        PropellerHelper::wp_filesys()->put_contents($file, $sitemap_content);
    }

    public function get_multisite_blog_name() {
        $blog_name = '';

        if (is_multisite()) {
            global $blog_id;

            $current_blog_details = get_blog_details( array( 'blog_id' => $blog_id ) );

            $blog_name = strtolower($current_blog_details->blogname) . '-';
        }

        return $blog_name;
    }

    public function add_to_yoast_sitemap($sitemap_custom_items) {
        global $propel;

        if (!isset($propel['sitemap_files']) || !is_array($propel['sitemap_files']) || !count($propel['sitemap_files']))
            $this->get_sitemap_files();
            
        if (is_array($propel['sitemap_files']) && count($propel['sitemap_files'])) {
            foreach ($propel['sitemap_files'] as $sitemap_file) {
                $sitemap_custom_items .= "\t" . '<sitemap>' . "\n";
                $sitemap_custom_items .= "\t\t" . '<loc>' . home_url('/' . basename($sitemap_file->url)) . '</loc>' . "\n";
                $sitemap_custom_items .= "\t\t" . '<lastmod>' . $sitemap_file->lastmod . '</lastmod>' . "\n";
                $sitemap_custom_items .= "\t" . '</sitemap>' . "\n";
            }
        }

        return $sitemap_custom_items;
    }

    private function get_sitemap_files() {
        global $propel;

        if (!defined('ABSPATH'))
            define( 'ABSPATH', __DIR__ . '/../../../' );

        $path  = ABSPATH . 'propel-' . $this->get_multisite_blog_name() . 'sitemap-*.xml';
		$files = glob( $path );

        $files_valid = $this->is_sitemap_valid();

        if (!$files_valid)
            $this->build_sitemap();
        else {
            foreach ($files as $sitemap_file) {
                $file = new stdClass();

                $file->url = home_url('/' . basename($sitemap_file));
                $file->lastmod = gmdate(DATE_ATOM, filemtime($sitemap_file));
                $propel['sitemap_files'][] = $file;
            }
        }
    }

    public function clear_sitemap() {
        if (!defined('ABSPATH'))
            define( 'ABSPATH', __DIR__ . '/../../../' );

        $path  = ABSPATH . 'propel-' . $this->get_multisite_blog_name() . 'sitemap-*.xml';
		$files = glob( $path );

        foreach ($files as $sitemap_file) {
            @PropellerHelper::wp_filesys()->delete($sitemap_file);
        }

        if (file_exists(ABSPATH . 'sitemap.xml'))
            @PropellerHelper::wp_filesys()->delete(ABSPATH . 'sitemap.xml');
    }

    public function get_files() {
        if (!defined('ABSPATH'))
            define( 'ABSPATH', __DIR__ . '/../../../' );

        $path  = ABSPATH . 'propel-' . $this->get_multisite_blog_name() . 'sitemap-*.xml';
		$files = glob( $path );

        $sitemap_files = [];

        foreach ($files as $sitemap_file) {
            $file = new stdClass();

            $file->url = home_url('/' . basename($sitemap_file));
            $file->lastmod = gmdate('Y-m-d H:i:s', filemtime($sitemap_file));
            $sitemap_files[] = $file;
        }

        return $sitemap_files;
    }

    public function has_index() {
        if (!defined('ABSPATH'))
            define( 'ABSPATH', __DIR__ . '/../../../' );

        return file_exists(ABSPATH . $this->get_multisite_blog_name() . 'sitemap.xml');
    }

    public function get_index() {
        if (!defined('ABSPATH'))
            define( 'ABSPATH', __DIR__ . '/../../../' );

        $path  = ABSPATH . $this->get_multisite_blog_name() . 'sitemap.xml';

        $file = new stdClass();

        $file->url = home_url('/' . $this->get_multisite_blog_name() . 'sitemap.xml');
        $file->lastmod = gmdate('Y-m-d H:i:s', filemtime($path));

        return $file;
    }

    private function is_sitemap_valid() {
        if (!defined('ABSPATH'))
            define( 'ABSPATH', __DIR__ . '/../../../' );

        $path  = ABSPATH . 'propel-' . $this->get_multisite_blog_name() . 'sitemap-*.xml';
		$files = glob( $path );

        $files_valid = false;

        if (is_array($files) && count($files)) {
            if (is_file($files[0])) {
                $now = time();

                if ($now - filemtime($files[0]) <= 60 * 60 * 24) { // 24 hours
                    $files_valid = true;
                }
            }
        }

        return $files_valid;
    }

    public function is_valid() {
        return $this->is_sitemap_valid();
    }

    public function get_languages() {
        if (!defined('ABSPATH'))
            define( 'ABSPATH', __DIR__ . '/../../../' );

        if (!defined('DB_NAME'))
            require_once ABSPATH . 'wp-config.php';

        if ($this->trp_active())
            return $this->get_trp_languages();

        return $this->build_default_langs();
    }   

    private function build_default_langs() {
        if (!defined('ABSPATH'))
            define( 'ABSPATH', __DIR__ . '/../../../' );

        if (!defined('DB_NAME'))
            require_once ABSPATH . 'wp-config.php';

        global $wpdb;

        $wp_locale = null;
        $langs = new stdClass();

        if (function_exists('get_locale')) {
            $wp_locale = get_locale();

            $langs->default_language = $wp_locale;
            $langs->avoid_lang_slug = $this->locale_to_lang($wp_locale);

            $langs->languages = [
                $wp_locale => $langs->avoid_lang_slug
            ];
        }
        else if (function_exists('get_option')) {
            $wp_locale = get_option('WPLANG');

            $langs->default_language = $wp_locale;
            $langs->avoid_lang_slug = $this->locale_to_lang($wp_locale);

            $langs->languages = [
                $wp_locale => $langs->avoid_lang_slug
            ];
        }
        else {
            $results = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "options WHERE option_name = %s", "WPLANG")
            );

            if ($results && isset($results->option_value) && !empty($results->option_value)) {
                $wp_locale = $results->option_value;
                
                $langs->default_language = $wp_locale;
                $langs->avoid_lang_slug = $this->locale_to_lang($wp_locale);

                $langs->languages = [
                    $wp_locale => $langs->avoid_lang_slug
                ];
            }
            else {
                $langs->default_language = 'nl_NL';
                $langs->avoid_lang_slug = 'nl';
    
                $langs->languages = [
                    'nl_NL' => 'nl',
                    'en_US' => 'en'
                ];
            }
        }
            
        return $langs;
    }

    private function locale_to_lang($locale) {
        if ($locale == 'en_US' || $locale == 'en_GB')
            return 'en';
        
        if (strpos($locale, '_') !== false)
            return explode('_', $locale)[1];

        return $locale;
    }

    public function get_trp_languages() {
        $trp_options = get_option('trp_settings');
            
        $language_data = new stdClass();

        $language_data->default_language = $trp_options['default-language'];
        $language_data->avoid_lang_slug = $trp_options['url-slugs'][$trp_options['default-language']];

        if (strpos($language_data->avoid_lang_slug, '_') !== false)
            $language_data->avoid_lang_slug = explode('_', $language_data->avoid_lang_slug)[1];

        $language_data->languages = $trp_options['url-slugs'];

        return $language_data;
    }
}