(function ($, window, document) {

    Propeller.AccountPaginator = {
        paging_container: $('.propeller-account-pagination'),
        prev: $('.propeller-account-pagination a.page-item.previous'),
        next: $('.propeller-account-pagination a.page-item.next'),
        loading_el: $('.propeller-account-list'),
        scroll_to_el: $('.propeller-account-table'),

        init: function () {
            $('.propeller-account-pagination a.page-item').off('click').click(this.do_paging);
            $('.sort-link').off('click').click(this.do_sorting);
            $('.orders-sort-mobile, .mobile-sort-select').off('change').change(this.do_mobile_sorting);
            
            // Initialize sorting states based on current URL
            var urlParams = Propeller.Global.parseQuery(window.location.search);
            if (urlParams.sort_field && urlParams.sort_order) {
                this.updateSortingStates(urlParams.sort_field, urlParams.sort_order);
            }
        },
        do_paging: function (event) {
            event.preventDefault();
            event.stopPropagation();
            var current = Propeller.Global.parseQuery(window.location.search);

            current.action = $(Propeller.AccountPaginator.paging_container).data('action');
            current.ppage = $(this).data('page');

            if ($(this).attr('disabled') == 'disabled')
                return;

            Propeller.Global.scrollTo($(Propeller.AccountPaginator.scroll_to_el));

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: current,
                loading: Propeller.AccountPaginator.loading_el,
                success: function (data, msg, xhr) {
                    $(Propeller.AccountPaginator.loading_el).html(data.content);

                    Propeller.AccountPaginator.init();
                    Propeller.Global.convertLocalDates();
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        do_sorting: function (event) {
            event.preventDefault();
            event.stopPropagation();

            var $clickedLink = $(this);
            var current = Propeller.Global.parseQuery(window.location.search);

            // Get action from pagination container, with fallback logic
            var action = $(Propeller.AccountPaginator.paging_container).data('action');
            
            // Fallback: detect page type and set appropriate action
            if (!action) {
                if (window.location.pathname.indexOf('quotes') > -1 || $('.quotations-list').length > 0) {
                    action = 'get_quotes';
                } else if (window.location.pathname.indexOf('orders') > -1 || $('.orders-list').length > 0 || $('.propeller-account-list').length > 0) {
                    action = 'get_orders';
                } else {
                    action = 'get_orders'; // default fallback
                }
            }
            
            current.action = action;
            current.sort_field = $clickedLink.data('sort-field');
            current.sort_order = $clickedLink.data('sort-order');
            
            // Debug output
            console.log('DEBUG SORTING: action =', action);
            console.log('DEBUG SORTING: current data =', current);

            // Reset pagination when sorting
            delete current.ppage;

            // Update URL without page reload
            var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + Propeller.Global.buildQuery(current);
            window.history.pushState({}, '', newUrl);

            // Update active states immediately
            Propeller.AccountPaginator.updateSortingStates(current.sort_field, current.sort_order);

            Propeller.Global.scrollTo($(Propeller.AccountPaginator.scroll_to_el));

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: current,
                loading: Propeller.AccountPaginator.loading_el,
                success: function (data, msg, xhr) {
                    $(Propeller.AccountPaginator.loading_el).html(data.content);
                    Propeller.AccountPaginator.init();
                    Propeller.Global.convertLocalDates();
                    // Re-apply active states after content reload
                    Propeller.AccountPaginator.updateSortingStates(current.sort_field, current.sort_order);
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        do_mobile_sorting: function (event) {
            var $select = $(this);
            var sortValue = $select.val();
            var current = Propeller.Global.parseQuery(window.location.search);
            
            // Handle different mobile dropdown formats
            if ($select.hasClass('mobile-sort-select')) {
                // New quotations format - toggle order based on current state
                var currentField = $select.data('current-field');
                var currentOrder = $select.data('current-order');
                
                current.sort_field = sortValue;
                
                // If clicking same field, toggle order; otherwise use DESC as default
                if (currentField === sortValue) {
                    current.sort_order = (currentOrder === 'DESC') ? 'ASC' : 'DESC';
                } else {
                    current.sort_order = 'DESC';
                }
                
                // Update data attributes for next toggle
                $select.data('current-field', current.sort_field);
                $select.data('current-order', current.sort_order);
            } else {
                // Original orders format with pipe separator
                var sortParts = sortValue.split('|');
                if (sortParts.length !== 2) return;
                
                current.sort_field = sortParts[0];
                current.sort_order = sortParts[1];
            }

            // Get action from pagination container, with fallback logic
            var action = $(Propeller.AccountPaginator.paging_container).data('action');
            
            // Fallback: detect page type and set appropriate action
            if (!action) {
                if (window.location.pathname.indexOf('quotes') > -1 || $('.quotations-list').length > 0) {
                    action = 'get_quotes';
                } else if (window.location.pathname.indexOf('orders') > -1 || $('.orders-list').length > 0 || $('.propeller-account-list').length > 0) {
                    action = 'get_orders';
                } else {
                    action = 'get_orders'; // default fallback
                }
            }
            
            current.action = action;

            // Reset pagination when sorting
            delete current.ppage;

            // Update URL without page reload
            var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + Propeller.Global.buildQuery(current);
            window.history.pushState({}, '', newUrl);

            // Update active states immediately
            Propeller.AccountPaginator.updateSortingStates(current.sort_field, current.sort_order);

            Propeller.Global.scrollTo($(Propeller.AccountPaginator.scroll_to_el));

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: current,
                loading: Propeller.AccountPaginator.loading_el,
                success: function (data, msg, xhr) {
                    $(Propeller.AccountPaginator.loading_el).html(data.content);
                    Propeller.AccountPaginator.init();
                    Propeller.Global.convertLocalDates();
                    // Re-apply active states after content reload
                    Propeller.AccountPaginator.updateSortingStates(current.sort_field, current.sort_order);
                },
                error: function () {
                    console.log('error', arguments);
                }
            });
        },

        updateSortingStates: function (currentSortField, currentSortOrder) {
            // Update desktop sort links
            $('.sort-link').each(function() {
                var $link = $(this);
                var linkField = $link.data('sort-field');
                
                // Remove active class from all links
                $link.removeClass('active');
                
                // Add active class to current sort field
                if (linkField === currentSortField) {
                    $link.addClass('active');
                }
                
                // Update sort order for next click (toggle logic)
                var nextOrder = (linkField === currentSortField && currentSortOrder === 'DESC') ? 'ASC' : 'DESC';
                $link.data('sort-order', nextOrder);
                
                // Update sort icons
                var $icon = $link.find('.sort-icon');
                if (linkField === currentSortField) {
                    // Show current sort direction
                    $icon.removeClass('sort-none sort-asc sort-desc');
                    if (currentSortOrder === 'ASC') {
                        $icon.addClass('sort-asc').html('↑');
                    } else {
                        $icon.addClass('sort-desc').html('↓');
                    }
                } else {
                    // Show inactive state
                    $icon.removeClass('sort-asc sort-desc').addClass('sort-none').html('↕');
                }
            });
            
            // Update mobile dropdowns
            $('.orders-sort-mobile').val(currentSortField + '|' + currentSortOrder);
            
            // Update quotations mobile dropdown
            $('.mobile-sort-select').each(function() {
                var $select = $(this);
                $select.val(currentSortField);
                $select.data('current-field', currentSortField);
                $select.data('current-order', currentSortOrder);
            });
        }
    };

    //Propeller.AccountPaginator.init();

}(window.jQuery, window, document));