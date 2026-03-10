<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col-12 col-md-6 mb-4">
    <div class="address-box">
        <div class="row">
            <div class="col-12">
                <div class="addr-title"><?php echo esc_html( __('Personal details', 'propeller-ecommerce-v2') ); ?></div>
                <div class="user-addr-details">
                    <?php echo esc_html($obj->get_salutation($user)); ?>
                    <?php echo esc_html($user->firstName); ?> <?php echo esc_html($user->middleName); ?> <?php echo esc_html($user->lastName); ?><br>
                    <?php echo esc_html($user->email); ?><br>
                    <?php echo esc_html($user->phone); ?>
                </div>
            </div>
        </div>
    </div>
</div>