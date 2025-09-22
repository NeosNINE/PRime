

    /**
     * Отправка форм (AJAX подгрузка контента)
     */
    submitForm('.ajax-submit', function (response, this_form) {

        if( this_form.attr('data-event') )
            eventDispatch(this_form.attr('data-event'), response);


        if( this_form.attr('data-redirect') ){

            if( this_form.attr('data-redirect') === 'back' ){

                redirectBack();

            }else if( this_form.attr('data-redirect') === 'back_with_update' ){

                redirectBack(true);

            }else{

                loadPage(this_form.attr('data-redirect'));

            }

        }


        if( this_form.attr('data-success-message') )
            swalSuccess(this_form.attr('data-success-message'));

    });


    /**
     * Если data-event и data-id явно не указаны - пытаемся автоматически проставить из URL
     */
    function setEventAndIdAttributes ( elem, event_name ){

        if( !$(elem).attr('data-event') && !$(elem).attr('data-id') ){

            let delete_url = new URL( $(elem).attr('data-action' ) );
            let delete_url_split = delete_url.pathname.split('/');

            let data_event, data_id;

            if( delete_url_split.length === 5 && parseInt(delete_url_split[4]) > 0 ){

                data_event = pluralize.singular(delete_url_split[2]) + '.' + event_name;
                data_id = parseInt(delete_url_split[4]);

            }else if( delete_url_split.length === 6 && parseInt(delete_url_split[5]) > 0 ){

                data_event = pluralize.singular(delete_url_split[3]) + '.' + event_name;
                data_id = parseInt(delete_url_split[5]);

            }

            data_event = data_event.replaceAll('-', '_');

            $(elem).attr('data-event', data_event);
            $(elem).attr('data-id', data_id);

        }

    }



    /**
     * Удаление объекта
     */
    eventClick('[data-delete-object]', function (elem) {

        if( isElemLoading(elem) )
            return false;


        //Если это мягкое удаление
        if( $(elem)[0].hasAttribute('data-soft-deletes') && !$(elem).attr('data-confirm-text') ){

            requestDelete(elem);

        }else{

            swalConfirm({
                title: $(elem).attr('data-confirm-text') ?? 'Вы уверены?',
                confirmButtonText: $(elem).attr('data-confirm-btn-text') ?? 'Удалить',
                cancelButtonText: $(elem).attr('data-cancel-btn-text') ?? 'Отмена',
                confirmButtonColor: '#d33'
            }, function(){

                requestDelete(elem);

            });

        }


    });


    /**
     * AJAX действия
     */
    eventClick('[data-ajax-action]', function (elem) {

        if( isElemLoading(elem) )
            return false;

        // Если есть подтверждение
        if( $(elem).attr('data-confirm-text') ){

            swalConfirm({
                title: $(elem).attr('data-confirm-text'),
                confirmButtonText: $(elem).attr('data-confirm-btn-text') ?? 'Да',
                cancelButtonText: $(elem).attr('data-cancel-btn-text') ?? 'Отмена',
                confirmButtonColor: '#3085d6'
            }, function(){

                requestAjaxAction(elem);

            });

        }else{

            requestAjaxAction(elem);

        }

    });


    /**
     * Выполнить AJAX действие
     */
    function requestAjaxAction( elem ){

        if( isElemLoading(elem) )
            return false;

        elemStartLoading(elem);

        setEventAndIdAttributes(elem, 'action');

        // Подготовка данных
        let data = {};

        // Добавляем дополнительные данные если указаны
        if( $(elem).attr('data-extra-data') && $(elem).attr('data-extra-data-source') ){
            let extraDataKey = $(elem).attr('data-extra-data');
            let extraDataSource = $(elem).attr('data-extra-data-source');
            let extraDataValue = $(extraDataSource).val();
            data[extraDataKey] = extraDataValue;
        }

        post(
            $(elem).attr('data-action'),
            data,
            function (response) {

                elemEndLoading(elem);

                if( response.success === false ){
                    return swalError(response.message || 'Что-то пошло не так.');
                }

                if( $(elem).attr('data-event') )
                    eventDispatch( $(elem).attr('data-event'), response.data || { id : $(elem).attr('data-id') } );

                if( $(elem).attr('data-success-text') )
                    swalSuccess($(elem).attr('data-success-text'));

                if( $(elem).attr('data-redirect') ){
                    loadPage($(elem).attr('data-redirect'));
                }

            },
            function (error) {
                elemEndLoading(elem);
                swalError('Ошибка при выполнении действия.');
            }
        );

    }



    /**
     * Выполнить AJAX запрос на удаление
     */
    function requestDelete( elem ){

        if( isElemLoading(elem) )
            return false;

        elemStartLoading(elem);

        setEventAndIdAttributes(elem, 'delete');

        request(
            'delete',
            $(elem).attr('data-action'),
            {},
            function (response) {

                elemEndLoading(elem);

                if( response !== '1' )
                    return swalError('Что-то пошло не так.');


                if( $(elem).attr('data-event') )
                    eventDispatch( $(elem).attr('data-event'), { id : $(elem).attr('data-id') } );


                if( $(elem).attr('data-redirect') ){

                    loadPage($(elem).attr('data-redirect'));

                }else{

                    //Если событие не указано и есть строка родительская - блокируем ее (подразумеваем, что строка - это информация об объекте, который удален)
                    //Если событие указано (в том числе автоматически, выше) - то тогда запускается событие и все. Обработка должна происходить уже в событии
                    if( $(elem).closest('tr').length && !$(elem).attr('data-event') )
                        disableTrRow($(elem).closest('tr'));

                }

                let success_msg = $(elem).attr('data-success-text') ?? 'Операция успешно выполнена.';

                if( $(elem)[0].hasAttribute('data-soft-deletes') ){

                    let action_restore = $(elem).attr('data-action');
                    action_restore = action_restore.replace('/delete/', '/restore/');

                    if( $(elem).next('[data-restore-object]').length ){

                        action_restore = $(elem).next('[data-restore-object]:first').attr('data-action');

                    }else if( $(elem).prev('[data-restore-object]').length ){

                        action_restore = $(elem).next('[data-restore-object]:first').attr('data-action');

                    }

                    toastr('<table class="w-100">' +
                        '<tr>' +
                            '<td>' + success_msg + '</td>' +
                            '<td>' +
                                '<a href="#" class="btn btn-primary btn-sm ml-10" data-restore-object data-skip-confirm data-action="' + action_restore + '">Отмена</a>' +
                            '</td>' +
                        '</tr>' +
                        '</table>', 'default', 10000);


                }else{

                    swalSuccess(success_msg);

                }

            },
            function (error,code) {

                elemEndLoading(elem);
                swalError( getErrorMessage( error, code ) );

            }
        );

    }



    /**
     * Восстановление удаленного объекта
     */
    eventClick('[data-restore-object]', function (elem) {

        if( isElemLoading(elem) )
            return false;


        //Если не нужно подтверждение
        if( $(elem)[0].hasAttribute('data-skip-confirm') ){

            requestRestore(elem);

        }else{

            swalConfirm({
                title: $(elem).attr('data-confirm-text') ?? 'Вы уверены?',
                confirmButtonText: 'Восстановить',
                cancelButtonText: 'Отмена',
                confirmButtonColor: '#0d6efd'
            }, function(){

                requestRestore(elem);

            });

        }


    });



    /**
     * Выполнить AJAX запрос на восстановление
     */
    function requestRestore( elem ){

        if( isElemLoading(elem) )
            return false;

        elemStartLoading(elem);

        setEventAndIdAttributes(elem, 'restore');

        request(
            'post',
            $(elem).attr('data-action'),
            {},
            function (response) {

                elemEndLoading(elem);

                if( $(elem).attr('data-event') )
                    eventDispatch( $(elem).attr('data-event'), { id : $(elem).attr('data-id') } );


                if( $(elem).attr('data-redirect') ){

                    loadPage($(elem).attr('data-redirect'));

                }else{

                    //Если событие не указано и есть строка родительская - разблокируем ее (подразумеваем, что строка - это информация об объекте, который восстанавливаем)
                    //Если событие указано (в том числе автоматически, выше) - то тогда запускается событие и все. Обработка должна происходить уже в событии
                    if( $(elem).closest('tr').length && !$(elem).attr('data-event') )
                        cancelDisableTrRow($(elem).closest('tr'));

                }


                //Кнопка Отменить в toast
                if( $(elem).closest('.toast-body').length ){

                    $(elem).closest('.toast').remove();

                }else{

                    toastr($(elem).attr('data-success-text') ?? 'Операция успешно выполнена.', 'success');

                }

                //Дополнительно удаляем все toast об удалении объекта
                $('[data-restore-object][data-action="' + $(elem).attr('data-action') + '"]').each(function (i, elem){

                    if( $(elem).closest('.toast-body').length )
                        $(elem).closest('.toast').remove();

                });


            },
            function (error,code) {

                elemEndLoading(elem);
                swalError( getErrorMessage( error, code ) );

            }
        );

    }


    /**
     * Эта функция запускается при загрузке страницы и блокирует строки
     */
    window.loadPageSetDisabledTrRows = function(){

        $('tr.disabled').each(function (i, tr){

            disableTrRow($(tr));

        });

    }




    /**
     * Заблокировать строку в таблице
     */
    window.disableTrRow = function ( tr ){

        if( $(tr)[0].hasAttribute('data-offcanvas-href') )
            $(tr).attr('data-offcanvas-href-disabled', $(tr).attr('data-offcanvas-href'));

        if( $(tr)[0].hasAttribute('data-modal-href') )
            $(tr).attr('data-modal-href-disabled', $(tr).attr('data-modal-href'));

        if( $(tr)[0].hasAttribute('data-href') )
            $(tr).attr('data-href-disabled', $(tr).attr('data-href'));

        $(tr).addClass('disabled')
            .removeAttr('data-offcanvas-href')
            .removeAttr('data-modal-href')
            .removeAttr('data-href');

        $(tr).find('[data-restore-object]').show();

    }




    /**
     * Отменить блокировку строки в таблице
     */
    window.cancelDisableTrRow = function ( tr ){

        if( $(tr)[0].hasAttribute('data-offcanvas-href-disabled') )
            $(tr).attr('data-offcanvas-href', $(tr).attr('data-offcanvas-href-disabled'));

        if( $(tr)[0].hasAttribute('data-modal-href-disabled') )
            $(tr).attr('data-modal-href', $(tr).attr('data-modal-href-disabled'));

        if( $(tr)[0].hasAttribute('data-href-disabled') )
            $(tr).attr('data-href', $(tr).attr('data-href-disabled'));

        $(tr).removeClass('disabled')
            .removeAttr('data-offcanvas-href-disabled')
            .removeAttr('data-modal-href-disabled')
            .removeAttr('data-href-disabled');

        $(tr).find('[data-restore-object]').hide();

    }



    /**
     *  Добавить строку table tr для объекта
     */
    window.objectTableRowInsert = function ( table_list, object_id, html_table_row, where_insert = 'top' ){

        let type, tr_route, data;

        table_list.each(function (i, table){

            //Если объект уже добавлен в таблицу - пропускаем (нужно две проверки, здесь и ниже в request)
            if( $(table).find('[data-id="' + object_id + '"]').length > 0 )
                return;

            type = getDataTableTrType(table);
            if( !type )
                return console.error('You must specify a "data-tr-type" for table attribute.');


            tr_route = $(table).attr('data-tr-route');
            if( !tr_route )
                tr_route = route(window.section_type + '.get_html_table_row');


            data = {
                'id': object_id,
                'type': type
            };

            if( $(table).attr('data-table-row-template') )
                data.template = $(table).attr('data-table-row-template');


            //Если передан HTML table row - используем его, иначе делаем request() на сервер
            if( typeof html_table_row === 'undefined' || !html_table_row ){

                request('GET', tr_route, data, function (response){
                    objectTableRowInsertHandler(table, table_list, object_id, response, where_insert);
                });

            }else{

                objectTableRowInsertHandler(table, table_list, object_id, html_table_row, where_insert);

            }

        });

    }

    /**
     *  Обработка HTML при добавлении
     */
    function objectTableRowInsertHandler( table, table_list, object_id, HTML, where_insert ){

        if( !HTML )
            return false;

        //Если таблица была без строк
        if( $(table_list).hasClass('table-no-rows-found') ){
            $(table_list).find('tbody tr').remove();
            $(table_list).removeClass('table-no-rows-found');
        }

        //Если объект уже добавлен в таблицу - пропускаем (нужно две проверки, здесь и выше)
        if( $(table).find('[data-id="' + object_id + '"]').length > 0 )
            return;

        if( where_insert === 'bottom' ){

            $(table).find('tbody').append(HTML);

        }else{

            $(table).find('tbody').prepend(HTML);

        }

        let added_tr = $(table).find('[data-id="' + object_id + '"]');
        added_tr.addClass('added_right_now');

        setTimeout(function () {
            added_tr.removeClass('added_right_now');
        }, 2500);


        //Обязательно запускаем pageInit
        doPageInitTimeout();

    }



    /**
     * Обновить строку table tr для объекта
     */
    window.objectTableRowUpdate = function ( tr_list, html_table_row ){

        let table, type, tr_route, data;

        tr_list.each(function (i, tr){

            table = $(tr).closest('table');


            type = getDataTableTrType(table);
            if( !type )
                return console.error('You must specify a "data-tr-type" for table attribute.');


            tr_route = $(table).attr('data-tr-route');
            if( !tr_route )
                tr_route = route(window.section_type + '.get_html_table_row');

            let object_id = $(tr).attr('data-id');

            data = {
                'id': object_id,
                'type': type
            };

            if( table.attr('data-table-row-template') )
                data.template = table.attr('data-table-row-template');

            //Если передан HTML table row - используем его, иначе делаем request() на сервер
            if( typeof html_table_row === 'undefined' || !html_table_row ){

                request('GET', tr_route, data, function (response){
                    objectTableRowUpdateHandler( table, tr, object_id, response );
                });

            }else{

                objectTableRowUpdateHandler( table, tr, object_id, html_table_row );

            }

        });

    }


    /**
     * Обработчик редактирования строки в таблице
     */
    function objectTableRowUpdateHandler( table, tr, object_id, HTML ){

        if( !HTML )
            return false;

        $(tr).before(HTML);
        $(tr).remove();

        let edited_tr = $(table).find('[data-id="' + object_id + '"]');
        edited_tr.addClass('edited_right_now');

        setTimeout(function () {
            edited_tr.removeClass('edited_right_now');
        }, 1500);


        //Обязательно запускаем pageInit
        doPageInitTimeout();

    }


    /**
     * Удаление
     */
    window.objectTableRowDelete = function ( tr_list ){

        tr_list.each(function (i, tr){

            disableTrRow(tr)

        });

    }


    /**
     * Восстановление
     */
    window.objectTableRowRestore = function ( tr_list ){

        tr_list.each(function (i, tr){

            cancelDisableTrRow(tr)

        });

    }


    /**
     * Получить data-tr-type таблицы
     */
    function getDataTableTrType( table ){

        let type = $(table).attr('data-tr-type');

        //Если тип явно не указан - пытаемся получить его из CSS класса таблицы
        if( !type ){

            $.each($(table).attr('class').split(/\s+/), function(index, css_class){
                if( _.endsWith(css_class, '-table') ){
                    type = pluralize.singular(css_class.substring(0, css_class.length - 6));
                }
            });

        }

        return type;

    }
