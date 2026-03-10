<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;

    $delivery_address = $warehouse->address;

    $checked = '';
    $checked_label = '';

    if (SessionController::has(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED) && SessionController::get(PROPELLER_DEFAULT_DELIVERY_ADDRESS_CHANGED) == $delivery_address->id ?? 0) {
        $checked = 'checked="checked"';
        $checked_label = 'selected';
    }
?>
<div class="col-12 col-md-6 form-group form-check delivery-address-block ">
    <label class="delivery-label form-check-label <?php echo esc_html($checked_label); ?>">
        <input type="radio" class="form-check-input delivery-addresses" name="delivery_address" value="<?php echo esc_html($delivery_address->id ?? 0); ?>" <?php echo esc_html($checked); ?>> 
        
        <div class="label-delivery-address">
            <?php echo esc_html($warehouse->name ?? ""); ?><br>
            <?php echo esc_html($warehouse->description ?? ""); ?><br />
           <br>
            <?php echo esc_html($delivery_address->number ?? ""); ?> <?php echo esc_html($delivery_address->street ?? ""); ?> <?php echo esc_html($delivery_address->numberExtension ?? ""); ?><br> 
            <?php echo esc_html($delivery_address->postalCode ?? ""); ?> <?php echo esc_html($delivery_address->city ?? ""); ?><br>
            <?php echo esc_html($countries[$delivery_address->country ?? "NL"]); ?><br>
            <?php echo esc_html((isset($delivery_address->email) && !empty($delivery_address->email)) ? $delivery_address->email : ''); ?>
        </div>
        
    </label>
</div>