(function ($, window, document) {

    Propeller.OffCanvasLayout = {
        init: function () {
            $('[data-bs-toggle="off-canvas-filters"]').off('click').click(function (e) {
                var $targetLayer = $('.propeller-catalog-filters');

                if ($targetLayer.length) {
                    $('.mobile-filter-wrapper').toggleClass('show');
                    $($targetLayer).toggleClass('open');
                }

                $(this).attr('aria-expanded', function(_, attr) { return !(attr == 'true') });
            });

            $('.propeller-catalog-filters').find('.close-filters').off('click').click(function (e) {
                var $offCanvasLayer = $('.filter-container');

                $('.mobile-filter-wrapper').toggleClass('show');
                $('.propeller-catalog-filters').toggleClass('open');

                $('[data-bs-target="#' + $offCanvasLayer.attr('id') + '"]').attr('aria-expanded', function(_, attr) { return !(attr == 'true') });
            });
            
            $('#filter-menu-show-selection').off('click').click(function(e) {
                var $offCanvasLayer = $('.filter-container');

                $('.mobile-filter-wrapper').toggleClass('show');
                $('.propeller-catalog-filters').toggleClass('open');

                $('[data-bs-target="#' + $offCanvasLayer.attr('id') + '"]').attr('aria-expanded', function(_, attr) { return !(attr == 'true') });
            });

            $('#filter-menu-clear-selection').off('click').click(function(e) {

                var $offCanvasLayer = $('.filter-container');

                $('.mobile-filter-wrapper').toggleClass('show');
                $('.propeller-catalog-filters').toggleClass('open');

                $('[data-bs-target="#' + $offCanvasLayer.attr('id') + '"]').attr('aria-expanded', function(_, attr) { return !(attr == 'true') });
                $('.btn-remove-active-filters').trigger('click');
            });
        }
    };

    //Propeller.OffCanvasLayout.init();

}(window.jQuery, window, document));