<?php
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class SessionController {
    public static function start() {
        $UserController = new UserController();

        // ini_set('session.cookie_samesite', 'None');
        // ini_set('session.cookie_secure', '1');

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();

            $time = (int) ini_get("session.gc_maxlifetime"); // Set expire time with secends.

            if (isset($_SESSION['timeout']) && (time() - $_SESSION['timeout']) > $time) {
                $UserController->logout(false);

                // exit();
            } else {
                $_SESSION['timeout'] = time();

                // TODO: see if we should really start a session for anonymous users?!?!
                $UserController->start_session();
                
                $UserController->refresh_access_token();

                if (!self::get(PROPELLER_CART)) {
                    self::set(PROPELLER_CART_INITIALIZED, false);
                    self::set(PROPELLER_CART_USER_SET, false);
                }
            }
        }
        else {
            $_SESSION['timeout'] = time();
            
            $UserController->refresh_access_token();
        }
    }

    public static function session_id() {
        return session_id();
    }

    public static function set($var_name, $value) {
        $_SESSION[$var_name] = $value;
    }

    public static function get($var_name) {
        if (!self::has($var_name))
            return null;

        return $_SESSION[$var_name];
    }

    public static function has($var_name) {
        if (isset($_SESSION[$var_name]))
            return true;

        return false;
    }

    public static function remove($var_name) {
        if (isset($_SESSION[$var_name]))
            unset($_SESSION[$var_name]);
    }

    public static function get_user_id() {
        return $_SESSION[PROPELLER_USER_ID];
    }

    public static function get_cart_id() {
        return $_SESSION[PROPELLER_CART_ID];
    }

    public static function remove_session_cookie() {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));        
    }

    public static function regenerate_id($wipe_previous = false) {
        // if (session_status() !== PHP_SESSION_NONE)
            session_regenerate_id($wipe_previous);
    }

    public static function end() {
        // if (session_status() !== PHP_SESSION_NONE) {
            session_unset();
        
            session_destroy();
            // session_write_close();
        // }
    }
}