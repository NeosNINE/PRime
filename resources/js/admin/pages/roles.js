

    /**
     * Устанавливаем стандартные события для ролей
     */
    setDefaultObjectEvents('role', 'roles', '.roles-table', 'name');




    /**
     * Проставить / убрать все checkbox группы
     */
    eventClick('.role_accesses_block .access-name', function (elem){

        let accesses = $(elem).closest('.accesses-line').find('.access');

        //Если есть хотя бы один чекбокс отмеченный
        if( accesses.find('input[type="checkbox"]:checked').length > 0 ){

            accesses.find('input[type="checkbox"]').each(function (index, checkbox){

                $(checkbox).prop('checked', false).trigger('change');

            });

        }else{

            accesses.find('input[type="checkbox"]').each(function (index, checkbox){

                if( $(checkbox).attr('disabled') )
                    return;

                $(checkbox).prop('checked', true).trigger('change');

            });

        }

    });



    /**
     * Устанавливают / убирают чекбокс определенного доступа для роли
     */
    eventChange('.accesses-line input[type="checkbox"]', function (checkbox){

        let data_if_specified_elems = $('.role_accesses_block').find('[data-if-specified="' + checkbox.attr('value') + '"]');

        if( checkbox.prop('checked') ){

            data_if_specified_elems.each(function(index, elem){

                $(elem).attr('disabled', false);
                $(elem).find('input[type=checkbox]').attr('disabled', false);

            });

        }else{

            data_if_specified_elems.each(function(index, elem){

                $(elem).attr('disabled', true);
                $(elem).find('input[type=checkbox]').prop('checked', false).attr('disabled', true);

            });

        }

    });
