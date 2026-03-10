(function ($, window, document) {

    // [product-slider productIds="1562,1568,1563,1564" clusterIds="39,22,31"]
    Propeller.Slider = {
        init: function() {
            $('.propeller-slider').each(function(index, el) {
                Propeller.Slider.process_slider(el, $(el).data());
            });
            
        },
        process_slider(slider, slider_data) {
            var request_data = {
                action: 'load_slider_products'
            };

            for (var key in slider_data) {
                if (slider_data.hasOwnProperty(key) && key != 'slider_id') {
                    if (typeof slider_data[key] == 'string' && slider_data[key].includes(','))
                        slider_data[key] = slider_data[key].split(',');

                    request_data[key] = slider_data[key];
                }
            }

            Propeller.Ajax.call({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: request_data,
                loading: $(slider),
                success: function(data, msg, xhr) {
                    $(slider).html(data.content);

                    Propeller.Slider.init_slider(slider);

                    if(Propeller.hasOwnProperty('Cart')) {
                        Propeller.Cart.init();
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
        },
        init_slider: function(slider) {
            if ($(slider).hasClass('slick-initialized')) {
                $(slider).slick('unslick');
                $(slider).slick('destroy');
            }

            $(slider).not('.slick-initialized').slick({
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
                            slidesToScroll: 3
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
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            dots: true,
                            arrows: false
                        }
                    }
                ]
            });
            $(slider).find('form .btn-addtobasket').off('click').on('click', function(event) {
        
                event.preventDefault();
                const $form = this.closest('form');
                Propeller.Cart.cart_add_item.call($form, event);
            });
        },
        
    }

    //Propeller.Slider.init();

}(window.jQuery, window, document));