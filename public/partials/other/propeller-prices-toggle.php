<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;

$switch = (SessionController::get(PROPELLER_SPECIFIC_PRICES) ? 'on' : 'off');

?>
<div class="propeller-price-toggle-wrapper">
    <div class="price-toggle price-<?php echo esc_attr($switch); ?>">
        <a class="toggle-link d-flex align-items-center justify-content-between justify-content-md-end" rel="nofollow">
            <span class="toggle">
            </span>
            <span class="toggle-label label-off"><?php echo esc_html( __('Excl. VAT', 'propeller-ecommerce-v2') ); ?></span>
            <span class="toggle-label label-on"><?php echo esc_html( __('Incl. VAT', 'propeller-ecommerce-v2') ); ?></span>
        </a>
    </div>
</div>