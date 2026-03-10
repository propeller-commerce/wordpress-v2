(function ($, window, document) {

    Propeller.Toast = {
        toast: $('#propel_toast'),
        init: function () {},
        setTitle: function(title) {
            this.toast.find('.propel-toast-title').html(title);
        },
        setSubtitle: function(title) {
            this.toast.find('.propel-toast-subtitle').html(title);
        },
        setBody: function(title) {
            this.toast.find('.propel-toast-body').html(title);
        },
        setToastClass: function(title) {
            this.toast.addClass(title);
        },
        show: function(title, subtitle, message, toastTypeClass, callback, delay = 500) {
            this.setTitle(title);
            this.setSubtitle(subtitle);
            this.setBody(message);
            this.setToastClass(toastTypeClass);

            if (delay != 500)
                this.toast.toast({ delay: delay });

            this.toast.on('hide.bs.toast', function () {
                if (typeof callback == 'function')
                    callback();
            });

            this.toast.toast('show');
        }
    };

}(window.jQuery, window, document));