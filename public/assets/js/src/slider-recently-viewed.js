(function ($, window, document) {

    Propeller.RecentlyViewedSlider = {
        init_slider: function () {
            if ($('#product-recently-viewed-slider').hasClass('slick-initialized')) {
                $('#product-recently-viewed-slider').slick('unslick');
                $('#product-recently-viewed-slider').slick('destroy');
            }
            $('#product-recently-viewed-slider').not('.slick-initialized').slick({
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
                            slidesToShow: 4,
                            slidesToScroll: 4
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            arrows: false,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            dots: true
                        }
                    }
                ]
            });
            $('#product-recently-viewed-slider').find('form .btn-addtobasket').off('click').on('click', function(event) {
                event.preventDefault();
                const $form = this.closest('form');
                Propeller.Cart.cart_add_item.call($form, event);
            });
        },
        init: function() {
            if (typeof window.slider_recent_products != 'undefined' && window.slider_recent_products.length) {
                var cluster_ids = [];
                var product_ids = [];

                for (var i = 0; i < window.slider_recent_products.length; i++) {
                    var tmp = window.slider_recent_products[i].split('-');

                    if (tmp[0] == 'CLUSTER')
                        cluster_ids.push(tmp[1]);
                    else 
                        product_ids.push(tmp[1]);
                }

                Propeller.Ajax.call({
                    url: PropellerHelper.ajax_url,
                    method: 'POST',
                    data: {
                        product_ids: product_ids,
                        cluster_ids: cluster_ids,
                        action: 'get_recently_viewed_products'
                    },
                    loading: $('#product-recently-viewed-slider'),
                    success: function(data, msg, xhr) {
                        $('#product-recently-viewed-slider').html(data.content);

                        Propeller.RecentlyViewedSlider.init_slider();

                        if (Propeller.hasOwnProperty('Cart')) {
                            Propeller.Cart.init();
                        }

                        if (Propeller.hasOwnProperty('ProductPlusMinusButtons')) {
                            Propeller.ProductPlusMinusButtons.init();
                        }
                        
                        if (Propeller.hasOwnProperty('Product')) {
                            $('form.add-favorite').each(function(i, el) {
                                var modal_id = $(el).closest('.modal').attr('id');
                                
                                $(el).closest('.modal').detach().appendTo("body");                                

                                $(`#${modal_id}`).on('shown.bs.modal', function (event) {
                                    $(el).off('submit').on('submit', function(event) {
                                        Propeller.Product.add_favorite(event, el);
                                    });
                                })
                            });
                        }
                    },
                    error: function() {
                        console.log('error', arguments);
                    }
                });
            }
        }
    };

    //Propeller.RecentlyViewedSlider.on_load();

}(window.jQuery, window, document));