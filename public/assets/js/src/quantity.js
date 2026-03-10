(function ($, window, document) {
    const {__, _x, _n, _nx} = wp.i18n;
    
    Propeller.ProductPlusMinusButtons = {
        initialized: false,
        old_value: null,
        init: function () {
            $('input.product-quantity-input').off('keypress').on('keypress', this.quantity_keypress);
            $('input.product-quantity-input').off('change').on('change', this.quantity_change);
            $('input.product-quantity-input').off('blur').on('blur', this.quantity_change);
            $('input.product-quantity-input').off('focus').on('focus', this.quantity_focus);
            
            $('.btn-price-request').off('click').on('click', this.add_price_request_product);

            if (Propeller.ProductPlusMinusButtons.initialized)
                return;
            
            // iOS-specific touch handling
            var lastTouchTime = 0;
            
            $(document).on('touchstart click', '.btn-quantity', function (e) {
                var currentTime = new Date().getTime();
                var $btn = $(this);
                
                // For touch devices, prevent click if touchstart just happened
                if (e.type === 'click' && currentTime - lastTouchTime < 500) {
                    e.preventDefault();
                    return false;
                }
                
                // For touch events, record the time
                if (e.type === 'touchstart') {
                    lastTouchTime = currentTime;
                }
                
                // Prevent rapid successive taps
                if ($btn.data('processing')) {
                    e.preventDefault();
                    return false;
                }
                $btn.data('processing', true);
                
                // Reset processing flag
                setTimeout(function() {
                    $btn.data('processing', false);
                }, 300);
                
                var $that = $(this).closest('form');
                var $btns = $that.find('.btn-quantity');
                var $quantity = $that.find('input.quantity');
                var $unit = eval($quantity.data("unit"));
                var $min = eval($quantity.data("min"));

                var oClickDelay = 0;
                var $val = parseInt($quantity.val());

                try {
                    clearTimeout(oClickDelay);
                } catch (e) {}

                if ($btn.data('type') === 'minus') {
                    var newVal = $val - $unit;
                    var isCartUpdate = $that.hasClass('update-basket-item-form');
                    
                    // For cart updates, allow going to 0 (for removal)
                    // For other forms, respect the minimum
                    if (isCartUpdate) {
                        if (newVal >= 0) {
                            $quantity.val(newVal);
                        }
                    } else {
                        if (newVal >= $min) {
                            $quantity.val(newVal);
                        }
                    }
                } else {
                    $quantity.val($val + $unit);
                }
                if (eval($quantity.val()) > $min) {
                    $btns.eq(0).attr('disabled', false);
                } else {
                    $btns.eq(0).attr('disabled', true);
                }

                // --- CART PAGE LOGIC: If quantity is 0, submit delete form and prevent update ---
                if ($that.hasClass('update-basket-item-form') && parseInt($quantity.val(), 10) === 0) {
                    var $container = $quantity.closest('.basket-item-container');
                    var $deleteForm = $container.find('form.delete-basket-item-form');
                    if ($deleteForm.length) {
                        $deleteForm.submit();
                        e.preventDefault();
                        return false;
                    }
                }

                // Submit the update form for cart items
                var isCartUpdate = $that.hasClass('update-basket-item-form');
                if (isCartUpdate) {
                    $(this).closest('form.update-basket-item-form').submit();
                } else {
                    $quantity.trigger('change').trigger('keyup');
                }
            });
            
            Propeller.ProductPlusMinusButtons.initialized = true;
        },
        add_price_request_product: function(event) {
            event.preventDefault();
                      
            // var comments = '';

            // if ($(this).hasClass('btn-cluster-price-request')) {
            //     var options_data = $('.cluster-config-dropdown-hidden').serializeObject();
                
            //     console.log(options_data);

            //     var cluster_id = $(this).data('cluster_id');
            //     var product_id = $(this).data('id');

            //     comments = `${cluster_id}: ${product_id} + ${options_data.join(',')}`;

            //     console.log(comments);
            // }

            // return;

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
        quantity_change: function(event) {
            var $input = $(this);
            var $form = $input.closest('form.update-basket-item-form');
            var $container = $input.closest('.basket-item-container');
            var min = parseInt($input.data('min')) || 1;
            // Allow empty input during typing, only restore on blur if still empty
            if ($input.val() === '' || isNaN(parseInt($input.val(), 10))) {
                if (event.type === 'blur') {
                    var fallback = Propeller.ProductPlusMinusButtons.old_value || min;
                    $input.val(fallback);
                }
                return false;
            }
            // Disallow 0 outside cart page, revert to max(min, old_value)
            if (!$form.length && parseInt($input.val(), 10) === 0) {
                var fallback = Math.max(min, Propeller.ProductPlusMinusButtons.old_value || min);
                $input.val(fallback);
                return false;
            }
            // Only allow 0 for removal if inside update-basket-item-form, or if min is 0
            if (parseInt($input.val(), 10) === 0) {
                if ($form.length) {
                    if ($container.attr('data-removing') === 'true') return false;
                    $container.attr('data-removing', 'true');
                    $input.val(0); // Ensure 0 is visible before deletion
                    var $deleteForm = $container.find('form.delete-basket-item-form');
                    if ($deleteForm.length) {
                        $deleteForm.submit();
                        event.preventDefault && event.preventDefault();
                        return false;
                    }
                } else if (min === 0) {
                    // Allow 0 if min is 0 (e.g., for special cases)
                    // Do nothing, let check_quantity handle it
                } else {
                    // Not allowed, revert
                    var fallback = Math.max(min, Propeller.ProductPlusMinusButtons.old_value || min);
                    $input.val(fallback);
                    return false;
                }
            }
            if ($container.attr('data-removing') === 'true') return false;
            if (Propeller.ProductPlusMinusButtons.check_quantity(this))
                Propeller.ProductPlusMinusButtons.old_value = parseInt($(this).val());
        },
        quantity_keypress: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            var $input = $(this);
            var $form = $input.closest('form.update-basket-item-form');
            var $container = $input.closest('.basket-item-container');
            var min = parseInt($input.data('min')) || 1;
            // Allow empty input during typing, only handle on Enter key
            if (($input.val() === '' || isNaN(parseInt($input.val(), 10))) && keycode == '13') {
                var fallback = Propeller.ProductPlusMinusButtons.old_value || min;
                $input.val(fallback);
                return false;
            }
            // Disallow 0 outside cart page, revert to max(min, old_value)
            if (!$form.length && parseInt($input.val(), 10) === 0) {
                var fallback = Math.max(min, Propeller.ProductPlusMinusButtons.old_value || min);
                $input.val(fallback);
                return false;
            }
            // Only allow 0 for removal if inside update-basket-item-form, or if min is 0
            if (parseInt($input.val(), 10) === 0 && !$form.length && min !== 0) {
                var fallback = Math.max(min, Propeller.ProductPlusMinusButtons.old_value || min);
                $input.val(fallback);
                return false;
            }
            if ($container.attr('data-removing') === 'true') return false;
            if (keycode == '13') {
                if ($form.length && parseInt($input.val(), 10) === 0) {
                    if ($container.attr('data-removing') === 'true') return false;
                    $container.attr('data-removing', 'true');
                    $input.val(0); // Ensure 0 is visible before deletion
                    var $deleteForm = $container.find('form.delete-basket-item-form');
                    if ($deleteForm.length) {
                        $deleteForm.submit();
                        event.preventDefault && event.preventDefault();
                        return false;
                    }
                }
                if ($container.attr('data-removing') === 'true') return false;
                if (Propeller.ProductPlusMinusButtons.check_quantity(this))
                    Propeller.ProductPlusMinusButtons.old_value = parseInt($(this).val());
            }
        },
        check_quantity: function(el) {
            var val = parseInt($(el).val());
            var unit = parseInt($(el).data("unit"));
            var min = parseInt($(el).data("min"));
            var $form = $(el).closest('form.update-basket-item-form');

            // Allow 0 for removal in cart
            if (val === 0 && $form.length) return true;
            
            // Allow 0 if min is 0
            if (val === 0 && min === 0) return true;

            // Only allow multiples of unit above min
            if ((val - min) % unit !== 0 || val < min) {
                var msg = PropellerHelper.translations.quantity_unit_string.replace('{unit}', unit).replace('{min}', min);
                Propeller.Toast.show("", "", msg, 'error');
                if (Propeller.ProductPlusMinusButtons.old_value)
                    $(el).val(Propeller.ProductPlusMinusButtons.old_value);

                return false;
            }

            return true;
        },
        quantity_focus: function(event) {
            var $input = $(this);
            var $form = $input.closest('form.update-basket-item-form');
            var min = parseInt($input.data('min')) || 1;
            if ($form.length && ($input.val() === '' || isNaN(parseInt($input.val(), 10)))) {
                $input.val(0);
            } else if (!$form.length && ($input.val() === '' || isNaN(parseInt($input.val(), 10)))) {
                $input.val(min);
            }
            Propeller.ProductPlusMinusButtons.old_value = parseInt($input.val()) || min;
        }
    };

    //Propeller.ProductPlusMinusButtons.init();

}(window.jQuery, window, document));