(function ($, window, document) {

    Propeller.Ajax = {
        overlay: null, 
        lazyload: null,
        init: function () {
            if (!this.lazyload && PropellerHelper.behavior.lazyload_images) {
                var lazy_options = {};

                // lazy_options.data_src = 'data_src';
                // lazy_options.use_native = true;

                lazy_options.treshold = 100;
                this.lazyload = new LazyLoad(lazy_options);
            }
        },
        hide_loader: function(xhr) {
            if (typeof xhr.loader != 'undefined' && xhr.loader) {
                xhr.loader.hide();
                xhr.loader = null;
            }
        },
        call: function(args) {
            var opts = {};
            
            opts.url = args.url;
            opts.type = args.method || 'GET';
            opts.data = args.data || {};
            opts.dataType = args.dataType || 'json';
            opts.success = args.success || null;
            opts.error = args.error || null;
            opts.complete = function(xhr, success) {
                // hide the loader and remove it's instance
                if (typeof opts.loader != 'undefined' && opts.loader) {
                    opts.loader.hide();
                    opts.loader = null;
                }
                
                // Additional cleanup: ensure any remaining overlays are removed
                if (loading) {
                    var $loadingElement = $(loading);
                    if ($loadingElement.data('plainOverlay')) {
                        $loadingElement.data('plainOverlay').hide();
                    }
                }

                if (typeof Propeller.Ajax.lazyload != 'undefined' && Propeller.Ajax.lazyload) {
                    Propeller.Ajax.lazyload.update();
                }
            }

            opts.data.nonce = window.newnonce || '';
            opts.data.lang = PropellerHelper.language;

            var loading = args.loading || null;
            opts.loader = null;

            if (loading && !opts.loader) {
                // First, ensure any existing overlays on this element are removed
                var $loadingElement = $(loading);
                if ($loadingElement.data('plainOverlay')) {
                    $loadingElement.data('plainOverlay').hide();
                }
                
                opts.loader = PlainOverlay.show($loadingElement[0], {
                    blur: 2,
                    opacity: 0.6,
                    fillColor: '#888',
                    style: {
                        // backgroundColor: 'rgba(136, 136, 136, 0.6)',
                        backgroundColor: 'transparent',
                        cursor: 'wait',
                        zIndex: 9000,
                        // face: `${PropellerHelper.base_assets_url}/img/loading.gif`
                        // face: document.getElementById('purchase_auth_loading_img')
                    }
                });
            }

            var ajax = $.ajax(opts);


            ajax.loader = opts.loader;

            return ajax;
        }
    };

    // $(function() {
    //     if (typeof Propeller.Ajax.lazyload != 'undefined' && Propeller.Ajax.lazyload) {
    //         Propeller.Ajax.lazyload.update();
    //     }
    // });

}(window.jQuery, window, document));