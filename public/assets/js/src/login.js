(function ($, window, document) {

    const {__, _x, _n, _nx} = wp.i18n;

    Propeller.Login = {
        postprocess: function(data) {
            if (data.is_logged_in) {
                Propeller.Toast.toast.on('shown.bs.toast', function () {
                    window.location.href = data.redirect;
                });

                Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'success', null);
            }
            else {
                // if(data.message_user) {
                //     var inputMail = $('input[name="user_mail"]');
                //     inputMail.parent('div.has-success').removeClass('has-success').addClass('has-error');
                //     $('span.input-user-message').html(data.message);
                //     $('span.input-user-message').addClass('input-error-message');
                // }
                // else if(data.message_pass) {
                //     var inputPassword = $('input[name="user_password"]');
                //     inputPassword.parent('div.has-success').removeClass('has-success').addClass('has-error');
                //     $('span.input-pass-message').html(data.message);
                //     $('span.input-pass-message').addClass('input-error-message');
                // }
                //else
                var inputMail = $('input[name="user_mail"]');
                inputMail.parent('div.has-success').removeClass('has-success').addClass('has-error');
                var inputPassword = $('input[name="user_password"]');
                inputPassword.parent('div.has-success').removeClass('has-success').addClass('has-error');
                Propeller.Toast.show('Propeller', '', data.message, 'error');
            }
        }
    };

}(window.jQuery, window, document));