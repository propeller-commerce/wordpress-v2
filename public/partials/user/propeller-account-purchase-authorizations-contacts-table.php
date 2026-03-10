<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\UserTypes;

?>
<div class="propeller-account-table">
    <div class="table-header d-flex align-items-center justify-content-between mb-3">
        <h4><?php echo esc_html(__('Authorization settings', 'propeller-ecommerce-v2')); ?></h4>

        <?php if (PROPELLER_PAC_ADD_CONTACTS && !is_null(UserController::user()) && UserController::user()->is_authorization_manager()) { ?>
            <button type="button" class="btn btn-addtobasket add-contact-button" data-bs-toggle="modal" data-bs-target="#addContactModal">
                <?php echo esc_html(__('Add contact', 'propeller-ecommerce-v2')); ?>
            </button>
        <?php } ?>
    </div>
    <?php if ($purchase_authorizations_contacts->contacts->itemsFound > 0) { ?>
        <div class="order-sorter">
            <?php apply_filters('propel_account_purchase_authorizations_contacts_table_header', $purchase_authorizations_contacts->contacts); ?>

            <div class="purchase-authorization-configs-list propeller-account-list">

                <?php apply_filters('propel_account_purchase_authorizations_contacts_table_list', $purchase_authorizations_contacts->contacts->items, $purchase_authorizations_contacts->contacts, $obj); ?>

            </div>
        </div>
    <?php } else { ?>
        <div class="no-results">
            <?php echo esc_html(__('There are no contacts in your company yet.', 'propeller-ecommerce-v2')); ?>
        </div>
    <?php } ?>
</div>

<?php if (PROPELLER_PAC_ADD_CONTACTS && !is_null(UserController::user()) && UserController::user()->is_authorization_manager()) { ?>
    <div class="propeller-address-modal propeller-add-contact-modal modal-fullscreen-sm-down modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header propel-modal-header">
                    <div class="modal-title" id="addContactModalLabel"><?php echo esc_html(__('Add contact', 'propeller-ecommerce-v2')); ?></div>

                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                        <span aria-hidden="true">
                            <svg class="icon icon-close">
                                <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="modal-body propel-modal-body">
                    <form name="add-contact" id="pac-add-contact" class="form-handler register-form checkout-form form-add-contact validate" method="post">
                        <input type="hidden" name="action" value="add_contact_to_company">
                        <input type="hidden" name="user_type" value="<?php echo esc_attr(UserTypes::CONTACT); ?>">
                        <input type="hidden" name="parentId" data-type="<?php echo esc_attr(UserTypes::CONTACT); ?>" value="<?php echo esc_attr(SessionController::get(PROPELLER_CONTACT_COMPANY_ID)); ?>">

                        <fieldset class="personal">
                            <div class="row form-group">
                                <div class="col-form-fields col-12">
                                    <div class="row g-3">
                                        <div class="col form-group col-user-taxnr">
                                            <label class="form-label" for="company_name"><?php echo esc_html(__('Company name', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                            <input type="text" name="company_name" readonly value="<?php echo esc_attr(SessionController::get(PROPELLER_CONTACT_COMPANY_NAME)); ?>" class="form-control required" id="company_name" placeholder="<?php echo esc_html(__('Company name', 'propeller-ecommerce-v2')); ?>*">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-form-fields col-12">
                                    <div class="row g-3">
                                        <div class="col-auto form-group form-check">
                                            <label class="btn-radio-checkbox form-check-label ps-0">
                                                <input type="radio" class="form-check-input" name="gender" value="M"> <span><?php echo esc_html(__('Mr.', 'propeller-ecommerce-v2')); ?></span>
                                            </label>
                                        </div>
                                        <div class="col-auto form-group form-check">
                                            <label class="btn-radio-checkbox form-check-label ps-0">
                                                <input type="radio" class="form-check-input" name="gender" value="F"> <span><?php echo esc_html(__('Mrs.', 'propeller-ecommerce-v2')); ?></span>
                                            </label>
                                        </div>
                                        <div class="col-auto form-group form-check">
                                            <label class="btn-radio-checkbox form-check-label ps-0">
                                                <input type="radio" class="form-check-input" name="gender" value="U"> <span><?php echo esc_html(__('Other', 'propeller-ecommerce-v2')); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-form-fields col-12">
                                    <div class="row g-3">
                                        <div class="col form-group col-user-mail">
                                            <label class="form-label" for="field_email"><?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*</label>
                                            <input type="email" name="email" value="" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" class="form-control required email" id="field_email" placeholder="<?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-form-fields col-12">
                                    <div class="row g-3">
                                        <div class="col form-group col-user-firstname">
                                            <label class="form-label" for="field_fname"><?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*</label>
                                            <input type="text" name="firstName" value="" class="form-control required" id="field_fname" placeholder="<?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*">
                                        </div>
                                        <div class="col-3 form-group col-user-middlename">
                                            <label class="form-label" for="field_mname"><?php echo esc_html(__('Insertion (optional)', 'propeller-ecommerce-v2')); ?></label>
                                            <input type="text" name="middleName" value="" class="form-control" id="field_mname" placeholder="<?php echo esc_html(__('Insertion (optional)', 'propeller-ecommerce-v2')); ?>">
                                        </div>
                                        <div class="col form-group col-user-lastname">
                                            <label class="form-label" for="field_lname"><?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*</label>
                                            <input type="text" name="lastName" value="" class="form-control required" id="field_lname" placeholder="<?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-form-fields col-12">
                                    <div class="row g-3">
                                        <div class="col-12 form-group col-user-phone">
                                            <label class="form-label" for="field_phone"><?php echo esc_html(__('Phone number', 'propeller-ecommerce-v2')); ?></label>
                                            <input type="text" name="phone" value="" class="form-control" minlength="6" id="field_phone" placeholder="<?php echo esc_html(__('Phone number', 'propeller-ecommerce-v2')); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <div class="row form-group form-group-submit d-none">
                            <div class="col-form-fields col-12">
                                <input type="submit" form="add-contact" class="btn-modal btn-proceed btn-add-contact-to-company" value="<?php echo esc_html(__('Send', 'propeller-ecommerce-v2')); ?>">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></button>
                    <button type="button" class="btn btn-addtobasket btn-pac-add-contact"><?php echo esc_html(__('Add contact', 'propeller-ecommerce-v2')); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Login Confirmation Modal -->
    <div class="propeller-address-modal modal modal-fullscreen-sm-down fade" id="delete_login_modal" tabindex="-1" role="dialog" aria-labelledby="deleteLoginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header propel-modal-header">
                    <div class="modal-title" id="deleteLoginModalLabel"><?php echo esc_html(__('Delete login account', 'propeller-ecommerce-v2')); ?></div>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                        <span aria-hidden="true">
                            <svg class="icon icon-close">
                                <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="modal-body propel-modal-body">
                    <p><?php echo esc_html(__('Are you sure you want to delete the login account for', 'propeller-ecommerce-v2')); ?> <strong id="delete_contact_name"></strong>?</p>
                    <p class="text-muted"><?php echo esc_html(__('This action cannot be undone.', 'propeller-ecommerce-v2')); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal btn-cancel" data-bs-dismiss="modal"><?php echo esc_html(__('Cancel', 'propeller-ecommerce-v2')); ?></button>
                    <button type="button" class="btn-modal btn-proceed btn-confirm-delete-login"><?php echo esc_html(__('Delete', 'propeller-ecommerce-v2')); ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
