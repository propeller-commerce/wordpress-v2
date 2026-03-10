<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col-12 col-lg-4 pe-lg-0 order-user-detail-wrapper">
    <div class="order-user-details">
        <div class="row align-items-start">
            <div class="col-12">
                <div class="checkout-title"><?php echo esc_html(__('Billing address', 'propeller-ecommerce-v2')); ?></div>
            </div>
            <div class="col-12 order-address-details">
                <div class="user-delivery-details">
                    <div class="user-fullname">
                        <?php echo esc_html($order->invoiceAddress[0]->company); ?><br>
                        <?php echo esc_html($obj->get_salutation($order->invoiceAddress)); ?>
                        <?php echo esc_html($order->invoiceAddress[0]->firstName); ?> <?php echo esc_html($order->invoiceAddress[0]->middleName); ?> <?php echo esc_html($order->invoiceAddress[0]->lastName); ?>
                    </div>

                    <?php echo esc_html($order->invoiceAddress[0]->street); ?> <?php echo esc_html($order->invoiceAddress[0]->number); ?> <?php echo esc_html($order->invoiceAddress[0]->numberExtension); ?><br>
                    <?php echo esc_html($order->invoiceAddress[0]->postalCode); ?> <?php echo esc_html($order->invoiceAddress[0]->city); ?><br>
                    <?php echo esc_html(!$countries[$order->invoiceAddress[0]->country] ? $order->invoiceAddress[0]->country : $countries[$order->invoiceAddress[0]->country]); ?>
                </div>
            </div>
        </div>
    </div>
</div>