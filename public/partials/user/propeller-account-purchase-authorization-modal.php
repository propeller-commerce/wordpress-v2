<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="propeller-address-modal propel-authorization-modal modal fade modal-fullscreen-sm-down" id="purchase_authorization_preview_modal" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="purchase_authorization_preview_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header propel-modal-header">
        <h5 class="modal-title" id="purchase_authorization_preview_modal_label">
          <?php echo esc_html(__('Preview authorization request', 'propeller-ecommerce-v2')); ?>
        </h5>
        <div class="pa-loader" style="width: 30px; height: 30px;">&nbsp;</div>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
          <span aria-hidden="true">
            <svg class="icon icon-close">
              <use class="header-shape-close" xlink:href="#shape-header-close"></use>
            </svg>
          </span>
        </button>
      </div>
      <div class="modal-body propel-modal-body">
        <div class="purchase-authorization-content"></div>
      </div>
      <div class="modal-footer">
        <div class="row w-100 align-items-baseline">
          <div class="col-12 col-md-6 order-md-1 order-2">
            <button type="button" data-bs-toggle="modal" data-bs-target="#delete_purchase_authorization" class="btn-continue btn-purchase-authorization-delete-confirm"><?php echo esc_html(__('Delete', 'propeller-ecommerce-v2')); ?></button>
          </div>
          <div class="col-12 col-md-6 order-md-2 order-1 d-flex justify-content-end">
            <button type="button" data-bs-toggle="modal" data-bs-target="#accept_purchase_authorization" class="btn-checkout btn-purchase-authorization-accept-confirm d-flex justify-content-end"><?php echo esc_html(__('Take over', 'propeller-ecommerce-v2')); ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="propeller-address-modal propel-authorization-modal modal fade modal-fullscreen-sm-down" id="delete_purchase_authorization" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delete_purchase_authorization_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header propel-modal-header">
        <h5 class="modal-title" id="delete_purchase_authorization_label"><?php echo esc_html(__('Delete authorization request?', 'propeller-ecommerce-v2')); ?></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
          <span aria-hidden="true">
            <svg class="icon icon-close">
              <use class="header-shape-close" xlink:href="#shape-header-close"></use>
            </svg>
          </span>
        </button>
      </div>
      <div class="modal-body propel-modal-body">
        <p>
          <?php echo esc_html(__('Are you sure you want to delete this authorization request?', 'propeller-ecommerce-v2')); ?>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo esc_html(__('No', 'propeller-ecommerce-v2')); ?></button>
        <button type="button" class="btn btn-primary btn-purchase-authorization-delete"><?php echo esc_html(__('Yes', 'propeller-ecommerce-v2')); ?></button>
      </div>
    </div>
  </div>
</div>

<div class="propeller-address-modal propel-authorization-modal modal fade modal-fullscreen-sm-down" id="accept_purchase_authorization" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="accept_purchase_authorization_label" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header propel-modal-header">
        <h5 class="modal-title" id="accept_purchase_authorization_label"><?php echo esc_html(__('Accept authorization request?', 'propeller-ecommerce-v2')); ?></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
          <span aria-hidden="true">
            <svg class="icon icon-close">
              <use class="header-shape-close" xlink:href="#shape-header-close"></use>
            </svg>
          </span>
        </button>
      </div>
      <div class="modal-body propel-modal-body">
        <h6>
          <?php echo esc_html(__('Are you sure you want to take over this authorization request?', 'propeller-ecommerce-v2')); ?>
        </h6>
        <p class="text-info">
          <?php echo esc_html(__('Your current shopping cart will be hidden until the authorization request is purchased. It will become visible again afterward.', 'propeller-ecommerce-v2')); ?>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo esc_html(__('No', 'propeller-ecommerce-v2')); ?></button>
        <button type="button" class="btn btn-primary btn-purchase-authorization-accept"><?php echo esc_html(__('Yes', 'propeller-ecommerce-v2')); ?></button>
      </div>
    </div>
  </div>
</div>

<div class="d-none">
  <img src="<?php echo esc_url(PROPELLER_PLUGIN_DIR_URL . 'public/assets/img/loading.gif'); ?>" id="purchase_auth_loading_img" />
</div>