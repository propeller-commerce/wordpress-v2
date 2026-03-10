'use strict';

window.Propeller || (window.Propeller = {});

(function ($, window, document) {

    var Modal = {
        modal: $('#add-to-basket-modal'),
        init: function () {
        },
        setProductId: function (productId) {
            this.modal.find('.propel-modal-header').html(productId);
        },
        show: function (item) {
            this.fillModalData(item);

            this.modal.modal('show');
        },
        show_content: function (content) {
            $('.modal-product-list').html(content);

            this.modal.modal('show');
        },
        fillModalData: function (item) {
            if (item.product.hasOwnProperty('images') && Array.isArray(item.product.images) && item.product.images.length > 0 && item.product.images[0].hasOwnProperty('images') && Array.isArray(item.product.images[0].images) && item.product.images[0].images.length > 0)
                $(Propeller.Modal.modal).find('.added-item-img').attr('src', item.product.images[0].images[0].url);
            else
                $(Propeller.Modal.modal).find('.added-item-img').attr('src', 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDcwIDcwIiB3aWR0aD0iNzAiIGhlaWdodD0iNzAiPgoJPHRpdGxlPk5ldyBQcm9qZWN0PC90aXRsZT4KCTxkZWZzPgoJCTxpbWFnZSAgd2lkdGg9IjcwIiBoZWlnaHQ9IjcwIiBpZD0iaW1nMSIgaHJlZj0iZGF0YTppbWFnZS9wbmc7YmFzZTY0LGlWQk9SdzBLR2dvQUFBQU5TVWhFVWdBQUFFWUFBQUJHQVFNQUFBQkw0SERIQUFBQUFYTlNSMElCMmNrc2Z3QUFBQU5RVEZSRjlmWDFzR21maWdBQUFBOUpSRUZVZUp4allCZ0ZvMkJvQWdBQ3ZBQUJ1R0VYQUFBQUFBQkpSVTVFcmtKZ2dnPT0iLz4KCQk8aW1hZ2Ugd2lkdGg9IjM2IiBoZWlnaHQ9IjM2IiBpZD0iaW1nMiIgaHJlZj0iZGF0YTppbWFnZS9wbmc7YmFzZTY0LGlWQk9SdzBLR2dvQUFBQU5TVWhFVWdBQUFDUUFBQUFrQ0FNQUFBRFczbWlxQUFBQUJHZEJUVUVBQUxHUEMveGhCUUFBQUFGelVrZENBSzdPSE9rQUFBQThVRXhVUlVkd1RNek16TTdPenM3T3pzM056YzNOemMzTnpkWFYxYzNOemM3T3pzM056ZEhSMGMzTnpjM056Yy9QejgzTnpjM056YzNOemMzTnpjek16TFhwdm9FQUFBQVRkRkpPVXdDVE1rYjc1VmdLd1dtMEZNeWdKZkoyaHVBdzd0MU9BQUFCS0VsRVFWUTR5N1hVMlphRElBd0FVSVFnYTlqeS8vODZJRlphb2NkNW1NbUxwM0pyUWd3eTloU21xR2REOXQrTTRxSUZWOE5NQmUyUmVrVHgzVURNcGtXT0lHYWphamdETXB5L2c0emgwemlVUFluazF6MmVEc09MUGYvbnllTldJN25QM0ZhcHJLbTNraE11OXQ1TXV5YmEreGJFV0hNR0xTWjFHZVlvZHpSS1NaR2d4Tnh6V1YzclViVGRFSklXUFpldUQ2TzJQaUZ6VnRlTVl4eGFPWGZrb3I5TXFDMXo3ZjRkN1RSeWhWN3ZqRGE0REdQRnJwRXR3ekR0MXdqak1NenJOVXJiTUE2Mk5WTERzUHg2RFZPZmhoSHdtdHFwQmEwL2JSSlVndUxXNk9pekJJMDZrcjZtNWhPSkk1ZkFFb3ZkMzJhMm8vMllHQUYrZVZBRHBYNnB5OStNcytjMk56eE04SElPR0llekdZVjZFV2plakhzNjczOW00ck5oOGhlRzhlZVBIZnNCR3VNUlc5SEFiWnNBQUFBQVNVVk9SSzVDWUlJPSIvPgoJPC9kZWZzPgoJPHN0eWxlPgoJPC9zdHlsZT4KCTx1c2UgaWQ9IkJhY2tncm91bmQiIGhyZWY9IiNpbWcxIiB4PSIwIiB5PSIwIiAvPgoJPHVzZSBpZD0iZ3JvdXAtMTQiIGhyZWY9IiNpbWcyIiB0cmFuc2Zvcm09Im1hdHJpeCgxLDAsMCwxLDE3LDE3KSIvPgo8L3N2Zz4=');
          
            var product_name = item.product.name.filter(function(name) {
                return name.language == PropellerHelper.language;
            });

            $(Propeller.Modal.modal).find('.added-item-img').attr('alt', product_name[0].value);
            $(Propeller.Modal.modal).find('.added-item-name').html(product_name[0].value);
            $(Propeller.Modal.modal).find('.added-item-sku').html(item.product.sku);
            $(Propeller.Modal.modal).find('.added-item-quantity').html(item.quantity);
            if (typeof item.childItems != 'undefined' && item.childItems.length) {
                $(Propeller.Modal.modal).find('.added-item-prices').addClass('d-inline-block');
                if ($(Propeller.Modal.modal).find('.added-item-price').hasClass('added-item-priceNet'))
                    $(Propeller.Modal.modal).find('.added-item-price').html(Propeller.Global.formatPrice(item.totalPriceNet));
                else
                    $(Propeller.Modal.modal).find('.added-item-price').html(Propeller.Global.formatPrice(item.totalPrice));
            }
            else 
                $(Propeller.Modal.modal).find('.added-item-prices').addClass('d-none');

            if ($(Propeller.Modal.modal).find('.added-total-price').length) {
                if ($(Propeller.Modal.modal).find('.added-total-price').hasClass('added-total-priceNet'))
                    $(Propeller.Modal.modal).find('.added-total-price').html(Propeller.Global.formatPrice(item.totalSumNet));
                else
                    $(Propeller.Modal.modal).find('.added-total-price').html(Propeller.Global.formatPrice(item.totalSum));
            } else if ($(Propeller.Modal.modal).find('.added-item-price').length) {
                if ($(Propeller.Modal.modal).find('.added-item-price').hasClass('added-item-priceNet'))
                    $(Propeller.Modal.modal).find('.added-item-price').html(Propeller.Global.formatPrice(item.totalSumNet));
                else
                    $(Propeller.Modal.modal).find('.added-item-price').html(Propeller.Global.formatPrice(item.totalSum));
            }
            
            if (typeof item.surcharges != 'undefined' && item.surcharges.length) {
                var content = `<li class='surcharges-title'>${PropellerHelper.translations.additional_surcharges}</li>`;

                item.surcharges.forEach(function(surcharge) {
                    content += `<li>`;
                    if (surcharge.type == 'FlatFee')
                        content += `${item.quantity} x ${PropellerHelper.currency} ${Propeller.Global.formatPrice(surcharge.value)} (${surcharge.names[0].value})`;
                    else 
                        content += `${item.quantity} x ${surcharge.value}% (${surcharge.names[0].value})`;

                    content += `</li>`;
                });

                $(Propeller.Modal.modal).find('.added-item-surcharges').html(content);
            } else {
                // Clear surcharges when product has no surcharges
                $(Propeller.Modal.modal).find('.added-item-surcharges').html('');
            }
            if (typeof item.childItems != 'undefined' && item.childItems.length) {
                var content = '';

                item.childItems.forEach(function(childItem) {
                    content += `<div class="childitem">`;
                    if ( $(Propeller.Modal.modal).find('.added-item-price').hasClass('added-item-priceNet'))
                        content += `${childItem.quantity} x ${childItem.product.name[0].value} (${PropellerHelper.currency} ${Propeller.Global.formatPrice(childItem.priceNet)})`;
                    else 
                        content += `${childItem.quantity} x ${childItem.product.name[0].value} (${PropellerHelper.currency} ${Propeller.Global.formatPrice(childItem.price)})`;

                    content += `</div>`;
                });

                $(Propeller.Modal.modal).find('.added-item-childItems').html(content);
            }
        }
    };

    Propeller.Modal = Modal;

    var BundleModal = {
        modal: $('#add-to-basket-modal'),
        init: function () {
        },
        setProductId: function (bundleId) {
            this.modal.find('.propel-modal-header').html(bundleId);
        },
        show: function (item) {
            this.fillModalData(item);

            this.modal.modal('show');
        },
        show_content: function (content) {
            $('.modal-product-list').html(content);

            this.modal.modal('show');
        },
        fillModalData: function (item) {

            $('.added-item-img').remove();
            $('.image').html('<img src="' + item.product.images[0].images[0].url + '" />');

            $('.added-item-name').html(item.bundle.name);
            $('.product-sku').remove();
            $('.added-item-quantity').html(item.quantity);
            if ($('.added-item-price').hasClass('added-item-priceNet'))
                $('.added-item-price').html(Propeller.Global.formatPrice(item.totalPriceNet));
            else
                $('.added-item-price').html(Propeller.Global.formatPrice(item.totalPrice));
            
        }
    };

    Propeller.BundleModal = BundleModal;

    var QuickModal = {
        modal: $('#add-to-basket-modal'),
        init: function () {
        },
        show_content: function () {
            var content = `${PropellerHelper.translations.quick_order}`;
            $('.modal-product-list').hide();
            $('.modal-title span').html(content);
            this.modal.modal('show');
        },
      
    };

    Propeller.QuickModal = QuickModal;

    var ModalForms = {
        init: function () {

        },
        modal_form_submit: function (event) {
            event.preventDefault();
            event.stopPropagation();

            var formData = $(this).serializeObject();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: formData,
                loading: $(this).closest('.modal-content'),
                success: function (data, msg, xhr) {
                    console.log('response data', data);

                    if (data.status) {
                        if (data.reload)
                            window.location.reload();
                    }
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        }
    }

    Propeller.ModalForms = ModalForms;

}(window.jQuery, window, document));