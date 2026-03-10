(function ($, window, document) {

    Propeller.ProductFixedWrapper = {
        scroll_to: true,
        init: function () {
            var productGalleryExists = false;

            if ($('.gallery-container').length) {
                var productGalleryOffsetTop = $('.gallery-container').offset().top;
                productGalleryExists = true;
            }

            $('a[href*="#"]:not([href="#"])').unbind('click').bind('click' , function() {
                if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') || location.hostname == this.hostname) {
                    var target = $(this).attr('href'),

                        headerHeight = $('.sticky-header').height() + 20; // Get fixed header height
                    target = target.length ? $(target) : $('[name=' + this.hash.slice(1) +']');

                    if ( target.offset().top ) {
                        $('html,body').animate({
                            scrollTop: target.offset().top - headerHeight
                        }, 500);
                        return false;
                    }
                }
            });

            $('#product-sticky-links a').off('click').click(function(e) {
                e.preventDefault();

                Propeller.ProductFixedWrapper.load_content(this);

                $('#product-sticky-links a').removeClass('active');
                $(this).addClass('active');

                if (Propeller.ProductFixedWrapper.scroll_to) {
                    var target = $($(this).attr('href'));

                    if (target.offset().top) {
                        var headerHeight = $('.sticky-header').height() + 20; // Get fixed header height

                        $('html,body').animate({
                            scrollTop: target.offset().top - headerHeight
                        }, 500);
                        return false;
                    }
                }

                return false;
            });


            $(window).scroll(function() {
                if (productGalleryExists) {
                    if ($(this).scrollTop() >= productGalleryOffsetTop) {
                        $('#fixed-wrapper').addClass("show");
                        $('#product-sticky-links').addClass("sticky");
                    } else {
                        $('#fixed-wrapper').removeClass("show");
                        $('#product-sticky-links').removeClass("sticky");
                    }
                }
            });

            // Store the currently active tab before any auto-loading
            var $originalActiveTab = $('#product-sticky-links a.nav-link.active');

            // Check if any tab is already marked as active from PHP
            var $activeTab = $('#product-sticky-links a.nav-link.active');
            
            if ($activeTab.length && $activeTab.data('tab')) {
                // If there's an active tab with data-tab attribute, load its content
                this.scroll_to = false;
                $activeTab.click();
            } else if ($('#product-sticky-links li').first().find('a.nav-link').data('tab') == 'specifications') {
                // Fallback: if first tab is specifications, activate it
                this.scroll_to = false;
                $('#product-sticky-links li').first().find('a.nav-link').click();
            }

            // Auto-load specifications content without changing visual state
            if (PropellerHelper.behavior.load_specifications && $('#product-sticky-links li').first().find('a.nav-link').data('tab') != 'specifications') {
                var $specsTab = $('#product-sticky-links li').find('a.nav-link[data-tab="specifications"]');
                
                if ($specsTab.length) {
                    // Load specifications content silently without clicking
                    this.scroll_to = false;
                    this.load_content($specsTab[0]);
                    
                    // Restore the original active tab visual state
                    if ($originalActiveTab.length) {
                        $('#product-sticky-links a').removeClass('active');
                        $originalActiveTab.addClass('active');
                    }
                }
            }
        },
        load_content: function(obj) {
            var data_type = $(obj).data('tab');
            var data_id = $(obj).data('id');
            var data_loaded = $(obj).data('loaded');

            if (typeof data_type != 'undefined' && !data_loaded) {
                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: {
                        id: data_id,
                        action: 'load_product_' + data_type
                    },
                    loading: $('#pane-' + data_type),
                    success: function(data, msg, xhr) {
                        $('#pane-' + data_type).html(data.content);
                        $(obj).data('loaded', 'true');

                        if (data_type == 'specifications')
                            Propeller.ProductFixedWrapper.init_specs_show_more();

                        Propeller.ProductFixedWrapper.scroll_to = true;

                        // Recalculate cluster sticky sidebar after content height changes
                        if (typeof Propeller.Product !== 'undefined' && typeof Propeller.Product.init_cluster_sticky_sidebar === 'function')
                            Propeller.Product.init_cluster_sticky_sidebar();
                    },
                    error: function() {
                        console.log('error', arguments);
                    }
                });
            }
        },
        init_specs_show_more: function() {
            $('a.load-attributes').off('click').click(function(e) {
                e.preventDefault();

                var data_type = $(this).data('tab');
                var data_id = $(this).data('id');
                var btn_container = $(this).closest('.show-more-container');
                
                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: {
                        id: data_id,
                        action: 'load_product_' + data_type,
                        ppage: $(this).data('page'),
                        offset: $(this).data('offset')
                    },
                    loading: $('#pane-' + data_type),
                    success: function(data, msg, xhr) {
                        $(btn_container).remove();

                        $('.product-specs-rows').append(data.content);
                        Propeller.ProductFixedWrapper.init_specs_show_more();

                        Propeller.ProductFixedWrapper.scroll_to = true;

                        // Recalculate cluster sticky sidebar after content height changes
                        if (typeof Propeller.Product !== 'undefined' && typeof Propeller.Product.init_cluster_sticky_sidebar === 'function')
                            Propeller.Product.init_cluster_sticky_sidebar();
                    },
                    error: function() {
                        console.log('error', arguments);
                    }
                });

                return false;
            });
        }
    };

    //Propeller.ProductFixedWrapper.init();

}(window.jQuery, window, document));