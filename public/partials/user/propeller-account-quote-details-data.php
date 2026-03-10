<?php
if (! defined('ABSPATH')) exit;

use Propeller\PropellerHelper;

?>
<div class="order-details">
    <div class="row align-items-end">
        <div class="col-12">
            <div class="order-total-details">
                <div class="row align-items-baseline">
                    <div class="col-4 col-md-2"><?php echo esc_html(__('Quote date:', 'propeller-ecommerce-v2')); ?></div>
                    <div class="col-8 col-md-10 order-date">
                        <?php echo esc_html(gmdate("d-m-Y", strtotime($order->createdAt))); ?>
                    </div>
                </div>
                <?php if (!empty($order->validUntil)) { ?>
                    <div class="row align-items-baseline">
                        <div class="col-4 col-md-2"><?php echo esc_html(__('Valid until:', 'propeller-ecommerce-v2')); ?></div>
                        <div class="col-8 col-md-10 order-date">
                            <span class="local-date" data-utc="<?php echo esc_attr($order->validUntil); ?>">
                                <?php echo esc_html(gmdate("d-m-Y", strtotime($order->validUntil))); ?>
                            </span>
                        </div>
                    </div>
                <?php } ?>
                <div class="row align-items-baseline">
                    <div class="col-4 col-md-2"><?php echo esc_html(__('Total:', 'propeller-ecommerce-v2')); ?></div>
                    <div class="col-8 col-md-10 order-total">
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="order-total-subtotal"><?php echo esc_html(PropellerHelper::formatPrice($order->total->net)); ?></span>
                    </div>
                </div>
                <?php if (!empty($order->remarks)) { ?>
                    <div class="row align-items-baseline">
                        <div class="col-4 col-md-2"><?php echo esc_html(__('Remarks:', 'propeller-ecommerce-v2')); ?></div>
                        <div class="col-12 col-md-10 col-xl-10"><?php echo esc_html($order->remarks); ?></span></div>
                    </div>
                <?php } ?>
                <?php if (!empty($order->reference)) { ?>
                    <div class="row align-items-baseline">
                        <div class="col-4 col-md-2"><?php echo esc_html(__('Reference:', 'propeller-ecommerce-v2')); ?></div>
                        <div class="col-12 col-md-10 col-xl-10"><?php echo esc_html($order->reference); ?></span></div>
                    </div>
                <?php } ?>
                <?php if ($order->paymentData->gross) { ?>
                    <div class="row align-items-baseline">
                        <div class="col-4 col-lg-2"><?php echo esc_html(__('Transaction costs', 'propeller-ecommerce-v2')); ?>:</div>
                        <div class="col-8 col-md-10 order-paymethod">
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
                            <span class="symbol"> - <?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="order-total-subtotal"><?php echo esc_html(PropellerHelper::formatPrice($order->paymentData->gross)); ?></span>

                        </div>
                    </div>
                <?php } ?>
                <?php if ($order->postageData->gross) { ?>
                    <div class="row align-items-baseline">
                        <div class="col-4 col-lg-2"><?php echo esc_html(__('Shipping costs', 'propeller-ecommerce-v2')); ?>:</div>
                        <div class="col-8 col-md-10 order-paymethod">
                            <?php if (!empty($order->postageData->carrier)) echo esc_html($order->postageData->carrier);
                            else echo esc_html($order->postageData->method) ?>
                            <span class="symbol"> - <?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="order-total-subtotal"><?php echo esc_html(PropellerHelper::formatPrice($order->postageData->gross)); ?></span>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>