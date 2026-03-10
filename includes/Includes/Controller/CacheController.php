<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class CacheController {
    const PROPELLER_MENU_TRANSIENT = 'propeller_menu';

    public static function get($cache_key) {
        if (false === ($cached = get_transient($cache_key)))
            return false;    

        return $cached;
    }

    public static function set($cache_key, $value, $expiration = PROPELLER_TRANSIENT_EXPIRATION) {
        if (false === get_transient($cache_key))
            $set = set_transient($cache_key, $value, $expiration);
    }

    public static function delete($cache_key) {
        delete_transient($cache_key);
    }

    public static function has($cache_key) {
        return !(false === get_transient($cache_key));
    }
}