<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col-12 col-md-6 mb-4">
    <div class="address-box">
        <div class="row">
            <div class="col-12">
                <div class="user-addr-details">
                    <span><?php echo esc_html( __('Password:', 'propeller-ecommerce-v2') ); ?></span>********<br>
                    <span>
                        <?php 
                            if($user->mailingList == 'Y')
                                echo esc_html(__('You are subscribed to our newsletter', 'propeller-ecommerce-v2'));
                            else
                                echo esc_html(__('You are not subscribed to our newsletter', 'propeller-ecommerce-v2'));
                        ?>
                    </span><br>
                </div>
            </div>
        </div>
        <div class="row address-links">
            <div class="col-12">
                <a class="address-edit open-modal-form" 
                    data-form-id="edit_address<?php echo esc_attr($user->userId); ?>"
                    data-title="<?php echo esc_attr( __('Password and newsletter', 'propeller-ecommerce-v2') ); ?>"
                    data-bs-target="#change_pwd_modal"
                    data-bs-toggle="modal"
                    role="button">
                    <?php echo esc_html( __('Modify', 'propeller-ecommerce-v2') ); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require $this->load_template('partials', '/user/propeller-account-change-password.php'); ?>
