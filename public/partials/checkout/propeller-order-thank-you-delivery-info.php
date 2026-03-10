<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col-12 col-lg-4 p-lg-0 order-user-detail-wrapper">
    <div class="order-user-details">
        <div class="row align-items-start">
            <div class="col-12">
                <div class="checkout-title"><?php echo esc_html(__('Delivery address', 'propeller-ecommerce-v2')); ?></div>
            </div>
            <div class="col-12 order-invoice-details">
                <div class="user-invoice-details">
                    <div class="user-fullname">
                        <?php echo esc_html($order->deliveryAddress[0]->company); ?><br>
                        <?php echo esc_html($obj->get_salutation($order->deliveryAddress[0])); ?>
                        <?php echo esc_html($order->deliveryAddress[0]->firstName); ?> <?php echo esc_html($order->deliveryAddress[0]->middleName); ?> <?php echo esc_html($order->deliveryAddress[0]->lastName); ?>
                    </div>

                    <?php echo esc_html($order->deliveryAddress[0]->street); ?> <?php echo esc_html($order->deliveryAddress[0]->number); ?> <?php echo esc_html($order->deliveryAddress[0]->numberExtension); ?><br>
                    <?php echo esc_html($order->deliveryAddress[0]->postalCode); ?> <?php echo esc_html($order->deliveryAddress[0]->city); ?><br>
                    <?php echo esc_html(!$countries[$order->deliveryAddress[0]->country] ? $order->deliveryAddress[0]->country : $countries[$order->deliveryAddress[0]->country]); ?>
                </div>

                <?php echo esc_html(wp_kses_post(apply_filters('propel_order_thank_you_shipping_info', $order, $this))); ?>

            </div>
        </div>
    </div>
</div>