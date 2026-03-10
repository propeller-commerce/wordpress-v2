<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\UserController;

$is_anonymous = !UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS ? true : false;

if ($is_anonymous)
    $delivery_address->id = '_guest';

$modal_title = '';

if (!empty($cart->delivery_address->street))
    $modal_title = __('Add delivery address', 'propeller-ecommerce-v2');
else
    $modal_title = __('Change delivery address', 'propeller-ecommerce-v2');

?>
<div class="col-auto">
    <a class="btn-address-add address-edit open-edit-modal-form" 
        data-form-id="edit_address<?php echo esc_attr($delivery_address->id); ?>" 
        data-title="<?php echo esc_attr($modal_title); ?>" 
        data-bs-target="#edit_address_modal_<?php echo esc_attr($delivery_address->id); ?>" 
        data-bs-toggle="modal" 
        role="button">
        
        <?php
        if (empty($delivery_address->street))
            echo esc_html(__('Add new delivery address', 'propeller-ecommerce-v2'));
        else
            echo esc_html(__('Change delivery address', 'propeller-ecommerce-v2'));
        ?>

    </a>
</div>