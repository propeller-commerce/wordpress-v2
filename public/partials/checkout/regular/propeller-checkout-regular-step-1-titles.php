<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;

?>
<div class="row align-items-center">
    <div class="col-6">
        <?php if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) != 'REQUEST') { ?>
            <div class="checkout-step"><?php echo esc_html( __('Step 1', 'propeller-ecommerce-v2') ); ?></div>
        <?php } ?>
        <div class="checkout-title"><?php echo esc_html( __('Invoice details', 'propeller-ecommerce-v2') ); ?></div>
    </div>
    <?php if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) != 'REQUEST') { ?>
        <div class="col-6 d-flex justify-content-end">
            <div class="checkout-step-nr">1/3</div>
        </div>
    <?php } ?>
</div>