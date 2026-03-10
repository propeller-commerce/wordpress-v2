(function ($, window, document) {

    Propeller.Favorites =  {
        init: function() {
            // Use event delegation for dynamically loaded content
            $(document).off('submit', 'form.delete-favorite-item-form').on('submit', 'form.delete-favorite-item-form', this.delete_favorite);
            $(document).off('submit', 'form.delete-favorites-items-form').on('submit', 'form.delete-favorites-items-form', this.bulk_remove_favorites);
            $(document).off('submit', 'form.delete-favorite-list').on('submit', 'form.delete-favorite-list', this.delete_favorite_list);
            
            if ($('.fav-items-check-all').length) 
                this.init_bulk_actions();

            $('#renameListbtn').off('click').on('click', this.renameFavList);
            
            // Initialize autocomplete
            if ($('#searchfavProducts').length)
                this.init_favorite_autocomplte();

            $('#remove_favorites').off('show.bs.modal').on('show.bs.modal', this.before_bulk_delete);
            
            // Initialize pagination for favorites using event delegation
            if ($('.propeller-account-pagination[data-action="get_favorite_list_page"]').length)
                this.init_pagination();
        },
        init_pagination: function() {
            // Use event delegation for pagination links (they get replaced on each page change)
            $(document).off('click', '.propeller-account-pagination[data-action="get_favorite_list_page"] a.page-item').on('click', '.propeller-account-pagination[data-action="get_favorite_list_page"] a.page-item', Propeller.Favorites.do_paging);
        },
        do_paging: function(event) {
            event.preventDefault();
            event.stopPropagation();

            var $link = $(this);
            
            if ($link.attr('disabled') == 'disabled')
                return false;

            var $paging_container = $link.closest('.propeller-account-pagination');
            var list_id = $paging_container.data('listId');
            
            // Fallback: try reading the attribute directly
            if (!list_id) {
                list_id = $paging_container.attr('data-list-id');
            }
            
            var page = $link.data('page');
            var offset = 12; // Default offset

            if (!list_id) {
                console.error('Favorites pagination: List ID not found');
                return false;
            }

            var data = {
                action: 'get_favorite_list_page',
                list_id: list_id,
                ppage: page,
                offset: offset
            };

            // Scroll to top of favorites table
            Propeller.Global.scrollTo($('.propeller-account-table'));

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: data,
                loading: $('.propeller-account-list'),
                success: function(response, msg, xhr) {
                    // Force remove blur filter from all possible elements
                    setTimeout(function() {
                        $('.propeller-account-list').css('filter', '');
                        $('.propeller-favorites-table').css('filter', '');
                        $('#favorites_' + list_id).css('filter', '');
                    }, 100);
                    
                    if (response.success) {
                        // Dispose and remove existing delete favorite modals to prevent duplicates
                        $('[id^="delete_favorite_"]').each(function() {
                            var modalInstance = bootstrap.Modal.getInstance(this);
                            if (modalInstance) {
                                modalInstance.dispose();
                            }
                            $(this).remove();
                        });
                        
                        // Update products content (includes new modals)
                        $('#favorites_' + list_id).html(response.content);
                        
                        // Update pagination
                        if (response.pagination) {
                            $paging_container.replaceWith(response.pagination);
                        } else {
                            $paging_container.hide();
                        }
                        
                        // Update product count
                        $('.favorite-products-count').html(response.total_items);
                        $('.favorite-products-count').data('total', response.total_items);
                        
                        // Reinitialize
                        Propeller.Cart.init();
                        Propeller.Favorites.init();
                    } else {
                        console.error('Pagination error:', response.message);
                    }
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        init_bulk_actions: function() {
            // Use event delegation for dynamically loaded checkboxes and buttons
            $(document).off('click', '.btn-remove-selected-favorites').on('click', '.btn-remove-selected-favorites', this.before_bulk_delete);
            $(document).off('click', '.add-to-cart-selected-favorites').on('click', '.add-to-cart-selected-favorites', this.bulk_add_to_cart_favorites);

            $(document).off('change', '.fav-items-check-all').on('change', '.fav-items-check-all', this.check_all_changed);
            $(document).off('change', '.favorite-item-check').on('change', '.favorite-item-check', this.item_check_changed);
        },
        check_all_changed: function(event) {
            $('.favorite-item-check').prop('checked', $(this).is(':checked'));

            $('.favorites-selected').html($('.favorite-item-check:checked').length);
            $('.favorites-total').html($('.favorite-item-check').length);

            if ($('.favorite-item-check:checked').length > 0 && !$('.favorites-bottom-panel-container').is(':visible')) 
                $('.favorites-bottom-panel-container').slideToggle();
            else if ($('.favorite-item-check:checked').length == 0 && $('.favorites-bottom-panel-container').is(':visible')) 
                $('.favorites-bottom-panel-container').slideToggle();
        },
        item_check_changed: function(event) {
            $('.fav-items-check-all').prop('checked', $('.favorite-item-check').length == $('.favorite-item-check:checked').length);

            $('.favorites-selected').html($('.favorite-item-check:checked').length);
            $('.favorites-total').html($('.favorite-item-check').length);

            if ($('.favorite-item-check:checked').length > 0 && !$('.favorites-bottom-panel-container').is(':visible')) 
                $('.favorites-bottom-panel-container').slideToggle();
            else if ($('.favorite-item-check:checked').length == 0 && $('.favorites-bottom-panel-container').is(':visible')) 
                $('.favorites-bottom-panel-container').slideToggle();
        },
        before_bulk_delete: function(event) {
            if (!$('input.favorite-item-check:checked').length)
                return;

            $('.total-favorites-remove').html($('input.favorite-item-check:checked').length);
            $('#delete_favorite_items_form').find('input.bulk-remove-favs').remove();

            $('input.favorite-item-check:checked').each(function(i, fav){
                var product_chunks = $(fav).val().split('-');

                $('<input>').attr({
                    type: 'hidden',
                    name: product_chunks[1].toLowerCase() + '_id[]',
                    value: product_chunks[0],
                    class: 'bulk-remove-favs'
                }).appendTo('#delete_favorite_items_form');
            });
        },
        bulk_remove_favorites: function(event) {
            event.preventDefault();

            if (!$('input.favorite-item-check:checked').length)
                return;
            
            var data = $(this).serializeObject();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: data,
                loading: $(this),
                success: function(response_data, msg, xhr) {
                    Propeller.Favorites.delete_favorite_callback(response_data, data);
                    $('#remove_favorites').modal('hide');
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        bulk_add_to_cart_favorites: function() {
            if (!$('input.favorite-item-check:checked').length)
                return;

            Propeller.Toast.show('Propeller', '', PropellerHelper.translations.fav_lists_bulk_cluster_msg, 'success');
            
            var data = {};

            data.action = $(this).data('action');
            data.list_id = $(this).data('list_id');

            var has_products = false;
            
            $('input.favorite-item-check:checked').each(function(i, fav){
                var product_chunks = $(fav).val().split('-');

                if (product_chunks[1] == 'PRODUCT' && parseInt($(fav).data('orderable')) == 1 && parseInt($(fav).data('price_on_request')) == 0) {
                    data['product_id[' + i + ']'] = product_chunks[0];
                    data['quantity[' + i + ']'] = $('#quantity-item-' + product_chunks[0]).val();

                    has_products = true;
                }
            });
            
            if (has_products) {
                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: data,
                    loading: $(this),
                    success: function(response_data, msg, xhr) {
                        Propeller.Cart.cart_postprocess(response_data);
                    },
                    error: function() {
                        console.log('error', arguments);
                    }
                });
            }            
        },
        postprocess: function(data) {
            if (data.reload)
                window.location.reload();
            if (data.change_list_name) {
                $('.favorite-list-name').html(data.list_name);
                $('#renameList').modal('hide');
            }
        },
        create_fav_list: function() {
            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'create_fav_list',
                    listName: $('#listNameval').val()
                },
                // loading: el,
                success: function(data, msg, xhr) {
                    if (typeof data.id != 'undefined')
                        window.location.reload();
                },
                error: function() {
                    console.log('error', arguments);
                }
            });
        },
        delete_favorite_list: function(event) {
            event.preventDefault();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: $(this).serializeObject(),
                loading: $(this).find('button[type="submit"]'),
                success: function(data, msg, xhr) {
                    if (data.success)
                        window.location.href = data.redirect_url;
                    else
                        Propeller.Toast.show('Propeller', '', data.message, data.success ? 'success' : 'error');
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        renameFavList: function(){
            let list_id = $("#searchfavProducts").attr('listId');
            let list_name = $("#listNameval").val();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'update_fav_list',
                    favListId: list_id,
                    listName: list_name
                },
                loading: $('#favorites_' + list_id),
                success: function(data, msg, xhr) {
                    if (data.id){
                        $('.favorite-list-name').html(list_name);
                    } else {
                        console.log(data);
                    }
                },
                error: function() {
                    console.log('error', arguments);
                }
            });
        },
        delete_favorite: function(event){
            event.preventDefault();
            
            var form_data = $(this).serializeObject();

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: form_data,
                loading: $(this),
                success: function(data, msg, xhr) {
                    var prop_name = typeof form_data['product_id[]'] != 'undefined' 
                        ? 'product_id[]'
                        : 'cluster_id[]';

                    $('#delete_favorite_' + form_data[prop_name]).modal('hide');
                    Propeller.Favorites.delete_favorite_callback(data, form_data);
                },
                error: function() {
                    console.log('error', arguments);
                }
            });

            return false;
        },
        delete_favorite_callback: function(data, form_data) {
            if (data.success) {
                var deleteCount = 0;
                
                if (typeof form_data['product_id[]'] != 'undefined' ) {
                    var prop_name = 'product_id[]';

                    // Count items to delete
                    if (typeof form_data[prop_name] == 'string') {
                        deleteCount = 1;
                    } else {
                        deleteCount = form_data[prop_name].length;
                    }

                    if (typeof form_data[prop_name] == 'string') {
                        $('#fav_product_' + form_data[prop_name]).slideUp(500, function(){
                            $(this).remove();
                            Propeller.Favorites.item_check_changed(null);
                        });
                    }
                    else {
                        for (var i = 0; i < form_data[prop_name].length; i++) {
                            $('#fav_product_' + form_data[prop_name][i]).slideUp(500, function(){
                                $(this).remove();
                                Propeller.Favorites.item_check_changed(null);
                            });
                        }
                    }    
                }
                
                if (typeof form_data['cluster_id[]'] != 'undefined' ) {
                    var prop_name = 'cluster_id[]';

                   
                    if (typeof form_data[prop_name] == 'string') {
                        deleteCount += 1;
                    } else {
                        deleteCount += form_data[prop_name].length;
                    }

                    if (typeof form_data[prop_name] == 'string') {
                        $('#fav_product_' + form_data[prop_name]).slideUp(500, function(){
                            $(this).remove();
                            Propeller.Favorites.item_check_changed(null);
                        });
                    }
                    else {
                        for (var i = 0; i < form_data[prop_name].length; i++) {
                            $('#fav_product_' + form_data[prop_name][i]).slideUp(500, function(){
                                $(this).remove();
                                Propeller.Favorites.item_check_changed(null);
                            });
                        }
                    }    
                }
                

                if (deleteCount > 0) {
                    setTimeout(function() {
                        var currentTotal = parseInt($('.favorite-products-count').data('total')) || parseInt($('.favorite-products-count').html()) || 0;
                        var newTotal = Math.max(0, currentTotal - deleteCount);
                        $('.favorite-products-count').data('total', newTotal);
                        $('.favorite-products-count').html(newTotal);
                        
                    
                        Propeller.Favorites.update_pagination_after_delete(newTotal);
                    }, 100);
                }
            }
        },
        update_pagination_after_delete: function(newTotal) {
            var $pagination = $('.propeller-account-pagination[data-action="get_favorite_list_page"]');
            if (!$pagination.length) return;
            
            var itemsPerPage = 12;
            var currentPage = parseInt($pagination.data('current')) || 1;
            var newTotalPages = newTotal > 0 ? Math.ceil(newTotal / itemsPerPage) : 1;
            var visibleItems = $('.favorite-item-check').length;
            
          
            if (visibleItems === 0 && currentPage > 1) {
                var targetPage = Math.min(currentPage - 1, newTotalPages);
                var $prevLink = $pagination.find('.previous.page-item');
                if ($prevLink.length && !$prevLink.hasClass('disabled')) {
                    $prevLink.attr('data-page', targetPage).click();
                }
                return;
            }
            
          
            if (currentPage > newTotalPages && visibleItems > 0) {
                var list_id = $pagination.data('list-id') || $pagination.attr('data-list-id');
                
                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'get_favorite_list_page',
                        list_id: list_id,
                        ppage: newTotalPages,
                        offset: itemsPerPage
                    },
                    loading: $('.propeller-account-list'),
                    success: function(data, msg, xhr) {
                        if (data.success) {
                          
                            $('#favorites_' + list_id).html(data.content);
                            
                           
                            if (data.pagination) {
                                $pagination.replaceWith(data.pagination);
                            } else {
                                $pagination.remove();
                            }
                            
                         
                            Propeller.Cart.init();
                            Propeller.Favorites.init();
                        }
                    },
                    error: function() {
                        console.log('error', arguments);
                    }
                });
                return;
            }
            
         
            if (newTotalPages <= 1) {
                $pagination.remove();
            } else {
               
                $pagination.attr('data-max', newTotalPages);
                $pagination.attr('data-current', currentPage);
                
                var pageText = (PropellerHelper.translations && PropellerHelper.translations.page) ? PropellerHelper.translations.page : 'page';
                var fromText = (PropellerHelper.translations && PropellerHelper.translations.from) ? PropellerHelper.translations.from : 'from';
                $pagination.find('.page-totals').html(
                    pageText + ' ' + currentPage + ' ' + fromText + ' ' + newTotalPages
                );
                
                var $prevBtn = $pagination.find('.previous.page-item');
                if (currentPage <= 1) {
                    $prevBtn.addClass('disabled').attr('disabled', 'disabled');
                } else {
                    $prevBtn.removeClass('disabled').removeAttr('disabled').attr('data-page', currentPage - 1);
                }
                
                var $nextBtn = $pagination.find('.next.page-item');
                if (currentPage >= newTotalPages) {
                    $nextBtn.addClass('disabled').attr('disabled', 'disabled');
                } else {
                    $nextBtn.removeClass('disabled').removeAttr('disabled').attr('data-page', currentPage + 1);
                }
            }
        },
        init_favorite_autocomplte: function() {
           
            if (window.searchAutoComplete) {
                try {
                    if (typeof window.searchAutoComplete.unInit === 'function') {
                        window.searchAutoComplete.unInit();
                    }
                } catch(e) {
                    // Ignore errors
                }
              
                $('#optionsWrap').html('');
            }
            
            let maxResults = 6;
            window.searchAutoComplete = new window.autoComplete({
                selector: '#searchfavProducts',
                wrapper: '#optionsWrap',
                threshold: 3,
                debounce: 300,
                cache: false,
                searchEngine: function(query, record) {
                    return 1;
                },
                data: {
                    keys: ['name'],
                    src: async (query) => {
                        let data = [];
                        await Propeller.Ajax.call({
                            url: PropellerHelper.ajax_url,
                            method: 'POST',
                            data: {
                                action: 'global_search',
                                term: query,
                                offset: Propeller.Global.maxSuggestions
                            },
                            dataType: 'json',
                        }).then(function (response) {
                            if (response.hasOwnProperty('items')) {
                                for (let i in response.items) {
                                    if (response.items[i].class == 'PRODUCT') {
                                        data.push({
                                            name: response.items[i].name[0].value,
                                            slug: response.items[i].slug[0].value,
                                            image: response.items[i].image,
                                            url: response.items[i].url,
                                            productId: response.items[i].productId,
                                            gross: response.items[i].price ? response.items[i].price.gross : '',
                                            quantity: response.items[i].price ? response.items[i].price.quantity : 0,
                                            sku: response.items[i].sku
                                        });
                                    }
                                    else {
                                        data.push({
                                            name: response.items[i].name[0].value,
                                            slug: response.items[i].slug[0].value,
                                            image: response.items[i].image,
                                            url: response.items[i].url,
                                            clusterId: response.items[i].clusterId,
                                            gross: '',
                                            quantity: '',
                                            sku: response.items[i].sku
                                        });
                                    }
                                }
                            }
                        }).catch(function (args) {
                            console.log('error', args)
                        });
                        return data;
                    },
                },
                resultsList: {
                    tag: "ul",
                    class: "propeller-autosuggest-items",
                    maxResults: maxResults+1,
                    element: (list, data) => {
                        let searchUrl = PropellerHelper.urls.search + data.query
                        let li = document.createElement('li');
                        li.className = 'propeller-autosuggest-item';
                        list.appendChild(li);
                    },
                },
                resultItem: {
                    tag: "li",
                    class: "propeller-autosuggest-item",
                    element: (item, data) => {
                        while (item.firstChild) {
                            item.removeChild(item.firstChild);
                        }
                        if(item.value.image !== 'all') {
                            let imgWrap = document.createElement('div');
                            let image = document.createElement('img');
                            imgWrap.className = 'autoComplete_item-img';
                            image.src = data.value.image;
                            image.width = image.height = 100;
                            imgWrap.appendChild(image);
                            item.appendChild(imgWrap);
                        }
                        let txtWrap = document.createElement('div');
                        txtWrap.className = 'autoComplete_item-name';
                        if (data.value.quantity >= 1){
                            stock = PropellerHelper.translations.in_stock;
                            stockClass = 'greenstock';
                        } else {
                            stock = PropellerHelper.translations.out_of_stock;
                            stockClass = 'redstock';
                        }
                        txtWrap.innerHTML = data.value.name+'<p class="skup">SKU: '+data.value.sku+'</p><p class="'+stockClass+'">'+stock+'</p>';
                        item.appendChild(txtWrap);
                        let priceWrap = document.createElement('div');
                        priceWrap.className = 'autoComplete_item-price';
                        priceWrap.innerHTML = data.value.gross != '' ? PropellerHelper.currency + ' ' + data.value.gross.toFixed(2) : '';
                        item.appendChild(priceWrap);
                    },
                    highlight: "autoComplete_highlight",
                    selected: "autoComplete_selected"
                },
                events: {
                    input: {
                        selection: (event) => {
                            if(!event.detail.selection.hasOwnProperty('value'))
                                return;

                            $('#addToList').modal('hide');

                            var list_id = $("#searchfavProducts").data('list_id');
                            
                            var countBeforeAdd = parseInt($('.favorite-products-count').data('total')) || parseInt($('.favorite-products-count').html()) || 0;

                            Propeller.Global.scrollTo('.propeller-favorites-table');

                            var request_data = {
                                action: 'add_favorite',
                                list_id: list_id,
                                update_list: $("#searchfavProducts").data('update_list'),
                                class: ''
                            };

                            if (typeof event.detail.selection.value.productId != 'undefined') {
                                request_data.product_id = [event.detail.selection.value.productId];
                                request_data.class = 'product';
                            } else if (typeof event.detail.selection.value.clusterId != 'undefined') {
                                request_data.cluster_id = [event.detail.selection.value.clusterId];
                                request_data.class = 'cluster';
                            }

                            Propeller.Ajax.call({
                                url: PropellerHelper.ajax_url,
                                method: 'POST',
                                data: request_data,
                                loading: $('.propeller-account-list'),
                                success: function(data, msg, xhr) {
                                    
                                    setTimeout(function() {
                                        $('.propeller-account-list').css('filter', '');
                                        $('.propeller-favorites-table').css('filter', '');
                                        $('#favorites_' + list_id).css('filter', '');
                                    }, 100);
                                    
                                    if (data.success){
                                        var isDuplicate = false;
                                        
                                        if (typeof data.content !== 'undefined') {
                                            $('[id^="delete_favorite_"]').each(function() {
                                                var modalInstance = bootstrap.Modal.getInstance(this);
                                                if (modalInstance) {
                                                    modalInstance.dispose();
                                                }
                                                $(this).remove();
                                            });
                                            
                                            $('#favorites_' + list_id).html(data.content);
                                            
                                            if (typeof data.pagination !== 'undefined') {
                                                var $existingPagination = $('.propeller-account-pagination[data-action="get_favorite_list_page"]');
                                                if (data.pagination) {
                                                    if ($existingPagination.length) {
                                                        $existingPagination.replaceWith(data.pagination);
                                                    } else {
                                                        $('#favorites_' + list_id).after(data.pagination);
                                                    }
                                                } else {
                                                    $existingPagination.remove();
                                                }
                                            }
                                            
                                            var countAfterAdd = 0;
                                            if (typeof data.total_count !== 'undefined') {
                                                countAfterAdd = data.total_count;
                                                $('.favorite-products-count').html(data.total_count);
                                                $('.favorite-products-count').data('total', data.total_count);
                                                
                                                if (countAfterAdd === countBeforeAdd) {
                                                    isDuplicate = true;
                                                }
                                            } else {
                                                $('.favorite-products-count').html($('.favorite-item-check').length);
                                            }
                                            
                                            Propeller.Cart.init();
                                            Propeller.Favorites.init();
                                        }
                                        
                                        $("#searchfavProducts").val('');     
                                    }
                                    
                                    if (data.success && isDuplicate) {
                                        Propeller.Toast.show('Propeller', '', PropellerHelper.translations.item_already_in_favorites, 'info');
                                    } else {
                                        Propeller.Toast.show('Propeller', '', data.message, data.success ? 'success' : 'error');
                                    }
                                },
                                error: function() {
                                    console.log('error', arguments);
                                }
                            });
                        }
                    }
                }
            });
        },
    };

    $( "#newfavlistcreate" ).off('click').on( "click", function() {
        $("#newfavlistcreate").addClass('button--loading');
        $("#newfavlistcreate").prop('disabled', true);
        Propeller.User.create_fav_list();
    });
    
}(window.jQuery, window, document));