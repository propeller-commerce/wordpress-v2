<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (is_array($order->shipments) && count($order->shipments)) { ?>
    <div class="order-details order-shipment-status">
        <div class="row">
            <div class="col-12">
                <h5><?php echo esc_html('Shipping details', 'propeller-ecommerce-v2'); ?></h5>
            </div>
        </div>
        <?php $index = 1;
        foreach ($order->shipments as $shipment) {
        ?>
            <div class="row align-items-baseline">
                <div class="col-6 col-lg-3 col-xl-2 label-title">
                    <?php
                        /* translators: %d: Current index of the displayed shipment */ 
                        echo esc_html(sprintf(__('Shipment %d', 'propeller-ecommerce-v2'), $index)); 
                    ?>
                </div>
                <div class="col-4 order-total">
                    <a href="#" class="order-shipment-details" data-action="view_shipment_details" data-title="<?php 
                        // translators: %d: Index of the shipment
                        echo esc_attr(sprintf(__('Shipment %d', 'propeller-ecommerce-v2'), $index)); 
                        ?>" data-order="<?php echo esc_attr($order->id); ?>" data-shipment="<?php echo esc_attr($shipment->id); ?>">
                        <?php echo esc_html(__('See details', 'propeller-ecommerce-v2')); ?>
                    </a>
                </div>
            </div>
        <?php
            $index++;
        } ?>
    </div>
<?php } ?>
