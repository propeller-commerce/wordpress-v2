(function ($, window, document) {

    Propeller.Product = {
        order_form: null,
        selected_options: [],
        init: function() {
            this.order_form = $('.add-to-basket-form');

            this.handle_order_button();
            this.init_cluster_sticky_sidebar();

            // linear clusters
            $('.cluster-dropdown').off('change').on('change', this.dropdown_changed);

            // configurable clusters
            $('.cluster-config-radio').off('change').on('change', this.cluster_config_radio_change);
            // $('.cluster-option-dropdown-hidden').off('change').on('change', this.config_option_changed);

            $(".cluster-config-dropdown").each(function(i, el){
                if (el.nodeName.toLowerCase() == 'select' && !el.tomselect) {
                    var t_select = new TomSelect(`#${$(el).attr('id')}` ,{
                        allowEmptyOption: true,
                        create: false, 
                        controlInput: null,
                        onChange: function(value) {
                            Propeller.Product.handle_cluster_dropdown_config_change(t_select);
                        }
                    });
                }
            });

            $(".cluster-option-dropdown").each(function(i, el){
                if (el.nodeName.toLowerCase() == 'select' && !el.tomselect) {
                    var t_select = new TomSelect(`#${$(el).attr('id')}`, {
                        allowEmptyOption: true,
                        create: false, 
                        controlInput: null,
                        onChange: function(value) {
                            Propeller.Product.handle_cluster_option_change(t_select);
                        },
                        render:{ 
                            option: function(data, escape) {
                                var dom = '<div>';
                                
                                if (typeof data.image != 'undefined' )
                                    dom += `<img class="cluster-option-img" src="${escape(data.image)}" />`
                                
                                dom += escape(data.text);

                                dom += '</div>';

                                return dom;
                            },
                            item: function(data, escape) {
                                var dom = '<div>';
                                
                                if (typeof data.image != 'undefined' )
                                    dom += `<img class="cluster-option-img" src="${escape(data.image)}" />`
                                
                                dom += escape(data.text);

                                dom += '</div>';

                                return dom;
                            },
                        }
                    });
                }
            });

            $('form.add-favorite').off('submit').on('submit', this.add_favorite);

            // Reload favorite modal content every time it opens
            $('[id^="add_favorite_modal_"]').off('show.bs.modal').on('show.bs.modal', function() {
                var modalId = $(this).attr('id');
                var parts = modalId.replace('add_favorite_modal_', '').split('_');
                var favClass = parts[0];
                var favId = parts.slice(1).join('_');

                var reloadData = { class: favClass };
                if (favClass === 'product')
                    reloadData.product_id = favId;
                else if (favClass === 'cluster')
                    reloadData.cluster_id = favId;

                Propeller.Product.reload_favorite_modal(favId, reloadData);
            });

            if ($('.slick-crossup').length)
                this.load_crossupsells();

            if(Propeller.hasOwnProperty('Cart')) {
                Propeller.Cart.init();
            } 
        },
        handle_order_button: function() {
            var disable = this.order_form.find('input[name="product_id"').val() == '';

            this.order_form.find('button[data-type="minus"]').prop('disabled', disable);
            this.order_form.find('button[data-type="plus"]').prop('disabled', disable);
            this.order_form.find('input.quantity').prop('disabled', disable);
            this.order_form.find('button.btn-addtobasket').prop('disabled', disable);
        },
        cluster_config_radio_change: function(e) {
            Propeller.Product.handle_cluster_config_radio_change(this);
        },
        cluster_config_change: function(e) {
            Propeller.Product.handle_cluster_config_change(this);
        },
        config_option_changed: function(e) {
            Propeller.Product.handle_cluster_option_change(this);
        },
        handle_cluster_dropdown_config_change: function(obj){
            
            if (obj.getValue() == '')
                return;

            var loader = $(obj.input).closest('div.dropdown');

            var config_data = $.extend({}, $('.cluster-config-dropdown').serializeObject(), $('.cluster-config-radio').serializeObject());
            var options_data = $('.cluster-option-dropdown-hidden').serializeObject();

            var options_vals = [];
            Object.keys(options_data).forEach(key => {
                var value = options_data[key];
                
                if (value)
                    options_vals.push(value);
            });

            $('.cluster-add-to-basket-form').find('input[name="options"]').val(options_vals.join(','));

            var request_data = $.extend({}, config_data, options_data);

            request_data.action = 'update_cluster_content';
            request_data.slug = $(obj.input).data('slug');
            request_data.cluster_id = $(obj.input).data('cluster_id');
            request_data.cluster_desc = $(obj.input).data('description');
            request_data.clicked_attr = $(obj.input).attr('name');
            request_data.clicked_val = $(obj.input).val();

            var gallery_wrapper_height = $('.gallery-wrapper').outerHeight();
            var desc_media_height = $('.propeller-desc-media').outerHeight();
            var desc_media_margin = $('.propeller-desc-media').css("marginTop");
            var specs_height = $('#pane-specifications').outerHeight();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: request_data,
                loading: loader,
                success: function(data, msg, xhr) {
                    $('.propeller-product-details').parent().html(data.content)
                        .find('.gallery-wrapper').css('height', gallery_wrapper_height + 'px')
                        .find('.propeller-desc-media').css('margin-top', desc_media_margin)
                        .find('.propeller-desc-media').css('height', desc_media_height + 'px')
                        .find('#pane-specifications').css('height', specs_height + 'px');
                    
                    Propeller.Validator.assign_validator($('.cluster-add-to-basket-form'));
                    Propeller.ProductPlusMinusButtons.init();
                    Propeller.Product.gallery_change();
                    Propeller.Product.gallery_swipe();
                    //Propeller.Product.cross_upsell_slider();
                    Propeller.Product.init();
                    Propeller.ProductFixedWrapper.init();

                    if(Propeller.hasOwnProperty('Cart')) {
                        Propeller.Cart.init();
                    }

                    Propeller.Product.load_spare_parts();
                },
                error: function() {
                    
                }
            });
        },
        handle_cluster_config_radio_change: function(obj){
            if ($(obj).val() == '')
                return;

            var loader = $(obj);

            if ($(obj).hasClass('cluster-config-dropdown-hidden'))
                loader = $(obj).closest('div.dropdown');

            var config_data = $.extend({}, $('.cluster-config-dropdown').serializeObject(), $('.cluster-config-radio').serializeObject());
            var options_data = $('.cluster-option-dropdown-hidden').serializeObject();

            var options_vals = [];
            Object.keys(options_data).forEach(key => {
                var value = options_data[key];
                
                if (value)
                    options_vals.push(value);
            });

            $('.cluster-add-to-basket-form').find('input[name="options"]').val(options_vals.join(','));

            var request_data = $.extend({}, config_data, options_data);

            request_data.action = 'update_cluster_content';
            request_data.slug = $(obj).data('slug');
            request_data.cluster_id = $(obj).data('cluster_id');
            request_data.cluster_desc = $(obj).data('description');
            request_data.clicked_attr = $(obj).attr('name');
            request_data.clicked_val = $(obj).val();

            var gallery_wrapper_height = $('.gallery-wrapper').outerHeight();
            var desc_media_height = $('.propeller-desc-media').outerHeight();
            var desc_media_margin = $('.propeller-desc-media').css("marginTop");
            var specs_height = $('#pane-specifications').outerHeight();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: request_data,
                loading: loader,
                success: function(data, msg, xhr) {
                    $('.propeller-product-details').parent().html(data.content)
                        .find('.gallery-wrapper').css('height', gallery_wrapper_height + 'px')
                        .find('.propeller-desc-media').css('margin-top', desc_media_margin)
                        .find('.propeller-desc-media').css('height', desc_media_height + 'px')
                        .find('#pane-specifications').css('height', specs_height + 'px');
                    
                    Propeller.Validator.assign_validator($('.cluster-add-to-basket-form'));
                    Propeller.ProductPlusMinusButtons.init();
                    Propeller.Product.gallery_change();
                    Propeller.Product.gallery_swipe();
                    //Propeller.Product.cross_upsell_slider();
                    Propeller.Product.init();
                    Propeller.ProductFixedWrapper.init();

                    if(Propeller.hasOwnProperty('Cart')) {
                        Propeller.Cart.init();
                    }

                    Propeller.Product.load_spare_parts();
                },
                error: function() {
                    console.log('error', arguments);
                }
            });
        },
        handle_cluster_option_change: function(obj) {
            // if (obj.getValue() == '') {
            //     return;
            // }

            // Only remove the default placeholder option for REQUIRED options
            // Non-required options should keep the "No [option]" option available
            var isRequired = $(obj.input).data('required');
            if (isRequired) {
                var defaultOptionValue = '';
                if (obj.options.hasOwnProperty(defaultOptionValue)) {
                    obj.removeOption(defaultOptionValue);
                }
            }

            var loader = $(obj.input).closest('div.dropdown');

            // Collect all configuration and option data the same way as config changes
            var config_data = $.extend({}, $('.cluster-config-dropdown').serializeObject(), $('.cluster-config-radio').serializeObject());
            var options_data_raw = $('.cluster-option-dropdown').serializeObject();

            // Convert flat option keys to nested structure
            var options_data = {};
            if (!options_data.option) {
                options_data.option = {};
            }

            // Parse option[123] keys into option.123 structure
            Object.keys(options_data_raw).forEach(key => {
                var match = key.match(/^option\[(\d+)\]$/);
                if (match) {
                    var optionId = match[1];
                    var value = options_data_raw[key];
                    if (value) { // Only include non-empty values
                        options_data.option[optionId] = value;
                    }
                }
            });

            var options_vals = [];
            if (options_data.option) {
                Object.keys(options_data.option).forEach(optionId => {
                    var value = options_data.option[optionId];
                    if (value) {
                        options_vals.push(value);
                    }
                });
            }

            $('.cluster-add-to-basket-form').find('input[name="options"]').val(options_vals.join(','));

            // Include both config and options data in the request
            var request_data = $.extend({}, config_data, options_data);

            request_data.action = 'update_cluster_price';
            request_data.slug = $(obj.input).data('slug');
            request_data.cluster_id = $(obj.input).data('cluster_id');

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: request_data,
                loading: loader,
                success: function(data, msg, xhr) {
                    $('.product-price-details').replaceWith(data.content);
                    $(obj.input).closest('div.dropdown').removeClass('has-error');
                    $(obj.input).closest('div.dropdown').addClass('has-success');
                    // Propeller.Frontend.init();
                    Propeller.Validator.assign_validator($('.cluster-add-to-basket-form'));
                    Propeller.ProductPlusMinusButtons.init();
                    Propeller.Product.gallery_change();
                    Propeller.Product.gallery_swipe();
                    //Propeller.Product.cross_upsell_slider();
                    Propeller.Product.init();
                    Propeller.ProductFixedWrapper.init();

                    if(Propeller.hasOwnProperty('Cart'))
                        Propeller.Cart.init();
                },
                error: function() {
                    // Handle error silently or show user-friendly message
                }
            });
        }, 
        handle_cluster_option_clear: function(obj) {
            // Only re-add the default placeholder option for REQUIRED options
            // Non-required options should keep their "No [option]" option available
            var isRequired = $(obj.input).data('required');
            if (isRequired) {
                var defaultOptionValue = '';
                var defaultOptionText = obj.input.querySelector('option[value=""]') ? 
                    obj.input.querySelector('option[value=""]').textContent : 'Select an option';
                
                if (!obj.options.hasOwnProperty(defaultOptionValue)) {
                    obj.addOption({
                        value: defaultOptionValue,
                        text: defaultOptionText
                    });
                }
            }
            
            // Clear the options from the form
            $('.cluster-add-to-basket-form').find('input[name="options"]').val('');
        }, 
        add_favorite: function(event, form) {
            event.preventDefault();
            
            var form_data = $(form).serializeObject();

            if (typeof form_data.action == 'undefined')
                return;

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $(form).find('button[type="submit"]'),
                success: function(data, msg, xhr) {
                    Propeller.Toast.show('Propeller', '', data.message, data.success ? 'success' : 'error');

                    var favClass = data.class.toLowerCase();

                    $(`#add_favorite_modal_${favClass}_${data.id}`).modal('hide');

                    Propeller.Product.reload_favorite_modal(data.id, data);

                    if (typeof data.analytics != 'undefined')
                        $('body').append(data.analytics);
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        reload_favorite_modal: function(product_id, data) {
            var favClass = data.class.toLowerCase();
            var form_data = {
                action: 'reload_favorite_modal',
                class: data.class
            }

            if (favClass == 'product')
                form_data.product_id = product_id;
            else if (favClass == 'cluster')
                form_data.cluster_id = product_id;

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                success: function(response, msg, xhr) {
                    if (response.success) {
                        var $modal = $(`#add_favorite_modal_${favClass}_${product_id}`);
                        $modal.find('.modal-content').replaceWith($(response.content).find('.modal-content'));

                        // Re-assign validator and submit handler for the new forms
                        Propeller.Validator.assign_validator($modal.find('form.validate'));

                        // Update heart icon based on whether product is still in any favorite list
                        var $btn = $(`button.btn-favorite[data-bs-target="#add_favorite_modal_${favClass}_${product_id}"]`);
                        var stillFavorite = $(response.content).find('form input[name="action"][value="delete_favorite"]').length > 0;
                        if (stillFavorite)
                            $btn.find('svg.icon-product-favorite').addClass('is-favorite');
                        else
                            $btn.find('svg.icon-product-favorite').removeClass('is-favorite');
                    }
                },
                error: function() {
                    console.log('error', arguments);
                }
            });
        },
        gallery_swipe: function() {
            var items = [];
            $('.gallery-item-slick' , '.slick-gallery').each( function() {
                var $figure = $(this),
                    $a = $figure.find('a'),
                    $src = $a.attr('href'),
                    $title = $figure.find('figcaption').html(),
                    $msrc = $figure.find('img').attr('src');
                if ($a.data('size')) {
                    var $size   = $a.data('size').split('x');
                    var item = {
                        src   : $src,
                        w   : $size[0],
                        h     : $size[1],
                        title   : $title,
                        msrc  : $msrc
                    };
                } else {
                    var item = {
                        src: $src,
                        w: 800,
                        h: 800,
                        title: $title,
                        msrc: $msrc
                    };
                    var img = new Image();
                    img.src = $src;

                    var wait = setInterval(function() {
                        var w = img.naturalWidth,
                            h = img.naturalHeight;
                        if (w && h) {
                            clearInterval(wait);
                            item.w = w;
                            item.h = h;
                        }
                    }, 30);
                }
                var index = items.length;
                items.push(item);
                $figure.unbind('click').click(function(event) {
                    event.preventDefault(); // prevent the normal behaviour i.e. load the <a> hyperlink
                    // Get the PSWP element and initialise it with the desired options
                    var $pswp = $('#pswp')[0];
                    console.log($pswp);
                    var options = {
                        index: index,
                        bgOpacity: 0.8,
                        showHideOpacity: true
                    }
                    new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options).init();
                });
            });
        },
        gallery_change: function() {
            $('.slick-gallery').not('.slick-initialized').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                adaptiveHeight: false,
                fade: true,
                asNavFor: '#product-thumb-slick',
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            dots: true
                        }
                    }
                ]
            });

            $('#product-thumb-slick').not('.slick-initialized').slick({
                slidesToShow: 4,
                slidesToScroll: 4,
                vertical:true,
                dots: false,
                autoplay:false,
                arrows: true,
                asNavFor: '#slick-gallery',
                focusOnSelect: true,
                centerMode: true,
                centerPadding: '20px',
                verticalSwiping:true,
                infinite: false,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            vertical: false,
                            slidesToShow: 3,
                            slidesToScroll: 1,
                            arrows: true,
                            focusOnSelect: true,
                            centerMode: false
                        }
                    }
                ]
            });
        },
        cross_upsell_slider: function(container = '') {
            container = container == '' ? '.slick-crossup' : '#' + container;

            $(container).not('.slick-initialized').slick({
                dots: false,
                infinite: false,
                arrows: true,
                speed: 300,
                slidesToShow: 4,
                slidesToScroll: 4,
                responsive: [
                    {
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            infinite: false,
                            arrows: true,
                        }
                    },
                    {
                        breakpoint: 991,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            dots: true,
                            arrows: false
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: "unslick"
                    }
                ]
            });
        },
        load_crossupsells: function() {
            // Propeller.Cart.init();
            // Propeller.ProductPlusMinusButtons.init();

            // if (typeof Propeller.Ajax.lazyload != 'undefined' && Propeller.Ajax.lazyload) {
            //     Propeller.Ajax.lazyload.update();
            // }

            this.cross_upsell_slider();
        

            return;
            if ($('.crossupsells-slider').length) {
                $('.crossupsells-slider').each(function(index, slider) {
                    Propeller.Ajax.call({
                        url: PropellerHelper.ajax_url,
                        method: 'POST',
                        data: {
                            slug: $(slider).data('slug'),
                            crossupsell_type: $(slider).data('type'),
                            id: $(slider).data('id'),
                            action: 'load_crossupsells',
                            class: $(slider).data('class')
                        },
                        loading: $(slider),
                        success: function(data, msg, xhr) {
                            $(slider).html(data.content);

                            //Propeller.Frontend.init();
                            Propeller.Cart.init();
                            Propeller.ProductPlusMinusButtons.init();

                            if (typeof Propeller.Ajax.lazyload != 'undefined' && Propeller.Ajax.lazyload) {
                                Propeller.Ajax.lazyload.update();
                            }
                            
                            Propeller.Product.cross_upsell_slider($(slider).attr('id'));
                        },
                        error: function() {
                            console.log('error', arguments);
                        }
                    });
                });
            }
        },
        load_spare_parts: function() {
            if (typeof PropellerSparepartsLive != 'undefined' && PropellerSparepartsLive.hasOwnProperty('Frontend') && Propeller.hasOwnProperty('SPL')) {
                Propeller.SPL.init();
            }
        },
        init_cluster_sticky_sidebar: function() {
            var $wrapper = $('.propeller-cluster-details .product-price-description-wrapper');
            var $leftCol = $('.propeller-cluster-details .gallery-wrapper');
            var $rightCol = $wrapper.closest('.col-12.col-lg-5');

            if (!$wrapper.length || !$leftCol.length || !$rightCol.length) return;

            // Clean up previous instance
            $(window).off('scroll.clusterSticky resize.clusterSticky');
            $wrapper.css({ 'position': '', 'top': '', 'width': '' });

            // Wait for DOM to settle
            setTimeout(function() {
                var leftHeight = $leftCol.outerHeight();
                var wrapperHeight = $wrapper.outerHeight();

                // Only activate if left side is significantly taller
                if (leftHeight <= wrapperHeight + 100) return;

                // Parent column needs relative positioning for the absolute state
                $rightCol.css('position', 'relative');

                var handleScroll = function() {
                    var scrollTop = $(window).scrollTop();
                    var viewportHeight = $(window).height();
                    var colTop = $rightCol.offset().top;
                    var colHeight = $rightCol.outerHeight();
                    var wrapperH = $wrapper.outerHeight();

                    // Point where wrapper bottom would leave the viewport (start sticking)
                    var stickyStart = colTop + wrapperH - viewportHeight;
                    // Point where wrapper would exceed the column bottom (stop sticking)
                    var stickyEnd = colTop + colHeight - wrapperH;

                    if (scrollTop <= stickyStart) {
                        // Normal flow: scroll with page
                        $wrapper.css({ 'position': '', 'top': '', 'width': '' });
                    } else if (scrollTop > stickyStart && scrollTop < stickyEnd) {
                        // Fixed: wrapper bottom pinned near viewport bottom
                        $wrapper.css({
                            'position': 'fixed',
                            'top': (viewportHeight - wrapperH - 20) + 'px',
                            'width': $rightCol.width() + 'px'
                        });
                    } else {
                        // Absolute: wrapper pinned at bottom of column
                        $wrapper.css({
                            'position': 'absolute',
                            'top': (colHeight - wrapperH) + 'px',
                            'width': $rightCol.width() + 'px'
                        });
                    }
                };

                $(window).on('scroll.clusterSticky', handleScroll);
                $(window).on('resize.clusterSticky', function() {
                    Propeller.Product.init_cluster_sticky_sidebar();
                });

                handleScroll();
            }, 100);
        },
    };

    //Propeller.Product.init();

}(window.jQuery, window, document));