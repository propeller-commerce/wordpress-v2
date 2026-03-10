(function ($, window, document) {

    const {__, _x, _n, _nx} = wp.i18n;

    Propeller.Cart = {
        badge: $('.propeller-mini-shoping-cart').find('span.badge'),
        item_updating: false,
        init: function () {
            // Propeller.Validator.assign_validator($('form.add-to-basket-form'), this.cart_add_item);

            $('form.add-to-basket-form').not('.cluster-add-to-basket-form').off('submit').submit(this.cart_add_item);
            $('form.cluster-add-to-basket-form').off('submit').submit(this.cart_add_item_cluster);
            $('form.update-basket-item-form').off('submit').submit(this.cart_update_item);
            $('form.add-to-basket-bundle-form').off('submit').submit(this.cart_add_bundle);
            $('form.update-basket-item-form input[name="notes"]').off('blur').blur(this.cart_update_item_blur);
            $('form.update-basket-item-form input[name="quantity"]').off('blur').blur(this.cart_update_item_blur);
            $('form.update-basket-item-form input[name="notes"]').off('keypress').keypress(this.cart_update_item_keypress);
            $('form.update-basket-item-form input[name="quantity"]').off('keypress').keypress(this.cart_update_item_keypress);
            $('form.update-basket-item-form input[name="quantity"]').off('keyup').keyup(this.quantity_key_up);
            $('form.update-basket-item-form input[name="quantity"]').off('focus').focus(this.cart_update_item_focus);
            $('form.basket-voucher-form').off('submit').submit(this.cart_add_action_code);
            $('form.basket-remove-voucher-form').off('submit').submit(this.cart_remove_action_code);
            $('form.delete-basket-item-form').off('submit').submit(this.cart_delete_item);
            $('form.dropshipment-form').find('select[name="country"]').bind('change' , function(){
                if($(this).find('option:selected').val() != 'NL') {
                    $('input[name="icp"]').val("Y");
                } else {
                    $('input[name="icp"]').val("N");
                }
            });
            $('form.propeller-oci-form-btn').off('click').on('click', this.submit_oci_form);

            $('input[name="order_type"]').off('change').change(this.cart_change_type);

            $('form.replenish-form').off('submit').submit(this.replenish);

            $('.btn-checkout-ajax').not('.btn-purchase-request-ajax').off('click').click(this.change_order_status);
            $('.btn-purchase-request-ajax').off('click').click(this.submit_purchase_request);

            $('.btn-basket-product-alternatives').off('click').click(this.load_item_crossupsells);

            $(window).off('focus').on('focus', function() {
                Propeller.Cart.load_mini_cart();
            });
        },
        load_mini_cart: function() {
            var el = $('#propel_mini_cart');

            var parent = el.parent();
        
            var cart_currency = $(el).find('.cart-label .cart-total .symbol');
            var cart_total = $(el).find('.cart-label .cart-total .propel-mini-cart-total-price');

            $(parent).css('height', $(parent).outerHeight());
            $(parent).css('width', $(parent).outerWidth());

            // $(cart_title).css('width', $(cart_title).outerWidth());
            // $(cart_title).css('height', $(cart_title).outerHeight());

            // $(cart_currency).css('width', $(cart_currency).outerWidth());
            // $(cart_currency).css('height',  $(cart_currency).outerHeight());
            
            // $(cart_total).css('width', $(cart_total).outerWidth());
            // $(cart_total).css('height', $(cart_total).outerHeight());

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'propel_load_mini_cart'
                },
                // loading: el,
                success: function(data, msg, xhr) {
                    if (typeof data.success != 'undefined' && data.success) {
                        $(el).find('.badge').text(data.badge);

                        // $(cart_title).fadeOut(1, function() {
                        //     $(this).html(data.title);
                        //     $(this).fadeIn(1);
                        // });

                        $(cart_currency).fadeOut(1, function() {
                            $(this).html(data.currency + ' ');
                            $(this).fadeIn(1);
                        });

                        $(cart_total).fadeOut(1, function() {
                            if(parseFloat(data.totals) >= 0)
                                $(this).html(data.totals);
                            else
                                $(this).html(Propeller.Global.formatPrice(0));
                            $(this).fadeIn(1);
                        });

                        // $(el).find('.cart-label .cart-title').text(data.title);
                        // $(el).find('.cart-label .cart-total .symbol').html(data.currency);
                        // $(el).find('.cart-label .cart-total .propel-mini-cart-total-price').text(data.totals);

                        Propeller.Cart.badge = $('.propeller-mini-shoping-cart').find('span.badge');
                    }
                },
                error: function() {
                    console.log('error', arguments);
                }
            });
        },
        replenish: function(event) {
            event.preventDefault();

            $('.quick-order-errors').html('');

            var loading_el = $(this).find('button[type="submit"]').length
                ? $(this).find('button[type="submit"]')
                : $(this);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: $(this).serializeObject(),
                loading: loading_el,
                success: function(data, msg, xhr) {
                    if (data.object == 'QuickOrder') {
                        if (data.postprocess.error) {
                            $('#replenish_form').find('button.btn-quick-order').removeClass('disabled').prop('disabled', false);

                            if (typeof data.postprocess.remove != 'undefined' && data.postprocess.remove.length) {
                                for (var i = 0; i < data.postprocess.remove.length; i++)
                                    $('#row-' + data.postprocess.remove[i]).find('button.remove-row').click();
                            }
                        }
                    }

                    Propeller.Cart.cart_postprocess(data);
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        change_order_status: function(event) {
            event.stopPropagation();
            event.preventDefault();

            var redirect_url = $(this).attr('href');

            var loading_el = $(this);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'cart_change_order_status',
                    order_status: $(this).attr('data-status')
                },
                loading: loading_el,
                success: function(data, msg, xhr) {
                    if (data.success)
                        window.location.href = redirect_url;
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        submit_oci_form: function(event) {
            event.stopPropagation();
            event.preventDefault();

            var loading_el = $(this);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'clear_oci_cart',
                    cart_id: $(this).data('cart')
                },
                loading: loading_el,
                success: function(data, msg, xhr) {
                    if (data.success)
                        $(loading_el).submit();
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        submit_purchase_request: function(event) {
            event.stopPropagation();
            event.preventDefault();

            var loading_el = $(this);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'submit_purchase_request',
                    cart_id: $(this).data('cart')
                },
                loading: loading_el,
                success: function(data, msg, xhr) {
                    Propeller.Toast.show('Propeller', '', data.message, data.success ? 'success' : 'error');
                
                    if (typeof data.redirect != 'undefined')
                        window.location.href = data.redirect;
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        cart_add_item: function(event) {
            event.stopPropagation();
            event.preventDefault();

            var el = this;
            
            if (this.tagName.toLowerCase() === 'form')
                el = $(this).find('input.quantity');
            else if (this.tagName.toLowerCase() === 'button')
                el = $(this).closest('form').find('input.quantity');

            if (Propeller.ProductPlusMinusButtons.check_quantity(el)) {
                Propeller.ProductPlusMinusButtons.old_value = parseInt($(el).val());
                
                if (PropellerHelper.behavior.stock_check) {
                    var quantity = parseInt($(this).find('input[name="quantity"]').val());
                    var stock = parseInt($(this).find('input[name="quantity"]').data('stock'));
                    var diff = quantity - stock;
    
                    if (quantity > stock) {
                        var basket_form = $(this);
                        var is_card = $(this).closest('.propeller-product-card').length > 0;

                        var product_card = $(this).closest('.propeller-product-card').length 
                            ? $(this).closest('.propeller-product-card')
                            : $(this).closest('.propeller-product-details');
    
                        var product_image = $(product_card).find(is_card ? '.product-card-image img.img-fluid' : '.product-image a img').attr('src');
                        var product_name = $(product_card).find(is_card ? '.product-name a' : '.product-name').html();
                        var product_sku = $(product_card).find('.product-code').html().split(':')[1];
                        
                        $('#add-pre-basket-modal').find('.added-item-quantity').html(quantity);
                        $('#add-pre-basket-modal').find('.added-item-stock').html(stock);

                        $('#add-pre-basket-modal').find('.product-pre-basket-options .added-item-quantity').html(stock);
    
                        $('#add-pre-basket-modal').find('.added-item-name').html(product_name);
                        $('#add-pre-basket-modal').find('.added-item-img').attr('src', product_image);
                        $('#add-pre-basket-modal').find('.added-item-sku').html(product_sku);
                        $('#add-pre-basket-modal').find('.added-item-diff').html(diff);

                        
                        if(stock == 0) {
                            $('#add-pre-basket-modal').find('.added-item-full-quantity').val(diff);
                            $('#add-pre-basket-modal').find('.added-item-full-quantity').prop('checked',false);
                            $('#add-pre-basket-modal').find('.added-item-full-stock').val(0).prop('checked',true);
                            $('#add-pre-basket-modal').find('.not-enough-stock').hide();
                            $('#add-pre-basket-modal').find('.out-of-stock').show();
                            $('#add-pre-basket-modal').find('.product-pre-basket-options').show();
                        }
                        else {
                            $('#add-pre-basket-modal').find('.added-item-full-quantity').val(quantity);
                            $('#add-pre-basket-modal').find('.added-item-full-quantity.enough-stock').prop('checked',true);
                            $('#add-pre-basket-modal').find('.added-item-full-quantity:not(.enough-stock)').prop('checked',false);
                            $('#add-pre-basket-modal').find('.added-item-full-stock').val(stock);
                            $('#add-pre-basket-modal').find('.out-of-stock').hide();
                            $('#add-pre-basket-modal').find('.not-enough-stock').show();
                            $('#add-pre-basket-modal').find('.product-pre-basket-options').hide();
                        }

                        $('#add-pre-basket-modal').off('shown.bs.modal').on('shown.bs.modal', function (event) {
                            $('form[name="add-product-pre-basket"]').off('submit').on('submit', function(e) {
                                e.preventDefault();

                                var stock_check_data = $(this).serializeObject();

                                // If selected quantity is 0, just close the modal
                                if (parseInt(stock_check_data.pre_basket_option) === 0) {
                                    $('#add-pre-basket-modal').modal('hide');
                                    return false;
                                }

                                var loading_el = $(this).find('.btn-proceed');

                                var data = $(basket_form).serializeObject();
                                data.quantity = stock_check_data.pre_basket_option;

                                Propeller.Ajax.call({
                                    url: PropellerHelper.ajax_url,
                                    method: 'POST',
                                    data: data,
                                    loading: loading_el,
                                    success: function(response, msg, xhr) {
                                        $('#add-pre-basket-modal').modal('hide');
                                        Propeller.Cart.cart_postprocess(response);

                                        if (typeof PropellerGA4 !== 'undefined') {
                                            var product = PropellerGA4.products.find(function(p) {
                                                return p.productId == data.product_id;
                                            });
                                        }

                                        $(window).trigger('propel-add-to-cart' , [basket_form, response]);
                                    },
                                    error: function() {
                                        $('#add-pre-basket-modal').modal('hide');
                                        console.log('error', arguments);
                                    }
                                });

                                return false;
                            });
                        });

                        $('#add-pre-basket-modal').modal('show');

                        return false;
                    }
                }

                var loading_el = $(this).find('button[type="submit"]').length
                    ? $(this).find('button[type="submit"]')
                    : $(this);
    
                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: $(this).serializeObject(),
                    loading: loading_el,
                    success: function(data, msg, xhr) {
                        Propeller.Cart.cart_postprocess(data);
                    },
                    error: function() {
                        console.log('error', arguments);
                    }
                });
            }
            
            return false;
        },
        cart_add_item_cluster: function(event, form) {
            event.stopPropagation();
            event.preventDefault();

            if (typeof form == 'undefined' || !form)
                form = $(this);


            $(this).validate();

            if (!$(this).valid()) 
                return false;

            el = $(form).find('input.quantity');
            
            if (Propeller.ProductPlusMinusButtons.check_quantity(el)) {
                Propeller.ProductPlusMinusButtons.old_value = parseInt($(el).val());
                
                if (PropellerHelper.behavior.stock_check) {
                    var quantity = parseInt($(form).find('input[name="quantity"]').val());
                    var stock = parseInt($(form).find('input[name="quantity"]').data('stock'));
                    var diff = quantity - stock;
    
                    if (quantity > stock) {
                        var basket_form = $(form);
                        var product_card = $(form).closest('.propeller-product-details');
    
                        var product_image = $(product_card).find('.product-card-image img.img-fluid').attr('src');
                        var product_name = $(product_card).find('.product-name a').html();
                        var product_sku = $(product_card).find('.product-code').html().split(':')[1];
                        
                        $('#add-pre-basket-modal').find('.added-item-quantity').html(quantity);
                        $('#add-pre-basket-modal').find('.added-item-stock').html(stock);

                        $('#add-pre-basket-modal').find('.product-pre-basket-options .added-item-quantity').html(stock);

                        $('#add-pre-basket-modal').find('.added-item-name').html(product_name);
                        $('#add-pre-basket-modal').find('.added-item-img').attr('src', product_image);
                        $('#add-pre-basket-modal').find('.added-item-sku').html(product_sku);
                        $('#add-pre-basket-modal').find('.added-item-diff').html(diff);

                        if(stock == 0) {
                            $('#add-pre-basket-modal').find('.added-item-full-quantity').val(diff);
                            $('#add-pre-basket-modal').find('.added-item-full-quantity').prop('checked',false);
                            $('#add-pre-basket-modal').find('.added-item-full-stock').val(0).prop('checked',true);
                            $('#add-pre-basket-modal').find('.not-enough-stock').hide();
                            $('#add-pre-basket-modal').find('.out-of-stock').show();
                            $('#add-pre-basket-modal').find('.product-pre-basket-options').show();
                        }
                        else {
                            $('#add-pre-basket-modal').find('.added-item-full-quantity').val(quantity);
                            $('#add-pre-basket-modal').find('.added-item-full-quantity.enough-stock').prop('checked',true);
                            $('#add-pre-basket-modal').find('.added-item-full-quantity:not(.enough-stock)').prop('checked',false);
                            $('#add-pre-basket-modal').find('.added-item-full-stock').val(stock);
                            $('#add-pre-basket-modal').find('.out-of-stock').hide();
                            $('#add-pre-basket-modal').find('.not-enough-stock').show();
                            $('#add-pre-basket-modal').find('.product-pre-basket-options').hide();
                        }

                        $('#add-pre-basket-modal').off('shown.bs.modal').on('shown.bs.modal', function (event) {
                            $('form[name="add-product-pre-basket"]').off('submit').on('submit', function(e) {
                                e.preventDefault();

                                var stock_check_data = $(this).serializeObject();

                                // If selected quantity is 0, just close the modal
                                if (parseInt(stock_check_data.pre_basket_option) === 0) {
                                    $('#add-pre-basket-modal').modal('hide');
                                    return false;
                                }

                                var loading_el = $(this).find('.btn-proceed');

                                var data = $(basket_form).serializeObject();
                                data.quantity = stock_check_data.pre_basket_option;

                                Propeller.Ajax.call({
                                    url: PropellerHelper.ajax_url,
                                    method: 'POST',
                                    data: data,
                                    loading: loading_el,
                                    success: function(data, msg, xhr) {
                                        $('#add-pre-basket-modal').modal('hide');
                                        Propeller.Cart.cart_postprocess(data);
                                    },
                                    error: function() {
                                        $('#add-pre-basket-modal').modal('hide');
                                        console.log('error', arguments);
                                    }
                                });

                                return false;
                            });
                        });

                        $('#add-pre-basket-modal').modal('show');

                        return false;
                    }
                }
    
                var loading_el = $(form).find('button[type="submit"]').length
                    ? $(form).find('button[type="submit"]')
                    : $(form);
    
                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: $(form).serializeObject(),
                    loading: loading_el,
                    success: function(data, msg, xhr) {
                        Propeller.Cart.cart_postprocess(data);
                    },
                    error: function() {
                        console.log('error', arguments);
                    }
                });
            }
            
            return false;
        },
        cart_add_bundle: function(event) {
            event.stopPropagation();
            event.preventDefault();

            var loading_el = $(this).find('button[type="submit"]').length
                ? $(this).find('button[type="submit"]')
                : $(this);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: $(this).serializeObject(),
                loading: loading_el,
                success: function(data, msg, xhr) {
                    Propeller.Cart.cart_postprocess(data);
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        cart_update_item_blur: function(event) {
            var $input = $(this);
            var $form = $input.closest('form.update-basket-item-form');
            var $container = $input.closest('.basket-item-container');
            if ($container.attr('data-removing') === 'true') return false;
            if ($input.val() == 0) {
                var $deleteForm = $container.find('form.delete-basket-item-form');
                if ($deleteForm.length) {
                    $deleteForm.submit();
                    return false;
                }
            }
            if (Propeller.ProductPlusMinusButtons.check_quantity(this)) {
                Propeller.ProductPlusMinusButtons.old_value = parseInt($(this).val());
                $form.submit();
            }
        },
        cart_update_item_keypress: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            var $input = $(this);
            var $form = $input.closest('form.update-basket-item-form');
            var $container = $input.closest('.basket-item-container');
            if ($container.attr('data-removing') === 'true') return false;
            if(keycode == '13') {
                if($input.val() == 0) {
                    var $deleteForm = $container.find('form.delete-basket-item-form');
                    if ($deleteForm.length) {
                        $deleteForm.submit();
                        return false;
                    }
                }
                if (Propeller.ProductPlusMinusButtons.check_quantity(this)) {
                    Propeller.ProductPlusMinusButtons.old_value = parseInt($(this).val());
                    $form.submit();
                }
            }
        },
        quantity_key_up: function(event) {
            var value = $(this).val();

            // Don't remove single '0' as it's used for deletion in cart
            if (value.indexOf('0') == 0 && value !== '0')
                value = value.replace(/^0+/, '');

            $(this).val(value);
        },
        cart_update_item_focus: function(event) {
            var $input = $(this);
            // Store the current value as old_value for potential reversion
            if ($input.val() !== '' && !isNaN(parseInt($input.val(), 10))) {
                Propeller.ProductPlusMinusButtons.old_value = parseInt($input.val());
            }
        },
        cart_update_item: function(event) {
            event.stopPropagation();
            event.preventDefault();
            var $form = $(this);
            var $input = $form.find('input[name="quantity"]');
            var $container = $form.closest('.basket-item-container');
            if ($container.attr('data-removing') === 'true') return false;
            if ($input.length && parseInt($input.val(), 10) === 0) {
                var $deleteForm = $container.find('form.delete-basket-item-form');
                if ($deleteForm.length) {
                    $deleteForm.submit();
                    return false;
                }
            }
            if (Propeller.Cart.item_updating)
                return;

            // Stock check for cart quantity update
            if (PropellerHelper.behavior.stock_check && $input.length) {
                var quantity = parseInt($input.val(), 10);
                var stock = parseInt($input.data('stock'), 10);

                if (!isNaN(stock) && quantity > stock) {
                    // Prevent re-entry from blur firing again when modal opens
                    Propeller.Cart.item_updating = true;

                    var diff = quantity - stock;

                    var product_image = $container.find('.product-image img.img-fluid').attr('src');
                    var product_name = $container.find('.product-name').first().text().trim();
                    var product_sku = $container.find('.product-sku').first().text().split(':')[1];

                    $('#add-pre-basket-modal').find('.added-item-quantity').html(quantity);
                    $('#add-pre-basket-modal').find('.added-item-stock').html(stock);
                    $('#add-pre-basket-modal').find('.product-pre-basket-options .added-item-quantity').html(stock);
                    $('#add-pre-basket-modal').find('.added-item-name').html(product_name);
                    $('#add-pre-basket-modal').find('.added-item-img').attr('src', product_image);
                    $('#add-pre-basket-modal').find('.added-item-sku').html(product_sku);
                    $('#add-pre-basket-modal').find('.added-item-diff').html(diff);

                    // Uncheck all radios first, then set correct state
                    $('#add-pre-basket-modal').find('input[name="pre_basket_option"]').prop('checked', false);

                    if (stock == 0) {
                        $('#add-pre-basket-modal').find('.added-item-full-quantity').val(diff);
                        $('#add-pre-basket-modal').find('.added-item-full-stock').val(0).prop('checked', true);
                        $('#add-pre-basket-modal').find('.not-enough-stock').hide();
                        $('#add-pre-basket-modal').find('.out-of-stock').show();
                        $('#add-pre-basket-modal').find('.product-pre-basket-options').show();
                    } else {
                        $('#add-pre-basket-modal').find('.added-item-full-quantity').val(quantity);
                        $('#add-pre-basket-modal').find('.added-item-full-quantity.enough-stock').prop('checked', true);
                        $('#add-pre-basket-modal').find('.added-item-full-stock').val(stock);
                        $('#add-pre-basket-modal').find('.out-of-stock').hide();
                        $('#add-pre-basket-modal').find('.not-enough-stock').show();
                        $('#add-pre-basket-modal').find('.product-pre-basket-options').hide();
                    }

                    var updateFormData = $form.serializeObject();
                    var prevQuantity = $input.data('prev_quantity');

                    // Bind form submit handler before showing modal
                    $('form[name="add-product-pre-basket"]').off('submit').on('submit', function(e) {
                        e.preventDefault();

                        var stock_check_data = $(this).serializeObject();

                        // If selected quantity is 0, just close the modal and revert
                        if (parseInt(stock_check_data.pre_basket_option) === 0) {
                            $('#add-pre-basket-modal').modal('hide');
                            return false;
                        }

                        var loading_el = $(this).find('.btn-proceed');

                        updateFormData.quantity = stock_check_data.pre_basket_option;

                        if (prevQuantity)
                            updateFormData.prev_quantity = prevQuantity;

                        Propeller.Ajax.call({
                            url: PropellerHelper.ajax_url,
                            method: 'POST',
                            data: updateFormData,
                            loading: loading_el,
                            success: function(data, msg, xhr) {
                                Propeller.Cart.item_updating = false;
                                $('#add-pre-basket-modal').modal('hide');
                                Propeller.Cart.cart_postprocess(data);
                            },
                            error: function() {
                                Propeller.Cart.item_updating = false;
                                $('#add-pre-basket-modal').modal('hide');
                                console.log('error', arguments);
                            }
                        });

                        return false;
                    });

                    // Revert quantity and unlock if modal is dismissed without submitting
                    $('#add-pre-basket-modal').off('hidden.bs.modal.cartupdate').on('hidden.bs.modal.cartupdate', function () {
                        if (Propeller.Cart.item_updating) {
                            Propeller.Cart.item_updating = false;
                            $input.val(prevQuantity);
                        }
                    });

                    $('#add-pre-basket-modal').modal('show');
                    return false;
                }
            }

            var loading_el = $form.find('button[type="submit"]').length
                ? $form.find('button[type="submit"]')
                : $form;

            var form_data = $form.serializeObject();

            if ($($input).data('prev_quantity'))
                form_data.prev_quantity = $($input).data('prev_quantity');

            Propeller.Cart.item_updating = true;
            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: loading_el,
                success: function(data, msg, xhr) {
                    Propeller.Cart.item_updating = false;
                    Propeller.Cart.cart_postprocess(data);
                },
                error: function() {
                    Propeller.Cart.item_updating = false;
                    console.log('error', arguments);
                }
            });

            return false;
        },
        cart_delete_item: function(event) {
            event.stopPropagation();
            event.preventDefault();

            var loading_el = $(this).find('button[type="submit"]').length
                ? $(this).find('button[type="submit"]')
                : $(this);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: $(this).serializeObject(),
                loading: loading_el,
                success: function(data, msg, xhr) {
                    Propeller.Cart.cart_postprocess(data);
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        cart_add_action_code: function(event) {
            event.stopPropagation();
            event.preventDefault();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: $(this).serializeObject(),
                loading: $(event.target).find('button[type="submit"]'),
                success: function(data, msg, xhr) {
                    Propeller.Cart.cart_postprocess(data);
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        cart_remove_action_code: function(event) {
            event.stopPropagation();
            event.preventDefault();
            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: $(this).serializeObject(),
                loading: $(event.target).find('button[type="submit"]'),
                success: function(data, msg, xhr) {
                  
                    Propeller.Cart.cart_postprocess(data);
                },
                error: function() {
                    
                    console.log('error', arguments);
                }
            });

            return false;
        },
        cart_change_type: function(event) {
            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: $(event.target).closest('form').serializeObject(),
                loading: $(event.target).closest('form'),
                success: function(data, msg, xhr) {
                    Propeller.Cart.cart_postprocess(data);
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        load_item_crossupsells: function(event) {
            var product_id = $(this).data('product_id');
            var crossupsess_container = $(`#product-alternatives-${product_id}`);
            var trigger_btn = this;

            if (!$(trigger_btn).hasClass('cs-loaded')) {
                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'load_item_crossupsells', 
                        product_id: product_id
                    },
                    loading: $(trigger_btn),
                    success: function(data, msg, xhr) {
                        $(trigger_btn).addClass('cs-loaded');

                        $(crossupsess_container).html(data.content).removeClass('collapse').slideDown(200);

                        Propeller.Cart.init();
                    },
                    error: function() {
                        console.log('error', arguments);
                    }
                });
            } else {
                $(crossupsess_container).slideToggle(200);
            }

            

            return false;
        },
        cart_postprocess: function(data) {
            Propeller.Cart.init();

            if (typeof data.postprocess != 'undefined') {
                if (typeof data.postprocess.content != 'undefined')
                    $('#shoppingcart').replaceWith(data.postprocess.content);
                if (typeof data.postprocess.analytics != 'undefined')
                    $('body').append(data.postprocess.analytics);
                if (typeof data.postprocess.badge != 'undefined')
                    this.cart_update_badge(data.postprocess.badge);
                if (typeof data.postprocess.totals != 'undefined')
                    this.cart_update_totals(data.postprocess.totals, data.postprocess.badge, data.postprocess.postageData, data.postprocess.taxLevels);
                if (typeof data.postprocess.items != 'undefined')
                    this.cart_update_items(data.postprocess.items);
                if (typeof data.postprocess.postageData != 'undefined')
                    this.cart_update_postage(data.postprocess.postageData);
                if (typeof data.postprocess.taxLevels != 'undefined')
                    this.cart_update_taxLevels(data.postprocess.taxLevels);
                if (typeof data.postprocess.redirect != 'undefined') {
                    window.setTimeout(function() {
                        window.location.href = data.postprocess.redirect;
                    }, typeof data.postprocess.analytics != 'undefined' ? 500 : 1);
                }                    
                if (typeof data.postprocess.reload != 'undefined') {
                    window.setTimeout(function() {
                        window.location.reload();
                    }, typeof data.postprocess.analytics != 'undefined' ? 500 : 1);
                }
                    

                if (typeof data.postprocess.remove != 'undefined') {
                    var $row = $('div[data-item-id="' + data.postprocess.remove + '"]');
                    $row.fadeOut(400, function(){
                        $(this).remove();
                        if(data.postprocess.badge == 0)
                            window.location.reload();
                    });
                }

                if (typeof data.postprocess.show_modal != 'undefined' && data.postprocess.show_modal) {
                    if (typeof data.postprocess.content != 'undefined')
                        Propeller.Modal.show_content(data.postprocess.content);
                    else if (typeof data.postprocess.item != 'undefined')
                        Propeller.Modal.show(data.postprocess.item, 'success');

                    if (typeof data.postprocess.crossupsells != 'undefined' && data.postprocess.crossupsells) {
                        $('.propeller-shopping-cart-popup-crossupsells').html(data.postprocess.crossupsells.content);
                        Propeller.Cart.init();
                    }
                }
                else if (typeof data.postprocess.show_bundle != 'undefined' && data.postprocess.show_bundle) {
                    if (typeof data.postprocess.content != 'undefined')
                        Propeller.BundleModal.show_content(data.postprocess.content);
                    else if (typeof data.postprocess.item != 'undefined')
                        Propeller.BundleModal.show(data.postprocess.item);
                }
                else if (typeof data.postprocess.show_quick_modal != 'undefined' && data.postprocess.show_quick_modal) {
                    Propeller.QuickModal.show_content();
                }
                else {
                    if (typeof data.postprocess.message === 'string' && data.postprocess.message.trim() !== '') {
                        var toastType = data.postprocess.error ? 'error' : 'success';
                        Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), data.postprocess.message, toastType);
                    }
                   
                    if (data.object == 'QuickOrder') {
                        $('.quick-order-errors').html(data.postprocess.message);
                    }
                }

                Propeller.Cart.init();
            }
        },
        postprocess: function(data) {
            Propeller.Cart.init();

            if (typeof data.analytics != 'undefined')
                $('body').append(data.analytics);

            if (typeof data.redirect != 'undefined') {
                window.setTimeout(function() {
                    window.location.href = data.redirect;
                }, typeof data.analytics != 'undefined' ? 500 : 1);    
            }
        },
        cart_update_badge(count) {
            this.badge.html(count);
        },
        cart_update_postage(postageData) {
            // $('.propel-total-shipping').html(Propeller.Global.formatPrice(postageData.postage));
        },
        cart_update_totals: function(totals, count, postageData, taxLevels) {
            $('.propel-total-items').html(count);
            if ($('.propel-total-subtotal').hasClass('propel-total-subtotalNet'))
                $('.propel-total-subtotal').html(Propeller.Global.formatPrice(totals.subTotalNet));
            else
                $('.propel-total-subtotal').html(Propeller.Global.formatPrice(totals.subTotal));
            $('.propel-total-voucher').html(Propeller.Global.formatPrice(totals.discount));
            $('.propel-total-excl-btw').html(Propeller.Global.formatPrice(totals.totalGross));
            $('.propel-total-btw').html(Propeller.Global.formatPrice(totals.totalNet - totals.totalGross));
                       
            if(totals.totalNet >= 0) {
                $('.propel-total-price').html(Propeller.Global.formatPrice(totals.totalNet));
                $('.propel-mini-cart-total-price').html(Propeller.Global.formatPrice(totals.totalNet));
            }
            else {
                $('.propel-total-price').html(Propeller.Global.formatPrice(0));
                $('.propel-mini-cart-total-price').html(Propeller.Global.formatPrice(0));
            }
       
            $('.propel-total-shipping').removeClass('orangec-c');
            $('.propel-total-shipping').html(Propeller.Global.formatPrice(postageData.price));
       
            if (Array.isArray(taxLevels) && taxLevels.length) {
                taxLevels.forEach(function(taxLevel) {
                  
                    const taxSelector = `.propel-postage-tax[data-tax="${taxLevel.taxPercentage.toString()}"]`;
                    const $element = $(taxSelector);

                    if ($element.length) 
                        $element.html(Propeller.Global.formatPrice(taxLevel.price));        
                });
            }
            if (count > 0) {
                if(totals.totalNet >= 0)
                    $('.propel-mini-cart-total-price').html(Propeller.Global.formatPrice(totals.totalNet));
                else
                    $('.propel-mini-cart-total-price').html(Propeller.Global.formatPrice(0));
            }
                
            else
                $('.propel-mini-cart-total-price').html(Propeller.Global.formatPrice(0));
        },
        cart_update_taxLevels: function(taxLevels) {
            if (Array.isArray(taxLevels) && taxLevels.length) {
                taxLevels.forEach(function(taxLevel) {
                    const taxSelector = `.propel-postage-tax[data-tax="${taxLevel.taxPercentage.toString()}"]`;
                    const $element = $(taxSelector);

                    if ($element.length) 
                        $element.html(Propeller.Global.formatPrice(taxLevel.price));
                });
            } else {
                $('.propel-postage-tax').html('0');
            }
        },
        cart_update_items: function(items) {
            // if (items.length) {
            //     $(items).each(function(index, item){
            //         $('.basket-item-container[data-item-id="' + item.id + '"]')
            //             .find('.basket-item-price').html(
            //                 Propeller.Global.formatPrice(item.totalPrice)
            //             );

            //     });
            // }
        }
    };

    //Propeller.Cart.init();
    $(function() {
        Propeller.Cart.load_mini_cart();
    });

}(window.jQuery, window, document));