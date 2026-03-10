(function ($, window, document) {

    Propeller.ReturnProductsForm = {
        init: function () {
            $('.return-quantity', '.return-form').off('change').on('change' , function(e) {
                if ($(this).val() > $(this).data('max'))
                    $(this).val($(this).data('max'));
                else if (parseInt($(this).val()) <= 0)
                    $(this).val(1);
            });

            $('.return-reason', '.return-form').off('change').on('change' , function(e) {
                var other = $(this).closest('form').find('#return_reason_other_' + $(this).data('id'));

                $('#return_reason_text_' + $(this).data('id'), '.return-form').val($(this).find('option:selected').text());

                if ($(this).find('option:selected').index() == $(this).find('option').length - 1) 
                    $(other).css('display','block');
                else
                    $(other).css('display','none');
            });

            $('.return-product', '.return-form').off('change').on('change' , function() {
                if ($(this).is(':checked'))
                    $('input[data-id=' + $(this).data('id') + '], select[data-id=' + $(this).data('id') + ']').not(this).removeAttr('disabled');
                else
                    $('input[data-id=' + $(this).data('id') + '], select[data-id=' + $(this).data('id') + ']').not(this).attr('disabled', 'disabled');
            });

        }, 

    };

    //Propeller.ReturnProductsForm.init();

}(window.jQuery, window, document));