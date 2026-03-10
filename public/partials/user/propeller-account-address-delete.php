<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<a class="address-delete open-modal-form" 
    data-form-id="delete_address<?php echo esc_attr($address->id); ?>"
    data-title="<?php echo esc_attr( __('Delete', 'propeller-ecommerce-v2') ); ?>"
    data-bs-target="#delete_address_modal_<?php echo esc_attr($address->id); ?>"
    data-bs-toggle="modal">
    <?php echo esc_html( __('Delete', 'propeller-ecommerce-v2') ); ?>
</a>

<?php echo esc_html( apply_filters('propel_address_delete_popup', $address) ); ?>
