<?php

namespace Propeller\Meta;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;

// <meta name="twitter:card" content="product" />
// <meta name="twitter:site" content="" />
// <meta name="twitter:title" content="" />
// <meta name="twitter:description" content="" />
// <meta name="twitter:image" content="" />

// <meta property="og:type" content="og:product" />
// <meta property="og:url" content="" />
// <meta property="og:title" content="" />
// <meta property="og:description" content="" />
// <meta property="og:image" content="" />

class MetaController {
    protected $yoast_available;
    protected $trp_available;

    protected $add_custom = false;

    public function __construct() {
        global $wp;

        $this->yoast_available = (in_array('wordpress-seo/wp-seo.php', apply_filters('active_plugins', get_option('active_plugins'))) && class_exists('WPSEO_Options'));
        $this->trp_available = (in_array('translatepress-multilingual/index.php', apply_filters('active_plugins', get_option('active_plugins'))) && class_exists('TRP_Translate_Press'));
        
        if ($this->yoast_available) {
            if ($this->add_custom) {
                add_filter('wpseo_frontend_presenters', [$this, 'meta_presenters']);
            }
            else {
                $this->apply_yoast_filters();
            }   
        }
        else {
            new MetaPresenter();
        }

        add_filter('get_canonical_url', [$this, 'get_canonical_url'], 10, 2);
        add_action('wp_head', [$this, 'generate_alternate_metas'], 10, 2);
        add_action( 'wp_head', [$this, 'add_yoast_canonical']);

        if (isset($wp->query_vars) && isset($wp->query_vars['pagename']) && (
            $wp->query_vars['pagename'] == PageController::get_slug(PageType::PRODUCT_PAGE) || 
            $wp->query_vars['pagename'] == PageController::get_slug(PageType::CLUSTER_PAGE) || 
            $wp->query_vars['pagename'] == PageController::get_slug(PageType::CATEGORY_PAGE)
        )) {
            if ($this->trp_available)
                add_filter('trp_hreflang', [$this, 'trpc_change_hreflang'], 10, 2 );
        }   
    }

    public function trpc_change_hreflang( $hreflang, $language ){
        return null;
    }

    public function get_canonical_url($canonical_url, $post) {
        global $propel;

        if (isset($propel['meta']) && count($propel['meta']) > 0 && isset($propel['meta']['canonical']))
            $canonical_url = $propel['meta']['canonical'];
        else if (isset($propel['meta']) && count($propel['meta']) > 0 && isset($propel['meta']['url']))
            $canonical_url = $propel['meta']['url'];

        return $canonical_url;
    }

    public function add_yoast_canonical() {
        global $propel;

        if (isset($propel['meta']) && count($propel['meta']) > 0 && isset($propel['meta']['canonical']))
            echo wp_kses_post('<link rel="canonical" href="' . esc_attr($propel['meta']['canonical']) .  '" />');
        else if (isset($propel['meta']) && count($propel['meta']) > 0 && isset($propel['meta']['url']))
            echo wp_kses_post('<link rel="canonical" href="' . esc_attr($propel['meta']['url']) .  '" />');
    }

    private function get_x_default() {
        global $propel, $wp_query;
        
        if (isset($wp_query->query_vars['pagename']) && !empty($propel['url_slugs'])) {
            $realm = $wp_query->query_vars['pagename'];

            $default_lang = $this->get_default_lang();
            $default_lang_short = explode('_', $default_lang)[0];

            $found = array_filter($propel['url_slugs'], function($obj) use ($default_lang_short) { 
                return strtolower($obj->language) == strtolower($default_lang_short); 
            });

            if (count($found)) {
                $slug = current($found)->value;

                $url = site_url('/' . $realm . '/' . $slug . '/');

                if (PROPELLER_ID_IN_URL)
                    $url = site_url('/' . $realm . '/' . $propel['data']->urlId . '/' . $slug . '/');

                return $url;
            }
        }
        else {
            return site_url('/');
        }

        return '';
    }

    public function generate_alternate_metas() {
        global $propel, $wp_query;

        $default_lang = $this->get_default_lang();

        $langs = $this->get_languages();

        $tags = [];
        
        if (isset($wp_query->query_vars['pagename']) && isset($propel['url_slugs']) && count($propel['url_slugs'])) {
            $realm = $wp_query->query_vars['pagename'];

            foreach ($langs as $lang) {
                $lang_code = strpos($lang, '_') ? explode('_', $lang)[0] : $lang;

                $found = array_filter($propel['url_slugs'], function($obj) use ($lang_code) { 
                    return strtolower($obj->language) == strtolower($lang_code); 
                });
    
                if (count($found) && !empty(current($found)->value)) {
                    $slug = current($found)->value;
                    
                    if ($default_lang != $lang) {
                        $url = site_url('/' . $lang_code . '/' . $realm . '/' . $slug . '/');

                        if (PROPELLER_ID_IN_URL)
                            $url = site_url('/' . $lang_code . '/' . $realm . '/' .$propel['data']->urlId . '/' . $slug . '/');
                            
                        $tags[str_replace('_', '-', $lang)] = $url;
                    }
                    else {  
                        $url = site_url('/' . $realm . '/' . $slug . '/');

                        if (PROPELLER_ID_IN_URL)
                            $url = site_url('/' . $realm . '/' . $propel['data']->urlId . '/' . $slug . '/');
                            
                        $tags[str_replace('_', '-', $lang)] = $url;
                    }
                }                    
            }
        }

        $tags['x-default'] = $this->get_x_default();

        echo "\r\n<!-- Propeller eCommerce alternate links -->";

        foreach ($tags as $lang => $url) {
            if (!empty($url))
                echo "\r\n" . '<link rel="alternate" hreflang="' . esc_attr($lang == 'x-default' ? $lang : strtolower(explode('-', $lang)[0])) . '" href="' . esc_url($url) . '" />';
        }         
        echo "\r\n";   
    }

