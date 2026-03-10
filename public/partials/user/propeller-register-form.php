<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\AddressType;
use Propeller\Includes\Enum\UserTypes;

?>
<div class="container-fluid px-0 propeller-register-wrapper">
    <div class="row">
        <div class="col-12 col-md-9 me-auto">
            <form name="register" class="form-handler register-form checkout-form validate" method="post">
                <input type="hidden" name="action" value="do_register">
                <input type="hidden" name="user_type" value="<?php echo esc_attr(UserTypes::CONTACT); ?>">

                <?php if (SessionController::has('register_referrer')) { ?>
                    <input type="hidden" name="referrer" value="<?php echo esc_url(SessionController::get('register_referrer')); ?>">
                <?php } ?>

                <fieldset class="personal">
                    <legend class="checkout-header">
                        <?php echo esc_html(__('Your details', 'propeller-ecommerce-v2')); ?>
                    </legend>
                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col-auto form-group">
                                    <label class="btn-radio-checkbox form-check-label ">
                                        <input type="radio" class="form-check-input user-type-radio" name="parentId" data-type="<?php echo esc_attr(UserTypes::CONTACT); ?>" value="" checked> <span><?php echo esc_html(__('Company', 'propeller-ecommerce-v2')); ?></span>
                                    </label>
                                </div>
                                <div class="col-auto form-group">
                                    <label class="btn-radio-checkbox form-check-label ">
                                        <input type="radio" class="form-check-input user-type-radio" name="parentId" data-type="<?php echo esc_html(UserTypes::CUSTOMER); ?>" value=""> <span><?php echo esc_html(__('Consumer', 'propeller-ecommerce-v2')); ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col-auto form-group">
                                    <label class="btn-radio-checkbox form-check-label ">
                                        <input type="radio" class="form-check-input" name="gender" value="M"> <span><?php echo esc_html(__('Mr.', 'propeller-ecommerce-v2')); ?></span>
                                    </label>
                                </div>
                                <div class="col-auto form-group">
                                    <label class="btn-radio-checkbox form-check-label ">
                                        <input type="radio" class="form-check-input" name="gender" value="F"> <span><?php echo esc_html(__('Mrs.', 'propeller-ecommerce-v2')); ?></span>
                                    </label>
                                </div>
                                <div class="col-auto form-group">
                                    <label class="btn-radio-checkbox form-check-label ">
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
                                <div class="col form-group col-user-taxnr">
                                    <label class="form-label" for="field_taxnr"><?php echo esc_html(__('VAT number', 'propeller-ecommerce-v2')); ?>*</label>
                                    <input type="text" name="taxNumber" value="" class="form-control required" id="field_taxnr" placeholder="<?php echo esc_html(__('VAT number', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col form-group col-user-cocnr">
                                    <label class="form-label" for="field_cocnr"><?php echo esc_html(__('CoC number', 'propeller-ecommerce-v2')); ?>*</label>
                                    <input type="text" name="cocNumber" value="" class="form-control required" id="field_cocnr" placeholder="<?php echo esc_html(__('CoC number', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col form-group col-user-taxnr">
                                    <label class="form-label" for="company_name"><?php echo esc_html(__('Company name', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                    <input type="text" name="company_name" value="" class="form-control required" id="company_name" placeholder="<?php echo esc_html(__('Company name', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col form-group col-user-firstname">
                                    <label class="form-label" for="field_fname"><?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*</label>
                                    <input type="text" name="firstName" value="" class="form-control required" id="field_fname" placeholder="<?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col-2 form-group col-user-middlename">
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
                                <div class="col-12 col-md-8 form-group col-user-phone">
                                    <label class="form-label" for="field_phone"><?php echo esc_html(__('Phone number', 'propeller-ecommerce-v2')); ?>*</label>
                                    <input type="text" name="phone" value="" class="form-control required" minlength="6" id="field_phone" placeholder="<?php echo esc_html(__('Phone number', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                            </div>
                        </div>
                    </div>

                </fieldset>
                <fieldset>
                    <legend class="checkout-header">
                        <?php echo esc_html(__('Address', 'propeller-ecommerce-v2')); ?>
                    </legend>
                    <input type="hidden" name="invoice_address[type]" value="<?php echo esc_attr(AddressType::INVOICE); ?>">
                    <input type="hidden" name="invoice_address[company]" value="" id="field_company">

                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col form-group col-user-zipcode">
                                    <label class="form-label" for="field_zipcode"><?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                    <input type="text" name="invoice_address[postalCode]" value="" class="form-control required" id="field_zipcode" placeholder="<?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col form-group col-user-address">
                                    <label class="form-label" for="field_address"><?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                    <input type="text" name="invoice_address[street]" value="" class="form-control required" id="field_address" placeholder="<?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col form-group col-user-address_number">
                                    <label class="form-label" for="field_invoice_address_number"><?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                    <input type="number" ondrop="return false;" onpaste="return false;" onkeypress="return event.charCode>=48 && event.charCode<=57" name="invoice_address[number]" value="" class="form-control required" id="field_invoice_address_number" placeholder="<?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col-3 form-group col-user-address_add">
                                    <label class="form-label" for="field_address_add"><?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?></label>
                                    <input type="text" name="invoice_address[numberExtension]" value="" class="form-control" id="field_address_add" placeholder="<?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col form-group col-user-city">
                                    <label class="form-label" for="field_city"><?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                    <input type="text" name="invoice_address[city]" value="" class="form-control required" id="field_city" placeholder="<?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col form-group col-user-country">
                                    <label class="form-label" for="field_country"><?php echo esc_html(__('Country', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>

                                    <?php
                                    $countries = propel_get_countries();
                                    $selected = 'NL';

                                    if (isset($address->country) && !empty($address->country))
                                        $selected = $address->country;
                                    ?>

                                    <select id="field_country" name="invoice_address[country]" class="form-control required">
                                        <?php foreach ($countries as $code => $name) { ?>
                                            <option value="<?php echo esc_attr($code); ?>" <?php echo esc_attr($code == $selected ? 'selected' : ''); ?>><?php echo esc_html($name); ?></option>
                                        <?php } ?>
                                    </select>
                                    <!-- <input type="text" name="invoice_address[country]" value="" class="form-control required" id="field_country"> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col-12 col-md-8 form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="save_delivery_address" value="Y" checked="checked" title="Save this as delivery address">
                                        <span><?php echo esc_html(__('Delivery address is the same as billing address', 'propeller-ecommerce-v2')); ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="new-delivery-address">
                    <legend class="checkout-header">
                        <?php echo esc_html(__('Delivery address', 'propeller-ecommerce-v2')); ?>
                    </legend>
                    <input type="hidden" name="delivery_address[type]" value="<?php echo esc_attr(AddressType::DELIVERY); ?>">
                    <input type="hidden" name="delivery_address[company]" value="" id="field_company">

                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col form-group col-user-zipcode">
                                    <label class="form-label" for="field_delivery_zipcode"><?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                    <input type="text" name="delivery_address[postalCode]" value="" class="form-control required" id="field_delivery_zipcode" placeholder="<?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col form-group col-user-address">
                                    <label class="form-label" for="field_delivery_address"><?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                    <input type="text" name="delivery_address[street]" value="" class="form-control required" id="field_delivery_address" placeholder="<?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col form-group col-user-address_number">
                                    <label class="form-label" for="field_delivery_address_number"><?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                    <input type="text" name="delivery_address[number]" value="" class="form-control required" id="field_delivery_address_number" placeholder="<?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col-3 form-group col-user-address_add">
                                    <label class="form-label" for="field_delivery_address_add"><?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?></label>
                                    <input type="text" name="delivery_address[numberExtension]" value="" class="form-control" id="field_delivery_address_add" placeholder="<?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col form-group col-user-city">
                                    <label class="form-label" for="field_delivery_city"><?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>
                                    <input type="text" name="delivery_address[city]" value="" class="form-control required" id="field_delivery_city" placeholder="<?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col form-group col-user-country">
                                    <label class="form-label" for="field_delivery_country"><?php echo esc_html(__('Country', 'propeller-ecommerce-v2')); ?><span class="label-required">*</span></label>

                                    <?php
                                    $countries = propel_get_countries();
                                    $selected = 'NL';

                                    if (isset($address->country) && !empty($address->country))
                                        $selected = $address->country;
                                    ?>

                                    <select id="field_delivery_country" name="delivery_address[country]" class="form-control required">
                                        <?php foreach ($countries as $code => $name) { ?>
                                            <option value="<?php echo esc_attr($code); ?>" <?php echo esc_attr($code == $selected ? 'selected' : ''); ?>><?php echo esc_html($name); ?></option>
                                        <?php } ?>
                                    </select>

                                    <!-- <input type="text" name="delivery_address[country]" value="" class="form-control required" id="field_delivery_country"> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend class="checkout-header">
                        <?php echo esc_html(__('Your password', 'propeller-ecommerce-v2')); ?>
                    </legend>
                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col form-group col-user-password">
                                    <label class="form-label" for="field_password"><?php echo esc_html(__('Password', 'propeller-ecommerce-v2')); ?>*</label>
                                    <input type="password" name="password" value="" class="form-control required" id="field_password" pattern=".{8,}" required title="<?php echo esc_attr(__('Your password must be at least 8 characters.', 'propeller-ecommerce-v2')); ?>" placeholder="<?php echo esc_html(__('Password', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                                <div class="col form-group col-user-password">
                                    <label class="form-label" for="field_password_verify"><?php echo esc_html(__('Repeat password', 'propeller-ecommerce-v2')); ?>*</label>
                                    <input type="password" name="password_verfification" value="" class="form-control required" id="field_password_verify" title="<?php echo esc_attr(__('Your password and password repeat do not match.', 'propeller-ecommerce-v2')); ?>" placeholder="<?php echo esc_html(__('Repeat password', 'propeller-ecommerce-v2')); ?>*">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="row form-group form-group-submit">
                    <div class="col-form-fields col-12">
                        <input type="submit" class="btn-green btn-proceed" value="<?php echo esc_html(__('Send', 'propeller-ecommerce-v2')); ?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>