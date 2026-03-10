(function ($, window, document) {

    Propeller.Filters = {
        sliders: [],
        slider_running: false,
        init: function (initNumericFilters = true) {

            $(document).off('change').on('change', 'form.filterForm input:not(.numeric-min):not(.numeric-max)', this.filters_change);

            $('a.btn-active-filter').off('click').on('click', this.active_filters_click);
            $('.btn-remove-active-filters').off('click').on('click', this.clear_filters);

            if (initNumericFilters)
                this.init_numeric_filters();

            $(document).off('propeller-search-success').on('propeller-search-success', this.init_filters);

            $('#pdp_new_tab').off('change').on('change', this.pdp_behavior);

            this.expand_filters();
        },
        init_filters: function (event, filters, active_filter) {
            Propeller.Filters.redraw_filters(filters, active_filter);
            Propeller.Filters.enable_filters();
        },
        init_numeric_filters: function () {
            $('.numeric-filter').each(function (i, container) {
                var $min = $(container).find('.numeric-min');
                var $max = $(container).find('.numeric-max');
                var $el = $(container).find('.slider')[0];

                try {
                    // https://refreshless.com/nouislider/

                    var slider = noUiSlider.create($el, {
                        start: [$($el).data('min'), $($el).data('max')],
                        connect: true,
                        range: {
                            'min': $($el).data('min'),
                            'max': $($el).data('max')
                        },
                        format: {
                            from: function (value) {
                                return Math.round(value);
                            },
                            to: function (value) {
                                return Math.round(value);
                            }
                        }
                    });

                    slider.on('slide', function (values) {
                        $min.val(values[0]);
                        $max.val(values[1]);
                    });

                    slider.on('set', function (values) {
                        if (!Propeller.Filters.slider_running) {
                            Propeller.Filters.slider_running = true;
                            var slug = $($el).closest('form').find('input[name="prop_value"]').val();

                            Propeller.Filters.apply_filter(
                                [
                                    {
                                        name: $min.attr('name'),
                                        value: values[0]
                                    },
                                    {
                                        name: $max.attr('name'),
                                        value: values[1]
                                    },
                                ], slug, true, function () {
                                    Propeller.Filters.slider_running = false;
                                });
                        }
                    });

                    $min.off("keypress").on("keypress", Propeller.Filters.min_max_keypress);
                    $max.off("keypress").on("keypress", Propeller.Filters.min_max_keypress);

                    $min.off("change").on("change", Propeller.Filters.handle_min);
                    $max.off("change").on("change", Propeller.Filters.handle_max);

                    Propeller.Filters.sliders.push(slider);
                }
                catch (ex) {
                    // probably slider is already initialized
                }
            });
        },
        min_max_keypress: function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode == '13') {
                e.preventDefault();
                e.stopPropagation();

                $(this).trigger('change');

                return false;
            }
        },
        handle_min: function (e) {
            var $max = $(this).closest('form').find('.numeric-max');

            if ($(this).val() < $(this).data('min') || $(this).val() > $max.data('max'))
                $(this).val($(this).data('min'));

            var slider = $(this).closest('form').find('.slider')[0];
            slider.noUiSlider.set([parseInt($(this).val()), null], true);
        },
        handle_max: function (e) {
            var $min = $(this).closest('form').find('.numeric-min');

            if ($(this).val() > $(this).data('max') || $(this).val() < $min.data('min'))
                $(this).val($(this).data('max'));

            var slider = $(this).closest('form').find('.slider')[0];
            slider.noUiSlider.set([null, parseInt($(this).val())], true);
        },
        filters_change: function (event) {
            var slug = $(this).closest('form').find('input[name="prop_value"]').val();

            Propeller.Filters.apply_filter([{
                name: $(this).attr('name'),
                value: $(this).attr('value'),
                type: $(this).data('type')
            }],
                slug,
                $(this).is(':checked')
            );
        },
        apply_filter: function (filters, slug, do_add, callback = null, current = {}) {
            Propeller.Filters.disable_filters();

            var pageUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;

            if (window.location.href.indexOf('?') > -1) {
                var urlParams = Propeller.Global.parseQuery(window.location.search);
                // Merge URL parameters with current object, giving priority to URL parameters
                for (var param in urlParams) {
                    if (urlParams.hasOwnProperty(param)) {
                        current[param] = urlParams[param];
                    }
                }
            }

            for (var i = 0; i < filters.length; i++) {
                if (do_add) {
                    if (filters[i].name.indexOf('[from]') > -1 || filters[i].name.indexOf('[to]') > -1) {
                        current[filters[i].name] = filters[i].value;
                    }
                    else {
                        if (typeof current[filters[i].name] == 'undefined')
                            current[filters[i].name] = [];

                        current[filters[i].name].push(encodeURIComponent(filters[i].value));

                        if (typeof current[filters[i].name + '[type]'] == 'undefined')
                            current[filters[i].name + '[type]'] = filters[i].type;
                    }
                }
                else {
                    if (typeof current[filters[i].name] == 'object') {
                        var filterIndex = current[filters[i].name].indexOf(filters[i].value);

                        current[filters[i].name].splice(filterIndex, 1);

                        if (current[filters[i].name].length == 0) {
                            delete current[filters[i].name];

                            if (typeof current[filters[i].name + '[type]'] != 'undefined')
                                delete current[filters[i].name + '[type]'];
                        }
                    }
                    else {
                        delete current[filters[i].name];
                    }
                }
            }

            // remove the page prop when changing filters
            delete current.ppage;
            delete current.active_filter;

            var path = '';
            var obj_id = null;

            if (slug == '') {
                if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.category) > -1)
                    path = PropellerHelper.slugs.category;
                else if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.search) > -1)
                    path = PropellerHelper.slugs.search;
                else if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.brand) > -1)
                    path = PropellerHelper.slugs.brand;
                else if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.machines) > -1)
                    path = PropellerHelper.slugs.machines;

                var url_chunks = new RegExp(`\/(${path})\/(.*?)\/`).exec(window.location.pathname);

                if (url_chunks !== null) {
                    if (PropellerHelper.behavior.ids_in_url) {
                        id_chunks = url_chunks[2].split('/');

                        slug = id_chunks[1];
                        obj_id = id_chunks[0];
                    }
                    else
                        slug = url_chunks[2];
                }
            }

            // Ensure action is set from container if not already set
            if (!current.action) {
                current.action = $(Propeller.product_container).parent().data('action');
            }

            // Get prop_name and prop_value from parent container
            var propName = $(Propeller.product_container).parent().data('prop_name');
            var propValue = $(Propeller.product_container).parent().data('prop_value');

            if (propName && propValue) {
                current[propName] = propValue;
            }

            // ALWAYS ensure manufacturers is set for brand pages
            if (!current.manufacturers) {
                // First try to extract from URL path
                if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.brand) > -1) {
                    var brandPath = PropellerHelper.slugs.brand;
                    var brandRegex = new RegExp(`\/(${brandPath})\/(.*?)\/`);
                    var brandChunks = brandRegex.exec(window.location.pathname);

                    if (brandChunks !== null) {
                        var brandSlug = brandChunks[2];
                        current.action = 'do_brand';
                        current.manufacturers = brandSlug;
                    }
                }
            }

            current.view = $(Propeller.product_container).parent().data('liststyle');
            current.offset = $('select[name="catalog-offset"]').val();
            current.sortInputs = $('select[name="catalog-sort"]').val();

            if (do_add)
                current.active_filter = filters[0].name;

            if (!obj_id && PropellerHelper.behavior.ids_in_url && window.location.pathname.indexOf('/' + PropellerHelper.slugs.category) > -1) {
                if (typeof $(Propeller.product_container).parent().data('obid') != 'undefined' && $(Propeller.product_container).parent().data('obid') != '') {
                    obj_id = $(Propeller.product_container).parent().data('obid');
                }
                else {
                    path = PropellerHelper.slugs.category;

                    var url_chunks = new RegExp(`\/(${path})\/(.*?)\/`).exec(window.location.pathname);

                    if (url_chunks !== null) {
                        if (PropellerHelper.behavior.ids_in_url) {
                            id_chunks = url_chunks[2].split('/');

                            obj_id = id_chunks[0];
                        }
                    }
                }

                if (obj_id)
                    current.obid = obj_id;
            }

            pageUrl += '?' + Propeller.Global.buildQuery(current);

            Propeller.Global.changeAjaxPage(current, $(document).attr('title'), pageUrl);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: current,
                loading: Propeller.product_container,
                success: function (data, msg, xhr) {
                    // Ensure any existing overlays are properly removed
                    if (xhr.loader) {
                        xhr.loader.hide();
                        xhr.loader = null;
                    }
                    $(Propeller.product_container).html(data.content);

                    Propeller.Filters.redraw_filters(data.filters, current.active_filter);

                    Propeller.Filters.enable_filters();

                    if (typeof data.categories != 'undefined')
                        Propeller.Filters.redraw_categories(data.categories);

                    Propeller.Frontend.init();
                    Propeller.CatalogListstyle.init(current.view);

                    if (callback && typeof callback == 'function')
                        callback();

                    // custom event filters appllied
                },
                error: function () {
                    console.log('error', arguments);
                }
            });
        },
        disable_filters: function () {
            $("form.filterForm .slider").each(function (i, el) {
                el.setAttribute("disabled", true);
            });

            $("form.filterForm :input").prop("disabled", true);
        },
        enable_filters: function () {
            $("form.filterForm .slider").each(function (i, el) {
                el.removeAttribute("disabled");
            });

            $("form.filterForm :input").prop("disabled", false);
        },
        clear_filters: function (event) {
            event.preventDefault();

            $("form.filterForm :input").prop('checked', false);

            var pageUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;

            var slug = $('form.filterForm:first').find('input[name="prop_value"]').val();

            var current = {};
            if (window.location.href.indexOf('?') > -1)
                current = Propeller.Global.parseQuery(window.location.search);


            for (var prop in current) {
                if (typeof current[prop] == 'object')
                    delete current[prop];
                else if (typeof current[prop] == 'string' && prop.includes('['))
                    delete current[prop];
            }

            if (typeof current.term != 'undefined')
                delete current.term;

            delete current.ppage;
            delete current.active_filter;

            var path = '';
            var obj_id = null;

            if (slug == '') {
                if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.category) > -1)
                    path = PropellerHelper.slugs.category;
                else if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.search) > -1)
                    path = PropellerHelper.slugs.search;
                else if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.brand) > -1)
                    path = PropellerHelper.slugs.brand;
                else if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.machines) > -1)
                    path = PropellerHelper.slugs.machines;

                var url_chunks = new RegExp(`\/(${path})\/(.*?)\/`).exec(window.location.pathname);
                if (url_chunks !== null) {
                    if (PropellerHelper.behavior.ids_in_url) {
                        id_chunks = url_chunks[2].split('/');

                        slug = id_chunks[1];
                        obj_id = id_chunks[0];
                    }
                    else
                        slug = url_chunks[2];
                }
            }

            if (!obj_id && PropellerHelper.behavior.ids_in_url && window.location.pathname.indexOf('/' + PropellerHelper.slugs.category) > -1) {
                path = PropellerHelper.slugs.category;

                var url_chunks = new RegExp(`\/(${path})\/(.*?)\/`).exec(window.location.pathname);

                if (url_chunks !== null) {
                    if (PropellerHelper.behavior.ids_in_url) {
                        id_chunks = url_chunks[2].split('/');

                        obj_id = id_chunks[0];
                    }
                }

                if (obj_id)
                    current.obid = obj_id;
            }

            current.action = $('form.filterForm:first').find('input[name="action"]').val();

            // Get prop_name and prop_value from filter form
            var propName = $('form.filterForm:first').find('input[name="prop_name"]').val();
            var propValue = $('form.filterForm:first').find('input[name="prop_value"]').val();

            if (propName && propValue) {
                current[propName] = propValue;
            }

            // ALWAYS ensure manufacturers is set for brand pages
            if (!current.manufacturers && window.location.pathname.indexOf('/' + PropellerHelper.slugs.brand) > -1) {
                var brandPath = PropellerHelper.slugs.brand;
                var brandRegex = new RegExp(`\/(${brandPath})\/(.*?)\/`);
                var brandChunks = brandRegex.exec(window.location.pathname);

                if (brandChunks !== null) {
                    var brandSlug = brandChunks[2];
                    current.action = 'do_brand';
                    current.manufacturers = brandSlug;
                }
            }

            current.view = $(Propeller.product_container).parent().data('liststyle');

            pageUrl += '?' + Propeller.Global.buildQuery(current);

            Propeller.Global.changeAjaxPage(current, $(document).attr('title'), pageUrl);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: current,
                loading: Propeller.product_container,
                success: function (data, msg, xhr) {
                    $(Propeller.product_container).html(data.content);

                    Propeller.Filters.redraw_filters(data.filters, null);

                    Propeller.Filters.enable_filters();

                    if (typeof data.categories != 'undefined')
                        Propeller.Filters.redraw_categories(data.categories);

                    Propeller.Frontend.init();
                    Propeller.CatalogListstyle.init(current.view);
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        clear_term_filter: function (term_obj) {
            event.preventDefault();

            var pageUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;

            var slug = $('form.filterForm:first').find('input[name="prop_value"]').val();

            var current = {};
            if (window.location.href.indexOf('?') > -1)
                current = Propeller.Global.parseQuery(window.location.search);

            delete current.term;
            delete current.ppage;
            delete current.active_filter;

            var path = '';
            var obj_id = null;

            if (slug == '') {
                if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.category) > -1)
                    path = PropellerHelper.slugs.category;
                else if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.search) > -1)
                    path = PropellerHelper.slugs.search;
                else if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.brand) > -1)
                    path = PropellerHelper.slugs.brand;
                else if (window.location.pathname.indexOf('/' + PropellerHelper.slugs.machines) > -1)
                    path = PropellerHelper.slugs.machines;

                var url_chunks = new RegExp(`\/(${path})\/(.*?)\/`).exec(window.location.pathname);
                if (url_chunks !== null) {
                    if (PropellerHelper.behavior.ids_in_url) {
                        id_chunks = url_chunks[2].split('/');

                        slug = id_chunks[1];
                        obj_id = id_chunks[0];
                    }
                    else
                        slug = url_chunks[2];
                }
            }

            if (!obj_id && PropellerHelper.behavior.ids_in_url && window.location.pathname.indexOf('/' + PropellerHelper.slugs.category) > -1) {
                path = PropellerHelper.slugs.category;

                var url_chunks = new RegExp(`\/(${path})\/(.*?)\/`).exec(window.location.pathname);

                if (url_chunks !== null) {
                    if (PropellerHelper.behavior.ids_in_url) {
                        id_chunks = url_chunks[2].split('/');

                        obj_id = id_chunks[0];
                    }
                }

                if (obj_id)
                    current.obid = obj_id;
            }

            current.action = $('form.filterForm:first').find('input[name="action"]').val();

            // Get prop_name and prop_value from filter form
            var propName = $('form.filterForm:first').find('input[name="prop_name"]').val();
            var propValue = $('form.filterForm:first').find('input[name="prop_value"]').val();

            if (propName && propValue) {
                current[propName] = propValue;
            }

            // ALWAYS ensure manufacturers is set for brand pages
            if (!current.manufacturers && window.location.pathname.indexOf('/' + PropellerHelper.slugs.brand) > -1) {
                var brandPath = PropellerHelper.slugs.brand;
                var brandRegex = new RegExp(`\/(${brandPath})\/(.*?)\/`);
                var brandChunks = brandRegex.exec(window.location.pathname);

                if (brandChunks !== null) {
                    var brandSlug = brandChunks[2];
                    current.action = 'do_brand';
                    current.manufacturers = brandSlug;
                }
            }

            current.view = $(Propeller.product_container).parent().data('liststyle');

            pageUrl += '?' + Propeller.Global.buildQuery(current);

            Propeller.Global.changeAjaxPage(current, $(document).attr('title'), pageUrl);

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: current,
                loading: Propeller.product_container,
                success: function (data, msg, xhr) {
                    $(Propeller.product_container).html(data.content);

                    Propeller.Filters.redraw_filters(data.filters, null);

                    Propeller.Filters.enable_filters();

                    if (typeof data.categories != 'undefined')
                        Propeller.Filters.redraw_categories(data.categories);

                    Propeller.Frontend.init();
                    Propeller.CatalogListstyle.init(current.view);
                },
                error: function () {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        active_filters_click: function (event) {
            if ($(this).data('type') == 'price') {
                Propeller.Filters.slider_running = true;
                var slug = $('input[name="price[from]"]').closest('form').find('input[name="prop_value"]').val();

                Propeller.Filters.apply_filter(
                    [
                        {
                            name: 'price[from]',
                            value: $('input[name="price[from]"]').val()
                        },
                        {
                            name: 'price[to]',
                            value: $('input[name="price[to]"]').val()
                        },
                    ], slug, false, function () {
                        Propeller.Filters.slider_running = false;
                    });
            }
            else {
                var attr_name = $(this).data('filter');
                var attr_value = $(this).data('value');

                if ($(this).hasClass('btn-active-filter-term')) {
                    Propeller.Filters.clear_term_filter(this);
                }
                else {
                    $('input[name="' + attr_name + '[]"][value="' + attr_value + '"]').prop('checked', false).change();
                }
            }
        },
        redraw_categories: function (categories) {
            if (!$('#propeller-catalog-filters').find('.catalog-menu').length)
                $('#propeller-catalog-filters').prepend(categories);
        },
        redraw_filters: function (filters, active_filter) {
            if (!$('#propeller-catalog-filters').find('.filter-container').length) {
                $('#propeller-catalog-filters').append(filters);
                Propeller.Filters.init(true);

                return;
            }

            $('#filtered_results').html($('#catalog_total').html());

            var newFilterContainer = $(filters);
            var oldFiltersContainer = $('.filter-container');

            var newFilters = $(filters).find('.filter');
            var oldFilters = $(oldFiltersContainer).find('.filter');

            for (var i = 0; i < newFilters.length; i++) {
                if (typeof $(newFilters[i]).attr('id') == 'undefined')
                    continue;

                if ($(newFilters[i]).attr('id') == 'price_filter_container' && active_filter == 'price_from') {
                    continue;
                }

                if ($(newFilters[i]).attr('id') != 'price_filter_container') { // don't update the price filter
                    if ($(newFilters[i]).attr('id') == active_filter && $(oldFiltersContainer).find('#' + active_filter).length > 0)
                        $(oldFiltersContainer).find('#' + active_filter).replaceWith(newFilters[i]);
                    else if ($(oldFiltersContainer).find('#' + $(newFilters[i]).attr('id')).length > 0)
                        $(oldFiltersContainer).find('#' + $(newFilters[i]).attr('id')).replaceWith(newFilters[i]);
                    else if ($(oldFiltersContainer).find('#' + $(newFilters[i]).attr('id')).length == 0) {
                        if (i > 0) {
                            if (typeof $(newFilters[i - 1]).attr('id') == 'undefined')
                                $(oldFiltersContainer).append(newFilters[i]);
                            else
                                $(oldFiltersContainer).find('#' + $(newFilters[i - 1]).attr('id')).after(newFilters[i]);
                        }
                        else
                            $(oldFiltersContainer).prepend(newFilters[i]);
                    }
                }
            }

            for (var i = 0; i < oldFilters.length; i++) {
                if (typeof $(oldFilters[i]).attr('id') == 'undefined')
                    continue;

                if ($(oldFilters[i]).attr('id') == active_filter)
                    continue;

                if ($(newFilterContainer).find('#' + $(oldFilters[i]).attr('id')).length == 0)
                    $(oldFilters[i]).remove();
            }

            var currentFilters = $('.filter-container').find('.filter').not('#price_filter_container');
            if (currentFilters.length) {
                for (var i = 0; i < currentFilters.length; i++) {
                    if ($(currentFilters[i]).find('.form-check').length == 0)
                        $(currentFilters[i]).remove();
                }
            }

            Propeller.Filters.init(true);

        },
        pdp_behavior: function (event) {
            $(this).is(':checked')
                ? $('.propeller-product-list').find('.propeller-product-card a').attr('target', '_blank')
                : $('.propeller-product-list').find('.propeller-product-card a').removeAttr('target');

            Propeller.Cookie.set('propeller_pdp_behavior', $(this).is(':checked') ? "true" : "false", 30);
        },
        expand_filters: function () {
            $('.filter-container div.filter').each(function (i, container) {
                var current = {};
                if (window.location.href.indexOf('?') > -1)
                    current = Propeller.Global.parseQuery(window.location.search);

                if ($(container).attr('id') == 'price_filter_container') {
                    if ((typeof current.price_from != 'undefined' || typeof current.price_to != 'undefined') && !$(container).find('div.numeric-filter').hasClass('show'))
                        $(container).find('button.btn-filter').click();
                } else {
                    if ($(container).find('form .form-check-input:checked').length > 0 && $(container).find('button.btn-filter').attr('aria-expanded') == 'false')
                        $(container).find('button.btn-filter').click();
                }
            });
        }
    };
    //Propeller.Filters.init();

}(window.jQuery, window, document));