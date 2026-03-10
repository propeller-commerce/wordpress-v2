(function ($, window, document) {

    const {__, _x, _n, _nx} = wp.i18n;

    Propeller.Order = {
        init: function() {
           $('.order-pdf-btn').off('click').on('click', this.download_order_pdf);
           $('.secure-attachment-btn').off('click').on('click', this.download_secure_attachment);
           $('.order-shipment-details').off('click').on('click', this.order_shipment_details);
        },
        order_shipment_details: function(event) {
            event.preventDefault();

            $('#shipment_details').find('.propel-shipment-modal-body').html('');    
            $('#shipment_details').find('#shipment_details_status').html('');    

            $('#shipment_details').find('#shipment_details_label').html($(this).data('title'));

            console.log($(this).data());
           
            var form_data = {
                action: $(this).data('action'),
                shipment: $(this).data('shipment')
            };

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $(this),
                success: function(data, msg, xhr) {
                    if (data.success) {        
                        $('#shipment_details').find('.propel-shipment-modal-body').html(data.content);    
                        $('#shipment_details').find('#shipment_details_status').html(data.status);    
                        $('#shipment_details').find('.shipment-track-and-traces').html(data.track_n_trace);    

                        $('#shipment_details').modal('show');
                    } else {
                        if (typeof data.message != 'undefined')
                            Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'error', null, 3000);
                    }
                },
                error: function() {
                    Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        download_order_pdf: function(event) {
            event.preventDefault();

            var form_data = {
                action: $(this).data('action'),
                order_id: $(this).data('order')
            };

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $(this),
                success: function(data, msg, xhr) {
                    if (data.success && typeof data.pdf_url != 'undefined') {                        
                        var pdf_link = document.createElement('a');
                        pdf_link.href = data.pdf_url;
                        pdf_link.download = data.filename;

                        $(pdf_link).on('click', function(event) {
                            setTimeout(function() {
                                Propeller.Ajax.call({
                                    url: PropellerHelper.ajax_url,
                                    method: 'POST',
                                    data: {
                                        action: 'delete_order_pdf',
                                        filename: data.filename
                                    },
                                    success: function(data, msg, xhr) {},
                                    error: function() {}
                                });
                            }, 1000);
                        });

                        pdf_link.click();
                        pdf_link.remove();
                    } else {
                        if (typeof data.message != 'undefined')
                            Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'error', null, 3000);
                    }
                },
                error: function() {
                    Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        download_secure_attachment: function(event) {
            event.preventDefault();

            var form_data = {
                action: $(this).data('action'),
                attachment_id: $(this).data('attachment')
            };

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $(this),
                success: function(data, msg, xhr) {
                    if (data.success && typeof data.pdf_url != 'undefined') {                        
                        var pdf_link = document.createElement('a');
                        pdf_link.href = data.pdf_url;
                        pdf_link.download = data.filename;

                        $(pdf_link).on('click', function(event) {
                            setTimeout(function() {
                                Propeller.Ajax.call({
                                    url: PropellerHelper.ajax_url,
                                    method: 'POST',
                                    data: {
                                        action: 'delete_attachment',
                                        filename: data.filename
                                    },
                                    success: function(data, msg, xhr) {},
                                    error: function() {}
                                });
                            }, 1000);
                        });

                        pdf_link.click();
                        pdf_link.remove();
                    } else {
                        if (typeof data.message != 'undefined')
                            Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'error', null, 3000);
                    }
                },
                error: function() {
                    Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        postprocess: function (data) {
            if (data.status) {
                if (data.reload)
                    window.location.reload();
                if (data.redirect)
                    window.location.href = data.redirect;

            } else if (data.return_success) {
                $('#return_modal_' + data.order_id).modal('hide');
                $('#return_modal_' + data.order_id).find('form').trigger('reset');
                $('#returnRequestSuccess').find('.return-email').html(data.order_email);
                $('#returnRequestSuccess').find('.return-order').html(data.order_id);
                $('#returnRequestSuccess').modal('show');
            } else if (data.success) {
                Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'success', null);
            } else {
                Propeller.Toast.show('Propeller', '', data.message, 'error');
            }
        }
    };

}(window.jQuery, window, document));