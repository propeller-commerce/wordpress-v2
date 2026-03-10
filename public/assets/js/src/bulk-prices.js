(function ($, window, document) {

    Propeller.BulkPrices = {
        init: function () {
            $(".bulk-prices .row:first-child").css("font-weight",'bold');
            $(".product-quantity-input").keyup(function(){
                var value = this.value;

                $(".bulk-prices .row").css("font-weight",'').filter(function(){
                    var parts = $(this).text().split("-");
                    if(parts[1] === undefined) {
                        parts[1] = '';
                    }
                    parts[1] == "" ? parts[1] = Number.MAX_SAFE_INTEGER : "";
                    return (parseInt(parts[0]) <= value && value <= parseInt(parts[1]))
                }).css("font-weight","bold");
            });

        }
    };

    //Propeller.BulkPrices.init();

}(window.jQuery, window, document));