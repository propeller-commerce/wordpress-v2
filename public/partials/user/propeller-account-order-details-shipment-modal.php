<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="shipment_details" class="propeller-add-to-basket-modal modal fade modal-fullscreen-sm-down" tabindex="-1" role="dialog" aria-labelledby="shipment_details_label">
    <div class="modal-dialog modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_add_favorite" class="modal-title">
                    <div class="modal-title" id="shipment_details_label"></div>
                </div>
                <div class="modal-subtitle ms-auto p-3" id="shipment_details_status"></div>

                <button type="button" class="close ms-0" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                    <span aria-hidden="true">
                        <svg class="icon icon-close">
                            <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="modal-body propel-modal-body propel-shipment-modal-body"></div>
        </div>
    </div>
</div>