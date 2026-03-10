<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="delete_favorite_<?php

                            use Propeller\Includes\Enum\ProductClass;

                            echo esc_attr($product->class == ProductClass::Product ? $product->productId : $product->clusterId); ?>" class="propeller-address-modal modal modal-fullscreen-sm-down fade" data-bs-backdrop="true" data-bs-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="deleteItemLabel">
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_add_favorite" class="modal-title">
                    <h5 class="modal-title" id="deleteItemLabel"><?php echo esc_html(__('Remove Item', 'propeller-ecommerce-v2') . ' "' . esc_html($product->get_name()) . '"'); ?></h5>
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
                <form name="delete-favorite-item-form" id="delete_favorite_item_form" class="delete-favorite-item-form form-horizontal form-handler modal-edit-form" method="post">
                    <input type="hidden" name="list_id" value="<?php echo esc_attr($list->id); ?>">
                    <input type="hidden" name="action" value="delete_favorite">
                    <?php if ($product->class == ProductClass::Product) { ?>
                        <input type="hidden" name="product_id[]" value="<?php echo esc_attr($product->productId); ?>">
                    <?php } else { ?>
                        <input type="hidden" name="cluster_id[]" value="<?php echo esc_attr($product->clusterId); ?>">
                    <?php } ?>

                    <h4><?php echo esc_html(__('Are you sure you want to remove this favorite item from this list?', 'propeller-ecommerce-v2')); ?></h4>

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