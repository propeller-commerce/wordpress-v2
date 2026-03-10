<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="renameList" class="propeller-address-modal modal modal-fullscreen-sm-down fade" tabindex="-1" role="dialog" aria-labelledby="deleteListLabel">
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_add_favorite" class="modal-title">
                    <h5 class="modal-title" id="deleteListLabel"><?php echo esc_html(__('Rename list', 'propeller-ecommerce-v2') . ' "' . esc_html($obj->data->name) . '"'); ?></h5>
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
                <form name="rename-favorite-list-form" id="rename_favorite_list_form" class="rename-favorite-list form-horizontal validate form-handler modal-edit-form" method="post">
                    <input type="hidden" name="list_id" value="<?php echo esc_attr($obj->data->id); ?>">
                    <input type="hidden" name="action" value="rename_favorite_list">

                    <fieldset>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-street">
                                        <label class="form-label" for="favorite_list_name"><?php echo esc_html(__('Name', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="text" name="name" value="<?php echo esc_attr($obj->data->name); ?>" placeholder="<?php echo esc_html(__('Name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="favorite_list_name">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-street">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" name="is_default" value="Y" aria-required="false">
                                            <span><?php echo esc_html(__('Set as default favorite list', 'propeller-ecommerce-v2')); ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <div class="row form-group form-group-submit propel-modal-foote">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col-6">
                                    <button type="button" class="btn-modal w-100 justify-content-center btn-modal-address" data-bs-dismiss="modal"><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></button>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn-modal btn-proceed w-100 justify-content-center btn-modal-address btn-modal-submit"><?php echo esc_html(__('Save', 'propeller-ecommerce-v2')); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>