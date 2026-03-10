(function ($, window, document) {

    const {__, _x, _n, _nx} = wp.i18n;

    Propeller.Register = {
        init: function() {
            $('input[type=radio][name=parentId]').off('change').change(function(e) {
                $('[name="user_type"]').val($(this).data('type'));

                if ($(this).data('type') == 'Customer') {
                    $('[name="taxNumber"]').removeClass('required');
                    $('[name="taxNumber"]').closest('.col.form-group').hide();
                    $('[name="cocNumber"]').removeClass('required');
                    $('[name="cocNumber"]').closest('.col.form-group').hide();
                    $('[name="company_name"]').removeClass('required');
                    $('[name="company_name"]').closest('.col.form-group').hide();
                    $('[name="invoice_address[company]"], [name="delivery_address[company]"]')
                    .removeClass('required')
                    .val('')
                    .attr('aria-invalid','false');                    
                }
                else {
                    $('[name="taxNumber"]').addClass('required');
                    $('[name="taxNumber"]').closest('.col.form-group').show();
                    $('[name="cocNumber"]').addClass('required');
                    $('[name="cocNumber"]').closest('.col.form-group').show();
                    $('[name="company_name"]').addClass('required');
                    $('[name="company_name"]').closest('.col.form-group').show();
                    $('[name="invoice_address[company]"], [name="delivery_address[company]"]')
                    .addClass('required')
                    .val($('[name="company_name"]').val())
                    .attr('aria-invalid','true');
                    $('[name="invoice_address[company]"]').addClass('required');
                    $('[name="invoice_address[company]"]').attr('aria-invalid','true');
                    $('[name="delivery_address[company]"]').addClass('required');
                    $('[name="delivery_address[company]"]').attr('aria-invalid','true');
                  
                }
            });

            $('body').off('change', "input[name='save_delivery_address']").on('change', "input[name='save_delivery_address']", function () {
              
                if ($(this).is(':checked')) {
                    // Hide delivery address fields when checked
                    $('.new-delivery-address').slideUp(500);
                    $('[name^="delivery_address["]').removeClass('required');
                }
                else
                {
                    // Show delivery address fields when unchecked
                    $('.new-delivery-address').slideDown(500);
                    
                    if ($('[name="user_type"]').val() == 'Customer') {
                        $('[name="delivery_address[company]"]').removeClass('required');
                    }
                    else {
                        $('[name="delivery_address[company]"]').addClass('required');
                    }
                    $('[name="delivery_address[street]"]').addClass('required');
                    $('[name="delivery_address[number]"]').addClass('required');
                    $('[name="delivery_address[postalCode]"]').addClass('required');
                    $('[name="delivery_address[city]"]').addClass('required');
                    $('[name="delivery_address[country]"]').addClass('required');
                }
            });

            $("input[name='save_delivery_address']").change();
            function syncCompanyNameFields() {
                const value = $('[name="company_name"]').val();
                $('[name="invoice_address[company]"], [name="delivery_address[company]"]').val(value);
            }

            $('[name="company_name"]').on('input', syncCompanyNameFields);
            syncCompanyNameFields();
            $('[name="invoice_address[company]"], [name="delivery_address[company]"]').removeClass('required').closest('.col.form-group').hide();
        },
        postprocess: function(data) {
            if (data.is_registered) {
                Propeller.Toast.toast.on('shown.bs.toast', function () {
                    window.location.href = data.redirect;
                });

                Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'success', null);
            }
            else if (data.reset_error) {
                var inputMail = $('input[name="user_mail"]');
                inputMail.parent('div.has-success').removeClass('has-success').addClass('has-error');
                $('span.input-user-message').html(data.message);
                $('span.input-user-message').addClass('input-error-message');
            }
            else {
                if (data.error) 
                    Propeller.Toast.show('Propeller', '', data.message, 'error', null);
                else
                    Propeller.Toast.show('Propeller', '', data.message, 'success', null);
            }
        }
    };

    //Propeller.Register.init();

}(window.jQuery, window, document));