    private function get_languages() {
        if ($this->trp_available) 
            return $this->get_trp_languages();

        return get_propel_languages();
    }

    private function get_default_lang() {
        if ($this->trp_available) {
            $trp_options = get_option('trp_settings');

            return $trp_options['default-language'];    
        }
        
        return PROPELLER_DEFAULT_LOCALE;
    }

    private function get_trp_languages() {
        $trp_options = get_option('trp_settings');
        
        return $trp_options['translation-languages'];
    }

    private function get_trp_slugs() {
        $trp_options = get_option('trp_settings');
        
        return $trp_options['url-slugs'];
    }

    public function meta_presenters($presenters) {
        $presenters[] = new MetaYoastPresenter();
        
        return $presenters;
    } 

    public function apply_yoast_filters() {
        $this->apply_yoast_html_filters();

        $this->apply_yoast_og_filters();

        $this->apply_yoast_twitter_filters();
    }

    private function apply_yoast_html_filters() {
        add_filter('wpseo_title', function($title){
            global $propel;

            if (isset($propel['meta']) && isset($propel['meta']['title']))
                $title = strlen($propel['meta']['title']) > 60 ? substr($propel['meta']['title'], 0, 60) : $propel['meta']['title'];

            return $title;
        });

        add_filter('wpseo_metadesc', function($description){
            global $propel;

            if (isset($propel['meta']) && isset($propel['meta']['description']))
                $description = strlen($propel['meta']['description']) > 152 ? substr($propel['meta']['description'], 0, 152) . '...' : $propel['meta']['description'];

            return $description;
        });

        add_filter('wpseo_canonical', function($canonical) {
            global $propel;

            if (isset($propel['meta']) && count($propel['meta']) > 0 && isset($propel['meta']['canonical']))
                $canonical = $propel['meta']['canonical'];
            else if (isset($propel['meta']) && isset($propel['meta']['url']))
                $canonical = $propel['meta']['url'];


            return $canonical;
        });

        // wpseo_primary_term
        // add_filter( 'wpseo_pre_analysis_post_focus_keyword', 'my_custom_focus_keyword', 10, 2 );

        // function my_custom_focus_keyword( $keyword, $post_id ) {
        //     // Example: set a default or dynamically generated keyphrase
        //     return 'best coffee shops in Amsterdam';
        // }

        // add_filter( 'wpseo_save_metabox_data', 'my_custom_focus_keyword_save', 10, 2 );

        // function my_custom_focus_keyword_save( $data, $post_id ) {
        //     $data['focuskw'] = 'new keyphrase';
        //     return $data;
        // }
    }

    private function apply_yoast_og_filters() {
        add_filter('wpseo_opengraph_title', function($value){
            global $propel;

            if (isset($propel['meta']) && isset($propel['meta']['title']))
                $value = strlen($propel['meta']['title']) > 60 ? substr($propel['meta']['title'], 0, 60) : $propel['meta']['title'];

            return $value;
        });

        add_filter('wpseo_opengraph_desc', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['description']))
                $value = strlen($propel['meta']['description']) > 62 ? substr($propel['meta']['description'], 0, 62) . '...' : $propel['meta']['description'];

            return $value;
        });

        add_filter('wpseo_opengraph_url', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['url']))
                return $propel['meta']['url'];

            return $value;
        });

        add_filter('wpseo_opengraph_type', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['type']))
                return $propel['meta']['type'];

            return $value;
        });

        add_filter('wpseo_og_locale', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['locale']))
                return $propel['meta']['locale'];

            return $value;
        });

        add_filter('wpseo_opengraph_site_name', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['locale']))
                return $propel['meta']['locale'];

            return $value;
        });

        add_filter('wpseo_opengraph_image', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['image']))
                return $propel['meta']['image'];

            return $value;
        });

        add_filter('wpseo_opengraph_site_name', function($value){
            return get_bloginfo('name');
        });
    }

    private function apply_yoast_twitter_filters() {
        add_filter('wpseo_twitter_title', function($value){
            global $propel;

            if (isset($propel['meta']) && isset($propel['meta']['title']))
                $value = strlen($propel['meta']['title']) ? substr($propel['meta']['title'], 0, 70) : $propel['meta']['title'];

            return $value;
        });

        add_filter('wpseo_twitter_description', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['description']))
                $value = strlen($propel['meta']['description']) > 197 ? substr($propel['meta']['description'], 0, 197) . '...' : $propel['meta']['description'];

            return $value;
        });

        add_filter('wpseo_twitter_site', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['url']))
                return $propel['meta']['url'];

            return $value;
        });

        add_filter('wpseo_twitter_card_type', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['type']))
                return $propel['meta']['type'];

            return $value;
        });

        add_filter('wpseo_twitter_image', function($value){
            global $propel;
            
            if (isset($propel['meta']) && isset($propel['meta']['image']))
                return $propel['meta']['image'];

            return $value;
        });
    }
}