

    window.daterangepickerInit = function (){

        if( $('[data-datepicker-enabled]').length ) {

            let daterangepicker_locale = [];

            daterangepicker_locale['en'] = {
                "format": "MM/DD/YYYY",
                "separator": " - ",
                "applyLabel": "Apply",
                "cancelLabel": "Cancel",
                "fromLabel": "From",
                "toLabel": "To",
                "customRangeLabel": "Custom",
                "weekLabel": "W",
                "daysOfWeek": [
                    "Su",
                    "Mo",
                    "Tu",
                    "We",
                    "Th",
                    "Fr",
                    "Sa"
                ],
                "monthNames": [
                    "January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December"
                ],
                "firstDay": 1
            };

            daterangepicker_locale['ru'] = {
                format: 'DD.MM.YYYY',
                applyLabel: 'Принять',
                cancelLabel: 'Отмена',
                invalidDateLabel: 'Выберите дату',
                daysOfWeek: ['Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс', 'Пн'],
                monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                firstDay: 1
            };

            $('[data-datepicker-enabled]').each(function (i, elem) {

                let showDropdowns = false;

                if ($(elem).attr('data-datepicker-showDropdowns') === 'true')
                    showDropdowns = true;


                //Выборка интервала дат от и до
                if ($(elem).attr('data-datepicker-type') === 'range') {

                    $(elem).daterangepicker({
                        "locale": daterangepicker_locale['ru'],
                        showDropdowns: showDropdowns,
                        "autoApply": true
                    });

                    //По умолчанию выбор идет только даты
                } else {

                    $(elem).daterangepicker({
                        "locale": daterangepicker_locale['ru'],
                        singleDatePicker: true,
                        showDropdowns: showDropdowns,
                        "autoApply": true
                    });

                }

            });

        }

    };
