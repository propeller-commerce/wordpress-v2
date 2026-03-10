<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="newfavlist" class="propeller-address-modal modal modal-fullscreen-sm-down fade" tabindex="-1" role="dialog" aria-labelledby="deleteListLabel">
    <svg style="display:none">
        <symbol viewBox="0 0 14 14" id="shape-close">
            <title><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></title>
            <path d="M13.656 12.212c.41.41.41 1.072 0 1.481a1.052 1.052 0 0 1-1.485 0L7 8.5l-5.207 5.193a1.052 1.052 0 0 1-1.485 0 1.045 1.045 0 0 1 0-1.481L5.517 7.02.307 1.788a1.045 1.045 0 0 1 0-1.481 1.052 1.052 0 0 1 1.485 0L7.001 5.54 12.208.348a1.052 1.052 0 0 1 1.485 0c.41.408.41 1.072 0 1.48L8.484 7.02l5.172 5.192z" />
        </symbol>
        <symbol viewBox="0 0 16 16" id="shape-error">
            <title><?php echo esc_html(__('Error', 'propeller-ecommerce-v2')); ?></title>
            <path d="M15.75 8A7.751 7.751 0 0 0 .25 8 7.75 7.75 0 0 0 8 15.75 7.75 7.75 0 0 0 15.75 8zM8 9.563a1.437 1.437 0 1 1 0 2.874 1.437 1.437 0 0 1 0-2.874zM6.635 4.395A.375.375 0 0 1 7.01 4h1.98c.215 0 .386.18.375.395l-.232 4.25A.375.375 0 0 1 8.759 9H7.24a.375.375 0 0 1-.374-.355l-.232-4.25z" fill="#E02B27" />
        </symbol>
        <symbol viewBox="0 0 16 12" id="shape-valid">
            <title><?php echo esc_html(__('Valid', 'propeller-ecommerce-v2')); ?></title>
            <path d="m6.566 11.764 9.2-9.253a.808.808 0 0 0 0-1.137L14.634.236a.797.797 0 0 0-1.131 0L6 7.782 2.497 4.259a.797.797 0 0 0-1.131 0L.234 5.397a.808.808 0 0 0 0 1.137l5.2 5.23a.797.797 0 0 0 1.132 0z" fill="#54A023" />
        </symbol>
    </svg>
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_add_favorite" class="modal-title">
                    <h5 class="modal-title" id="deleteListLabel"><?php echo esc_html(__('New favorite list', 'propeller-ecommerce-v2')); ?></h5>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                    <span aria-hidden="true">
                        <svg class="icon icon-close">
                            <use class="header-shape-close" xlink:href="#shape-close"></use>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="modal-body propel-modal-body">
                <form name="new-favorite-list-form" id="new_favorite_list_form" class="new-favorite-list form-horizontal validate form-handler modal-edit-form" method="post">
                    <input type="hidden" name="action" value="create_favorite_list">

                    <fieldset>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-street">
                                        <label class="form-label" for="favorite_list_name"><?php echo esc_html(__('Name', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="text" name="name" value="" placeholder="<?php echo esc_html(__('Name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="favorite_list_name">
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