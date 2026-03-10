<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (isset($option->shortDescription) && is_array($option->shortDescription) && count($option->shortDescription) && !empty($option->shortDescription[0]->value)) {
?>

    <div class="propeller-option-modal propeller-address-modal modal fade modal-fullscreen-sm-down" tabindex="-1" role="dialog" aria-labelledby="modal-title" id="configOptionModal_<?php echo esc_html($option->id); ?>">
        <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header propel-modal-header">
                    <div id="propel_modal_title" class="modal-title">
                        <span><?php echo esc_html($option->name[0]->value); ?></span>
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
                    <div class="config-description">
                        <?php echo esc_html($option->shortDescription[0]->value); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
