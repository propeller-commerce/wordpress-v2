(function ($, window, document) {
    // const {__, _x, _n, _nx} = wp.i18n;

    window.quickOrderAutoComplete = {};

    Propeller.QuickOrder = {
        quickRowRecords: 6,
        just_removed: false,
        init: function() {
            $('#fileUpload').off('change').change(this.handle_file_upload);
            $('.propeller-quick-order #add-row').off('click').on('click', this.add_row);
            $('.propeller-quick-order #quick-order-table').off('click').on('click', '.remove-row', this.remove_row);

            this.init_autocomplete();
            this.init_quantity();
            this.init_form();

            $('#nonce').val($('meta[name=security]').attr('content'));
        },
        handle_file_upload: function() {
            var fileName= $(this).val().split('\\').pop();
            $(this).next().find('span').html(fileName);
        },
        add_row: function() {
            Propeller.QuickOrder.quickRowRecords++;

            $('.propeller-quick-order #quick-order-table').append(`<div class="quick-order-row row" id="row-${Propeller.QuickOrder.quickRowRecords}">
                <div class="col-2 product-code">
                    <input type="text" name="product-code-row-${Propeller.QuickOrder.quickRowRecords}" value="" class="form-control product-code" id="product-code-row-${Propeller.QuickOrder.quickRowRecords}" data-row="${Propeller.QuickOrder.quickRowRecords}">
                    <input type="hidden" name="product-id-row-${Propeller.QuickOrder.quickRowRecords}" value="" class="product-id" id="product-id-row-${Propeller.QuickOrder.quickRowRecords}">
                </div>
                <div class="col-4 product-name ps-0">
                    <input type="text" name="product-name-row-${Propeller.QuickOrder.quickRowRecords}" value="" disabled class="form-control product-name" id="product-name-row-${Propeller.QuickOrder.quickRowRecords}" data-row="${Propeller.QuickOrder.quickRowRecords}">
                </div>
                <div class="col-2 product-price ps-0">
                    <input type="text" name="product-price-row-${Propeller.QuickOrder.quickRowRecords}" value="" disabled class="form-control product-price" id="product-price-row-${Propeller.QuickOrder.quickRowRecords}" data-row="${Propeller.QuickOrder.quickRowRecords}" data-price="">
                </div>
                <div class="col-1 product-quantity ps-0">
                    <input type="number" ondrop="return false;" onpaste="return false;" onkeypress="return event.charCode>=48 && event.charCode<=57" name="product-quantity-row-${Propeller.QuickOrder.quickRowRecords}" value="" class="form-control product-quantity" id="product-quantity-row-${Propeller.QuickOrder.quickRowRecords}" data-row="${Propeller.QuickOrder.quickRowRecords}" data-id="">
                </div>
                <div class="col-2 product-total-price ps-0">
                    <input type="text" name="product-total-row-${Propeller.QuickOrder.quickRowRecords}" value="" disabled class="form-control product-total" id="product-total-row-${Propeller.QuickOrder.quickRowRecords}" data-row="${Propeller.QuickOrder.quickRowRecords}" data-total="">
                </div>
                <div class="remove-row col-1 d-flex align-items-center" data-row="${Propeller.QuickOrder.quickRowRecords}">
                    <button type="button" class="remove-row">
                        <svg class="icon icon-remove">
                            <use class="shape-remove" xlink:href="#shape-remove"></use>
                        </svg>
                    </button>
                </div>
            </div>`);

            Propeller.QuickOrder.focus_code($('#product-code-row-' + Propeller.QuickOrder.quickRowRecords));

            Propeller.QuickOrder.init_quantity();
        },
        remove_row: function() {
            if (!Propeller.QuickOrder.just_removed) { 
                var child = $(this).closest('.quick-order-row').nextAll();

                child.each(function (i, el) {
                    var id = $(el).attr('id');

                    var dig = parseInt(id.substring(4));
                    var new_index = dig - 1;

                    var productCode = $(el).children('.product-code').find('input.product-code');
                    $(productCode).attr('id', 'product-code-row-' + new_index);
                    $(productCode).attr('data-row', new_index);
                    $(productCode).attr('name', 'product-code-row-' + new_index);

                    var productId = $(el).children('.product-code').find('input.product-id');
                    $(productId).attr('id', 'product-id-row-' + new_index);
                    $(productId).attr('data-row', new_index);
                    $(productId).attr('name', 'product-id-row-' +  + new_index);

                    var productName = $(el).children('.product-name').children('input.product-name');
                    $(productName).attr('id', 'product-name-row-' +  + new_index);
                    $(productName).attr('data-row', new_index);
                    $(productName).attr('name', 'product-name-row-' + new_index);

                    var productPrice = $(el).children('.product-price').children('input.product-price');
                    $(productPrice).attr('id', 'product-price-row-' + new_index);
                    $(productPrice).attr('data-row', new_index);
                    $(productPrice).attr('name', 'product-price-row-' + new_index);

                    var productQuantity = $(el).children('.product-quantity').children('input.product-quantity');
                    $(productQuantity).attr('id', 'product-quantity-row-' + new_index);
                    $(productQuantity).attr('data-row', new_index);
                    $(productQuantity).attr('name', 'product-quantity-row-' + new_index);

                    var productTotal = $(el).children('.product-total-price').children('input.product-total');
                    $(productTotal).attr('id', 'product-total-row-' + new_index);
                    $(productTotal).attr('data-row', new_index);
                    $(productTotal).attr('name', 'product-total-row-' + new_index);

                    $(el).attr('id', 'row-' + new_index);
                });

                $(this).closest('.quick-order-row').remove();

                if (typeof window.quickOrderAutoComplete[$(this).attr('id')] != 'undefined' && 
                    window.quickOrderAutoComplete[$(this).attr('id')] &&
                    window.quickOrderAutoComplete[$(this).attr('id')].unInit) {
                        window.quickOrderAutoComplete[$(this).attr('id')].unInit()
                        window.quickOrderAutoComplete[$(this).attr('id')] = null;
                }

                // Propeller.QuickOrder.init_autocomplete();
                Propeller.QuickOrder.init_quantity();
                Propeller.QuickOrder.calculate_totals();

                Propeller.QuickOrder.quickRowRecords--;

                Propeller.QuickOrder.just_removed = true;
            }
            else {
                Propeller.QuickOrder.just_removed = false;
            }  
        },
        init_autocomplete: function() {
            $('.propeller-quick-order input.product-code').each(function(index, el) {
                Propeller.QuickOrder.focus_code(el);
            });
        },
        remove_aria_attributes: function(el) {
            var attributes = $.map(el.attributes, function(item) {
                return item.name;
            });
            
            $.each(attributes, function(i, item) {
                if (item.indexOf('aria') !== -1)
                    $(el).removeAttr(item);
            });
        },
        focus_code: function(el) {
            var el_id = $(el).attr('id');

            var maxResults = 6;

            window.quickOrderAutoComplete[el_id] = new window.autoComplete({
                selector: '#' + el_id,
                threshold: 3,
                debounce: 300,
                cache: false,
                wrapper: true,
                searchEngine: function(query, record) {
                    return 'loose';
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
                                subaction: 'quick_order',
                                sku: query,
                                offset: 0
                            },
                            dataType: 'json',
                        }).then(function (response) {
                            if (response.hasOwnProperty('items')) {
                                for (let i in response.items) {
                                    var minQty = response.items[i].minimumQuantity;
                                    if(response.items[i].unit >= response.items[i].minimumQuantity)
                                        minQty = response.items[i].unit;
                                    data.push({
                                        label: response.items[i].name[0].value,
                                        value: response.items[i].sku,
                                        name: response.items[i].name[0].value,
                                        sku: response.items[i].sku,
                                        id:  response.items[i].productId,
                                        net_price: response.items[i].price.gross,
                                        quantity: minQty,
                                        image:  response.items[i].image,
                                        total: response.items[i].price.gross
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
                                sku: '',
                                id:  'error-404',
                                image:  PropellerHelper.base_assets_url + 'img/no-image-small.webp'
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
                        if (item.value.image != 'all') {
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
                        // txtWrap.innerHTML = data.value.name;
                        
                        txtWrap.innerHTML = data.value.name + (data.value.sku != '' ? '<br><span class="autoComplete_item-sku">SKU: ' + data.value.sku + '<span>' : '');
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

                            var existing_codes = [];
                            $('.propeller-quick-order input.product-code').not('#product-code-row-' + index).each(function(index, field) {
                                if (typeof $(field).val() != 'undefined' && $(field).val().trim() != '')
                                    existing_codes.push($(field).val());
                            });

                            if (existing_codes.indexOf(item.sku) !== -1) {
                                Propeller.Toast.show('Propeller', '', 'Product is already in the list', 'error');
                                $('.propeller-quick-order #product-code-row-' + index).val('');
                                return;
                            }
                            
                            $('.propeller-quick-order #product-code-row-' + index).val(item.sku);
                            $('.propeller-quick-order #product-code-row-' + index).attr('readonly', 'readonly');

                            $('.propeller-quick-order #product-name-row-' + index).val(item.name);
                            $('.propeller-quick-order #product-price-row-' + index).val(Propeller.Global.formatPrice(item.net_price));
                            $('.propeller-quick-order #product-price-row-' + index).attr('data-price', item.net_price);
                            $('.propeller-quick-order #product-quantity-row-' + index).val(item.quantity);
                            $('.propeller-quick-order #product-quantity-row-' + index).attr('data-id', item.id);
                            $('.propeller-quick-order #product-total-row-' + index).val(Propeller.Global.formatPrice(item.total));
                            $('.propeller-quick-order #product-total-row-' + index).attr('data-total', item.total);

                            Propeller.QuickOrder.calculate_totals();

                            if ($('.propeller-quick-order #product-code-row-' + (index + 1)).length) 
                                $('.propeller-quick-order #product-code-row-' + (index + 1)).focus();

                            if (typeof window.quickOrderAutoComplete['#product-code-row-' + index] != 'undefined' && window.quickOrderAutoComplete['#product-code-row-' + index].unInit) {
                                window.quickOrderAutoComplete['#product-code-row-' + index].unInit()
                                window.quickOrderAutoComplete['#product-code-row-' + index] = null;
                            }

                            return false;
                        }
                    }
                }
            });
        },
        init_quantity: function() {
            $('.propeller-quick-order input.product-quantity').off('blur').blur(this.blur_quantity);
        },
        blur_quantity: function() {
            var quantity = $(this).val();

            if (isNaN(parseInt(quantity)))
                return;

            var index = $(this).data('row');
            var price = $('#product-price-row-' + index).data('price');
            var total = parseFloat(price) * quantity;
            $('.propeller-quick-order #product-total-row-' + index).attr('data-total', total).val(Propeller.Global.formatPrice(total));

            Propeller.QuickOrder.calculate_totals();
        },
        init_form: function() {
            $('.btn-quick-order').off('click').on('click', this.collect_items);
        },
        collect_items: function(event) {
            event.preventDefault();
            event.stopPropagation();

            var items = [];
            var quantities = $('.propeller-quick-order input.product-quantity');
            for (var i = 0; i < quantities.length; i++) {
                if ($(quantities[i]).attr('data-id') == '')
                    continue;

                items.push($(quantities[i]).attr('data-id') + '-' + $(quantities[i]).val());
            }
            
            if (!items.length) {
                Propeller.Toast.show('Propeller', '', PropellerHelper.translations.quick_order_no_items, 'error');
                return false;
            }

            $(this).closest('form.replenish-form').find('input[name="items"]').val(items.join(','));

            $(this).closest('form.replenish-form').submit();

            // $(this).addClass('disabled').attr('disabled', 'disabled');

            Propeller.QuickOrder.reset_form();

            return false;
        },
        calculate_totals: function() {
            var totals = $('.propeller-quick-order input.product-total');
            var total = 0;

            var total_quantities = $('.propeller-quick-order input.product-quantity');
            var total_items = 0;

            for (var i = 0; i < totals.length; i++) {
                if ($(totals[i]).attr('data-total') == '')
                    continue;

                total += parseFloat($(totals[i]).attr('data-total'));
            }

            for (var i = 0; i < total_quantities.length; i++) {
                if ($(total_quantities[i]).val() == '')
                    continue;

                total_items += parseInt($(total_quantities[i]).val());
            }

            var exclbtw = Propeller.Global.getPercentage(Propeller.TaxCodes.H, total);
            var subtotal = total - exclbtw;

            $('.propel-total-quick-items').html(total_items);
            $('.propel-total-quick-subtotal').attr('data-subtotal', subtotal).html(Propeller.Global.formatPrice(subtotal));
            $('.propel-total-quick-excl-btw').attr('data-exclbtw', exclbtw).html(Propeller.Global.formatPrice(exclbtw));
            $('.propel-total-quick-price').attr('data-total', total).html(Propeller.Global.formatPrice(total));
        },
        reset_form: function() {
            var rows = $('div.quick-order-row').length;

            $('div.quick-order-row').remove();
            this.quickRowRecords = 0;

            for (var i = 0; i < rows; i++)
                this.add_row();

            $('#fileUpload').val('');
            $(this).closest('form.replenish-form').find('input[name="items"]').val('');
            $(this).addClass('disabled').removeAttr('disabled');

        }
    };

    //Propeller.QuickOrder.init();

}(window.jQuery, window, document));