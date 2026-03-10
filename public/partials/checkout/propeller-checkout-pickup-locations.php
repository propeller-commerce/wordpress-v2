<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (isset($obj->warehouses) && isset($obj->warehouses->itemsFound) && $obj->warehouses->itemsFound > 0) {
?>
    <div class="row g-3 delivery-addresses-wrapper">
        <?php
        foreach ($obj->warehouses->items as $warehouse) {
            if (isset($warehouse->address))
                wp_kses_post(apply_filters('propel_checkout_pickup_location', $warehouse));
        }
        ?>
    </div>
    <div class="row g-3 delivery-addresses-wrapper">
        <div class="col-form-fields col-12 col-md-8 orderconfirm-mail">
            <label class="form-label" for="orderconfirm_email"><?php echo esc_html(__('Order confirmation email', 'propeller-ecommerce-v2')); ?></label>
            <input name="orderconfirm_email" value="<?php echo esc_attr($orderconfirm_email); ?>" class="form-control" id="orderconfirm_email" placeholder="<?php echo esc_attr(__('Order confirmation email', 'propeller-ecommerce-v2')); ?>">
        </div>
    </div>
<?php
} else {
    echo esc_html(__('No pickup locations', 'propeller-ecommerce-v2'));
}