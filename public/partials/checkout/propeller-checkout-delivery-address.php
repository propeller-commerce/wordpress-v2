<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
    use Propeller\Includes\Controller\UserController;

    $checked = '';
    $checked_label = '';

    if (SessionController::has(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED) && SessionController::get(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED) == $delivery_address->id) {
        $checked = 'checked="checked"';
        $checked_label = 'selected';
    }
    else if (isset($delivery_address->isDefault) && $delivery_address->isDefault == 'Y' && !SessionController::get(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED)) {
        $checked = 'checked="checked"';
        $checked_label = 'selected';
    } else if (!UserController::is_propeller_logged_in()) {
        $checked = 'checked="checked"';
        $checked_label = 'selected';
    }
?>
<div class="col-12 col-md-6 form-group form-check delivery-address-block ">
    <label class="delivery-label form-check-label <?php echo esc_attr($checked_label); ?> <?php echo esc_html( isset($delivery_address->isDefault) && $delivery_address->isDefault == 'Y' ? 'is_default' : '' ); ?>">
        <input type="radio" class="form-check-input delivery-addresses required" name="delivery_address" value="<?php echo esc_attr($delivery_address->id); ?>" <?php echo esc_attr((string) $checked); ?>>
    
        <div class="label-delivery-address">
            <?php echo esc_html($delivery_address->company ); ?><br>
            <?php echo esc_html($delivery_address->firstName); ?> <?php echo esc_html($delivery_address->middleName); ?> <?php echo esc_html($delivery_address->lastName); ?><br>
            <?php echo esc_html($delivery_address->street); ?> <?php echo esc_html($delivery_address->number); ?> <?php echo esc_html($delivery_address->numberExtension); ?><br>
            <?php echo esc_html($delivery_address->postalCode); ?> <?php echo esc_html($delivery_address->city); ?><br>
            <?php echo esc_html($countries[$delivery_address->country]); ?><br>
            <?php echo esc_html($delivery_address->email); ?>
        </div>
        
        <?php if (PROPELLER_EDIT_ADDRESSES) { ?>
            <a class="btn-address-edit address-edit open-edit-modal-form" 
                data-form-id="edit_address<?php echo esc_attr($delivery_address->id); ?>"
                data-title="<?php echo esc_html(__('Edit delivery address', 'propeller-ecommerce-v2')); ?>"
                data-bs-target="#edit_address_modal_<?php echo esc_attr($delivery_address->id); ?>"
                data-bs-toggle="modal"
                role="button">
                <?php echo esc_html(__('Edit', 'propeller-ecommerce-v2')); ?>
            </a>
        <?php } ?>
    </label>
</div>