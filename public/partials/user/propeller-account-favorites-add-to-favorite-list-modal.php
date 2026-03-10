<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="addToList" class="propeller-address-modal add-to-favorite-modal modal modal-fullscreen-sm-down fade" tabindex="-1" role="dialog" aria-labelledby="AddToListLabel">
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_add_favorite" class="modal-title">
                    <h5 class="modal-title" id="AddToListLabel"><?php echo esc_html(__('Add product to list', 'propeller-ecommerce-v2') . ' "' . esc_html($obj->data->name) . '"'); ?></h5>
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
                <form name="add-to-favorite-list-form" id="add_to_favorite_list_form" class="add-to-favorite-list-form form-horizontal" method="post">
                    <input type="hidden" name="list_id" value="<?php echo esc_attr($obj->data->id); ?>">
                    <input type="hidden" name="action" value="rename_favorite_list">

                    <fieldset>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-street">
                                        <label class="form-label visually-hidden" for="searchfavProducts"><?php echo esc_html(__('Search for product', 'propeller-ecommerce-v2')); ?>*</label>

                                        <input
                                            id="searchfavProducts"
                                            type="search"
                                            name="term"
                                            class="form-control"
                                            data-update_list="1"
                                            data-list_id="<?php echo esc_html($obj->data->id); ?>"
                                            placeholder="<?php echo esc_html(__('Search for product', 'propeller-ecommerce-v2')); ?>"
                                            value=""
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
                <div class="row form-group form-group-submit propel-modal-foote">
                    <div class="col-form-fields col-12">
                        <div class="row g-3">
                            <div class="col-12 justify-content-center">
                                <button type="button" class="btn-modal w-100 justify-content-center btn-modal-address" data-bs-dismiss="modal"><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- <div class="modal-footer propel-modal-footer">
                
            </div> -->
        </div>
    </div>
</div>