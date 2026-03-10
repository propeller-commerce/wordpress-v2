<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\AddressType;

?>
<form name="checkout" class="form-handler checkout-form validate" method="post" action="">

    <input type="hidden" name="action" value="cart_step_1" />
    <input type="hidden" name="step" value="<?php echo esc_attr($slug); ?>" />
    <input type="hidden" name="next_step" value="<?php echo esc_attr(!empty($cart->deliveryAddress->street) ? 3 : 2); ?>" />

    <input type="hidden" name="type" value="<?php echo esc_attr(AddressType::INVOICE); ?>" />

    <div class="row form-group form-group-submit">
        <div class="col-form-fields col-12">
            <div class="row g-3">
                <div class="col-12 col-md-10">
                    <?php if (!empty($cart->invoiceAddress->street)) { ?>
                        <button type="submit" class="btn-proceed"><?php echo esc_html(__('Continue', 'propeller-ecommerce-v2')); ?></button>
                    <?php } else { ?>
                        <div class="btn-proceed"><?php echo esc_html(__('Please fill in your invoice address in order to continue', 'propeller-ecommerce-v2')); ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</form>