<?php

namespace Propeller\Meta;

if ( ! defined( 'ABSPATH' ) ) exit;

use Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter;

class MetaYoastPresenter extends Abstract_Indexable_Presenter {
    public function present() {
        global $propel;
        $tags = [];

        if (isset($propel['meta']) && count($propel['meta']) > 0) {
            $tags[] = '<meta property="keywords" content="' . esc_attr($propel['meta']['keywords']) . '" />';
            
            // opengraph
            $tags[] = '<meta property="og:type" content="' . esc_attr($propel['meta']['type']) . '" />';
            $tags[] = '<meta property="og:url" content="' . esc_attr($propel['meta']['url']) . '" />';
            $tags[] = '<meta property="og:title" content="' . esc_attr($propel['meta']['title']) . '" />';
            $tags[] = '<meta property="og:description" content="' . esc_attr($propel['meta']['description']) . '" />';

            if (isset($propel['meta']['image']))
                $tags[] = '<meta property="og:image" content="' . esc_attr($propel['meta']['image']) . '" />';
        
            // twitter
            $tags[] = '<meta name="twitter:card" content="' . esc_attr($propel['meta']['type']) . '" />';
            $tags[] = '<meta name="twitter:site" content="' . esc_attr($propel['meta']['url']) . '" />';
            $tags[] = '<meta name="twitter:title" content="' . esc_attr($propel['meta']['title']) . '" />';
            $tags[] = '<meta name="twitter:description" content="' . esc_attr($propel['meta']['description']) . '" />';

            if (isset($propel['meta']['image']))
                $tags[] = '<meta name="twitter:image" content="' . esc_attr($propel['meta']['image']) . '" />';

            $tags[] = '<!-- Propeller SEO: Yoast -->';
        }

        return implode(PHP_EOL . "\t", $tags);
    }

    public function get() {
        return '';
    }
}