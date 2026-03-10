<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

?>
<svg style="display:none">
    <symbol viewBox="0 0 14 14" id="shape-header-close">
        <title><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></title>
        <path d="M13.656 12.212c.41.41.41 1.072 0 1.481a1.052 1.052 0 0 1-1.485 0L7 8.5l-5.207 5.193a1.052 1.052 0 0 1-1.485 0 1.045 1.045 0 0 1 0-1.481L5.517 7.02.307 1.788a1.045 1.045 0 0 1 0-1.481 1.052 1.052 0 0 1 1.485 0L7.001 5.54 12.208.348a1.052 1.052 0 0 1 1.485 0c.41.408.41 1.072 0 1.48L8.484 7.02l5.172 5.192z" />
    </symbol>
</svg>
<div id="change_pwd_modal" class="propeller-address-modal modal modal-fullscreen-sm-down fade" tabindex="-1" role="dialog" aria-labelledby="modal-title">
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_title" class="modal-title">
                    <span><?php echo esc_html(__('Password and newsletter', 'propeller-ecommerce-v2')); ?></span>
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
                <form name="change-address-pwd-form" id="change_pwd" class="form-horizontal validate form-handler modal-form modal-edit-form" method="post">
                    <input type="hidden" name="action" value="change_pwd">

                    <fieldset>
                        <legend class="checkout-header">
                            <?php echo esc_html(__('Change password?', 'propeller-ecommerce-v2')); ?>
                        </legend>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-password">
                                        <label class="form-label" for="field_password"><?php echo esc_html(__('New password', 'propeller-ecommerce-v2')); ?></label>
                                        <input type="password" name="user_password" value="" placeholder="<?php echo esc_html(__('New password', 'propeller-ecommerce-v2')); ?>" class="form-control" id="field_password">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-password">
                                        <label class="form-label" for="field_password_verify"><?php echo esc_html(__('Repeat new password', 'propeller-ecommerce-v2')); ?></label>
                                        <input type="password" name="user_password_verfification" value="" placeholder="<?php echo esc_html(__('Repeat new password', 'propeller-ecommerce-v2')); ?>" class="form-control" id="field_password_verify">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="mt-5 mb-5">
                        <legend class="checkout-header">
                            <?php echo esc_html(__('Newsletter subscription', 'propeller-ecommerce-v2')); ?>
                        </legend>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" name="mailing_subscribe" value="<?php if ($user->mailingList == 'Y') { ?>Y<?php } else { ?>N<?php } ?>" <?php if ($user->mailingList == 'Y') { ?> checked <?php } ?>>
                                            <span><?php echo esc_html(__('Yes, I want to subscribe to the newsletter', 'propeller-ecommerce-v2')); ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row form-group form-group-submit">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col-12">
                                    <button type="submit" class="btn-modal btn-proceed w-100 justify-content-center" id="submit_change_password"><?php echo esc_html(__('Save changes', 'propeller-ecommerce-v2')); ?></button>
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