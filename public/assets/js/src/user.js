(function ($, window, document) {

    Propeller.User = {
        is_switching_vat: false,
        init: function () {
            $('.price-toggle a').off('click').click(this.custom_prices);
            $('.contact-companies .contact-company').off('click').on('click', this.company_change);

            this.init_purchase_authorization_setup();

            this.init_purchase_authorization_requests();

            this.fix_mini_account_dropdown();
        },
        init_purchase_authorization_setup: function () {
            $('.purchase-authorization-contact-item .purchase-authorization-limit').off('blur').on('blur', this.purchase_authorization_limit_change);
            $('.purchase-authorization-contact-item .purchase-authorization-limit').off('input').on('input', this.purchase_authorization_limit_input);
            $('.purchase-authorization-contact-item .purchase-authorization-role').off('change').on('change', this.purchase_authorization_role_change);
            $('.purchase-authorization-contact-item .btn-purchase-authorization-contact-create').off('click').on('click', this.purchase_authorization_contact_submit);
            $('.purchase-authorization-contact-item .btn-purchase-authorization-contact-update').off('click').on('click', this.purchase_authorization_contact_submit);
            $('.purchase-authorization-contact-item .btn-purchase-authorization-contact-delete').off('click').on('click', this.purchase_authorization_contact_submit);

            $('.propeller-purchase-authorizations-contacts-pagination').find('a.page-item').off('click').on('click', this.purchase_authorizations_contacts_paging);

            $('.btn-purchase-authorization-auth-create').off('click').on('click', this.purchase_authorization_auth_create);
            
            // Handle delete login modal
            $('#delete_login_modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var contactName = button.data('contact_name');
                var contactId = button.data('contact_id');
                
                $('#delete_contact_name').text(contactName);
                $('.btn-confirm-delete-login').data('contact_id', contactId);
            });
            
            $('.btn-confirm-delete-login').off('click').on('click', this.purchase_authorization_auth_delete);

            $('.btn-pac-add-contact').off('click').on('click', function (event) {
                event.preventDefault();

                $('#pac-add-contact').submit();

                return false;
            });
        },
        init_purchase_authorization_requests: function () {
            $('#purchase_authorization_preview_modal').on('show.bs.modal', this.preview_purchase_authorization);
            $('#purchase_authorization_preview_modal').on('hidden.bs.modal', this.reset_purchase_authorization_modal);

            // Handle the confirmation button clicks to transfer cart_id
            $(document).on('click', '.btn-purchase-authorization-delete-confirm', this.prepare_delete_confirmation);
            $(document).on('click', '.btn-purchase-authorization-accept-confirm', this.prepare_accept_confirmation);

            // Bind the actual delete/accept actions
            $('#delete_purchase_authorization').on('shown.bs.modal', this.init_delete_purchase_authorization_buttons);
            $('#delete_purchase_authorization').on('hidden.bs.modal', this.reset_delete_purchase_authorization_modal);

            $('#accept_purchase_authorization').on('shown.bs.modal', this.init_accept_purchase_authorization_buttons);
        },
        prepare_delete_confirmation: function(event) {
            var cart_id = $(this).data('cart');
            // Store cart_id temporarily so the confirmation modal can access it
            $('#delete_purchase_authorization').data('pending-cart-id', cart_id);
        },
        prepare_accept_confirmation: function(event) {
            var cart_id = $(this).data('cart');
            // Store cart_id temporarily so the confirmation modal can access it
            $('#accept_purchase_authorization').data('pending-cart-id', cart_id);
        },
        preview_purchase_authorization: function (event) {
            var modal_content = $(this).find('.purchase-authorization-content');
            var btn_delete = $(this).find('.btn-purchase-authorization-delete-confirm');
            var btn_accept = $(this).find('.btn-purchase-authorization-accept-confirm');

            // clear previous content
            $(modal_content).html('');

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                loading: modal_content,
                data: {
                    action: 'preview_authorization_request',
                    cart_id: $(event.relatedTarget).data('cart')
                },
                success: function (data, msg, xhr) {
                    if (data.success && typeof data.content != 'undefined') {
                        $(modal_content).html(data.content);

                        $(btn_delete).data('cart', data.cart_id);
                        $(btn_accept).data('cart', data.cart_id);
                    }

                },
                error: function () {
                    console.log('error', arguments);
                }
            });
        },
        init_delete_purchase_authorization_buttons: function (event) {
           
            var cart_id = $('#delete_purchase_authorization').data('pending-cart-id');
            $('.btn-purchase-authorization-delete').data('cart', cart_id);

            $('.btn-purchase-authorization-delete').off('click').on('click', Propeller.User.delete_purchase_authorization);
        },
        init_accept_purchase_authorization_buttons: function (event) {
            
            var cart_id = $('#accept_purchase_authorization').data('pending-cart-id');
            $('.btn-purchase-authorization-accept').data('cart', cart_id);

            $('.btn-purchase-authorization-accept').off('click').on('click', Propeller.User.accept_purchase_authorization);
        },
        delete_purchase_authorization: function (event) {
            event.preventDefault();
            event.stopPropagation();
            
            var cart_id = $(this).data('cart');
            
            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                loading: $('#delete_purchase_authorization').find('.modal-content'),
                data: {
                    action: 'delete_authorization_request',
                    cart_id: cart_id
                },
                success: function (data, msg, xhr) {
                    if (data.success) {
                        $('#delete_purchase_authorization').modal('hide');
                        $('#purchase_authorization_preview_modal').modal('hide');
                        $(`div.purchase-authorization-item[data-cart="${data.cart_id}"]`).remove();
                  
                    }
                },
                error: function () {
                    console.log('error', arguments);
                    Propeller.Toast.show('Propeller', '', 'An error occurred while deleting', 'error');
                }
            });
            
            return false;
        },
        accept_purchase_authorization: function (event) {
            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                loading: $('#accept_purchase_authorization').find('.modal-content'),
                data: {
                    action: 'accept_authorization_request',
                    cart_id: $(this).data('cart')
                },
                success: function (data, msg, xhr) {
                    if (data.success) {
                        Propeller.Toast.show('Propeller', '', data.message, data.success ? 'success' : 'error');

                        $('#accept_purchase_authorization').modal('hide');
                        $('#purchase_authorization_preview_modal').modal('hide');

                        if (typeof data.redirect != 'undefined')
                            window.location.href = data.redirect;
                    }
                },
                error: function () {
                    console.log('error', arguments);
                }
            });
        },
        reset_purchase_authorization_modal: function (event) {
            $(this).find('.purchase-authorization-content').html('');
            $(this).find('.btn-purchase-authorization-delete-confirm').removeData('cart');
            $(this).find('.btn-purchase-authorization-accept-confirm').removeData('cart');
        },
        reset_delete_purchase_authorization_modal: function (event) {
            $(this).find('.btn-purchase-authorization-delete').removeData('cart');
        },
        purchase_authorization_limit_input: function (event) {
            var $input = $(this);
            var $container = $input.closest('.purchase-authorization-contact-item');
            var inputValue = $input.val().trim();
            var $label = $container.find('.label-authorization');
            var $limitSpan = $container.find('.label-authorization-limit');
            
            // Get currency symbol from the input group
            var currency = $container.find('.input-group-text').text();
            
            // Update label in real-time based on input value
            if (inputValue === '' || inputValue === null || inputValue === '0') {
                // Empty or zero value
                if (inputValue === '' || inputValue === null) {
                    $label.html(PropellerHelper.translations.no_limit_unlimited);
                } else {
                    $label.html(`<span class="label-authorization-limit">${currency}${inputValue}</span> ${PropellerHelper.translations.limit_auth_required}`);
                }
            } else {
                // Has a positive value
                var numericValue = parseFloat(inputValue);
                if (!isNaN(numericValue) && numericValue > 0) {
                    $label.html(`${PropellerHelper.translations.purchases_up_to} <span class="label-authorization-limit">${currency}${inputValue}</span> ${PropellerHelper.translations.allowed}`);
                }
            }
        },
        purchase_authorization_limit_change: function (event) {
            var $input = $(this);
            var $container = $input.closest('.purchase-authorization-contact-item');
            var inputValue = $input.val().trim();
            var initialValue = $input.data('initial-value');
            
            // Store initial value on first load if not already stored
            if (typeof initialValue === 'undefined') {
                initialValue = $input.attr('value') || '';
                $input.data('initial-value', initialValue);
            }

            // If value hasn't changed, do nothing
            if (inputValue === initialValue) {
                return;
            }

            // Determine which action to trigger based on input state
            var $btnUpdate = $container.find('.btn-purchase-authorization-contact-update');
            var $btnDelete = $container.find('.btn-purchase-authorization-contact-delete');
            var $btnCreate = $container.find('.btn-purchase-authorization-contact-create');

            // Case 1: Input is empty - trigger delete if update button exists
            if (inputValue === '' || inputValue === null) {
                if ($btnDelete.length > 0) {
                    // Trigger delete action
                    $btnDelete.click();
                }
            }
            // Case 2: Input has value and update button exists - trigger update
            else if ($btnUpdate.length > 0) {
                var numericValue = parseFloat(inputValue);
                if (!isNaN(numericValue) && numericValue >= 0) {
                    $btnUpdate.click();
                }
            }
            // Case 3: Input has value and create button exists - trigger create
            else if ($btnCreate.length > 0) {
                var numericValue = parseFloat(inputValue);
                if (!isNaN(numericValue) && numericValue >= 0) {
                    $btnCreate.click();
                }
            }
        },
        purchase_authorization_role_change: function (event) {
            var $select = $(this);
            var $container = $select.closest('.purchase-authorization-contact-item');
            var $btnUpdate = $container.find('.btn-purchase-authorization-contact-update');
            var $btnCreate = $container.find('.btn-purchase-authorization-contact-create');

            // Trigger update if update button exists, otherwise create
            if ($btnUpdate.length > 0) {
                $btnUpdate.click();
            } else if ($btnCreate.length > 0) {
                $btnCreate.click();
            }
        },
        purchase_authorization_contact_submit: function (event) {
            event.preventDefault();

            var $button = $(this);
            var $container = $button.closest('.purchase-authorization-contact-item');
            var $input = $container.find('.purchase-authorization-limit');

            var form_data = {
                action: $button.data('action'),
                contact_id: $container.data('contact_id'),
                limit: $input.val(),
                purchase_autorization_id: $container.data('purchase_autorization_id'),
                purchase_authorization_role: $container.find('.purchase-authorization-role').val()
            };

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $button,
                success: function (data, msg, xhr) {
                    Propeller.Toast.show('Propeller', '', data.message, data.success ? 'success' : 'error');
                    if (data.success && typeof data.content != 'undefined') {
                        $('.purchase-authorization-configs-list').html(data.content);
                        Propeller.User.init_purchase_authorization_setup();
                        
                        // Update initial values for all inputs after successful operation
                        $('.purchase-authorization-contact-item .purchase-authorization-limit').each(function() {
                            var $field = $(this);
                            $field.data('initial-value', $field.val());
                        });
                    }
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        purchase_authorizations_contacts_paging: function (event) {
            event.preventDefault();

            var form_data = {
                'action': $(this).closest('.propeller-purchase-authorizations-contacts-pagination').data('action'),
                'page': $(this).data('page')
            };

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $(this),
                success: function (data, msg, xhr) {
                    if (data.success && typeof data.content != 'undefined') {
                        $('.purchase-authorization-configs-list').html(data.content);
                        Propeller.User.init_purchase_authorization_setup();
                        
                        // Update initial values for all inputs after pagination
                        $('.purchase-authorization-contact-item .purchase-authorization-limit').each(function() {
                            var $field = $(this);
                            $field.data('initial-value', $field.val());
                        });
                    }
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        purchase_authorization_auth_create: function (event) {
            event.preventDefault();

            var form_data = {
                action: $(this).data('action'),
                contact_id: $(this).data('contact_id'),
                email: $(this).data('email'),
                displayName: $(this).data('displayname')
            };

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $(this),
                success: function (data, msg, xhr) {
                    Propeller.User.postprocess(data);
                },
                error: function () {
                    console.log('error', arguments);
                }
            });
            return false;
        },
        purchase_authorization_auth_delete: function (event) {
            event.preventDefault();

            var $button = $(this);
            var form_data = {
                action: 'delete_contact_login',
                contact_id: $button.data('contact_id')
            };

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $button,
                success: function (data, msg, xhr) {
                    $('#delete_login_modal').modal('hide');
                    Propeller.User.postprocess(data);
                },
                error: function () {
                    $('#delete_login_modal').modal('hide');
                    console.log('error', arguments);
                }
            });
            return false;
        },
        fix_mini_account_dropdown: function () {
            $('.dropdown-account li a').on('click', function (event) {
                $(this).closest('.dropdown-account').toggleClass('show');
            });

            $('.contact-companies .contact-company').off('click').on('click', Propeller.User.company_change);
        },
        custom_prices: function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (!Propeller.User.is_switching_vat) {
                Propeller.User.is_switching_vat = true;

                var show_specific_prices = 0;

                if ($(this).closest('.price-toggle').hasClass('price-on')) {
                    $(this).closest('.price-toggle').removeClass('price-on').addClass('price-off');
                    show_specific_prices = 0;
                }
                else {
                    $(this).closest('.price-toggle').removeClass('price-off').addClass('price-on');
                    show_specific_prices = 1;
                }

                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'user_prices',
                        active: show_specific_prices
                    },
                    success: function (data, msg, xhr) {
                        Propeller.User.is_switching_vat = false;
                        if (data.success && data.reload)
                            window.location.reload();
                    },
                    error: function () {
                        Propeller.User.is_switching_vat = false;
                        console.log('error', arguments);
                    }
                });
            }

            return false;
        },
        company_change: function (event) {
            var company_id = $(this).data('id');
            
            // Get company data immediately
            var companies_arr = JSON.parse($('#contact_companies_object').val());
            var selected_company = companies_arr.filter(function (c) {
                return c.companyId == parseInt(company_id);
            });

            // Update modal content before showing it
            if (selected_company.length > 0) {
                $('#propel_company_switch_modal').find('.selected-company-name').html(selected_company[0].name);
                
                // Check if addresses exist and have city before setting it
                if (selected_company[0].addresses && 
                    selected_company[0].addresses.length > 0 && 
                    selected_company[0].addresses[0].city) {
                    $('#propel_company_switch_modal').find('.selected-company-city').html(selected_company[0].addresses[0].city);
                }
            }

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                // loading: $('.contact-companies-dropdown'),
                data: {
                    action: 'company_switch',
                    company: company_id
                },
                success: function (data, msg, xhr) {
                    if (data.success && data.reload)
                        window.location.reload();
                },
                error: function () {
                    console.log('error', arguments);
                }
            });
        },
        view_authorization_request: function (event) {
            event.preventDefault();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                loading: $(this),
                data: {
                    action: 'view_purchase_authorization',
                    cart_id: $(this).data('cart')
                },
                success: function (data, msg, xhr) {
                    if (data.success && data.content) {
                        // TODO: implement big modal with cart content
                    }
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        load_mini_account: function () {
            var el = $('#propel_mini_account');
            // var parent = el.parent();

            // $(parent).css('height', $(parent).outerHeight());
            // $(parent).css('width', $(parent).outerWidth());

            // $(el).find('.account-title').css('width', $(el).find('.account-title').outerWidth());
            // $(el).find('.account-title').css('height', $(el).find('.account-title').outerHeight());

            // $(el).find('.account-user').css('width', $(el).find('.account-user').outerWidth());
            // $(el).find('.account-user').css('height', $(el).find('.account-user').outerHeight());

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'propel_load_mini_account',
                    ref: window.location.href
                },
                success: function (data, msg, xhr) {
                    if (typeof data.content != 'undefined') {
                        $(el).find('.account-title').fadeOut(10, function () {
                            $(this).html(data.title);
                            $(this).fadeIn(10);
                        });

                        $(el).find('.account-user').fadeOut(10, function () {
                            $(this).html(data.name);
                            $(this).fadeIn(10);
                        });

                        if ($(data.content).find('input[name="user_mail"]').length > 0) {
                            $(data.content).find('input[name="user_mail"]').val($(el).find('#header-dropdown-account').find('input[name="user_mail"]').val());
                            $(data.content).find('input[name="user_password"]').val($(el).find('#header-dropdown-account').find('input[name="user_password"]').val());
                        }


                        $(el).find('#header-dropdown-account').html(data.content);

                        Propeller.Validator.assign_validator($('form.header-login-form'));

                        // do other initialization methods
                        Propeller.User.fix_mini_account_dropdown();
                    }
                },
                error: function () {
                    console.log('error', arguments);
                }
            });
        },
        postprocess: function (data) {
            Propeller.Toast.show('Propeller', '', data.message, data.success ? 'success' : 'error');

            if (data.success && typeof data.analytics != 'undefined')
                $('body').append(data.analytics);

            if (data.reload)
                window.location.reload();
        }
    };

    $(function () {
        Propeller.User.load_mini_account();
    });

}(window.jQuery, window, document));