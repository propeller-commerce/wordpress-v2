(function ($, window, document) {

    Propeller.Paginator = {
        prev: $('.propeller-listing-pagination a.page-item.previous'),
        next: $('.propeller-listing-pagination a.page-item.next'),
        init: function () {
            $('.propeller-listing-pagination a.page-item').off('click').click(this.do_paging);
            $('.liststyle-options .btn-liststyle').off('click').click(this.sort_offset);
            $('select[name="catalog-offset"]').off('change').change(this.sort_offset);
            $('select[name="catalog-sort"]').off('change').change(this.sort_offset);

            var prevItem = $('.propeller-listing-pagination a.page-item.previous');
            var nextItem = $('.propeller-listing-pagination a.page-item.next');
            var pagesAll = $('.propeller-listing-pagination a.page-item').not(prevItem).not(nextItem);
            var current = $('.propeller-listing-pagination').data('current');
            var total = $('.propeller-listing-pagination').data('max');

            // Determine number of buttons based on screen width
            var isMobile = $(window).width() < 767;
            var maxButtons = isMobile ? 3 : 8;

            var nStartActive = 1;
            var nEndActive = maxButtons;

            if (total > maxButtons) {
                if (current > 1) {
                    // Center the current page when possible
                    var halfButtons = Math.floor(maxButtons / 2);
                    nStartActive = Math.max(1, current - halfButtons);
                    nEndActive = nStartActive + maxButtons - 1;

                    // Adjust if we've gone past the end
                    if (nEndActive > total) {
                        nEndActive = total;
                        nStartActive = Math.max(1, total - maxButtons + 1);
                    }
                }
            } else {
                nEndActive = total;
            }

            pagesAll.each(function (i, el) {
                if (i >= nStartActive - 1 && i < nEndActive) {
                    $(el).addClass('visible');
                } else {
                    $(el).removeClass('visible');
                }
            });

            $('.dots', '.propeller-listing-pagination').hide();

            // Hide previous/first buttons when on first page
            if (current > 1) {
                prevItem.addClass('visible');
                $('.propeller-listing-pagination a.page-item.first-page').addClass('visible');
            } else {
                prevItem.removeClass('visible');
                $('.propeller-listing-pagination a.page-item.first-page').removeClass('visible');
            }

            // Hide next/last buttons when on last page
            if (current < total) {
                nextItem.addClass('visible');
                $('.propeller-listing-pagination a.page-item.last-page').addClass('visible');
            } else {
                nextItem.removeClass('visible');
                $('.propeller-listing-pagination a.page-item.last-page').removeClass('visible');
            }

            if (nStartActive > 1 && !isMobile) {
                pagesAll.eq(0).addClass('visible');
            }
            if (nStartActive > 2 && !isMobile) {
                $('#dots-prev').css('display', 'flex');
            }
            if (nEndActive < pagesAll.length && !isMobile) {
                pagesAll.eq(pagesAll.length - 1).addClass('visible');
            }
            if ((nEndActive < pagesAll.length - 1) && !isMobile) {
                $('#dots-next').css('display', 'flex');
            }

        },
        sort_offset: function (event) {
            event.preventDefault();
            event.stopPropagation();

            if ($(this).hasClass('btn-liststyle')) {
                var $catalogProductList = $('.propeller-product-list');

                var $this = $(this);
                var $liststyle = $this.data('liststyle');

                $('.liststyle-options').find('.btn-liststyle').each(function (i, el) {
                    var $that = $(el);
                    $that.removeClass('active');
                    $catalogProductList.removeClass($that.data('liststyle'));
                });
                $this.addClass('active');

                // Set class on productlist
                $catalogProductList.addClass($liststyle);
                $('#propeller-catalog-filters').parent().data('liststyle', $liststyle);
                $(Propeller.product_container).parent().data('liststyle', $liststyle);

                return false;
            }

            var pageUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            var current = Propeller.Global.parseQuery(window.location.search);

            // Get action from clicked element or parent container
            current.action = $(this).data('action') || $('#propeller-catalog-filters').parent().data('action');
            current.offset = $('select[name="catalog-offset"]').val();
            current.sortInputs = $('select[name="catalog-sort"]').val();
            current.view = $(this).data('liststyle');

            // Get prop_name and prop_value from clicked element or parent container
            var propName = $(this).data('prop_name') || $('#propeller-catalog-filters').parent().data('prop_name');
            var propValue = $(this).data('prop_value') || $('#propeller-catalog-filters').parent().data('prop_value');

            // Extract brand name from URL if we're on a brand page
            if (window.location.pathname.indexOf('/brand/') > -1) {
                var pathParts = window.location.pathname.split('/');
                var brandIndex = pathParts.indexOf('brand');
                if (brandIndex > -1 && pathParts[brandIndex + 1]) {
                    current.action = 'do_brand';
                    current.manufacturers = pathParts[brandIndex + 1];
                }
            } else if (propName && propValue) {
                current[propName] = propValue;
            }

            if (PropellerHelper.behavior.ids_in_url && window.location.pathname.indexOf('/' + PropellerHelper.slugs.category) > -1) {
                if (typeof $(Propeller.product_container).parent().data('obid') != 'undefined' && $(Propeller.product_container).parent().data('obid') != '') {
                    current.obid = $(Propeller.product_container).parent().data('obid');
                }
            }

            delete current.ppage;

            pageUrl += '?' + Propeller.Global.buildQuery(current);

            Propeller.Global.changeAjaxPage(current, $(document).attr('title'), pageUrl);

            Propeller.Global.scrollTo($(Propeller.product_container));
            Propeller.CatalogListstyle.init(current.view);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: current,
                loading: $(Propeller.product_container),
                success: function (data, msg, xhr) {
                    $(Propeller.product_container).html(data.content);

                    Propeller.Frontend.init();
                    Propeller.CatalogListstyle.init(current.view);
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        do_paging: function (event) {
            event.preventDefault();
            event.stopPropagation();

            var pageUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            var current = Propeller.Global.parseQuery(window.location.search);

            // Get action from clicked element or parent container
            current.action = $(this).data('action') || $('#propeller-catalog-filters').parent().data('action');
            current.offset = $('select[name="catalog-offset"]').val();
            current.sortInputs = $('select[name="catalog-sort"]').val();
            current.view = $(Propeller.product_container).parent().data('liststyle');

            if (PropellerHelper.behavior.ids_in_url && window.location.pathname.indexOf('/' + PropellerHelper.slugs.category) > -1) {
                if (typeof $(Propeller.product_container).parent().data('obid') != 'undefined' && $(Propeller.product_container).parent().data('obid') != '') {
                    current.obid = $(Propeller.product_container).parent().data('obid');
                }
            }

            // Get prop_name and prop_value from clicked element or parent container
            var propName = $(this).data('prop_name') || $('#propeller-catalog-filters').parent().data('prop_name');
            var propValue = $(this).data('prop_value') || $('#propeller-catalog-filters').parent().data('prop_value');

            // Extract brand name from URL if we're on a brand page
            if (window.location.pathname.indexOf('/brand/') > -1) {
                var pathParts = window.location.pathname.split('/');
                var brandIndex = pathParts.indexOf('brand');
                if (brandIndex > -1 && pathParts[brandIndex + 1]) {
                    current.action = 'do_brand';
                    current.manufacturers = pathParts[brandIndex + 1];
                }
            } else if (propName && propValue) {
                current[propName] = propValue;
            }

            current.ppage = $(this).data('page');

            if (PropellerHelper.behavior.ids_in_url) {
                current.obid = $(this).data('obid');

                if (typeof current.obid == 'undefined' || current.obid == '')
                    delete current.obid;
            }

            if ($(this).attr('disabled') == 'disabled')
                return;

            pageUrl += '?' + Propeller.Global.buildQuery(current);

            Propeller.Global.changeAjaxPage(current, $(document).attr('title'), pageUrl);

            Propeller.Global.scrollTo($(Propeller.product_container));

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: current,
                loading: $(Propeller.product_container),
                success: function (data, msg, xhr) {
                    $(Propeller.product_container).html(data.content);

                    Propeller.Frontend.init();
                    Propeller.Filters.init(false);
                    Propeller.CatalogListstyle.init(current.view);
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        }
    };

    // Re-initialize on window resize to handle orientation changes
    var resizeTimer;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            Propeller.Paginator.init();
        }, 250);
    });

    //Propeller.Paginator.init();

}(window.jQuery, window, document));