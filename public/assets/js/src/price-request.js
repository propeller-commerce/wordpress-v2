(function ($, window, document) {
    const {__, _x, _n, _nx} = wp.i18n;

    Propeller.PriceRequest = {
        priceRequestRowRecords: 5,
        init: function() {
            $('#add-pr-row').off('click').on('click', this.add_row);

            this.init_quantity();

            $('.price-request-form').off('submit').on('submit', this.send_price_request);

            this.init_autocomplete();

            $('.btn-price-request').off('click').on('click', this.add_product);
        },
        add_product: function(event) {
            event.preventDefault();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'propel_add_pr_product',
                    id: $(this).data('id'),
                    code: $(this).data('code'),
                    name: $(this).data('name'),
                    quantity: $(this).data('quantity'),
                    minquantity: $(this).data('minquantity'),
                    unit: $(this).data('unit'),
                },
                loading: $(this),
                success: function(data, msg, xhr) {
                    if(data.success)
                        Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'success');
                    else 
                        Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'error');
                },
                error: function() {
                    console.log('error', arguments);
                }
            });
            
            return false;
        },
        init_quantity: function() {
            $('.remove-pr-row').off('click').on('click', this.remove_product);

            if (Propeller.ProductPlusMinusButtons.initialized)
                $('.btn-request-quantity').off('click');

            $('.btn-request-quantity').off('click').on('click', this.handle_quantity);
        },
        add_row: function() {
            Propeller.PriceRequest.priceRequestRowRecords++;

            $('#price-request-table').append(`<div class="price-request-row row" id="row-${Propeller.PriceRequest.priceRequestRowRecords}">
                <div class="col-5 col-sm-2 col-lg-2 product-code">
                    <input type="text" name="product-code-row[${Propeller.PriceRequest.priceRequestRowRecords}]" value="" class="form-control product-code" id="product-code-row-${Propeller.PriceRequest.priceRequestRowRecords}" data-row="${Propeller.PriceRequest.priceRequestRowRecords}">
                    <input type="hidden" name="product-id-row[${Propeller.PriceRequest.priceRequestRowRecords}]" value="" class="product-id" id="product-id-row-${Propeller.PriceRequest.priceRequestRowRecords}">
                </div>
                <div class="col-7 col-sm-5 col-lg-4 product-name ps-0">
                    <input type="text" name="product-name-row[${Propeller.PriceRequest.priceRequestRowRecords}]" value="" readonly class="form-control product-name" id="product-name-row-${Propeller.PriceRequest.priceRequestRowRecords}" data-row="${Propeller.PriceRequest.priceRequestRowRecords}">
                </div>
                <div class="col-8 col-sm-3 col-lg-1 product-quantity px-sm-0">
                    <div class="input-group product-quantity">
                        <label class="visually-hidden" for="product-quantity-row-<?php echo (int) $index; ?>"><?php echo __('Quantity', 'propeller-ecommerce-v2'); ?></label>
                        <span class="input-group-prepend incr-decr">
                            <button type="button" class="btn-request-quantity" 
                            data-type="minus">-</button>
                        </span>
                        <input
                            type="number"
                            ondrop="return false;" 
                            onpaste="return false;"
                            onkeypress="return event.charCode>=48 && event.charCode<=57" 
                            class="form-control product-request-quantity"
                            value=""
                            name="product-quantity-row[${Propeller.PriceRequest.priceRequestRowRecords}]"
                            id="product-quantity-row-${Propeller.PriceRequest.priceRequestRowRecords}" 
                            data-row="${Propeller.PriceRequest.priceRequestRowRecords}" 
                            data-id=""
                            data-unit="1" data-quantity="1"
                            >  
                        <span class="input-group-append incr-decr">
                            <button type="button" class="btn-request-quantity" data-type="plus">+</button>
                        </span>
                    </div>
                </div>
                <div class="remove-pr-row col-1 ps-4 d-flex align-items-center" data-row="${Propeller.PriceRequest.priceRequestRowRecords}">
                    <button type="button" class="remove-pr-row">
                        <svg class="icon icon-remove">
                            <use class="shape-remove" xlink:href="#shape-remove"></use>
                        </svg>
                    </button>
                </div>
            </div>`);

            Propeller.PriceRequest.init_autocomplete();
            Propeller.PriceRequest.init_quantity();
        },
        remove_product: function(event) {
            event.preventDefault();

            var row = $(this).closest('.price-request-row.row');

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'propel_remove_pr_product',
                    code: $(this).data('code')
                },
                loading: $(this),
                success: function(data, msg, xhr) {
                    if (typeof data.message != 'undefined'){
                        if(data.success)
                            Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'success');
                        else    
                            Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'error');
                    }
                                            
                    $(row).remove();
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        init_autocomplete: function() {
            $('.propeller-price-request input.product-code').off('click').on('click', this.focus_code);
            // $('input.product-code').off('focus').on('focus', this.focus_code);
        },
        focus_code: function() {
            if (window.priceRequestAutoComplete && window.priceRequestAutoComplete.unInit) {
                window.priceRequestAutoComplete.unInit()
                window.priceRequestAutoComplete = null;
            }
            var maxResults = 6;
            window.priceRequestAutoComplete = new window.autoComplete({
                selector: '#' + $(this).attr('id'),
                threshold: 3,
                debounce: 300,
                cache: false,
                searchEngine: function(query, record) {
                    return 1;
                },
                data: {
                    keys: ['value'],
                    src: async (query) => {
                        let data = [];
                        await Propeller.Ajax.call({
                            url: PropellerHelper.ajax_url,
                            method: 'POST',
                            data: {
                                action: 'quick_product_search',
                                sku: query,
                                offset: 0
                            },
                            dataType: 'json',
                        }).then(function (response) {
                            if (response.hasOwnProperty('items')) {
                                for (let i in response.items) {
                                    data.push({
                                        label: response.items[i].name[0].value,
                                        value: response.items[i].sku,
                                        name: response.items[i].name[0].value,
                                        sku: response.items[i].sku,
                                        id:  response.items[i].productId,
                                        quantity: response.items[i].minimumQuantity,
                                        minquantity: response.items[i].minimumQuantity,
                                        unit: response.items[i].unit,
                                        image:  response.items[i].image
                                    })
                                }
                            }
                        }).catch(function (args) {
                            console.log('error', args)
                        });

                        if(!data.length) {
                            data.push({
                                name: PropellerHelper.translations.no_results_found,
                                label: PropellerHelper.translations.no_results_found,
                                value: `${PropellerHelper.translations.no_results_found_for} ${query}`,
                                id:  'error-404',
                                image:  null, // Don't set a broken image URL
                            })
                        }
                        return data;
                    },
                },
                resultsList: {
                    tag: "ul",
                    class: "propeller-autosuggest-items",
                    maxResults: maxResults,
                    element: (list, data) => {
                        list.style.width="500px";
                    },
                },
                resultItem: {
                    tag: "li",
                    class: "propeller-autosuggest-item",
                    element: (item, data) => {
                        //Clear.
                        while (item.firstChild) {
                            item.removeChild(item.firstChild);
                        }
                        // Set img.
                        if (data.value.image && data.value.image !== 'all') {
                            let imgWrap = document.createElement('div');
                            let image = document.createElement('img');
                            imgWrap.className = 'autoComplete_item-img';
                            image.src = data.value.image;
                            image.width = image.height = 35;
                            imgWrap.appendChild(image);
                            item.appendChild(imgWrap);
                        }
                        // Set text.
                        let txtWrap = document.createElement('div');
                        txtWrap.className = 'autoComplete_item-name';
                        txtWrap.innerHTML = data.value.name;
                        item.appendChild(txtWrap);
                    },
                    highlight: "autoComplete_highlight",
                    selected: "autoComplete_selected"
                },
                events: {
                    input: {
                        selection: (event) => {
                            event.preventDefault();

                            if (!event.detail.selection.hasOwnProperty('value')) {
                                return;
                            }

                            let item = event.detail.selection.value;
                            
                            if(item.id === 'error-404') {
                                return;
                            }

                            var index = $(event.target).data('row');

                            if (Propeller.PriceRequest.check_code(item.sku, index)) {
                                Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), __('This product is already in the list.', 'propeller-ecommerce-v2'), 'error');
                                $('.propeller-price-request #product-code-row-' + index).val('');
                                return;
                            }

                            if (item.class == 'cluster') {
                                Propeller.Ajax.call({
                                    url: `${PropellerHelper.urls.product}${item.id}/${item.slug}`,
                                    method: 'GET',
                                    dataType: 'HTML',
                                    loading: $('.price-request-form'),
                                    success: function(data, msg, xhr) {
                                        $('.cluster-por-sku').html(item.sku);

                                        $('#propel_modal_cluster_por_body').html($(data).find('.cluster-add-to-basket-form').prop('outerHTML'));
                                        $('#propel_modal_cluster_por_body').find('.btn-addtobasket').closest('div.d-flex').remove();
                                        $('#propel_modal_cluster_por_body').find('.product-price-details').addClass('m-0');

                                        $('#cluster_por_modal').modal('show');

                                        Propeller.Product.init();

                                        Propeller.Validator.assign_validator($('#propel_modal_cluster_por_body .cluster-add-to-basket-form'));

                                        $('.btn-modal-cluster-por').off('click').on('click', function(event) {
                                            Propeller.PriceRequest.add_cluster(item, index);
                                        });
                                    },
                                    error: function() {
                                        console.log('error', arguments);
                                    }
                                });                                
                            } else {
                                $('.propeller-price-request #product-code-row-' + index).val(item.sku);
                                $('.propeller-price-request #product-name-row-' + index).val(item.name);
                                $('.propeller-price-request #product-quantity-row-' + index).val(item.quantity);
                                $('.propeller-price-request #product-quantity-row-' + index).attr('data-id', item.id);
                                $('.propeller-price-request #product-quantity-row-' + index).attr('data-minquantity', item.minquantity);
                                $('.propeller-price-request #product-quantity-row-' + index).attr('data-unit', item.unit);
                                
                                if ($('.propeller-price-request #product-code-row-' + (index + 1)).length) 
                                    $('.propeller-price-request #product-code-row-' + (index + 1)).focus();

                                if (window.priceRequestAutoComplete && window.priceRequestAutoComplete.unInit) {
                                    window.priceRequestAutoComplete.unInit()
                                    window.priceRequestAutoComplete = null;
                                }
                            }

                            return false;
                        }
                    }
                }
            });
            // $(this).click();
            $(this).focus();
        },
        check_code(code, index) {
            var codes = $('.price-request-table').find('.product-code').not('#product-code-row-' + index);

            for (var i = 0; i < codes.length; i++) {
                if ($(codes[i]).val() == code)
                    return true;
            }

            return false;
        },
        send_price_request: function(event) {
            event.preventDefault();

            var form_data = $(this).serializeObject();

            var has_values = false;

            var pcodes = $("[id^=product-code-row-]");

            for (var i = 0; i < pcodes.length; i++) {
                if ($(pcodes[i]).val() != '') {
                    has_values = true;
                    break;
                }
            }
            
            if (!has_values) {
                Propeller.Toast.show('Propeller', '', PropellerHelper.translations.price_request_no_items, 'error');
                return false;
            }
            

            var form = this;

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $(this),
                success: function(data, msg, xhr) {
                    if (data.success)
                        Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'success');
                    else 
                        Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.message, 'error');
                    
                    if (data.success) {
                        Propeller.PriceRequest.reset_form(form);
                        window.location.reload();
                    }
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        handle_quantity: function(event) {
            event.preventDefault(); 

            var $that = $(this).closest('.price-request-row');
            var $btns = $that.find('.btn-request-quantity');
            var $quantity = $that.find('input.product-request-quantity');
            var $unit = eval($quantity.data("unit"));
            var $min = eval($quantity.data("minquantity"));

            var oClickDelay = 0;

            var $btn = $(this);
            var $val = eval($quantity.val());

            try {
                clearTimeout(oClickDelay);
            } catch (e) {
            }

            if ($(this).data('type') === 'minus') {
                if (($val - $unit) >= $min) {
                    $quantity.val($val - $unit);
                }
            } else {
                $quantity.val($val + $unit);
            }

            if (eval($quantity.val()) > $min) {
                $btns.eq(0).attr('disabled', false);
            } else {
                $btns.eq(0).attr('disabled', true);
            }

            $quantity.trigger('change');

            return false;
        },
        reset_form: function(form) {
            $(form).find('input,textarea').each(function(index, el){
                $(el).val('');
            })
        },
        add_cluster: function(item, index) {
            if ($('#propel_modal_cluster_por_body .cluster-add-to-basket-form').valid()) {
                var cluster_options = $('#request_comment').val() + `${item.sku} - Configuration:\n`;

                var serialized = $('#propel_modal_cluster_por_body .cluster-add-to-basket-form').serializeObject();

                $.each(serialized, function(key, value){
                    if (key.indexOf('option_') > -1) {
                        var option_name = $(`select[name="${key}"]`).closest('.col-12.m-3').find('h3').html().trim();
                        var option_value = $(`select[name="${key}"]`).find(`option[value="${value}"]`).html().trim();

                        console.log(option_name, option_value);
                        cluster_options += `   ${option_name} (${key.split('_')[1]}): ${option_value} (${value})\n`;
                    } else if (key == 'product_id' && $(`select[name="${key}"]`).length) {
                        var option_value = $(`select[name="${key}"]`).find(`option[value="${value}"]`).html().trim()
                        cluster_options += ` Composition: ${option_value} (${value})\n`;
                    }
                });
    
                $('#request_comment').val(cluster_options + "\n\n");

                $('.propeller-price-request #product-code-row-' + index).val(item.sku);
                $('.propeller-price-request #product-name-row-' + index).val(item.name);
                $('.propeller-price-request #product-quantity-row-' + index).val(item.quantity);
                $('.propeller-price-request #product-quantity-row-' + index).attr('data-id', item.id);
                $('.propeller-price-request #product-quantity-row-' + index).attr('data-minquantity', 1);
                $('.propeller-price-request #product-quantity-row-' + index).attr('data-unit', 1);
                
                if ($('.propeller-price-request #product-code-row-' + (index + 1)).length) 
                    $('.propeller-price-request #product-code-row-' + (index + 1)).focus();
    
                if (window.priceRequestAutoComplete && window.priceRequestAutoComplete.unInit) {
                    window.priceRequestAutoComplete.unInit()
                    window.priceRequestAutoComplete = null;
                }
    
                $('#cluster_por_modal').modal('hide');
            }            
        }
    }
}(window.jQuery, window, document));