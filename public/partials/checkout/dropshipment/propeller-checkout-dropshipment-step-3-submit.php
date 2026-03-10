<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
?>
<div class="row form-group form-group-submit">
    <div class="col-form-fields col-12">
        <div class="row g-3">
            <div class="col-12">
                <?php if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) == 'REQUEST') { ?>
                    <button type="submit" class="btn-proceed btn-green"><?php echo esc_html( __('Quote request overview & payment', 'propeller-ecommerce-v2') ); ?></button>
                <?php } else { ?> 
                    <button type="submit" class="btn-proceed btn-green"><?php echo esc_html( __('Continue', 'propeller-ecommerce-v2') ); ?></button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>