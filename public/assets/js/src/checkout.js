(function ($, window, document) {

    const {__, _x, _n, _nx} = wp.i18n;

    Propeller.Checkout = {
        selected_delivery_addr: undefined,
        selected_invoice_addr: undefined,
        selected_pickup_addr: undefined,
        init: function() {
            this.selected_invoice_addr = $('div#invoice').find('input.invoice-addresses:checked');                
            this.selected_delivery_addr = $('div#delivery').find('input.delivery-addresses:checked');                
            this.selected_pickup_addr = $('div#pickup').find('input.delivery-addresses:checked'); 

            this.init_tabs(); 

            $('.btn-cart-process').off('click').on('click', function(event) {
                if ($(this).closest('form').valid()) {
                    $(this).attr('disabled', 'disabled');
                    $(this).closest('form').submit();
                }
            });

            $('.propel-delivery-options').off('click').on('click', function(event) {
                $('#shipping_method').val($(this).data('method'));

                if ($(this).data('method') == PropellerHelper.order_types.PICKUP) {
                    $('.propel-checkout-shipping-info').hide();
                    $('input[name="orderconfirm_email"]').addClass('required');
                }
                else  {
                    $('.propel-checkout-shipping-info').show();
                    $('input[name="orderconfirm_email"]').removeClass('required');
                }
            });

            if ($('#shipping_method').val() == PropellerHelper.order_types.PICKUP)
                $('.propel-checkout-shipping-info').hide();
        },
        init_tabs: function() {
            $('#delivery-tabs').on('show.bs.tab', function(event) {
                if ($(event.target).data('target') == '#pickup') {
                    Propeller.Checkout.selected_delivery_addr = $('div#delivery').find('input.delivery-addresses:checked');

                    if (!$('div#pickup').find('input.delivery-addresses:checked').length)
                        $('div#pickup').find('label.delivery-label').first().click();
                    else if (typeof Propeller.Checkout.selected_pickup_addr != 'undefined')
                        $('div#pickup').find($(Propeller.Checkout.selected_pickup_addr).closest('label.delivery-label')).click();
                }
                else {
                    Propeller.Checkout.selected_pickup_addr = $('div#pickup').find('input.delivery-addresses:checked');

                    if (typeof Propeller.Checkout.selected_delivery_addr != 'undefined') 
                        $('div#delivery').find($(Propeller.Checkout.selected_delivery_addr).closest('label.delivery-label')).click();
                    else 
                        $('div#delivery').find('label.delivery-label.is_default').click();
                }                
            });
        },
        postprocess: function(data) {
            if (typeof data.message != 'undefined')
                Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'success', null);
            if (typeof data.reload != 'undefined')
                window.location.reload();
            if (typeof data.redirect != 'undefined')
                window.location.href = data.redirect;
        }
    };

}(window.jQuery, window, document));