<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
    use Propeller\Includes\Controller\UserController;

    $checked = '';
    $checked_label = '';

    if (SessionController::has(PROPELLER_DEFAULT_INVOICE_ADDRESS_CHANGED) && SessionController::get(PROPELLER_DEFAULT_INVOICE_ADDRESS_CHANGED) == $invoice_address->id) {
        $checked = 'checked="checked"';
        $checked_label = 'selected';
    }
    else if (isset($invoice_address->isDefault) && $invoice_address->isDefault == 'Y' && !SessionController::get(PROPELLER_DEFAULT_INVOICE_ADDRESS_CHANGED)) {
        $checked = 'checked="checked"';
        $checked_label = 'selected';
    } else if (!UserController::is_propeller_logged_in()) {
        $checked = 'checked="checked"';
        $checked_label = 'selected';
    }
?>
<div class="col-12 col-md-6 form-group form-check invoice-address-block ">
    <label class="invoice-label form-check-label <?php echo esc_attr($checked_label); ?> <?php echo esc_html( isset($invoice_address->isDefault) && $invoice_address->isDefault == 'Y' ? 'is_default' : '' ); ?>">
        <input type="radio" class="form-check-input invoice-addresses required" name="invoice_address" value="<?php echo esc_attr($invoice_address->id); ?>" <?php echo esc_attr((string) $checked); ?>>
    
        <div class="label-invoice-address">
            <?php echo esc_html($invoice_address->company ); ?><br>
            <?php echo esc_html($invoice_address->firstName); ?> <?php echo esc_html($invoice_address->middleName); ?> <?php echo esc_html($invoice_address->lastName); ?><br>
            <?php echo esc_html($invoice_address->street); ?> <?php echo esc_html($invoice_address->number); ?> <?php echo esc_html($invoice_address->numberExtension); ?><br>
            <?php echo esc_html($invoice_address->postalCode); ?> <?php echo esc_html($invoice_address->city); ?><br>
            <?php echo esc_html($countries[$invoice_address->country]); ?><br>
            <?php echo esc_html($invoice_address->email); ?>
        </div>
        
        <?php if (PROPELLER_EDIT_ADDRESSES) { ?>
            <a class="btn-address-edit address-edit open-edit-modal-form" 
                data-form-id="edit_address<?php echo esc_attr($invoice_address->id); ?>"
                data-title="<?php echo esc_html(__('Edit invoice address', 'propeller-ecommerce-v2')); ?>"
                data-bs-target="#edit_address_modal_<?php echo esc_attr($invoice_address->id); ?>"
                data-bs-toggle="modal"
                role="button">
                <?php echo esc_html(__('Edit', 'propeller-ecommerce-v2')); ?>
            </a>
        <?php } ?>
    </label>
</div>