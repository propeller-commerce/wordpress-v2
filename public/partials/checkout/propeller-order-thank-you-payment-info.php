<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ($order->status != 'REQUEST') { ?>
    <div class="col-12 col-lg-4 ps-lg-0 order-user-detail-wrapper">
        <div class="order-user-details">
            <?php if (!empty($order->paymentData->method)) { ?>
                <div class="row align-items-start">
                    <div class="col-12">
                        <div class="checkout-title"><?php echo esc_html(__('Payment method', 'propeller-ecommerce-v2')); ?></div>
                    </div>
                    <div class="col-12">
                        <div class="paymethod-details">
                            <?php
                            $paymethods_array = new \WP_Query(array(
                                'post_type' => 'paymethods'
                            ));

                            $matched = false;

                            foreach ($paymethods_array->posts as $post) {
                                if ($order->paymentData->method === $post->post_title) {
                                    echo esc_html($post->post_excerpt);
                                    $matched = true;
                                    break;
                                }
                            }

                            if (!$matched) {
                                echo esc_html($order->paymentData->method);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (PROPELLER_PARTIAL_DELIVERY) { ?>
                <div class="row align-items-start mt-4">
                    <div class="col-12">
                        <div class="checkout-title"><?php echo esc_html(__('Partial delivery', 'propeller-ecommerce-v2')); ?></div>
                    </div>
                    <div class="col-12">
                        <div class="paymethod-details">
                            <?php if ($order->postageData->partialDeliveryAllowed == 'N')
                                echo esc_html(__("I'd like to receive all products at once.", 'propeller-ecommerce-v2'));
                            else
                                echo esc_html(__("I would like to receive the available products as soon as possible, the other products will be delivered later on.", 'propeller-ecommerce-v2'));
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (PROPELLER_SELECTABLE_CARRIERS) {
                if (!empty($order->postageData->carrier)) { ?>
                    <div class="row align-items-start mt-4">
                        <div class="col-12">
                            <div class="checkout-title"><?php echo esc_html(__('Carriers', 'propeller-ecommerce-v2')); ?></div>
                        </div>
                        <div class="col-12">
                            <div class="paymethod-details">
                                <?php echo esc_html($order->postageData->carrier); ?>
                            </div>
                        </div>
                    </div>
            <?php }
            } ?>
            <?php if (PROPELLER_USE_DATEPICKER) {
                if (!empty($order->postageData->requestDate)) { ?>
                    <div class="row align-items-start mt-4">
                        <div class="col-12">
                            <div class="checkout-title"><?php echo esc_html(__('Preferred delivery date', 'propeller-ecommerce-v2')); ?></div>
                        </div>
                        <div class="col-12">
                            <div class="paymethod-details">
                                <?php
                                echo esc_html(gmdate("d-m-Y", strtotime($order->postageData->requestDate)));
                                ?>
                            </div>
                        </div>
                    </div>
            <?php }
            } ?>
        </div>
    </div>
<?php } ?>
