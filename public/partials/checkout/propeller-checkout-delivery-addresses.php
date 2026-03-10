<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\OrderType;

?>
<fieldset>
    <div class="row form-group">
        <div class="col-form-fields col-12">

            <input type="hidden" name="method" id="shipping_method" value="<?php echo esc_attr($cart->postageData->method); ?>" />

            <ul class="nav nav-pills nav-fill mb-2" id="delivery-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link propel-delivery-options <?php echo esc_html( $cart->postageData->method == $obj->get_order_types()->items[OrderType::REGULAR]->value ? 'active' : '' ); ?>" data-method="<?php echo esc_attr($obj->get_order_types()->items[OrderType::REGULAR]->value); ?>" id="delivery-tab" data-bs-toggle="pill" data-bs-target="#delivery" type="button" role="tab" aria-controls="delivery" aria-selected="<?php echo esc_html( $cart->postageData->method == $obj->get_order_types()->items[OrderType::REGULAR]->value ? 'true' : 'false' ); ?>">
                        <?php if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) != 'REQUEST') echo esc_html(__('Please deliver my order', 'propeller-ecommerce-v2'));
                        else echo esc_html(__('Please deliver my quote', 'propeller-ecommerce-v2')); ?>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link propel-delivery-options <?php echo esc_attr($cart->postageData->method == $obj->get_order_types()->items[OrderType::PICKUP]->value ? 'active' : ''); ?>" data-method="<?php echo esc_attr($obj->get_order_types()->items[OrderType::PICKUP]->value); ?>" id="pickup-tab" data-bs-toggle="pill" data-bs-target="#pickup" type="button" role="tab" aria-controls="pickup" aria-selected="<?php echo esc_attr($cart->postageData->method == $obj->get_order_types()->items[OrderType::PICKUP]->value ? 'true' : 'false'); ?>">
                        <?php if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) != 'REQUEST') echo esc_html(__('Let me pick up my order', 'propeller-ecommerce-v2'));
                        else echo esc_html(__('Let me pick up my quote', 'propeller-ecommerce-v2'));  ?>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="delivery-tabContent">
                <div class="tab-pane fade <?php echo esc_attr($cart->postageData->method == $obj->get_order_types()->items[OrderType::REGULAR]->value ? 'show active' : ''); ?>" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">
                    <div class="row g-3 delivery-addresses-wrapper">
                        <?php                        
                            foreach ($delivery_addresses as $delivery_address) {
                                if (!empty($delivery_address->street))
                                    apply_filters('propel_checkout_delivery_address', $delivery_address, $cart, $obj);
                            }
                        ?>
                    </div>
                    <div class="row">
                        <?php apply_filters('propel_checkout_delivery_address_new', $cart, $obj); ?>
                    </div>
                </div>
                <div class="tab-pane fade <?php echo esc_attr($cart->postageData->method == $obj->get_order_types()->items[OrderType::PICKUP]->value ? 'show active' : ''); ?>" id="pickup" role="tabpanel" aria-labelledby="pickup-tab">
                    <?php apply_filters('propel_checkout_pickup_locations', $cart, $obj); ?>
                </div>
            </div>
        </div>
    </div>
</fieldset>