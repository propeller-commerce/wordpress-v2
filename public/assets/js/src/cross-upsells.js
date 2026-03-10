(function ($, window, document) {

    Propeller.Crossupsells = {
        destroy_sliders: function() {
            if ($('.crossupsells-slider').length) {
                $('.crossupsells-slider').each(function(index, slider) {
                    if ($(slider).hasClass('slick-initialized')) {
                        try {
                            $(slider).slick('destroy');
                        }
                        catch (ex) {
                            console.log(ex);
                        }
                    }
                });
            }
        },
        reinit_sliders: function(container) {
            try {
                $(container).slick('reInit');
            }
            catch (ex) {
                console.log(ex);
            }
        },
        load_crossupsells: function() {
            if ($('.crossupsells-slider').length) {
                $('.crossupsells-slider').each(function(index, slider) {
                    Propeller.Ajax.call({
                        url: PropellerHelper.ajax_url,
                        method: 'POST',
                        data: {
                            slug: $(slider).data('slug'),
                            crossupsell_type: $(slider).data('type'),
                            action: 'load_crossupsells',
                            class: $(slider).data('class')
                        },
                        loading: $(slider),
                        success: function(data, msg, xhr) {
                            $(slider).html(data.content);

                            if(Propeller.hasOwnProperty('Frontend')) {
                                Propeller.Frontend.init();
                            }
                            Propeller.ProductPlusMinusButtons.init();
                            Propeller.Crossupsells.cross_upsell_slider($(slider).attr('id'));
                        },
                        error: function() {
                            console.log('error', arguments);
                        }
                    });
                });
            }
        },
        cross_upsell_slider: function(container = '') {
            container = container == '' ? '.crossupsells-slider' : '#' + container;

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
    };

}(window.jQuery, window, document));