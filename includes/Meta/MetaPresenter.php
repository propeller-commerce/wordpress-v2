<?php

namespace Propeller\Meta;

if ( ! defined( 'ABSPATH' ) ) exit;

class MetaPresenter {
    public function __construct() {
        add_action('wp_head', [$this, 'present'], 1, 1);
    }

    public function present() {
        global $propel;
        $tags = [];

        if (isset($propel['meta']) && count($propel['meta']) > 0) {
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

            $tags[] = '<!-- Propeller SEO -->';
        }

        echo wp_kses_post(implode(PHP_EOL . "\t", $tags) . PHP_EOL);
    }
}