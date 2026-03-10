(function ($, window, document) {

    Propeller.CheckoutForms = {
        init: function () {
            if (window.location.href == PropellerHelper.urls.checkout_summary) {
                var items_count = $('.propel-total-items').html();
                if (typeof items_count != 'undefined' && items_count.trim() == '0')
                    window.location.href = PropellerHelper.urls.cart;
            }

            var delivery_address_fields = $('.new-delivery-address input.required, .new-delivery-address select.required');

            var $forms = $('form.form-handler');

            $forms.each(function(){
                var $thisForm = $(this);
                $(this).find('.form-check input[type="radio"]').not('.user-type-radio').unbind('change').bind('change' , function(e) {
                    var $this = $(this);

                    /* Toggle delivery address */
                    if( $this.attr('name') == 'add_delivery_address' ) {
                        if($this.is(':checked') && $this.attr('value') == 'Y') {
                            $('.new-delivery-address').slideDown(500);

                            $(delivery_address_fields).each(function(index, field){
                                $("input[name^='" + $(field).attr('name') + "']").addClass('required');
                            });
                        } else if( $this.is(':checked') && $this.attr('value') == 'N') {
                            $('.new-delivery-address').slideUp(500);

                            $(delivery_address_fields).each(function(index, field){
                                $("input[name^='" + $(field).attr('name') + "']").removeClass('required');
                            });
                        }
                    }
                    if( $this.attr('name') == 'delivery_address' ) {
                        $thisForm.find('.delivery-label').removeClass('selected');
                        $('.delivery-addresses-wrapper').find('label.error').remove();
                        $('.delivery-addresses-wrapper').find('input').removeClass('error');
                        $('.delivery-addresses-wrapper').find('input[type="radio"]').not($this).removeAttr('checked');
                        $this.closest('.delivery-label').addClass('selected').show().find('select,input').addClass('required').attr('required' , true);
                    }
                    if( $this.attr('name') == 'invoice_address' ) {
                        $thisForm.find('.invoice-label').removeClass('selected');
                        $('.invoice-addresses-wrapper').find('label.error').remove();
                        $('.invoice-addresses-wrapper').find('input').removeClass('error');
                        $('.invoice-addresses-wrapper').find('input[type="radio"]').not($this).removeAttr('checked');
                        $this.closest('.invoice-label').addClass('selected').show().find('select,input').addClass('required').attr('required' , true);
                    }
                    if( $this.attr('name') == 'carrier' ) {
                        $thisForm.find('.carrier-label').removeClass('selected');
                        $('.carriers').find('label.error').remove();
                        $('.carriers').find('input').removeClass('error');
                        $this.closest('.carrier-label').addClass('selected').show().find('select,input').addClass('required').attr('required' , true);

                    }
                    if( $this.attr('name') == 'delivery_select' ) {
                        $thisForm.find('.delivery').removeClass('selected');
                        $('.deliveries').find('label.error').remove();
                        $('.deliveries').find('input').removeClass('error');
                        $this.closest('.delivery').addClass('selected').show().find('select,input').addClass('required').attr('required' , true);

                    }
                    if( $this.attr('name') == 'payMethod' ) {
                        $thisForm.find('.paymethod').removeClass('selected');
                        $('.paymethods').find('label.error').remove();
                        $('.paymethods').find('input').removeClass('error');
                        $this.closest('.paymethod').addClass('selected').show().find('select,input').addClass('required').attr('required' , true);

                    }
                });
                $(this).find('.form-check input[type="checkbox"]').unbind('change').bind('change' , function(e) {
                    var $this = $(this);

                    /* Toggle delivery address */
                    if( $this.attr('name') == 'save_delivery_address' ) {
                        if(!$this.is(':checked')) {
                            $('.new-delivery-address').slideDown(500);
                        } else {
                            $('.new-delivery-address').slideUp(500);
                        }
                    }

                });
            });

            $("input[name='add_delivery_address']").change();
        }
    };

}(window.jQuery, window, document));