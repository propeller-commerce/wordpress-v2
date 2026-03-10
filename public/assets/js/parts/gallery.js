(function ($, window, document) {
    var PhotoSwipeGallerySlick = {
        init: function () {
            var items = [];
            $('.gallery-item-slick' , '#slick-gallery').each( function() {
                var $figure = $(this),
                    $a = $figure.find('a'),
                    $src = $a.attr('href'),
                    $title = $figure.find('figcaption').html(),
                    $msrc = $figure.find('img').attr('src');
                if ($a.data('size')) {
                    var $size   = $a.data('size').split('x');
                    var item = {
                        src   : $src,
                        w   : $size[0],
                        h     : $size[1],
                        title   : $title,
                        msrc  : $msrc
                    };
                } else {
                    var item = {
                        src: $src,
                        w: 800,
                        h: 800,
                        title: $title,
                        msrc: $msrc
                    };
                    var img = new Image();
                    img.src = $src;

                    var wait = setInterval(function() {
                        var w = img.naturalWidth,
                        h = img.naturalHeight;
                        if (w && h) {
                            clearInterval(wait);
                            item.w = w;
                            item.h = h;
                        }
                    }, 30);
                }
                var index = items.length;
                items.push(item);
                $figure.unbind('click').click(function(event) {
                    event.preventDefault(); // prevent the normal behaviour i.e. load the <a> hyperlink
                    // Get the PSWP element and initialise it with the desired options
                    var $pswp = $('#pswp')[0];
                    console.log($pswp);
                    var options = {
                        index: index,
                        bgOpacity: 0.8,
                        showHideOpacity: true
                    }
                    new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options).init();
                });
            });
        
		},
    };
    
    var ProductSlickGallery = {
		init: function () {
            $('#slick-gallery').not('.slick-initialized').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                adaptiveHeight: false,
                fade: true,
                asNavFor: '#product-thumb-slick',
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {                            
                            dots: true                        
                        }
                    }
                ]
            });
            $('#product-thumb-slick').not('.slick-initialized').slick({
                slidesToShow: 3,
                slidesToScroll: 3,
                vertical:true,
                dots: false,
                autoplay:false,
                arrows: true,
                asNavFor: '#slick-gallery',
                focusOnSelect: true,
                centerMode: true,
                centerPadding: '20px',
                verticalSwiping:true,
                infinite: false,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            vertical: false,
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            arrows: true,
                            focusOnSelect: true,
                            centerMode: false
                        }
                    }
                ]
            });
        },
    };
    
    $(function() {       
        PhotoSwipeGallerySlick.init();
        ProductSlickGallery.init();    
    });

})(jQuery);	
