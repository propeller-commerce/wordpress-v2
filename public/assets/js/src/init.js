(function ($) {

    if (!window.hasOwnProperty('Propeller')) {
        return;
    }

    $(document).trigger('propeller_frontend_init');

    for (const key in window.Propeller) {
        if (typeof window.Propeller[key].init != 'undefined')
            window.Propeller[key].init();

        if (typeof window.Propeller[key].on_load != 'undefined')
            window.Propeller[key].on_load();
    }

    if(PropellerHelper && PropellerHelper.hasOwnProperty('debug')) {
        console.log('Propeller Frontend Initialized ✅');
    }

})(jQuery);