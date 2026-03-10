(function ($, window, document) {

    Propeller.SpareParts = {
        init: function() {
            $(document).off('keydown').on('keydown', '#term-machine', this.search_spare_parts); 
            $(document).off('click').on('click', '#spare-parts-btn-search', this.search_spare_parts); 
            $('.machines-back').off('click').on('click', function(event) {
                event.preventDefault();
                
                window.history.back();
                
                return false;
            });
        },
        search_spare_parts: function(event) {

            if(event.keyCode == 13 || event.type == 'click' ) {
                if($('#term-machine').val().length == 0) {
                    event.preventDefault();
                    return false;
                }
                var slug = $('#term-machine').closest('.machine-grid-container').data('prop_value');
                var $el = this;
                var $val = $('#term-machine').val();

                current = Propeller.Global.parseQuery(window.location.search);

                if (typeof current.term != 'undefined')
                    delete current.term;

                Propeller.Filters.apply_filter(
                [
                    {
                        name: 'term',
                        value: $('#term-machine').val()
                    }
                ], slug, true, function() {
                    Propeller.SpareParts.init();
                }, current);

                return false
            }
        }
    };

}(window.jQuery, window, document));