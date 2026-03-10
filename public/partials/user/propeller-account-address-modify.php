<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<a class="address-edit open-modal-form d-flex"
    data-form-id="edit_address<?php echo esc_attr($address->id); ?>"
    data-title="<?php echo esc_attr(__('Modify', 'propeller-ecommerce-v2')); ?>"
    data-bs-target="#edit_address_modal_<?php echo esc_attr($address->id); ?>"
    data-bs-toggle="modal"
    role="button">
    <?php echo esc_html(__('Modify', 'propeller-ecommerce-v2')); ?>
</a>
<?php apply_filters('propel_address_popup', $address, esc_attr($address->type)); ?>
