<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\UserController;
?>
<svg style="display: none;">
    <symbol viewBox="0 0 64 64" id="shape-checkout-warning">
        <title><?php echo esc_html(__('Warning', 'propeller-ecommerce-v2')); ?></title>
        <path d="M60.329 50.047 36.611 6.752a5.24 5.24 0 0 0-9.222 0L3.671 50.047a5.362 5.362 0 0 0 .09 5.358A5.217 5.217 0 0 0 8.282 58h47.436a5.217 5.217 0 0 0 4.521-2.595 5.362 5.362 0 0 0 .09-5.358ZM34.815 22.041l-.775 19a1 1 0 0 1-.999.959h-2.08a1 1 0 0 1-.999-.959l-.775-19A1 1 0 0 1 30.186 21h3.631a1 1 0 0 1 .999 1.041ZM32 52a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
    </symbol>
</svg>
<div class="row form-group form-group-submit">
    <div class="col-form-fields col-12">
        <div class="row g-3">
            <div class="col-12">
                <?php if (empty($cart->deliveryAddress->street) && !UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS) { ?>
                    <div class="alert alert-info mt-4" role="alert">
                        <h4 class="alert-heading d-flex justify-content-start align-items-center">
                            <svg class="icon icon-warning" aria-hidden="true">
                                <use xlink:href="#shape-checkout-warning"></use>
                            </svg>
                            <span><?php echo esc_html(__('Delivery address', 'propeller-ecommerce-v2')); ?></span>
                        </h4>
                        <?php echo esc_html(__('Please fill in your delivery address in order to continue', 'propeller-ecommerce-v2')); ?>
                    </div>
                <?php } else { ?>
                    <button type="submit" class="btn-proceed btn-green"><?php echo esc_html(__('Continue', 'propeller-ecommerce-v2')); ?></button>
                <?php } ?>

                <?php /* if (!empty($cart->deliveryAddress->street)) { ?>
                    <button type="submit" class="btn-proceed btn-green"><?php echo esc_html( __('Continue', 'propeller-ecommerce-v2') ); ?></button>
                <?php } else { ?>
                    <div class="alert alert-info mt-4" role="alert">
                        <h4 class="alert-heading d-flex justify-content-start align-items-center">
                            <svg class="icon icon-warning" aria-hidden="true">
                                <use xlink:href="#shape-checkout-warning"></use>
                            </svg>
                            <span><?php echo esc_html( __('Delivery address', 'propeller-ecommerce-v2') ); ?></span>
                        </h4>
                        <?php echo esc_html( __('Please fill in your delivery address in order to continue', 'propeller-ecommerce-v2') ); ?>
                    </div>
                <?php } */ ?>
            </div>
        </div>
    </div>
</div>