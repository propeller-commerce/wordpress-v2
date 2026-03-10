<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\AddressTypeCart;

$rand = wp_rand(1, 10000);
$selected = !isset($delivery_address->country) || empty($delivery_address->country) ? 'NL' : $delivery_address->country;
?>
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
<div id="edit_address_modal_<?php echo esc_attr($delivery_address->id); ?>" class="propeller-address-modal modal fade modal-fullscreen-sm-down" tabindex="-1" role="dialog" aria-labelledby="propel_modal_edit_title_<?php echo esc_attr($rand); ?>">
    <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_edit_title_<?php echo esc_attr($rand); ?>" class="modal-title">
                    <span>
                        <?php
                        if (($delivery_address->id !== 0 && $delivery_address->id !== "_guest") || ($delivery_address->id == "_guest" && !empty($delivery_address->street)))
                            echo esc_html(__('Edit delivery address', 'propeller-ecommerce-v2'));
                        else
                            echo esc_html(__('Add delivery address', 'propeller-ecommerce-v2')); ?>
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
            <div class="modal-body propel-modal-body" id="propel_modal_edit_body_<?php echo esc_attr($rand); ?>">
                <form name="add-delivery-address-form" id="edit_address<?php echo esc_attr($delivery_address->id); ?>" class="form-horizontal validate form-handler modal-edit-form dropshipment-form" method="post">
                    <input type="hidden" name="id" value="<?php echo esc_attr($delivery_address->id); ?>">
                    <input type="hidden" name="type" value="<?php echo esc_attr(AddressTypeCart::DELIVERY); ?>">
                    <input type="hidden" name="action" value="cart_update_address">
                    <input type="hidden" name="isDefault" value="<?php echo esc_attr(isset($delivery_address->isDefault) ? $delivery_address->isDefault : 'N'); ?>">
                    <input type="hidden" name="icp" value="<?php echo esc_attr((isset($delivery_address->icp) && $delivery_address->icp != '') ? $delivery_address->icp : "N"); ?>">
                    <input type="hidden" name="<?php echo esc_attr((int) $delivery_address->id == 0 ? 'add_delivery_address' : 'update_delivery_address'); ?>" value="Y" />

                    <input type="hidden" name="save_delivery_address" value="Y" />

                    <input type="hidden" name="carrier" value="" />

                    <fieldset class="personal">
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-company">
                                        <?php
                                        $company_required = !UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS ? '' : ' required';
                                        ?>

                                        <label class="form-label" for="company_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Company', 'propeller-ecommerce-v2')); ?><?php echo esc_html(!empty($company_required) ? '*' : ''); ?></label>
                                        <input type="text" name="company" value="<?php echo esc_attr($delivery_address->company); ?>" placeholder="<?php echo esc_html(__('Company', 'propeller-ecommerce-v2')); ?>*" class="form-control<?php echo esc_attr($company_required); ?>" id="company_<?php echo esc_attr($rand); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-auto form-group ">
                                        <label class="btn-radio-checkbox -label ">
                                            <input type="radio" class="-input" name="gender" value="M" <?php echo esc_html(($delivery_address->gender == 'M' ? 'checked' : '')); ?>> <span><?php echo esc_html(__('Mr.', 'propeller-ecommerce-v2')); ?></span>
                                        </label>
                                    </div>
                                    <div class="col-auto form-group ">
                                        <label class="btn-radio-checkbox -label ">
                                            <input type="radio" class="-input" name="gender" value="F" <?php echo esc_html(($delivery_address->gender == 'F' ? 'checked' : '')); ?>> <span><?php echo esc_html(__('Mrs.', 'propeller-ecommerce-v2')); ?></span>
                                        </label>
                                    </div>
                                    <div class="col-auto form-group ">
                                        <label class="btn-radio-checkbox -label ">
                                            <input type="radio" class="-input" name="gender" value="U" <?php echo esc_html(($address->gender == 'U' ? 'checked' : '')); ?>> <span><?php echo esc_html(__('Other', 'propeller-ecommerce-v2')); ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col form-group col-user-firstname">
                                        <label class="form-label" for="firstName_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="text" name="firstName" value="<?php echo esc_attr($delivery_address->firstName); ?>" placeholder="<?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="firstName_<?php echo esc_attr($rand); ?>">
                                    </div>
                                    <div class="col-2 form-group col-user-middlename">
                                        <label class="form-label" for="middleName_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Insertion', 'propeller-ecommerce-v2')); ?></label>
                                        <input type="text" name="middleName" value="<?php echo esc_attr($delivery_address->middleName); ?>" placeholder="<?php echo esc_html(__('Insertion (optional)', 'propeller-ecommerce-v2')); ?>" class="form-control" id="middleName_<?php echo esc_attr($rand); ?>">
                                    </div>
                                    <div class="col form-group col-user-lastname">
                                        <label class="form-label" for="lastName_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="text" name="lastName" value="<?php echo esc_attr($delivery_address->lastName); ?>" placeholder="<?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="lastName_<?php echo esc_attr($rand); ?>">
                                    </div>
                                </div>
                            </div>
                        </div> <?php
                                if ($delivery_address->id === "_guest" || $delivery_address->id === "anonymous") { ?>
                            <div class="row form-group">
                                <div class="col-form-fields col-12">
                                    <div class="form-row">
                                        <div class="col form-group col-user-phone">
                                            <label class="form-label" for="phone_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Phone number', 'propeller-ecommerce-v2')); ?>*</label>
                                            <input type="text" name="phone" value="<?php echo esc_attr($delivery_address->phone); ?>" placeholder="<?php echo esc_html(__('Phone number', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="phone_<?php echo esc_attr($rand); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                    </fieldset>
                    <fieldset>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col form-group col-user-street">
                                        <label class="form-label" for="street_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="text" name="street" value="<?php echo esc_attr($delivery_address->street); ?>" placeholder="<?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="street_<?php echo esc_attr($rand); ?>">
                                    </div>
                                    <div class="col form-group col-user-street-number">
                                        <label class="form-label" for="number_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="text" name="number" value="<?php echo esc_attr($delivery_address->number); ?>" placeholder="<?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="number_<?php echo esc_attr($rand); ?>">
                                    </div>
                                    <div class="col-3 form-group col-user-address_add">
                                        <label class="form-label" for="number_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?></label>
                                        <input type="text" name="numberExtension" value="<?php echo esc_attr($delivery_address->numberExtension); ?>" placeholder="<?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?>" class="form-control" maxlength="7" id="numberExtension_<?php echo esc_attr($rand); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col form-group col-user-zipcode">
                                        <label class="form-label" for="code_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="text" name="postalCode" value="<?php echo esc_attr($delivery_address->postalCode); ?>" placeholder="<?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="code_<?php echo esc_attr($rand); ?>">
                                    </div>
                                    <div class="col form-group col-user-city">
                                        <label class="form-label" for="city_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="text" name="city" value="<?php echo esc_attr($delivery_address->city); ?>" placeholder="<?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="city_<?php echo esc_attr($rand); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-country">
                                        <label class="form-label" for="country_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Country', 'propeller-ecommerce-v2')); ?>*</label>

                                        <select id="country_<?php echo esc_attr($rand); ?>" name="country" class="form-control required">
                                            <?php foreach ($countries as $code => $name) { ?>
                                                <option value="<?php echo esc_attr($code); ?>" <?php echo esc_attr($code == $selected ? 'selected' : ''); ?>><?php echo esc_html($name); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-address">
                                        <label class="form-label" for="email_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('E-mail', 'propeller-ecommerce-v2')); ?>*</label>
                                        <input type="email" name="email" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" value="<?php echo esc_attr($delivery_address->email); ?>" placeholder="<?php echo esc_html(__('E-mail', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="email_<?php echo esc_attr($rand); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12 form-group col-user-notes">
                                        <label class="form-label" for="notes_<?php echo esc_attr($rand); ?>"><?php echo esc_html(__('Remarks (optional)', 'propeller-ecommerce-v2')); ?></label>
                                        <textarea name="notes" placeholder="<?php echo esc_html(__('Remarks (optional)', 'propeller-ecommerce-v2')); ?>" class="form-control" id="notes_<?php echo esc_attr($rand); ?>"><?php echo esc_html($delivery_address->notes); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row form-group form-group-submit propel-modal-foote">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col d-flex justify-content-start">
                                    <button type="submit" class="btn-modal btn-proceed btn-modal-address btn-modal-submit" id="submit_edit_address<?php echo esc_attr($rand); ?>">
                                        <?php
                                        if (UserController::is_propeller_logged_in())
                                            echo esc_html(__('Save this address', 'propeller-ecommerce-v2'));
                                        else
                                            echo esc_html(__('Use this address', 'propeller-ecommerce-v2'));
                                        ?>
                                    </button>
                                </div>
                                <div class="col-auto">
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