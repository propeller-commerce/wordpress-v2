<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="cluster_por_modal" class="propeller-address-modal propel-cluster-por-modal modal fade modal-fullscreen-sm-down" tabindex="-1" role="dialog" aria-labelledby="propel_modal_cluster_por">
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_cluster_por" class="modal-title">
                    <span><?php echo esc_html(__('Configure cluster', 'propeller-ecommerce-v2')); ?> <span class="cluster-por-sku"></span></span>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                    <span aria-hidden="true">
                        <svg class="icon icon-close">
                            <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="modal-body propel-modal-body" id="propel_modal_cluster_por_body"></div>
            <div class="propel-modal-footer modal-footer justify-content-center">
                <button type="button" class="btn-modal btn-proceed btn-modal-address btn-modal-submit btn-modal-cluster-por">
                    <?php echo esc_html(__("Add to price on request", "propeller-ecommerce-v2")); ?>
                </button>
            </div>
        </div>
    </div>
</div>