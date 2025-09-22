

    /**
     * Список событий
     */
    window.events_list = new Map();


    /**
     * Установить слушателя события.
     * Проверка идет по коду выполняемой функции.
     * То есть название событие и выполняемый код только один раз устанавливается как слушатель, чтобы избежать множественного запуска одного и того же кода.
     */
    window.setEventListener = function ( event_name, func ){

        let event_key = event_name + '_' + func.toString().hashCode();

        if( events_list.get(event_key) )
            return console.log( 'Event Already registered: ' + event_name + ' : ' + func.toString() );

        events_list.set( event_key, true );

        $(document).on( event_name, func );

    }


    /**
     * Список событий, которые были инициированные только что.
     * Это нужно для того, чтобы при загрузке событий с сервера не выполнились несколько раз оди и те же события.
     * Уникальность проверяется через id, которое в параметрах.
     * То есть если необходимо сделать так, чтобы при подтягивании событий с севера не выполнялось такое же событие, нужно
     * чтобы в параметрах указываться ID, как на сервере, так и на клиенте.
     * У сущностей (пользователи, роли и т.д.) по умолчанию есть id, поэтому одни и те же события повторно не вызываются.
     */
    window.dispatch_right_now_from_js_list = [];



    /**
     * Запустить событие trigger
     * @event_name - ключ события
     * @params - что передаем в событие, может быть объект, строка и др.
     * @dispatch_right_now_from_js - true если событие запущено прямо сейчас через JS (а не подтянута с сервера через AJAX)
     */
    window.eventDispatch = function ( event_name, params, dispatch_right_now_from_js = true ){

        if( !params )
            params = {};


        if( $.isArray(params) || typeof params === "object" ){

            params['dispatch_right_now_from_js'] = dispatch_right_now_from_js;

        }else{

            return console.error("В eventDispatch необходимо передавать объект или массив.");

        }


        //Если указан параметр id и это событие было вызвано прямо сейчас на клиенте - добавляем в список вызванных событий на клиенте
        if( params.id && dispatch_right_now_from_js )
            window.dispatch_right_now_from_js_list[event_name + '_' + params.id] = true;


        $(document).trigger( event_name, params );

    }


    /**
     * Проверить, можем ли мы запустить событие (проверка по ID параметров идет)
     * Так же удаляется значение из dispatch_right_now_from_js_list, которое проверялось
     */
    window.checkIfCanEventDispatch = function ( event_name, params ) {

        if( params && params.id && window.dispatch_right_now_from_js_list[event_name + '_' + params.id] ){

            delete window.dispatch_right_now_from_js_list[event_name + '_' + params.id];

            return false;

        }

        return true;

    }




    /**
     * Получить список слушателей
     */
    window.getEventListeners = function (){
        return events_list.keys();
    }



    /**
     * Установить стандартные события для объекта
     * @object_key_singular - ключ объекта в единственном числе. Пример: user
     * @object_key_plural - ключ объекта во множественном числе. Пример: users
     * @object_class_table - CSS класс таблицы. Пример: .users-table
     * @add_select_text_field - ключ поля объекта, который использовать как текст в option, в select при добавлении объекта. Пример: name.
     *                         Так же можно передавать функцию, где будет единственный аргумент объект: тогда можно сформировать комбинацию полей.
     *                         Пример: function (user){ return user.name + ' ' + user.surname; }
     */
    window.setDefaultObjectEvents = function ( object_key_singular, object_key_plural, object_class_table, add_select_text_field ){

        if( !add_select_text_field )
            add_select_text_field = 'id';


        /**
         * Событие добавления объекта
        */
        setEventListener( object_key_singular + '.add', function (event, object_data){

            if( typeof object_data.event_data !== 'undefined' )
                object_data = object_data.event_data;

            objectTableRowInsert( $(object_class_table), object_data.id, object_data.html_table_row );

            $('select[name="' + object_key_plural + '[]"], select[name="' + object_key_plural + '_id[]"], select[name="' + object_key_singular + '_id"], select[name="' + object_key_singular + '_id[]"]').each(function (index, select) {

                $(select).append($('<option>', {
                    value : object_data.id,
                    text : (typeof add_select_text_field === 'function') ? add_select_text_field(object_data) : object_data[add_select_text_field],
                    selected : ifDispatchRightNowFromJS(object_data)
                }));

                if( ifDispatchRightNowFromJS(object_data) )
                    $(select).closest('.input').removeClass('no-active');

            });

        });



        /**
         * Событие редактирования объекта
         */
        setEventListener( object_key_singular + '.edit', function (event, object_data){

            if( typeof object_data.event_data !== 'undefined' )
                object_data = object_data.event_data;

            objectTableRowUpdate( $(object_class_table + ' tr[data-id="'+ object_data.id +'"]'), object_data.html_table_row );

        });



        /**
         * Событие удаления объекта
         */
        setEventListener( object_key_singular + '.delete', function (event, object_data){

            if( typeof object_data.event_data !== 'undefined' )
                object_data = object_data.event_data;

            objectTableRowDelete( $(object_class_table + ' tr[data-id="'+ object_data.id +'"]') );

            //Если открыто модальное окно - возвращаемся назад (если событие вызвал текущий юзер)
            //Предполагается, что если удаление происходит в момент открытие окна - значит в модальном окне информация об объекте
            if( window.modal_page && ifDispatchRightNowFromJS(object_data) )
                redirectBack();

        });



        /**
         * Событие восстановления объекта
         */
        setEventListener( object_key_singular + '.restore', function (event, object_data){

            if( typeof object_data.event_data !== 'undefined' )
                object_data = object_data.event_data;

            objectTableRowRestore( $(object_class_table + ' tr[data-id="'+ object_data.id +'"]') );

        });

    }


    /**
     * Проверка кто запустил eventDispatch
     */
    window.ifDispatchRightNowFromJS = function( object_data ) {

        if( object_data.dispatch_right_now_from_js )
            return true;

        return false;

    }
