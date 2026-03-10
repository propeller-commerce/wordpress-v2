<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<svg style="display:none">
    <symbol viewBox="0 0 14 14" id="shape-header-close">
        <title><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></title>
        <path d="M13.656 12.212c.41.41.41 1.072 0 1.481a1.052 1.052 0 0 1-1.485 0L7 8.5l-5.207 5.193a1.052 1.052 0 0 1-1.485 0 1.045 1.045 0 0 1 0-1.481L5.517 7.02.307 1.788a1.045 1.045 0 0 1 0-1.481 1.052 1.052 0 0 1 1.485 0L7.001 5.54 12.208.348a1.052 1.052 0 0 1 1.485 0c.41.408.41 1.072 0 1.48L8.484 7.02l5.172 5.192z" />
    </symbol>
</svg>
<div id="delete_address_modal_<?php echo esc_attr($address->id); ?>" class="propeller-address-modal modal modal-fullscreen-sm-down fade" tabindex="-1" role="dialog" aria-labelledby="modal-title">
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_title" class="modal-title">
                    <span><?php echo esc_html($address->type == 'delivery'
                                ? __('Delete delivery address', 'propeller-ecommerce-v2')
                                : __('Delete invoice address', 'propeller-ecommerce-v2')); ?></span>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                    <span aria-hidden="true">
                        <svg class="icon icon-close">
                            <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="modal-body propel-modal-body" id="propel_modal_body">
                <form name="delete-address-form" id="delete_address<?php echo esc_attr($address->id); ?>" class="form-horizontal validate form-handler modal-form modal-edit-form" method="post">
                    <input type="hidden" name="id" value="<?php echo esc_attr($address->id); ?>">
                    <input type="hidden" name="action" value="delete_address">
                    <div class="row g-3">
                        <div class="col-12">
                            <p><?php echo esc_html(__('Are you sure you want to delete this address?', 'propeller-ecommerce-v2')); ?></p>
                        </div>
                        <div class="address-details col-12">
                            <div class="address">
                                <?php echo esc_html($address->company); ?><br>
                                <?php echo esc_html($address->firstName); ?> <?php echo esc_html($address->lastName); ?><br>
                                <?php echo esc_html($address->street); ?> <?php echo esc_html($address->number); ?> <?php echo esc_html($address->numberExtension); ?><br>
                                <?php echo esc_html($address->postalCode); ?> <?php echo esc_html($address->city); ?><br>
                                <?php
                                $code = $address->country;
                                $countries = propel_get_countries();

                                echo esc_html(!$countries[$code] ? $code : $countries[$code]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group form-group-submit propel-modal-foote">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="submit" class="btn-modal btn-proceed" id="submit_delete_address<?php echo esc_attr($address->id); ?>" value="<?php echo esc_attr(__('Delete', 'propeller-ecommerce-v2')); ?>">
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn-modal btn-cancel" data-bs-dismiss="modal"><?php echo esc_html(__('Cancel', 'propeller-ecommerce-v2')); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<div id="propel_modal_recycle"></div>