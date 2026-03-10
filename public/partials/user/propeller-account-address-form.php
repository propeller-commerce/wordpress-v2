<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
?>
<form name="edit-address-form" id="edit_address<?php echo esc_attr($address->id); ?>" class="form-horizontal validate form-handler modal-edit-form" method="post">
    <?php if ($address->id > 0) { ?>
        <input type="hidden" name="id" value="<?php echo esc_attr($address->id); ?>">
    <?php } ?>
    <input type="hidden" name="type" value="<?php echo esc_attr($address->type); ?>">
    <input type="hidden" name="action" value="<?php echo is_numeric($address->id) ? 'update_address' : 'add_address' ?>">

    <fieldset class="personal">
        <div class="row form-group">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col-12 form-group col-user-company">
                        <label class="form-label" for="company_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Company name', 'propeller-ecommerce-v2')); ?><?php if (SessionController::get(PROPELLER_USER_DATA)->__typename !== 'Customer') { ?>*<?php } ?></label>
                        <input type="text" name="company" placeholder="<?php echo esc_html(__('Company name', 'propeller-ecommerce-v2')); ?>" value="<?php echo esc_attr($address->company); ?>" class="form-control <?php if (SessionController::get(PROPELLER_USER_DATA)->__typename !== 'Customer') { ?> required <?php } ?>" id="company_<?php echo esc_attr($address->id); ?>" aria-describedby="val_company_<?php echo esc_attr($address->id); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col-auto form-group ">
                        <label class="btn-radio-checkbox -label ">
                            <input type="radio" class="-input" name="gender" value="M" <?php echo esc_html(($address->gender == 'M' ? 'checked' : '')); ?>> <span><?php echo esc_html(__('Mr.', 'propeller-ecommerce-v2')); ?></span>
                        </label>
                    </div>
                    <div class="col-auto form-group ">
                        <label class="btn-radio-checkbox -label ">
                            <input type="radio" class="-input" name="gender" value="F" <?php echo esc_html(($address->gender == 'F' ? 'checked' : '')); ?>> <span><?php echo esc_html(__('Mrs.', 'propeller-ecommerce-v2')); ?></span>
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
                        <label class="form-label" for="firstName_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*</label>
                        <input type="text" name="firstName" value="<?php echo esc_attr($address->firstName); ?>" placeholder="<?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="firstName_<?php echo esc_attr($address->id); ?>">
                    </div>
                    <div class="col-3 form-group col-user-middlename">
                        <label class="form-label" for="middleName_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Insertion (optional)', 'propeller-ecommerce-v2')); ?></label>
                        <input type="text" name="middleName" value="<?php echo esc_attr($address->middleName); ?>" placeholder="<?php echo esc_html(__('Insertion (optional)', 'propeller-ecommerce-v2')); ?>" class="form-control" id="middleName_<?php echo esc_attr($address->id); ?>">
                    </div>
                    <div class="col form-group col-user-lastname">
                        <label class="form-label" for="lastName_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*</label>
                        <input type="text" name="lastName" value="<?php echo esc_attr($address->lastName); ?>" placeholder="<?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="lastName_<?php echo esc_attr($address->id); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col-12 form-group col-user-address">
                        <label class="form-label" for="email_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('E-mail', 'propeller-ecommerce-v2')); ?>*</label>
                        <input type="email" name="email" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" value="<?php echo esc_attr($address->email); ?>" placeholder="<?php echo esc_html(__('E-mail', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="email_<?php echo esc_attr($address->id); ?>">
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <div class="row form-group">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col form-group col-user-street">
                        <label class="form-label" for="street_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?>*</label>
                        <input type="text" name="street" value="<?php echo esc_attr($address->street); ?>" placeholder="<?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="street_<?php echo esc_attr($address->id); ?>">
                    </div>
                    <div class="col form-group col-user-street-number">
                        <label class="form-label" for="number_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?>*</label>
                        <input type="text" name="number" value="<?php echo esc_attr($address->number); ?>" placeholder="<?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="number_<?php echo esc_attr($address->id); ?>">
                    </div>
                    <div class="col-3 form-group col-user-address_add">
                        <label class="form-label" for="number_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?></label>
                        <input type="text" name="numberExtension" value="<?php echo esc_attr($address->numberExtension); ?>" placeholder="<?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?>" class="form-control" maxlength="7" id="numberExtension_<?php echo esc_attr($address->id); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col form-group col-user-zipcode">
                        <label class="form-label" for="postalCode_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*</label>
                        <input type="text" name="postalCode" value="<?php echo esc_attr($address->postalCode); ?>" placeholder="<?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="postalCode_<?php echo esc_attr($address->id); ?>">
                    </div>
                    <div class="col form-group col-user-city">
                        <label class="form-label" for="city_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*</label>
                        <input type="text" name="city" value="<?php echo esc_attr($address->city); ?>" placeholder="<?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="city_<?php echo esc_attr($address->id); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col-12 form-group col-user-country">
                        <label class="form-label" for="country_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Country', 'propeller-ecommerce-v2')); ?>*</label>

                        <!-- <input type="text" name="country" value="<?php echo esc_attr($address->country); ?>" class="form-control required" id="country_<?php echo esc_attr($address->id); ?>"> -->

                        <?php
                        $countries = propel_get_countries();
                        $selected = 'NL';

                        if (isset($address->country) && !empty($address->country))
                            $selected = $address->country;
                        ?>

                        <select id="country_<?php echo esc_attr($address->id); ?>" name="country" class="form-control required">
                            <?php foreach ($countries as $code => $name) { ?>
                                <option value="<?php echo esc_attr($code); ?>" <?php echo esc_html(($code == $selected ? 'selected' : '')); ?>><?php echo esc_html($name); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col-12 form-group col-user-notes">
                        <label class="form-label" for="notes_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Remarks (optional)', 'propeller-ecommerce-v2')); ?></label>
                        <textarea name="notes" placeholder="<?php echo esc_html(__('Remarks (optional)', 'propeller-ecommerce-v2')); ?> " class="form-control" id="notes_<?php echo esc_attr($address->id); ?>"><?php echo esc_html($address->notes); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col-12 ">
                        <label class="-label">
                            <input class="-input" type="checkbox" name="isDefault" value="Y" aria-required="true" <?php echo esc_html(isset($address->isDefault) && $address->isDefault == 'Y' ? 'checked' : ''); ?>>
                            <?php if ($address->type == 'delivery') { ?>
                                <span><?php echo esc_html(__('Set as default delivery address', 'propeller-ecommerce-v2')); ?></span>
                            <?php } else { ?>
                                <span><?php echo esc_html(__('Set as default billing address', 'propeller-ecommerce-v2')); ?></span>
                            <?php } ?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <div class="row form-group form-group-submit propel-modal-foote">
        <div class="col-form-fields col-12">
            <div class="row g-3">
                <div class="col-12">
                    <button type="submit" class="btn-modal btn-proceed w-100 justify-content-center btn-modal-address btn-modal-submit" id="submit_edit_address<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Send', 'propeller-ecommerce-v2')); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>