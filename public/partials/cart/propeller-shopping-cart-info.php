<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (sizeof($obj->get_items())) {
    if ($cart->total->totalNet >= 0) { ?>
        <div class="row align-items-start justify-content-between">
            <div class="col-12 col-sm-6">
                <a href="<?php echo esc_url(home_url()); ?>" class="btn-continue">
                    <?php echo esc_html(__('Continue shopping', 'propeller-ecommerce-v2')); ?>
                </a>
            </div>
        </div>
    <?php } else { ?>
        <svg style="display: none;">
            <symbol viewBox="0 0 64 64" id="shape-checkout-warning">
                <title><?php echo esc_html(__('Warning', 'propeller-ecommerce-v2')); ?></title>
                <path d="M60.329 50.047 36.611 6.752a5.24 5.24 0 0 0-9.222 0L3.671 50.047a5.362 5.362 0 0 0 .09 5.358A5.217 5.217 0 0 0 8.282 58h47.436a5.217 5.217 0 0 0 4.521-2.595 5.362 5.362 0 0 0 .09-5.358ZM34.815 22.041l-.775 19a1 1 0 0 1-.999.959h-2.08a1 1 0 0 1-.999-.959l-.775-19A1 1 0 0 1 30.186 21h3.631a1 1 0 0 1 .999 1.041ZM32 52a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
            </symbol>
        </svg>
        <div class="row align-items-start justify-content-between alert-error">
            <div class="col-12">
                <div class="alert alert-info mt-4" role="alert">
                    <h4 class="alert-heading d-flex justify-content-start align-items-center">
                        <svg class="icon icon-warning" aria-hidden="true">
                            <use xlink:href="#shape-checkout-warning"></use>
                        </svg>
                        <span><?php echo esc_html(__('Something went wrong!', 'propeller-ecommerce-v2')); ?></span>
                    </h4>
                    <?php echo esc_html(__('Please remove your action code if applied or delete your items from the cart in order to proceed to checkout.', 'propeller-ecommerce-v2')); ?>
                </div>
            </div>
        </div>

    <?php }
} else { ?>
    <div class="row">
        <div class="col-12">
            <p><?php echo esc_html(__('Your shopping cart is empty.', 'propeller-ecommerce-v2')); ?></p>
        </div>
    </div>
<?php } ?>
