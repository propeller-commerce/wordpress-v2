/**
 * Propeller Plugin JS Library
*/
'use strict';

window.Propeller || (window.Propeller = {});

(function($, window, document) {

    Propeller.$window = $(window);
	Propeller.$body = $(document.body);
	
	// Detect Internet Explorer
	Propeller.isIE = navigator.userAgent.indexOf("Trident") >= 0;
	// Detect Edge
	Propeller.isEdge = navigator.userAgent.indexOf("Edge") >= 0;
	// Detect Mobile
	Propeller.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    Propeller.get_active_tab = function() {
        var urlParams = new URLSearchParams(window.location.search);
        var params = Object.fromEntries(urlParams);

        return typeof params.tab != 'undefined' ? params.tab : 'general';
    };
    
    // Helper functions and extensions
    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    var Ajax = {
        init: function () {

        },
        call: function(args) {
            var overlay = null;
            var opts = {};

            opts.url = args.url;
            opts.type = args.method || 'GET';
            opts.data = args.data || {};
            opts.dataType = args.dataType || 'json';
            opts.success = args.success || null;
            opts.error = args.error || null;
            opts.complete = function() {
                // hide the loader and remove it's instance
                if (typeof overlay != 'undefined' && overlay) {
                    overlay.hide();
                    overlay = null;
                }
            }

            opts.data.nonce = propeller_admin_ajax.nonce;
            
            var loading = args.loading || null;
            if (loading)
                overlay = PlainOverlay.show($(loading)[0], {
                    blur: 2,
                    style: {
                        fillColor: '#888'
                        // background: 'transparent', 
                        // face: place loader here
                    }
                });
            
            var ajax = $.ajax(opts);

            return ajax;
        }
    };

    Propeller.Ajax = Ajax;



    var Admin = {
        account_pages_checked: false,
        accordion: null,
		init: function () {
            $('#exclusions').off('change').on('change', this.handle_exclusions);
            $('#closed_portal').off('change').on('change', this.display_exclusions);
            $('#use_recaptcha').off('change').on('change', this.display_recaptcha);
            $('#add_page_btn').off('click').on('click', this.add_new_row);
            $('.delete-btn').off('click').on('click', this.delete_row);
            $('#confirmDeletePage').off('click').on('click', this.confirm_delete_page);
            $('#use_sso').off('change').on('change', this.display_sso);
            $('#use_ga4').off('change').on('change', this.display_ga4);
            $('#sso_provider').off('change').on('change', this.change_sso_provider);
            $('#use_cxml').off('change').on('change', this.display_cxml);

            $('#propel_settings_form').off('submit').on('submit', this.submit_form);
            $('#propel_pages_form').off('submit').on('submit', this.submit_form);
            $('#propel_behavior_form').off('submit').on('submit', this.submit_form);
            $('#propeller_cache_form').off('submit').on('submit', this.submit_form);
            $('#propeller_rw_rules_form').off('submit').on('submit', this.submit_form);
            $('#propel_valuesets_form').off('submit').on('submit', this.submit_form);
            
            // Built with https://github.com/michu2k/Accordion
            try {
                this.accordion = new Accordion('.accordion-container');
            } catch (ex) {}
            

            $('.propel-add-lng-btn').off('click').on('click', this.add_slug_row);

            $('#generate_sitemap').off('click').on('click', this.generate_sitemap);

            this.check_slug_buttons();
		},
        change_sso_provider: function(event) {  
            event.preventDefault();

            var provider = $(this).val();

            Propeller.Ajax.call({
                url: propeller_admin_ajax.ajaxurl,
                method: 'POST',
                data: {
                    action: 'propel_get_sso_provider_config',
                    provider: provider,
                    nonce: propeller_admin_ajax.nonce
                },
                loading: $('#sso_config'),
                success: function(data, msg, xhr) {
                    if (data.success)
                        $('#sso_config').html(data.config);
                    else 
                        Propeller.Alert.show(data.message, data.success);
                },
                error: function() {
                    // Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        add_slug_row: function(event) {
            event.preventDefault();

            var template = $('#slug_row_template').html();
            var page_id = $(this).data('page_id');
            var index = $(this).closest('.propel-page-row').data('index');

            var slug_id = Propeller.Admin.get_next_slug_index(index);
            
            template = template.replaceAll('{slug-id}', slug_id);
            template = template.replaceAll('{page-id}', page_id);
            template = template.replaceAll('{index}', index);

            $('.page-slugs-container-' + index).append(template);

            if ($(this).closest('.page-slug-row').find('.page-slugs-languages option').length == $('.page-slugs-container-' + index).find('.page-slug-row').length)
                $('.page-slugs-container-' + index).find('.propel-add-lng-btn').remove();
            else
                $(this).remove();

            $('.propel-add-lng-btn').off('click').on('click', Propeller.Admin.add_slug_row);

            return false;
        },
        get_next_slug_index: function(index) {
            var last_slug_id = parseInt($('.page-slugs-container-' + index).find('.page-slug-row:last-child').data('id'));
            
            return last_slug_id + 1;
        },
        check_slug_buttons: function() {
            $('.page-slug-containers').each(function(i, obj) {
                console.log($(obj).data('index'));
                
                var langs_length = $(obj).find('.page-slugs-languages:first option').length;
                var slugs = $(obj).find('.page-slug-row').length;

                if (langs_length == slugs)
                    $(obj).find('.propel-add-lng-btn').remove();
                else 
                    $(obj).find('.propel-add-lng-btn:not(:last-child)').remove();
            });
        },
        submit_form: function(event) {
            event.preventDefault();

            if (window.tagInstances) {
                for (var id in window.tagInstances) {
                    var element = document.getElementById(id);
                    var instance = window.tagInstances[id];
                    if (element && instance && typeof instance.getValue === 'function') {
                       
                        var currentValue = instance.getValue();
                        
                        if (currentValue) {
                            var tags = currentValue.split(',').filter(function(tag) {
                                return tag.trim() !== '0';
                            });
                            currentValue = tags.join(',');
                        }
                        
                        element.value = currentValue;
                    }
                }
            }

            Propeller.Ajax.call({
                url: propeller_admin_ajax.ajaxurl,
                method: 'POST',
                data: $(this).serializeObject(),
                loading: $(this),
                success: function(data, msg, xhr) {
                    Propeller.Alert.show(data.message, data.success);
                },
                error: function() {
                    // Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        handle_exclusions: function(event) {
            event.preventDefault();

            $('#excluded_pages').val($(this).val().join(','));
        },
        display_exclusions: function(event) {
            if ($(this).is(':checked'))
                $('#exclusions_container').show();
            else 
                $('#exclusions_container').hide();
        },
        display_recaptcha: function(event) {
            if ($(this).is(':checked'))
                $('#recaptcha_settings').show();
            else 
                $('#recaptcha_settings').hide();
        },
        display_sso: function(event) {
            if ($(this).is(':checked'))
                $('#sso_container').show();
            else 
                $('#sso_container').hide();
        },
        display_ga4: function(event) {
            if ($(this).is(':checked'))
                $('#ga4_container').show();
            else 
                $('#ga4_container').hide();
        },
        display_cxml: function(event) {
            if ($(this).is(':checked'))
                $('#cxml_container').show();
            else 
                $('#cxml_container').hide();
        },
        add_new_row: function(event) {
            event.preventDefault();

            var lastIndex = $('.propel-pages-container').find('.propel-page-row').length;
            
            var template = $('#page_row_template').html();
            template = template.replaceAll('{index}', lastIndex);

            $('.propel-pages-container > .accordion-container').append(template);

            if (Propeller.Admin.accordion) {
                Propeller.Admin.accordion.update();
                Propeller.Admin.accordion.open(lastIndex);
            }
                
            $('.propel-add-lng-btn').off('click').on('click', Propeller.Admin.add_slug_row);
            $('.delete-btn').off('click').on('click', Propeller.Admin.delete_row);

            return false;
        },
        delete_row: function(event) {
            event.preventDefault();
            
            var id = parseInt($(this).attr('data-id'));
            var pageName = $(this).attr('data-name') || 'this page';
            
            // Update modal content
            $('#deletePageMessage').text('Are you sure you want to delete "' + pageName + '"? This action cannot be undone.');
            
            // Store the button reference and ID for later use
            $('#deletePageModal').data('delete-btn', $(this));
            $('#deletePageModal').data('page-id', id);
            
            // Show the modal
            $('#deletePageModal').modal('show');
        },
        confirm_delete_page: function(event) {
            event.preventDefault();
            
            var modal = $('#deletePageModal');
            var deleteBtn = modal.data('delete-btn');
            var id = modal.data('page-id');
            
            // Remove the page row
            deleteBtn.closest('.propel-page-acc-row').remove();
            
            // Add to delete list if it's an existing page
            if (id > 0) {
                var delPagesArr = $('#delete_pages').val().split(',').filter(function(item) {
                    return item !== '';
                });
                delPagesArr.push(id);
                $('#delete_pages').val(delPagesArr.join(','));
                
                // Submit the form to actually delete the page
                $('#propel_pages_form').submit();
            }
            
            // Hide the modal
            modal.modal('hide');
        },
        generate_sitemap: function(event) {
            event.preventDefault();

            Propeller.Ajax.call({
                url: propeller_admin_ajax.ajaxurl,
                method: 'POST',
                timeout: 0,
                data: {
                    action: 'propel_generate_sitemap',
                    nonce: propeller_admin_ajax.nonce
                },
                loading: $(this),
                success: function(data, msg, xhr) {
                    Propeller.Alert.show(data.message, data.success);

                    if (data.reload)
                        window.location.href = `?page=propeller-sitemap`;
                },
                error: function() {
                    // Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        }
    };

    Propeller.Admin = Admin;


    var Translations = {
        init: function() {
            $('#scroll_top').off('click').on('click', this.scroll_to_top);

            $('#scan_translations').off('click').on('click', this.scan_translations);

            $('#propel_translations_form').off('submit').on('submit', this.submit_form);
            $('#create_translations_form').off('submit').on('submit', this.create_translations_file);
            $('#generate_translations_btn').off('click').on('click', this.generate_translations);
            $('#restore_translations_form').off('submit').on('submit', this.restore_translations);
        },
        create_translations_file: function(e) {
            e.preventDefault();
            
            Propeller.Ajax.call({
                url: propeller_admin_ajax.ajaxurl,
                method: 'POST',
                data: $(this).serializeObject(),
                loading: $(this),
                success: function(data, msg, xhr) {
                    if (data.success)
                        window.location.href = `?page=propeller-translations&${data.action}=true&file=${data.file}`;

                    Propeller.Translations.load_backups();
                },
                error: function() {
                    // Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        submit_form: function(e) {
            e.preventDefault();
            
            Propeller.Ajax.call({
                url: propeller_admin_ajax.ajaxurl,
                method: 'POST',
                data: $(this).serializeObject(),
                loading: $(this),
                success: function(data, msg, xhr) {
                    Propeller.Alert.show(data.message, data.success);
                    Propeller.Translations.load_backups();
                },
                error: function() {
                    // Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        scan_translations: function(e) {
            e.preventDefault();
            
            Propeller.Ajax.call({
                url: propeller_admin_ajax.ajaxurl,
                method: 'POST',
                data: {
                    action: 'scan_translations',
                    nonce: propeller_admin_ajax.nonce
                },
                loading: $(this),
                success: function(data, msg, xhr) {
                    Propeller.Alert.show(data.message, data.success);
                    Propeller.Translations.load_backups();
                },
                error: function() {
                    // Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        generate_translations: function(e) {
            e.preventDefault();

            var translations_form_data = $('#propel_translations_form').serializeObject();
            delete translations_form_data.action;

            // Get data from button attributes instead of form
            var button_data = {
                page: 'propeller-translations',
                action: 'generate_translations',
                po_file: $(this).data('po-file'),
                generate_translations: true,
                nonce: propeller_admin_ajax.nonce
            };

            var data = {};
            Object.assign(data, button_data, translations_form_data);
            
            Propeller.Ajax.call({
                url: propeller_admin_ajax.ajaxurl,
                method: 'POST',
                data: data,
                loading: $(this),
                success: function(data, msg, xhr) {
                    Propeller.Alert.show(data.message, data.success);
                    Propeller.Translations.load_backups();
                },
                error: function() {
                    // Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        restore_translations: function(event) {
            event.preventDefault();

            var form_data = $(this).serializeObject();
            form_data.action = 'restore_translations';
            form_data.nonce = propeller_admin_ajax.nonce;

            Propeller.Ajax.call({
                url: propeller_admin_ajax.ajaxurl,
                method: 'POST',
                data: form_data,
                loading: $(this),
                success: function(data, msg, xhr) {
                    if (data.success)
                        window.location.reload();
                    else 
                        Propeller.Alert.show(data.message, data.success);
                },
                error: function() {
                    // Propeller.Toast.show('Propeller, __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });

            return false;
        },
        load_backups: function() {
            Propeller.Ajax.call({
                url: propeller_admin_ajax.ajaxurl,
                method: 'POST',
                data: {
                    action: 'load_translations_backups',
                    nonce: propeller_admin_ajax.nonce
                },
                loading: $('#backup_date'),
                success: function(data, msg, xhr) {
                    if (data.success)
                        $('#backup_date').html(data.options);
                    else 
                        Propeller.Alert.show(data.message, data.success);
                },
                error: function() {
                    // Propeller.Toast.show('Propeller', __('just now', 'propeller-ecommerce-v2'), arguments[0].responseText, 'error', null, 3000);
                    console.log('error', arguments);
                }
            });
        },
        scroll_to_top: function(e) {
            $("html, body").animate({ scrollTop: 0 }, "fast");

            return false;
        }
    };

    Propeller.Translations = Translations;

    var Alert = {
        alert: '.propel-alert',
        init: function() {},
        show: function(message, success) {
            $(this.alert).removeClass('alert-success');
            $(this.alert).removeClass('alert-danger');

            $(this.alert).addClass(success ? 'alert-success' : 'alert-danger');

            $(this.alert).find('.propel-alert-body').html(message);

            $(this.alert).fadeTo(2000, 500).slideUp(500, function() {
                $(this.alert).slideUp(500);
            });
            // $(this.alert).alert();
        }
    };

    Propeller.Alert = Alert;

    $(function() {
        for (const key in Propeller) {
            if (typeof Propeller[key].init != 'undefined')
                Propeller[key].init();
        }   

        // Initialize use-bootstrap-tag
        if (typeof UseBootstrapTag !== 'undefined') {
            window.tagInstances = {};
            document.querySelectorAll('[data-use-bootstrap-tag]').forEach(function(element) {
                var instance = UseBootstrapTag(element);
                window.tagInstances[element.id] = instance;
            });
        }

        $('#propel_tabs a[href="#' + Propeller.get_active_tab() + '"]').tab('show');
        
        setTimeout(function() {
            if (typeof $.fn.tooltip !== 'undefined') {
                // Destroy any existing tooltips first
                $('.help-tooltip').tooltip('dispose');
                
                // Initialize tooltips
                $('.help-tooltip').tooltip({
                    container: 'body',
                    html: true,
                    placement: 'top',
                    trigger: 'hover focus',
                    delay: { show: 300, hide: 100 },
                    boundary: 'viewport'
                });
                
            } else {
                console.warn('Bootstrap tooltip is not available');
            }
        }, 100);

        // API Key show/hide functionality
        $('.toggle-password').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var target = button.data('target');
            var input = $(target);
            var eyeIcon = button.find('.eye-icon');
            var originalValue = input.data('original-value');
            var isVisible = button.data('is-visible') === true;
            
            if (!isVisible) {
                // Show the API key by replacing the input entirely
                if (originalValue && originalValue.length > 0) {
                    // Store all attributes and properties
                    var inputClasses = input.attr('class');
                    var inputId = input.attr('id');
                    var inputName = input.attr('name');
                    var inputPlaceholder = input.attr('placeholder');
                    var inputRequired = input.prop('required');
                    
                    // Create new text input with the actual value
                    var newInput = $('<input>')
                        .attr('type', 'text')
                        .attr('class', inputClasses)
                        .attr('id', inputId)
                        .attr('name', inputName)
                        .attr('placeholder', inputPlaceholder)
                        .prop('required', inputRequired)
                        .val(originalValue)
                        .data('original-value', originalValue);
                    
                    // Replace the input
                    input.replaceWith(newInput);
                    
                    button.data('is-visible', true);
                    // Change to eye-off icon (visible/crossed out)
                    eyeIcon.html('<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>');
                } else {
                    console.warn('No API key value available in data-original-value');
                }
            } else {
                // Hide the API key by replacing with password input
                var currentInput = $(target);
                var currentVisibleValue = currentInput.val();
                
                // Update the stored value if user modified it
                if (currentVisibleValue && currentVisibleValue.length > 0) {
                    originalValue = currentVisibleValue;
                }
                
                // Store all attributes and properties
                var inputClasses = currentInput.attr('class');
                var inputId = currentInput.attr('id');
                var inputName = currentInput.attr('name');
                var inputPlaceholder = currentInput.attr('placeholder');
                var inputRequired = currentInput.prop('required');
                
                // Create new password input with masked value
                var newInput = $('<input>')
                    .attr('type', 'password')
                    .attr('class', inputClasses)
                    .attr('id', inputId)
                    .attr('name', inputName)
                    .attr('placeholder', inputPlaceholder)
                    .prop('required', inputRequired)
                    .val('•'.repeat(32))
                    .data('original-value', originalValue);
                
                // Replace the input
                currentInput.replaceWith(newInput);
                
                button.data('is-visible', false);
                // Change back to eye icon (hidden/visible)
                eyeIcon.html('<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>');
            }
        });
        
        // Handle direct editing of password fields (when user types without showing)
        $(document).on('input', 'input[type="password"][data-original-value]', function() {
            var input = $(this);
            var currentValue = input.val();
            var maskedValue = '•'.repeat(32);
            
            // If user is typing something different than the masked bullets, update the stored value
            if (currentValue !== maskedValue) {
                input.data('original-value', currentValue);
            }
        });

        // Copy to clipboard functionality
        $('.copy-to-clipboard').on('click', function() {
            var target = $(this).data('target');
            var input = $(target);
            var button = $(this);
            var originalHtml = button.html();
            var originalValue = input.data('original-value');
            
            // Create a temporary input to copy the original value
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(originalValue).select();
            
            try {
                document.execCommand('copy');
                tempInput.remove();
                
                // Show success feedback
                button.html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>');
                button.addClass('btn-success');
                
                // Reset after 2 seconds
                setTimeout(function() {
                    button.html(originalHtml);
                    button.removeClass('btn-success');
                }, 2000);
                
            } catch (err) {
                tempInput.remove();
                console.error('Failed to copy text: ', err);
                
                // Show error feedback
                button.html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>');
                button.addClass('btn-danger');
                
                // Reset after 2 seconds
                setTimeout(function() {
                    button.html(originalHtml);
                    button.removeClass('btn-danger');
                }, 2000);
            }
        });

        // Handle form submission to ensure original values are sent
        // Use capture phase to run before other handlers
        if (document.getElementById('propel_settings_form')) {
            document.getElementById('propel_settings_form').addEventListener('submit', function(e) {
                // Ensure we submit the actual API keys, not the masked values
                $('#api_key, #order_api_key').each(function() {
                    var input = $(this);
                    var currentValue = input.val();
                    var originalValue = input.data('original-value');
                    var maskedValue = '•'.repeat(32);
                    
                    // If the current value is the masked bullets, use the original value
                    // Otherwise, the user has typed something new, so use the current value
                    if (currentValue === maskedValue && originalValue && originalValue.length > 0) {
                        input.val(originalValue);
                    }
                    // If user cleared the field or typed new value, currentValue will be used as-is
                });
            }, true); // Use capture phase
        }
        
        // Handle Firebase API key submission in behavior form
        if (document.getElementById('propel_behavior_form')) {
            document.getElementById('propel_behavior_form').addEventListener('submit', function(e) {
                // Ensure we submit the actual Firebase API key, not the masked value
                $('#firebase_api_key').each(function() {
                    var input = $(this);
                    var currentValue = input.val();
                    var originalValue = input.data('original-value');
                    var maskedValue = '•'.repeat(32);
                    
                    // If the current value is the masked bullets, use the original value
                    // Otherwise, the user has typed something new, so use the current value
                    if (currentValue === maskedValue && originalValue && originalValue.length > 0) {
                        input.val(originalValue);
                    }
                    // If user cleared the field or typed new value, currentValue will be used as-is
                });
            }, true); // Use capture phase
        }        
    });		

}(window.jQuery, window, document));
