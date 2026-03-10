<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

$dhl_track_trace_url = 'https://my.dhlparcel.nl/home/tracktrace/';

?>

<div class="order-details">
    <div class="row align-items-start">
        <div class="col-12 order-1">
            <div class="order-total-details">
                <div class="row align-items-baseline">
                    <div class="col-4 col-lg-2 label-title"><?php echo esc_html(__('Order date:', 'propeller-ecommerce-v2')); ?></div>
                    <div class="col-4 order-date">
                        <?php echo esc_html(gmdate("d-m-Y", strtotime($order->createdAt))); ?>
                    </div>
                    <div class="col-4 col-lg-4 ms-auto order-shippment-links order-3 order-lg-2">
                        <?php if (!empty($order->status)) { ?>
                            <div class="order-shipment-status d-flex justify-content-end">
                                <span class="shipment-<?php echo esc_html($order->status); ?>">
                                    <?php
                                    $orderstatus_array = new \WP_Query(array(
                                        'post_type' => 'orderstatuses'
                                    ));

                                    $matched = false;

                                    foreach ($orderstatus_array->posts as $post) {
                                        if ($order->status === $post->post_title) {
                                            echo esc_html($post->post_excerpt);
                                            $matched = true;
                                            break;
                                        }
                                    }

                                    if (!$matched) {
                                        echo esc_html($order->status);
                                    }
                                    ?>

                                </span>
                            </div>
                        <?php } ?>

                    </div>
                </div>
                <div class="row align-items-baseline">
                    <div class="col-4 col-lg-2 label-title"><?php echo esc_html(__('Total:', 'propeller-ecommerce-v2')); ?></div>
                    <div class="col-4 order-total">
                        <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><span class="order-total-subtotal"><?php echo esc_html(PropellerHelper::formatPrice($order->total->net)); ?></span>
                    </div>
                </div>
                <div class="row align-items-baseline">
                    <div class="col-4 col-lg-2 label-title"><?php echo esc_html(__('Payment method:', 'propeller-ecommerce-v2')); ?></div>
                    <div class="col-4 order-paymethod">
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
                <?php /* if (count($order->shipments)) {
                    foreach ($order->shipments as $shipment) {
                        if (is_array($shipment->trackAndTrace) && count($shipment->trackAndTrace)) {
                ?>
                            <?php foreach ($shipment->trackAndTrace as $trace_code) {
                                if ($trace_code->carrierId == 0)
                                    continue;
                            ?>
                                <div class="row align-items-baseline">
                                    <div class="col-6 col-lg-5 col-xl-4 label-title">
                                        <?php echo esc_html( __('Track and trace:', 'propeller-ecommerce-v2') ); ?>
                                    </div>
                                    <div class="col-4 order-shippingmethod">
                                        <a href="<?php echo esc_url( $dhl_track_trace_url . $trace_code->code ); ?>" target="_blank">
                                            <?php echo esc_html( $trace_code->code ); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                <?php }
                    }
                } */ ?>

                <!-- <div class="row align-items-baseline">
                        <div class="col-6 col-lg-5 col-xl-4 label-title"><?php echo esc_html(__('Shipping method:', 'propeller-ecommerce-v2')); ?></div>
                        <div class="col-4 order-shippingmethod">
                        Post-NL
                        </div>
                    </div> -->

            </div>

        </div>

        <?php if (!empty($order->remarks)) { ?>
            <div class="col-12 order-lg-3 order-2">
                <div class="row align-items-baseline">
                    <div class="col-6 col-md-2 label-title"><?php echo esc_html(__('Remarks', 'propeller-ecommerce-v2')); ?>:</div>
                    <div class="col-12 col-md-10 order-remarks">
                        <?php echo esc_html($order->remarks); ?>
                    </div>
                </div>

            </div>
        <?php } ?>
        <?php if (!empty($order->reference)) { ?>
            <div class="col-12 order-lg-3 order-2">
                <div class="row align-items-baseline">
                    <div class="col-6 col-md-2 label-title"><?php echo esc_html(__('References', 'propeller-ecommerce-v2')); ?>:</div>
                    <div class="col-12 col-md-10 order-remarks">
                        <?php echo esc_html($order->reference); ?>
                    </div>
                </div>

            </div>
        <?php } ?>
        <div class="col-12 order-4">
            <div class="order-links text-lg-end">

                <?php echo esc_html(apply_filters('propel_order_details_pdf', $order)); ?>

                <?php echo esc_html(apply_filters('propel_order_details_returns', $order)); ?>

                <?php echo esc_html(apply_filters('propel_order_details_reorder', $order)); ?>

            </div>
        </div>

    </div>
    <?php echo esc_html(apply_filters('propel_order_details_attachments', $order, $this)); ?>
</div>