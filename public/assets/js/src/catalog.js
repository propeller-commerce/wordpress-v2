(function ($, window, document) {

    Propeller.CatalogListstyle = {
        init: function (list_style = 'blocks') {
            var $catalogProductList = $('.propeller-product-list');

            if (PropellerHelper.ga4 && PropellerHelper.ga4_tracking)
                $('.propeller-product-card-link').off('click').on('click', this.select_item_event);

            $('.liststyle-options').find('.btn-liststyle').off('click').on('click' , function(event) {
                event.preventDefault();
                event.stopPropagation();

                var $this = $(this);
                var $liststyle = $this.data('liststyle');

                $('.liststyle-options').find('.btn-liststyle').each(function(i, el) {
                    var $that = $(el);
                    $that.removeClass('active');
                    $catalogProductList.removeClass($that.data('liststyle'));
                });
                $this.addClass('active');

                // Set class on productlist
                $catalogProductList.addClass($liststyle);
                $('#propeller-catalog-filters').parent().data('liststyle', $liststyle);
                $(Propeller.product_container).parent().data('liststyle', $liststyle);

                // var pageUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                // var current = Propeller.Global.parseQuery(window.location.search);
                // current.view = $liststyle;
            
                // pageUrl += '?' + Propeller.Global.buildQuery(current);

                // Propeller.Global.changeAjaxPage(current, $(document).attr('title'), pageUrl);

                return false;
            });

            $('.liststyle-options').find('.btn-liststyle').removeClass('active');
            $('.liststyle-options').find('[data-liststyle="' + list_style + '"]').addClass('active');
        },
        select_item_event: function(event) {
            event.preventDefault();

            var btn = this;

            if (!PropellerHelper.ga4 || !PropellerHelper.ga4_tracking)
                return;

            var product_id = $(this).closest('div.propeller-product-card').data('sku'); 

            if (typeof window.ga4data != 'undefined' && typeof window.ga4data.ecommerce.items != 'undefined') {
                var product = window.ga4data.ecommerce.items.filter(function(product) {
                    return product.item_id == product_id;
                });

                if (product) {
                    Propeller.CatalogListstyle.select_item(product);

                    window.setTimeout(function() {
                        window.location.href = $(btn).attr('href');
                    }, 10);
                }                    
            }

            return false;
        },
        select_item: function(product) {
            if (!PropellerHelper.ga4 || !PropellerHelper.ga4_tracking)
                return;

            var dataLayer = window.dataLayer || [];

            var items = product;   

            dataLayer.push({ ecommerce: null });  // Clear the previous ecommerce object.
            dataLayer.push({
                event: "select_item",
                ecommerce: {
                    item_list_id: window.ga4data.ecommerce.item_list_id,
                    item_list_name: window.ga4data.ecommerce.item_list_name,
                    items: items
                }
            });
        }
    };

    //Propeller.CatalogListstyle.init();

}(window.jQuery, window, document));