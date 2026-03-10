<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\AddressType;

?>
<div class="propeller-account-table propeller-account-wrapper">
    <div class="account-details">
        <div class="row">

            <?php apply_filters('propel_my_account_user_details_title', __('Your details', 'propeller-ecommerce-v2')); ?>

        </div>
        <div class="row">

            <?php apply_filters('propel_my_account_user_details', $obj->get_user(), $obj); ?>
            <?php if ($obj->get_user()->__typename !== 'Customer')
                apply_filters('propel_my_account_company_details', $obj->get_user(), $obj); ?>


        </div>
    </div>
    <div class="default-addresses">
        <div class="row">

            <?php apply_filters('propel_my_account_addresses_title', __('My addresses', 'propeller-ecommerce-v2')); ?>

        </div>
        <div class="row">

            <?php apply_filters('propel_address_box', $obj->get_default_address(AddressType::INVOICE), $obj, __('Default billing address', 'propeller-ecommerce-v2'), true, false); ?>

            <?php apply_filters('propel_address_box', $obj->get_default_address(AddressType::DELIVERY), $obj, __('Default delivery address', 'propeller-ecommerce-v2'), true, false); ?>

        </div>
    </div>
</div>