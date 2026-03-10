<?php
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class FlashController {
    public static function add($key, $value) {
        SessionController::set(PROPELLER_FLASH_PREFIX . $key, $value);
    }

    public static function get($key) {
        if (SessionController::has(PROPELLER_FLASH_PREFIX . $key))
            return SessionController::get(PROPELLER_FLASH_PREFIX . $key);
        
        return null;
    }

    public static function remove($key) {
        if (SessionController::has(PROPELLER_FLASH_PREFIX . $key))
            SessionController::remove(PROPELLER_FLASH_PREFIX . $key);
    }

    public static function flash($key) {
        $value = SessionController::get(PROPELLER_FLASH_PREFIX . $key);
        
        self::remove($key);

        return $value;
    }

    public static function all() {
        $flashes = [];

        foreach ($_SESSION as $key => $value) {
            if (strpos($key, PROPELLER_FLASH_PREFIX) !== false) {
                $flashes[$key] = $value;
            }
        }

        return $flashes;
    }

    public static function clear() {
        $flashes = self::all();

        foreach ($flashes as $key => $value) 
            SessionController::remove($key);
    }
}