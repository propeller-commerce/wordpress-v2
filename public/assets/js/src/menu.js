(function ($, window, document) {

    var Menu = {
        init: function() {
            var active = $('ul.main-propeller-category').find('a.active');
            var activeSpan = $(active).closest('.main-item').find('span')[0];
         	var activeSubSpan = $(active).closest('.main-subitem').find('span')[0];
            if (active.length > 0) {
                if (active.hasClass('has-children') && $(activeSpan).attr('aria-expanded') != 'true') {
                    $(activeSpan).attr('aria-expanded', 'true');
                    $(activeSpan).click();
                }
            	else if (active.hasClass('has-children') && $(activeSubSpan).attr('aria-expanded') != 'true') {
                    $(activeSubSpan).attr('aria-expanded', 'true');
                    $(activeSubSpan).click();
                }
               
                var immediate = $(active).closest('.main-propeller-category-subsubmenu');
                var immediate_parent = $(immediate).closest('.main-propeller-category-subsubmenu');
                var root_parent_button = $(active).closest('.main-item').find('span')[0];

                if ($(root_parent_button).attr('aria-expanded') == 'false')
                    $(root_parent_button).click();

                if (immediate_parent.length > 0 && !$(immediate_parent).hasClass('show'))
                    $(immediate_parent).addClass('show');

                if (immediate.length > 0 && !$(immediate).hasClass('show'))
                    $(immediate).addClass('show');
            }
        }
    };

    Propeller.Menu = Menu

}(window.jQuery, window, document));