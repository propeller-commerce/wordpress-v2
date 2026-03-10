(function ($, window, document) {

    const {__, _x, _n, _nx} = wp.i18n;

    Propeller.days = PropellerHelper.days;
    Propeller.months = PropellerHelper.months;

    Propeller.Calendar = {
        init: function () {
            $('.form-check input.custom-date').off('change').change(this.show_calendar);
            $('.form-check input.custom-date').off('click').click(this.show_calendar);
        },
        show_calendar: function (e) {
            if ($(this).is(':checked') && $(this).hasClass('custom-date')) {
                $(this).closest('form').find('.delivery.selected').removeClass('selected');

                $('.deliveries').find('label.error').remove();
                $('.deliveries').find('input').removeClass('error');
                $(this).closest('.delivery').addClass('selected').show().find('select,input').addClass('required').attr('required', true);

                var elem = this;
                var calendar = null;
                var today = new Date();
                var calendar_start = new Date();

                calendar_start.setDate(today.getDate() + 3);

                calendar = $('#calendar-wrapper').calendar({
                    weekDayLength: 1,
                    date: calendar_start,
                    disable: function (date) {
                        var dayOfWeek = date.getDay();
                        var isWeekend = (dayOfWeek === 6) || (dayOfWeek  === 0);

                        return date <= today || isWeekend;
                    },
                    onClickDate: function (date) {
                        $('#calendar-wrapper').updateCalendarOptions({
                            date: date
                        });

                        var selectedDate = new Date(date);
                        var month = ('0' + (selectedDate.getMonth() + 1)).slice(-2);
                        var day = ('0' + (selectedDate.getDate())).slice(-2)

                        var date_val = `${selectedDate.getFullYear()}-${month}-${day}T00:00:00Z`;

                        $(elem).closest('label.form-check-label').find('input[name="delivery_select"]').val(date_val);

                        $(elem).closest('label.form-check-label').find('svg').remove();
                        $(elem).closest('label.form-check-label').find('div.delivery-day').removeClass('d-none').html(Propeller.days[selectedDate.getDay()]);
                        $(elem).closest('label.form-check-label').find('div.delivery-date').removeClass('d-none').html(selectedDate.getDate() + ' ' + Propeller.months[selectedDate.getMonth()]);

                        $('#datePickerModal').modal('hide');
                    },
                    showYearDropdown: true,
                    startOnMonday: true,
                    enableMonthChange: true,
                    // whether to disable year view
                    enableYearView: true,
                    // shows a Today button on the bottom of the calendar
                    showTodayButton: false,
                    // highlights all other dates with the same week-day
                    highlightSelectedWeekday: false,
                    // highlights the selected week that contains the selected date
                    highlightSelectedWeek: false,

                    monthMap: {
                        1: Propeller.months[0],
                        2: Propeller.months[1],
                        3: Propeller.months[2],
                        4: Propeller.months[3],
                        5: Propeller.months[4],
                        6: Propeller.months[5],
                        7: Propeller.months[6],
                        8: Propeller.months[7],
                        9: Propeller.months[8],
                        10: Propeller.months[9],
                        11: Propeller.months[10],
                        12: Propeller.months[11],
                    }, 

                    dayMap: {
                        0: Propeller.days[0],
                        1: Propeller.days[1],
                        2: Propeller.days[2],
                        3: Propeller.days[3],
                        4: Propeller.days[4],
                        5: Propeller.days[5],
                        6: Propeller.days[6]
                    }
                });

                $('#datePickerModal').modal('show');
            }
        }
    };

    //Propeller.Calendar.init();

}(window.jQuery, window, document));