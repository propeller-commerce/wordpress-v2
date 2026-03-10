<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;

?>

<div class="checkout-wrapper-steps">
    <div class="row align-items-center">
        <div class="col-6">
            <div class="checkout-step"><?php echo esc_html(__('Step 2', 'propeller-ecommerce-v2')); ?></div>
            <div class="checkout-title"><?php echo esc_html(__('Delivery details', 'propeller-ecommerce-v2')); ?></div>
        </div>

        <div class="col-6 d-flex justify-content-end">
            <div class="checkout-step-nr">2/3</div>
        </div>
    </div>
    <!-- <div class="row align-items-center">
        <?php /*apply_filters('propel_checkout_delivery_address', $cart->deliveryAddress, $cart, $obj); */ ?>
    </div> -->
</div>
<?php if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) != 'REQUEST') { ?>
    <div class="checkout-wrapper-steps">
        <div class="row align-items-center">
            <div class="col-6">
                <div class="checkout-step"><?php echo esc_html(__('Step 3', 'propeller-ecommerce-v2')); ?></div>
                <div class="checkout-title"><?php echo esc_html(__('Payment details', 'propeller-ecommerce-v2')); ?></div>
            </div>
            <div class="col-6 d-flex justify-content-end">
                <div class="checkout-step-nr">3/3</div>
            </div>
        </div>
    </div>
<?php } ?>
