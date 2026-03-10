<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;

if (SessionController::get(PROPELLER_CONTACT_COMPANY_ID)) {
?>
    <div class="col-12 col-md-6 mb-4">
        <div class="address-box">
            <div class="row">
                <div class="col-12">
                    <div class="addr-title"><?php echo esc_html(__('Company details', 'propeller-ecommerce-v2')); ?></div>
                    <div class="user-addr-details">
                        <?php echo esc_html(SessionController::get(PROPELLER_CONTACT_COMPANY_NAME)); ?>
                        <?php if (!empty($user->company->taxNumber)) { ?>
                            <div><span><?php echo esc_html(__('Tax number:', 'propeller-ecommerce-v2')); ?>&nbsp;</span><?php echo esc_html($user->company->taxNumber); ?></div>
                        <?php } ?>
                        <?php if (!empty($user->company->cocNumber)) { ?>
                            <span><?php echo esc_html(__('Chamber of Commerce (CoC) number:', 'propeller-ecommerce-v2')); ?>&nbsp;</span><?php echo esc_html($user->company->cocNumber); ?><br>
                        <?php } ?>
                        <?php if (!empty($user->company->debtorId)) { ?>
                            <span><?php echo esc_html(__('Debtor ID:', 'propeller-ecommerce-v2')); ?>&nbsp;</span><?php echo esc_html($user->company->debtorId); ?><br>
                        <?php } ?>
                        <?php if (!empty($user->company->email)) { ?>
                            <span><?php echo esc_html(__('Email:', 'propeller-ecommerce-v2')); ?>&nbsp;</span><?php echo esc_html($user->company->email); ?><br>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
