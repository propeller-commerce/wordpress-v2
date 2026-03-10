(function ($, window, document) {

    const {__, _x, _n, _nx} = wp.i18n;

    // https://github.com/jquery-validation/jquery-validation
    // https://jqueryvalidation.org/validate/
    Propeller.Validator = {
        init: function () {
            $.validator.setDefaults({ ignore: '' });

            this.set_messages();
            
            this.assign_validator($('form.validate').not('.modal-edit-form'));

            this.assign_validator($('form.page-login-form'));
            this.assign_validator($('form.header-login-form'));

            // trigger validation for modal forms
            $('.modal').on('shown.bs.modal', function (event) {
                if ($(this).has('form.validate'))
                    Propeller.Validator.assign_validator($(this).find('form.validate'));
            });
        },
        set_messages: function() {
            jQuery.extend(jQuery.validator.messages, {
                required: PropellerHelper.validator.required,
                remote: PropellerHelper.validator.remote,
                email: PropellerHelper.validator.email,
                url: PropellerHelper.validator.url,
                date: PropellerHelper.validator.date,
                dateISO: PropellerHelper.validator.dateISO,
                number: PropellerHelper.validator.number,
                digits: PropellerHelper.validator.digits,
                creditcard: PropellerHelper.validator.creditcard,
                equalTo: PropellerHelper.validator.equalTo,
                accept: PropellerHelper.validator.accept
            });
        },
        assign_validator: function (forms, submit_handler = null, error_handler = null) {
            if (!forms.length)
                return;

            $(forms).each(function() { $(this).validate({
                debug: false,
                // ignore: [],
                rules: {
                    password: {
                        required: true,
                        minlength: 8
                    },
                    password_verfification: {
                        required: true,
                        equalTo: '[name="password"]',
                        minlength: 8
                    }
                },
                highlight: function (element) {
                    if ($(element).is(':radio') && $(element).attr("name") == "payMethod") 
                        $(element).closest('.paymethods').removeClass('has-success').addClass('has-error');
                    else if ($(element).is(':radio') && $(element).attr("name") == "carrier") 
                        $(element).closest('.carriers').removeClass('has-success').addClass('has-error');
                    else if ($(element).is(':radio') && $(element).attr("name") == "delivery_select")
                        $(element).closest('.deliveries').removeClass('has-success').addClass('has-error');	
                    else if ($(element).is(':radio'))
                        $(element).closest('label').removeClass('has-success').addClass('has-error');   
                    else if ($(element).hasClass('cluster-option-dropdown'))
                        $(element).closest('.dropdown').removeClass('has-success').addClass('has-error');
                    else
                        $(element).parent().removeClass('has-success').addClass('has-error');
                },
                unhighlight: function (element) {
                    if ($(element).is(':radio') && $(element).attr("name") == "payMethod") 
                        $(element).closest('.paymethods').addClass('has-success').removeClass('has-error');  
                    else if ($(element).is(':radio') && $(element).attr("name") == "carrier") 
                        $(element).closest('.carriers').addClass('has-success').removeClass('has-error');
                    else if ($(element).is(':radio') && $(element).attr("name") == "delivery_select") 
                        $(element).closest('.deliveries').addClass('has-success').remove('has-error');
                    else if ($(element).is(':radio'))
                        $(element).closest('label').addClass('has-success').removeClass('has-error');
                    else if ($(element).hasClass('cluster-option-dropdown'))
                        $(element).closest('.dropdown').addClass('has-success').removeClass('has-error');
                    else
                        $(element).parent().addClass('has-success').removeClass('has-error');
                },
                invalidHandler: submit_handler ? submit_handler : Propeller.Validator.default_error_handler,
                submitHandler: error_handler ? error_handler : Propeller.Validator.default_submit_handler
            }); });
        },
        default_submit_handler: function (form, event) {
            if ($(form).hasClass('cluster-add-to-basket-form'))
                return Propeller.Cart.cart_add_item_cluster(event, form);
            if ($(form).hasClass('add-favorite'))
                return Propeller.Product.add_favorite(event, form);

            var form_data = $(form).serializeObject();

            if (Propeller.Validator.use_recaptcha(form)) {
                grecaptcha.ready(function() {
                    grecaptcha.execute(PropellerHelper.behavior.recaptcha_site_key, {action: 'submit'}).then(function(token) {
                        form_data.rc_token = token;

                        Propeller.Ajax.call({
                            url: PropellerHelper.ajax_url,
                            method: 'POST',
                            data: form_data,
                            loading: $(form).find('[type="submit"]'),
                            success: function (data, msg, xhr) {
                                if (typeof data.postprocess != undefined && typeof data.object != 'undefined') {
                                    Propeller[data.object].postprocess(data.postprocess);
                                    $(document).trigger('propeller_submit_success', data)
                                    $(form).trigger('reset');
                                }
                            },
                            error: function () {
                                Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                                console.log('error', arguments);
                            }
                        });
                    });
                });
            }
            else {
                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: form_data,
                    loading: $(form).find('[type="submit"]'),
                    success: function (data, msg, xhr) {
                        if (typeof data.postprocess != undefined && typeof data.object != 'undefined') {
                            Propeller[data.object].postprocess(data.postprocess);
                            $(document).trigger('propeller_submit_success', data)
                            $(form).trigger('reset');
                        }
                    },
                    error: function () {
                        Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                        console.log('error', arguments);
                    }
                });
            }
        },
        default_error_handler: function (event, validator) {
            event.preventDefault();
            event.stopPropagation();

            if (validator.errorList.length > 0) {
                for (var i = 0; i < validator.errorList.length; i++)
                    Propeller.Validator.display_error(validator.errorList[i]);
            }

            return false;
        },
        display_error: function (err) {
            if ($(err.element).is(':radio')) {
                if($(err.element).attr("name") == 'payMethod'|| $(err.element).attr("name") == 'carrier' || $(err.element).attr("name") == 'delivery_select' ) {
                    if (!$(err.element).closest('.col-form-fields').find('span.input-error-message').length)
                        $('<span class="input-error-message">' + err.message + '</span>').insertBefore($(err.element).closest('.radios-container'));
                    else
                        $(err.element).closest('.col-form-fields').find('span.input-error-message').html(err.message);

                    $(err.element).closest('.radios-container').find('.form-check-label').off('click').click(function (event) {
                        $(this).removeClass('input-error');
                        $(this).closest('.radios-container').parent().find('span.input-error-message').hide();
                    });
                }
                else {
                    if (!$(err.element).closest('.radios-container').find('span.input-error-message').length)
                        $('<span class="input-error-message">' + err.message + '</span>').insertAfter($(err.element).closest('.radios-container'));
                    else
                        $(err.element).closest('.radios-container').find('span.input-error-message').html(err.message);

                    $(err.element).closest('.radios-container').find('.form-check-label').off('click').click(function (event) {
                        $(this).removeClass('input-error');
                        $(this).closest('.radios-container').parent().find('span.input-error-message').hide();
                    });
                }
            } else if ($(err.element).hasClass('cluster-option-dropdown')) {
                var dd_content = $(err.element).closest('.dropdown');
                $(dd_content).find('.ts-control').addClass('input-error');

                if (!$(dd_content).find('span.input-error-message').length) 
                    $('<span class="input-error-message">' + err.message + '</span>').insertAfter($(dd_content).find('.ts-wrapper'));
                    
                else 
                    $(dd_content).find('span.input-error-message').html(err.message);
                $(dd_content).off('click').click(function (event) {
                    $(this).find('.ts-control').removeClass('input-error');
                    $(this).closest('span.input-error-message').hide();
                });

            } else {
                $(err.element).addClass('input-error');

                if (!$(err.element).siblings('span.input-error-message').length)
                    $('<span class="input-error-message">' + err.message + '</span>').insertAfter(err.element);
                else
                    $(err.element).siblings('span.input-error-message').html(err.message);

                $(err.element).off('focus').focus(function (event) {
                    $(this).removeClass('input-error');
                    $(this).next('span.input-error-message').hide();
                });
            }
        },
        use_recaptcha: function(form) {
            return typeof PropellerHelper.behavior.use_recaptcha != 'undefined' && 
                   PropellerHelper.behavior.use_recaptcha == true && 
                   typeof PropellerHelper.behavior.recaptcha_site_key != 'undefined' && 
                   PropellerHelper.behavior.recaptcha_site_key != '' && 
                   ($(form).hasClass('page-login-form') || $(form).hasClass('header-login-form') || $(form).hasClass('register-form'));
        }
    };

    //Propeller.Validator.init();

}(window.jQuery, window, document));