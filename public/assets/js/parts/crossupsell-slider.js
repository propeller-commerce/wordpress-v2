(function ($) {	
    var ProductCrossUpsellSlider = {
		init: function () {
            $('.propeller-slider').not('.slick-initialized').slick({
                dots: true,
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
                    breakpoint: 768,
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
		},
    };

    ProductCrossUpsellSlider.init();
})(jQuery);	