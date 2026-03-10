<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

use Propeller\Includes\Controller\SessionController;
use Propeller\Propeller as Propeller;

if (wp_doing_ajax()) {
    session_start();

    Propeller::register_pages();
    Propeller::register_settings();
    Propeller::register_behavior();

    if (!defined('PROPELLER_LANG')) {
        if (wp_doing_ajax() && isset($_REQUEST['lang'])) {
            define('PROPELLER_LANG', $_REQUEST['lang']);
        } else if (SessionController::has(PROPELLER_SESSION_LANG)) {
            define('PROPELLER_LANG', SessionController::get(PROPELLER_SESSION_LANG));
        }
    }

    if (!defined('PROPELLER_LANG')) {
        define('PROPELLER_LANG', PROPELLER_FALLBACK_LANG);
    }
}

require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/BaseAjax.php');
require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/ShoppingCart.php');
require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/Category.php');
require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/User.php');
require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/Product.php');
require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/Address.php');
require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/Order.php');
require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/Machine.php');
require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/PriceRequest.php');
require_once(PROPELLER_PLUGIN_DIR . 'includes/Ajax/Favorite.php');

if (defined('PROPELLER_PLUGIN_EXTEND_DIR') && file_exists(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-ajax.php')) {
    require_once(PROPELLER_PLUGIN_EXTEND_DIR . DIRECTORY_SEPARATOR . 'custom-ajax.php');
}
