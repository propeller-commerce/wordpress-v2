(function($, window, document) {

    const { __, _x, _n, _nx } = wp.i18n;

    Propeller.Address = {
        init: function() {
            $('.address-set-default').off('submit').submit(function(e){
                e.preventDefault();

                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: $(this).serializeObject(),
                    loading: $(this).find('[type="submit"]'),
                    success: function(data, msg, xhr) {
                        if (typeof data.postprocess != undefined && typeof data.object != 'undefined')
                            Propeller[data.object].postprocess(data.postprocess);
                    },
                    error: function() {
                        Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                        console.log('error', arguments);
                    }
                });

                return false;
            })
        },
        postprocess: function(data) {
            if (data.status) {
                if (data.reload)
                    window.location.reload();
            }
            else {
                Propeller.Toast.show('Propeller', '', data.message, 'error');
            }
        }
    };

    //Propeller.Address.init();

}(window.jQuery, window, document));