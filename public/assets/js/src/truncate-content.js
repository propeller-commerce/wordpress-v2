(function ($, window, document) {

    Propeller.TruncateContent = {
        init: function () {

            $('.product-truncate').each(function (e) {
                var $this = $(this);
                var $truncateContent = $this.find('.product-truncate-content');
                var $initialContentHeight = $truncateContent.outerHeight();
                var $truncateButton = $this.find('.product-truncate-button a');

                if ($truncateContent.hasClass('show-more')) {
                    $truncateContent.addClass('truncate-description');
                }
                var $truncatedContentHeight = $truncateContent.outerHeight();

                $truncateButton.unbind('click').bind('click', function (e) {
                    var $this = $(this);
                    e.preventDefault();
                    if ($truncateContent.hasClass('show-more')) {
                        $truncateContent.css('height', $initialContentHeight);
                    } else {
                        $truncateContent.css('height', $truncatedContentHeight);
                    }
                    $truncateButton.children().toggle();
                    $truncateContent.toggleClass('show-more show-less');
                });
            });
        },
    };

    //Propeller.TruncateContent.init();

}(window.jQuery, window, document));