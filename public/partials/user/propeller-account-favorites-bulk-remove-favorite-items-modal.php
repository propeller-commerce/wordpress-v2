<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="remove_favorites" class="propeller-address-modal modal modal-fullscreen-sm-down fade" tabindex="-1" role="dialog" aria-labelledby="deleteItemsLabel">
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_add_favorite" class="modal-title">
                    <h5 class="modal-title" id="deleteItemsLabel">
                        <?php echo esc_html(__('Remove Items', 'propeller-ecommerce-v2')); ?>&nbsp;
                        (<span class="total-favorites-remove"></span> <?php echo esc_html(__('products', 'propeller-ecommerce-v2')); ?>)
                    </h5>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                    <span aria-hidden="true">
                        <svg class="icon icon-close">
                            <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="modal-body propel-modal-body">
                <form name="delete-favorite-items-form" id="delete_favorite_items_form" class="delete-favorites-items-form form-horizontal form-handler modal-edit-form" method="post">
                    <input type="hidden" name="list_id" value="<?php echo esc_attr($list->id); ?>">
                    <input type="hidden" name="action" value="delete_favorite">

                    <h4><?php echo esc_html(__('Are you sure you want to remove these favorite items from this list?', 'propeller-ecommerce-v2')); ?></h4>

                    <div class="row form-group form-group-submit propel-modal-foote">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col-6">
                                    <button type="button" class="btn-modal w-100 justify-content-center btn-modal-address" data-bs-dismiss="modal"><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></button>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn-modal btn-proceed w-100 justify-content-center btn-modal-address btn-modal-submit"><?php echo esc_html(__('Remove', 'propeller-ecommerce-v2')); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>