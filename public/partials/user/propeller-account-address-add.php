<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<a class="btn-address-edit address-edit open-edit-modal-form"
    data-form-id="edit_address<?php echo esc_html( $address->id ); ?>"
    data-title="<?php echo esc_attr($title); ?>"
    data-bs-target="#edit_address_modal_<?php echo esc_html( $address->id ); ?>"
    data-type="<?php echo esc_html( $type ); ?>"
    data-bs-toggle="modal"
    role="button">
    <?php echo esc_html($title); ?>
</a>

<?php echo esc_html( apply_filters('propel_address_popup', $address, $type) ); ?>
