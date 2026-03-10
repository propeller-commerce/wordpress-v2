<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PurchaseAuthorizationRoles;
use Propeller\PropellerHelper;

$has_login = $contact->login && !empty($contact->login);


// Check if this contact is the currently logged-in user
$current_user_data = SessionController::get(PROPELLER_USER_DATA);
$is_logged_in_auth_manager = $current_user_data->is_authorization_manager();
$is_current_user = ($current_user_data && isset($current_user_data->userId) && $current_user_data->userId == $contact->contactId);

?>
<div class="row purchase-authorization-contact-item align-items-center g-0" data-contact_id="<?php echo esc_attr($contact->contactId); ?>" data-purchase_autorization_id="<?php echo esc_attr($contact->purchaseAuthorizationConfigs->itemsFound > 0 ? $contact->purchaseAuthorizationConfigs->items[0]->id : ''); ?>">
    <div class="col-md-1 col-xl-1">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('#', 'propeller-ecommerce-v2')); ?></span>
        <span class="px-0 col-auto <?php echo esc_attr(!$has_login ? 'text-secondary' : ''); ?>">
            <?php echo esc_html($contact->contactId); ?>
        </span>
    </div>
    <div class="col-md-3">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Contact', 'propeller-ecommerce-v2')); ?></span>
        <span class="px-0 col-auto <?php echo esc_attr(!$has_login ? 'text-secondary' : ''); ?> contact-name">
            <?php echo esc_html(sprintf('%s %s %s', $contact->firstName, $contact->middleName, $contact->lastName)); ?>
        </span>
    </div>
    <div class="col-md-3">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Role', 'propeller-ecommerce-v2')); ?></span>

        <?php
        if ($has_login) { ?>
            <?php
            // Check if contact is an Authorization Manager
            $isAuthManager = ($contact->purchaseAuthorizationConfigs->itemsFound > 0 && $contact->purchaseAuthorizationConfigs->items[0]->purchaseRole == PurchaseAuthorizationRoles::AUTHORIZATION_MANAGER);


            ?>

            <select class="form-control purchase-authorization-role" name="purchase_authorization_role" <?php echo (!$is_logged_in_auth_manager || $is_current_user) ? 'disabled' : ''; ?>>
                <option value="<?php echo esc_attr(PurchaseAuthorizationRoles::PURCHASER); ?>" <?php echo esc_attr($contact->purchaseAuthorizationConfigs->itemsFound > 0 && $contact->purchaseAuthorizationConfigs->items[0]->purchaseRole == PurchaseAuthorizationRoles::PURCHASER ? 'selected' : ''); ?>><?php echo esc_html(PurchaseAuthorizationRoles::PURCHASER); ?></option>
                <option value="<?php echo esc_attr(PurchaseAuthorizationRoles::AUTHORIZATION_MANAGER); ?>" <?php echo esc_attr($contact->purchaseAuthorizationConfigs->itemsFound > 0 && $contact->purchaseAuthorizationConfigs->items[0]->purchaseRole == PurchaseAuthorizationRoles::AUTHORIZATION_MANAGER ? 'selected' : ''); ?>><?php echo esc_html(PurchaseAuthorizationRoles::AUTHORIZATION_MANAGER); ?></option>
            </select>


        <?php } ?>

    </div>
    <div class="<?php if (PROPELLER_PAC_ADD_CONTACTS) {
                    echo 'col-md-3';
                } else {
                    echo 'col-md-5 text-end';
                } ?>">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Limit', 'propeller-ecommerce-v2')); ?></span>
        <span class="px-0 col-auto">
            <?php if ($has_login) { ?>
                <?php
                // Determine if we should show the input field
                // Show if: no config yet OR has config and is a PURCHASER
                $hasNoConfig = ($contact->purchaseAuthorizationConfigs->itemsFound == 0);
                $isPurchaser = (!$hasNoConfig && $contact->purchaseAuthorizationConfigs->items[0]->purchaseRole == PurchaseAuthorizationRoles::PURCHASER);

                if ($hasNoConfig || $isPurchaser) { ?>
                    <div class="input-group mt-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><?php echo esc_html(PropellerHelper::currency()); ?></div>
                        </div>
                        <input type="number" class="form-control purchase-authorization-limit" step="1" name="purchase_authorization_limit" value="<?php echo esc_attr($contact->purchaseAuthorizationConfigs->itemsFound > 0 ? $contact->purchaseAuthorizationConfigs->items[0]->authorizationLimit : ''); ?>" placeholder="<?php echo esc_attr(__('none', 'propeller-ecommerce-v2')); ?>" />

                    </div>
                    <?php if ($hasNoConfig) { ?>
                        <div class="label-authorization"><?php echo esc_html(__('No limit applied - leave empty for unlimited', 'propeller-ecommerce-v2')); ?></div>
                        <?php } else if ($isPurchaser) {
                        if ($contact->purchaseAuthorizationConfigs->items[0]->authorizationLimit > 0) { ?>
                            <div class="label-authorization"><?php echo esc_html(__('Purchases up to', 'propeller-ecommerce-v2')); ?> <span class="label-authorization-limit"><?php echo esc_html(PropellerHelper::currency()); ?><?php echo esc_attr($contact->purchaseAuthorizationConfigs->itemsFound > 0 ? $contact->purchaseAuthorizationConfigs->items[0]->authorizationLimit : ''); ?></span> <?php echo esc_html(__('allowed', 'propeller-ecommerce-v2')); ?></div>
                        <?php } else { ?>
                            <div class="label-authorization"> <span class="label-authorization-limit"><?php echo esc_html(PropellerHelper::currency()); ?><?php echo esc_attr($contact->purchaseAuthorizationConfigs->itemsFound > 0 ? $contact->purchaseAuthorizationConfigs->items[0]->authorizationLimit : ''); ?></span> <?php echo esc_html(__('limit - authorization required for any purchase', 'propeller-ecommerce-v2')); ?></div>
                    <?php }
                    } ?>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            <?php } ?>
        </span>
    </div>
    <div class="col-md-2 text-md-end purchase-authorization-buttons">
        <?php if ($has_login) { ?>
            <?php if ($contact->purchaseAuthorizationConfigs->itemsFound > 0) { ?>
                <a href="#" class="btn-authorization btn-purchase-authorization-contact-update d-none" data-action="update_purchase_authorization_config" data-id="<?php echo esc_attr($contact->purchaseAuthorizationConfigs->items[0]->id); ?>">
                    <?php echo esc_html(__('Save', 'propeller-ecommerce-v2')); ?>
                </a>
                <a href="#" class="btn-authorization btn-purchase-authorization-contact-delete d-none" data-action="delete_purchase_authorization_config" data-id="<?php echo esc_attr($contact->purchaseAuthorizationConfigs->items[0]->id); ?>">
                    <?php echo esc_html(__('Delete', 'propeller-ecommerce-v2')); ?>
                </a>
            <?php } else if ($contact->purchaseAuthorizationConfigs->itemsFound == 0) { ?>
                <a href="#" class="btn-authorization btn-purchase-authorization-contact-create d-none" data-action="create_purchase_authorization_config">
                    <?php echo esc_html(__('Create limit', 'propeller-ecommerce-v2')); ?>
                </a>
            <?php }
            if (PROPELLER_PAC_ADD_CONTACTS && $is_logged_in_auth_manager && !$is_current_user) { ?>
                <a href="#" class="btn-authorization btn-purchase-authorization-auth-delete"
                    data-bs-toggle="modal"
                    data-bs-target="#delete_login_modal"
                    data-action="delete_contact_login"
                    data-contact_id="<?php echo esc_attr($contact->contactId); ?>"
                    data-contact_name="<?php echo esc_attr(sprintf('%s %s %s', $contact->firstName, $contact->middleName, $contact->lastName)); ?>">
                    <?php echo esc_html(__('Delete account login', 'propeller-ecommerce-v2')); ?>
                </a>
            <?php }
        } else if (PROPELLER_PAC_ADD_CONTACTS && $is_logged_in_auth_manager) { ?>
            <a href="#" class="btn-authorization btn-purchase-authorization-auth-create"
                data-action="create_contact_login"
                data-contact_id="<?php echo esc_attr($contact->contactId); ?>"
                data-email="<?php echo esc_attr($contact->email); ?>"
                data-displayname="<?php echo esc_attr(sprintf('%s %s %s', $contact->firstName, $contact->middleName, $contact->lastName)); ?>">
                <?php echo esc_html(__('Create account login', 'propeller-ecommerce-v2')); ?>
            </a>
        <?php } ?>
    </div>
</div>