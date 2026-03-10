<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Enum\AddressTypeCart;

$invoice_address = $this->get_invoice_address();

$id = wp_rand(1, 100);

?>
<div class="propeller-checkout-wrapper">
    <div class="container-fluid px-0 checkout-header-wrapper">
        <div class="row align-items-start">
            <div class="col-12 col-sm me-auto checkout-header">
                <h1><?php echo esc_html(__('Order', 'propeller-ecommerce-v2')); ?></h1>
            </div>
        </div>
    </div>
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="checkout-wrapper-steps">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="checkout-step"><?php echo esc_html(__('Step 1', 'propeller-ecommerce-v2')); ?></div>
                            <div class="checkout-title"><?php echo esc_html(__('Invoice details', 'propeller-ecommerce-v2')); ?></div>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <div class="checkout-step-nr">1/3</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <form name="checkout" class="form-handler checkout-form validate" method="post" action="">
                                <input type="hidden" name="action" value="cart_update_address" />
                                <input type="hidden" name="step" value="<?php echo esc_attr($slug); ?>" />
                                <input type="hidden" name="next_step" value="2" />
                                <input type="hidden" name="type" value="<?php echo esc_attr(AddressTypeCart::INVOICE); ?>" />
                                <input type="hidden" name="icp" value="N" />

                                <fieldset class="personal">
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-mail">
                                                    <label class="form-label" for="email_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="email" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" name="email" value="<?php echo esc_attr($invoice_address->email); ?>" placeholder="<?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*" class="form-control required email" id="email_<?php echo esc_attr($id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-password">
                                                    <label class="form-label" for="password_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('Password (optional)', 'propeller-ecommerce-v2')); ?></label>
                                                    <input type="password" name="password" value="" class="form-control" placeholder="<?php echo esc_html(__('Password (optional)', 'propeller-ecommerce-v2')); ?>" id="password_<?php echo esc_attr($id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-auto form-group form-check">
                                                    <label class="btn-radio-checkbox form-check-label ">
                                                        <input type="radio" class="form-check-input" name="gender" value="M" <?php echo esc_attr((string) $invoice_address->gender == 'M' ? 'checked' : ''); ?>> <span><?php echo esc_html(__('Mr.', 'propeller-ecommerce-v2')); ?></span>
                                                    </label>
                                                </div>
                                                <div class="col-auto form-group form-check">
                                                    <label class="btn-radio-checkbox form-check-label ">
                                                        <input type="radio" class="form-check-input" name="gender" value="F" <?php echo esc_attr((string) $invoice_address->gender == 'F' ? 'checked' : ''); ?>> <span><?php echo esc_html(__('Mrs.', 'propeller-ecommerce-v2')); ?></span>
                                                    </label>
                                                </div>
                                                <div class="col-auto form-group form-check">
                                                    <label class="btn-radio-checkbox form-check-label ">
                                                        <input type="radio" class="form-check-input" name="gender" value="U" <?php echo esc_attr((string) $invoice_address->gender == 'U' ? 'checked' : ''); ?>> <span><?php echo esc_html(__('Unknown', 'propeller-ecommerce-v2')); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md form-group col-user-firstname">
                                                    <label class="form-label" for="firstName_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="firstName" value="<?php echo esc_attr($invoice_address->firstName); ?>" placeholder="<?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="firstName_<?php echo esc_attr($id); ?>">
                                                </div>
                                                <div class="col-12 col-md form-group col-user-middlename">
                                                    <label class="form-label" for="middleName_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('Insertion (optional)', 'propeller-ecommerce-v2')); ?></label>
                                                    <input type="text" name="middleName" value="<?php echo esc_attr($invoice_address->middleName); ?>" placeholder="<?php echo esc_html(__('Insertion (optional)', 'propeller-ecommerce-v2')); ?>" class="form-control" id="middleName_<?php echo esc_attr($id); ?>">
                                                </div>
                                                <div class="col-12 col-md form-group col-user-lastname">
                                                    <label class="form-label" for="lastName_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="lastName" value="<?php echo esc_attr($invoice_address->lastName); ?>" placeholder="<?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="lastName_<?php echo esc_attr($id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-phone">
                                                    <label class="form-label" for="phone_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('Phone number', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="phone" value="<?php echo esc_attr($invoice_address->phone); ?>" placeholder="<?php echo esc_html(__('Phone number', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="phone_<?php echo esc_attr($id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <legend class="checkout-header">
                                        <?php echo esc_html(__('Billing address', 'propeller-ecommerce-v2')); ?>
                                    </legend>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-company">
                                                    <label class="form-label" for="company_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('Company name', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="company" value="<?php echo esc_attr($invoice_address->company); ?>" placeholder="<?php echo esc_html(__('Company name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="company_<?php echo esc_attr($id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-8 form-group col-user-street">
                                                    <label class="form-label" for="street_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('Street name', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="street" value="<?php echo esc_attr($invoice_address->street); ?>" placeholder="<?php echo esc_html(__('Street number', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="street_<?php echo esc_attr($id); ?>">
                                                </div>
                                                <div class="col-4 form-group col-user-street-number">
                                                    <label class="form-label" for="number_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="number" value="<?php echo esc_attr($invoice_address->number); ?>" placeholder="<?php echo esc_html(__('Number', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="number_<?php echo esc_attr($id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-address_add">
                                                    <label class="form-label" for="numberExtension_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?></label>
                                                    <input type="text" name="numberExtension" value="<?php echo esc_attr($invoice_address->numberExtension); ?>" placeholder="<?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?>" class="form-control" id="numberExtension_<?php echo esc_attr($id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-zipcode">
                                                    <label class="form-label" for="postalCode_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="postalCode" value="<?php echo esc_attr($invoice_address->postalCode); ?>" placeholder="<?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="postalCode_<?php echo esc_attr($id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-city">
                                                    <label class="form-label" for="city_<?php echo esc_attr($id); ?>"><?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="city" value="<?php echo esc_attr($invoice_address->city); ?>" placeholder="<?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="city_<?php echo esc_attr($id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-country">
                                                    <label class="form-label" for="country_<?php echo esc_attr($address->id); ?>"><?php echo esc_html(__('Country', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <?php
                                                    $countries = propel_get_countries();
                                                    $selected = 'NL';

                                                    if (isset($invoice_address->country) && !empty($invoice_address->country))
                                                        $selected = $invoice_address->country;
                                                    ?>

                                                    <select id="country_<?php echo esc_attr($id); ?>" name="country" class="form-control required">
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
                                                <div class="col-12 col-md-8 form-group col-user-taxnr">
                                                    <label class="form-label" for="taxnr"><?php echo esc_html(__('VAT number', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="taxnr" value="" placeholder="<?php echo esc_html(__('VAT number', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="taxnr">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row form-group form-group-submit">
                                    <div class="col-form-fields col-12">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-8">
                                                <button type="submit" class="btn-proceed"><?php echo esc_html(__('Continue', 'propeller-ecommerce-v2')); ?></button>
                                                <!--<a href="/checkout-2" class="btn-proceed"><?php echo esc_html(__('Continue', 'propeller-ecommerce-v2')); ?></a>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="checkout-wrapper-steps">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="checkout-step"><?php echo esc_html(__('Step 2', 'propeller-ecommerce-v2')); ?></div>
                            <div class="checkout-title"><?php echo esc_html(__('Delivery', 'propeller-ecommerce-v2')); ?></div>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <div class="checkout-step-nr">2/3</div>
                        </div>
                    </div>
                </div>
                <div class="checkout-wrapper-steps">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="checkout-step"><?php echo esc_html(__('Step 3', 'propeller-ecommerce-v2')); ?></div>
                            <div class="checkout-title"><?php echo esc_html(__('Shipping method', 'propeller-ecommerce-v2')); ?></div>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <div class="checkout-step-nr">3/3</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">

                <?php include $this->partials_dir . '/cart/propeller-shopping-cart-totals.php' ?>
            </div>
        </div>
    </div>
</div>


<?php include $this->partials_dir . '/other/propeller-toast.php' ?>
