<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

?>
<div class="row order-item purchase-authorization-item g-0" data-cart="<?php echo esc_attr($purchase_authorization->cartId); ?>">
    <div class="col-md-1 col-xl-1">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('#', 'propeller-ecommerce-v2')); ?></span>
        <span class="px-0 col-auto">#<?php // echo esc_html($purchase_authorization->id); 
                                        ?></span>
    </div>
    <div class="col-md-3">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Date', 'propeller-ecommerce-v2')); ?></span>
        <span class="px-0 col-auto"><?php echo esc_html(gmdate("d-m-Y", strtotime($purchase_authorization->lastModifiedAt))); ?></span>
    </div>
    <div class="col-md-1 col-xl-1">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Qty', 'propeller-ecommerce-v2')); ?></span>
        <span class="px-0 col-auto"><?php echo esc_html(sizeof($purchase_authorization->items)); ?>
    </div>
    <div class="col-md-2">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Request total', 'propeller-ecommerce-v2')); ?></span>
        <span class="px-0 col-auto">
            <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
            <?php echo esc_html(PropellerHelper::formatPrice($purchase_authorization->total->totalNet)); ?></span>
        </span>
    </div>
    <div class="col-md-2">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Requested by', 'propeller-ecommerce-v2')); ?></span>
        <span class="px-0 col-auto">
            <?php echo esc_html(sprintf('%s %s %s', $purchase_authorization->contact->firstName, $purchase_authorization->contact->middleName, $purchase_authorization->contact->lastName)); ?>
        </span>
    </div>
    <div class="col-md-3 text-md-end">
        <a href="#" data-bs-toggle="modal" data-bs-target="#purchase_authorization_preview_modal" data-cart="<?php echo esc_attr($purchase_authorization->cartId); ?>" class="purchase-authorization-details-link">
            <?php echo esc_html(__('View authorization request', 'propeller-ecommerce-v2')); ?>
        </a>
    </div>
</div>