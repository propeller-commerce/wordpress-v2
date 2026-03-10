<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<svg style="display:none">
    <symbol viewBox="0 0 14 14" id="shape-header-close">
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
<div id="edit_address_modal_<?php echo esc_attr($address->id); ?>" class="propeller-address-modal modal modal-fullscreen-sm-down fade" tabindex="-1" role="dialog" aria-labelledby="propel_modal_edit_title_<?php echo esc_attr($address->id); ?>">
    <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_edit_title_<?php echo esc_attr($address->id); ?>" class="modal-title">
                    <span>
                        <?php
                        if ($address->type == 'delivery')
                            echo esc_html((int) $address->id == 0
                                ? __('Add delivery address', 'propeller-ecommerce-v2')
                                : __('Edit delivery address', 'propeller-ecommerce-v2'));
                        else
                            echo esc_html((int) $address->id == 0
                                ? __('Add billing address', 'propeller-ecommerce-v2')
                                : __('Edit billing address', 'propeller-ecommerce-v2'));
                        ?>
                    </span>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                    <span aria-hidden="true">
                        <svg class="icon icon-close">
                            <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="modal-body propel-modal-body" id="propel_modal_edit_body_<?php echo esc_attr($address->id); ?>">

                <?php apply_filters('propel_address_form', $address); ?>

            </div>
        </div>
    </div>
</div>