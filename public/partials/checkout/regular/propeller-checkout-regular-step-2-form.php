<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\AddressTypeCart;

?>

<form name="checkout-delivery" class="form-handler checkout-form validate" method="post">
    <?php if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) == 'REQUEST') { ?>
        <input type="hidden" name="action" value="cart_step_3" />
        <input type="hidden" name="step" value="<?php echo esc_attr($slug); ?>" />
        <input type="hidden" name="next_step" value="summary" />
    <?php } else { ?>
        <input type="hidden" name="action" value="cart_step_2" />
        <input type="hidden" name="step" value="<?php echo esc_attr($slug); ?>" />
        <input type="hidden" name="next_step" value="3" />
    <?php } ?>

    <input type="hidden" name="type" value="<?php echo esc_attr( AddressTypeCart::DELIVERY ); ?>" />
    <input type="hidden" name="phone" value="none-provided" />

    <?php echo wp_kses_post( apply_filters('propel_checkout_delivery_addresses', $cart, $obj) ); ?>
    <?php if (!PROPELLER_SELECTABLE_CARRIERS)
        echo wp_kses_post(apply_filters('propel_checkout_shipping_method', $cart, $obj));
    ?>

    <?php echo wp_kses_post( apply_filters('propel_checkout_regular_step_2_submit', $cart, $obj) ); ?>

</form>