<?php

namespace Propeller\Includes\Query;

if ( ! defined( 'ABSPATH' ) ) exit;

class MediaImage {
    static function setSearchOptions($args) {
        $props = [];
        
        if (isset($args['description'])) $props['description'] = $args['description'];
        if (isset($args['tag'])) $props['tag'] = $args['tag'];
        if (isset($args['sort'])) $props['sort'] = $args['sort'];
        if (isset($args['page'])) $props['page'] = $args['page'];
        if (isset($args['offset'])) $props['offset'] = $args['offset'];
        
        return $props;
    }
}