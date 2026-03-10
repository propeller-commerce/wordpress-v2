<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<a class="btn-return return-request open-edit-modal-form" 
    data-form-id="return_order<?php echo esc_attr($order->id); ?>"
    data-title="<?php echo esc_attr( __('Return request', 'propeller-ecommerce-v2') ); ?>"
    data-bs-target="#return_modal_<?php echo esc_attr($order->id); ?>"
    data-bs-toggle="modal"
    role="button">
    <?php echo esc_html( __('Return request', 'propeller-ecommerce-v2') ); ?>
</a>

<?php echo esc_html( apply_filters('propel_order_details_returns_form', $order) ); ?>
