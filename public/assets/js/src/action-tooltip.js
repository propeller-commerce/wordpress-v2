(function ($, window, document) {

    Propeller.ActionTooltip = {
        init: function () {
            $('.actioncode-tooltip').each(function() { new bootstrap.Tooltip(this, {container: '.actioncode-tooltip', html: true}); });
        },
    };

    //Propeller.ActionTooltip.init();

}(window.jQuery, window, document));