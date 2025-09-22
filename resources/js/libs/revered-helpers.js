import Lang from './lang'

    /**
     * Объект для получения перевода
     */
    window.Lang = new Lang({
        messages: window.translations,
        locale: 'ru',
        fallback: 'en'
    });


    window.is_touch_device = 'ontouchstart' in document.documentElement;


    /**
     Установить обработчики события
     */
    window.setEvent = function(event, elem, func, off_event = false, preventDefault = true){
        let body = $('body');

        if( off_event )
            body.off(event, elem);

        body.on(event, elem, function (event) {

            if( preventDefault !== false )
                event.preventDefault();

            return func($(this),event);

        });
    };

    /**
     Событие клика по элементу
     */
    window.eventClick = function (elem, func, off_event = false, preventDefault = true) {
        setEvent('click', elem, func,off_event,preventDefault);
    };


    /**
     Событие направление на элемент
     */
    window.eventHover = function (elem, func, off_event = false) {
        setEvent('hover', elem, func, off_event);
    };


    /**
     Событие фокуса элемента
     */
    window.eventFocus = function (elem, func, off_event = false) {
        setEvent('focus', elem, func, off_event);
    };


    /**
     Событие потери фокуса элемента
     */
    window.eventFocusOut = function (elem, func, off_event = false) {
        setEvent('focusout', elem, func, off_event);
    };


    /**
     Событие выхода из элемента
     */
    window.eventBlur = function (elem, func, off_event = false) {
        setEvent('blur', elem, func, off_event);
    };


    /**
     Событие изменение элемента
     */
    window.eventChange = function (elem, func, off_event = false) {
        setEvent('change', elem, func, off_event);
    };


    /**
     Проверка принадлежности
     */
    window.isElemBelongTo = function (elem, selector) {
        const controlElem = elem.closest(selector);

        return controlElem && controlElem.contains(elem)
    }

    /**
     Задержка выполнения
     */
    window.debounce = function(fn, delay = 250) {
        let timeout;

        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn(...args), delay);
        };
    }

    /**
     Событие изменение элемента вместе с кнопками
     */
    window.eventChangeWithKeyPress = function (elem, func, off_event = false) {
        setEvent('change', elem, func, off_event);
        setEvent('keyup', elem, func, off_event);
    };


    /**
     Событие изменение элемента вместе с кнопками (задержка события, для отправки ajax)
     */
    window.eventChangeWithKeyPressDebounce = function (elem, func, func_start = false, wait = 350, off_event = false){

        setEvent('change keyup', elem, debounce(func, func_start, wait), off_event);

    };


    /**
     * Debounce
     */
    window.debounce_list = [];
    window.debounce = function (func, func_start = false, wait = 350){

        return function (...args) {

            if( func_start )
                func_start(...args);

            if( debounce_list[func] ) {
                clearTimeout(debounce_list[func]);
            }

            debounce_list[func] = setTimeout(function (){ func(...args) }, wait);

        }

    }


    /**
     Событие отправки формы
     */
    window.eventSubmit = function (elem, func, off_event = false) {
        setEvent('submit', elem, func, off_event);
    };


    /**
         Событие наведения мышки на элементу
     */
    window.eventMouseEnter = function (elem, func, off_event = false) {
        setEvent('mouseenter', elem, func, off_event);
    };


    /**
        Событие отвода мышки от элемента
     */
    window.eventMouseLeave = function (elem, func, off_event = false) {
        setEvent('mouseleave', elem, func, off_event);
    };


    /**
     Событие срабатывает при нажатии любой кнопки мыши
     */
    window.eventMouseDown = function (elem, func, off_event = false) {

        if( is_touch_device ){

            setEvent('touchstart', elem, func, off_event, false);

        }else{

            setEvent('mousedown', elem, func, off_event, false);

        }

    };


    /**
     Событие срабатывает при отпускании любой кнопки мыши
     */
    window.eventMouseUp = function (elem, func, off_event = false) {

        if( is_touch_device ){

            setEvent('touchend', elem, func, off_event, false);

        }else{

            setEvent('mouseup', elem, func, off_event, false);

        }

    };


    /**
        Событие срабатывает при движении мышки
     */
    window.eventMouseMove = function (elem, func, off_event = false) {
        setEvent('mousemove', elem, func, off_event);
    };


    /**
     * Событие перехода клавиши клавиатуры в нажатое состояние
     */
    window.eventKeyDown = function(func){
        $('body').keydown(func);
    };


    /**
     * Событие ввода символа с клавиатуры
     */
    window.eventKeyPress = function(func){
        $('body').keypress(func);
    };


    /**
     * Событие возвращения клавиши клавиатуры в не нажатое состояние
     */
    window.eventKeyUp = function(func){
        $('body').keyup(func);
    };


    /**
     * Событие клика вне элемента
     */
    window.eventClickOutside = function(elem, ignore_elem, func){

        if( typeof ignore_elem === 'function' ){
            func = ignore_elem;
            ignore_elem = elem;
        }

        $(document).on('click', function (e) {

            if( !$(elem).is(e.target) && $(elem).has(e.target).length === 0 && !$(ignore_elem).is(e.target) && $(ignore_elem).has(e.target).length === 0 ){

                func(elem);

            }

        });

    }



    /**
        Отправить AJAX запрос
        @method - POST, GET, PUT, DELETE
        @url - URL куда отправляем запрос
        $data - данные, которые передаем
        @success_func - callback function при успехе
        @error_func - callback function при ошибке
        @request_queue - если TRUE то выполняем запросы по очереди
     */
    window.request_queue_list = [];
    window.request_loading_now = false;
    window.request = function ( method, url, data, success_func, error_func, request_queue = true ){

        if( isFunction(data) ){

            if( isFunction(success_func) )
                error_func = success_func;

            success_func = data;
            data = {};

        }

        if( request_queue && request_loading_now )
            return addRequestToQueue( method, url, data, success_func, error_func );


        data = requestDataPrepare( data, method );

        if( request_queue )
            window.request_loading_now = true;

        const csrfToken = getCSRF();
        $.ajax({
            url: url,
            type: method,
            data: data,
            cache: false,
            headers: Object.assign({ 'X-Alt-Referer': window.location.href }, csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            success: function( response, textStatus, xhr ){

                if( success_func ) {

                    success_func(response, textStatus, xhr);

                }else{

                    pageLoadingEnd();

                }

            },
            error: function (error,i,code) {

                if( error_func ){

                    error_func(error,code);

                }else{

                    showError(error,code);
                    pageLoadingEnd();

                }

            },
            complete: function(){

                window.request_loading_now = false;
                doRequestFromQueue();

            }
        });

    };



    /**
     * Добавить запрос request в очередь
     */
    window.addRequestToQueue = function( method, url, data, success_func, error_func ){

        window.request_queue_list.push({
            method: method,
            url: url,
            data: data,
            success_func: success_func,
            error_func: error_func
        });

    }



    /**
     * Выполнить запрос из очереди
     */
    window.doRequestFromQueue = function(){

        if( !window.request_queue_list.length )
            return;

        let queue = window.request_queue_list.shift();

        request( queue.method, queue.url, queue.data, queue.success_func, queue.error_func );

    }



    /**
     * Подготовить параметры запроса к выполнению request
     */
    window.requestDataPrepare = function ( data, method ){

        if( typeof data == 'undefined' )
            data = {};

        if( method !== 'GET' ){

            if( !data._token )
                data._token = getCSRF();


            if( method === 'DELETE' )
                data._method = 'delete';


            if( method === 'PUT' )
                data._method = 'put';

        }


        if( typeof data == 'string' ){

            data = data + '&ajax=true';

        }else if( $.isArray(data) ){

            data.push({name: 'ajax', value: true});

        }else{

            data.ajax = true;

        }

        return data;

    }



    /**
     * Выполнить GET запрос
     * @param url
     * @param data
     * @param success_func
     * @param error_func
     * @param request_queue
     */
    window.get = function ( url, data, success_func, error_func, request_queue = true ){
        request('GET', url, data, success_func, error_func, request_queue);
    }


    /**
     * Выполнить POST запрос
     * @param url
     * @param data
     * @param success_func
     * @param error_func
     * @param request_queue
     */
    window.post = function ( url, data, success_func, error_func, request_queue = true ){
        request('POST', url, data, success_func, error_func, request_queue);
    }




    /**
     *  Начать загрузку страницы
     */
    window.pageLoadingStart = function () {
        $('body').addClass('page-loading');
    };



    /**
     *  Закончить загрузку страницы
     */
    window.pageLoadingEnd = function () {
        $('body').removeClass('page-loading');
    };



    /**
     *  Проверить идет ли сейчас загрузка страницы
     */
    window.isPageLoadingNow = function () {

        if( $('body').hasClass('page-loading') )
            return true;

        return  false;

    };



    /**
        Вывести ошибку
    */
    window.showError = function ( error, code ){

        log( error, 'error' );

        let error_msg = getErrorMessage( error, code );

        if( typeof toastr !== "undefined" ){

            toastr(error_msg, 'error');

        }else if( typeof window.active_form !== 'undefined' && window.active_form.find('.action-error').length > 0 ){

            window.active_form.find('.action-error').html(error_msg).show();
            window.active_form.find('.action-success').hide();

        }else{

            alert(error_msg);

        }

        if( isFormLoading() )
            formLoadingEnd();

    };


    /**
     * Получить сообщение об ошибке
     */
    window.getErrorMessage = function( error, code ){

        let msg = false;

        if( !code )
            msg = error;

        if( typeof error.responseJSON !== "undefined" && typeof error.responseJSON.errors !== 'undefined' ){

            $.each(error.responseJSON.errors,function (key,row) {
                error = row[0];
                msg = error;
                return;
            });


        }else if( typeof error.responseJSON !== 'undefined' && typeof error.responseJSON.message !== 'undefined' ){
            error = error.responseJSON.message;
            msg = error;
        }

        if( !msg )
            msg = code;


        if( typeof error.responseText !== 'undefined' ){

            msg += ': ' + error.responseText;

        }

        return msg;

    };



    /**
     * Сообщение об успехе
     */
    window.showSuccess = function (msg){

        if( typeof toastr !== "undefined" ){

            toastr(msg, 'success');

        }else if( typeof window.active_form !== 'undefined' && window.active_form.find('.action-success').length > 0 ){

            window.active_form.find('.action-success').html(msg).show();
            window.active_form.find('.action-error').hide();

        }else{

            alert(msg);

        }

        return msg;

    }


    /**
     Проверка ялявется ли переменная функцией
     */
    window.isFunction = function(obj) {
        return !!(obj && obj.constructor && obj.call && obj.apply);
    };


    /**
     Собрать массив данных из формы в JSON
     [data-inputs-group] - все поля внутри этих элементов формируются в группу
     [data-inputs-to-group] - все поля внутри элементом с этим атрибутом формируются в группу
     [data-inputs-key] - используется для того же элмента у котрого [data-inputs-to-group], чтобы показать из какого поля нужно формировать ключи
     Пример как использовать:
     <div class="test">
         <div data-inputs-group="general">
             <input name="name" value="Это просто пример">
             <input name="desc" value="Описание примера">
         </div>
         <div data-inputs-group="data">
            <input name="name" value="Массив с данными">
            <div data-inputs-to-group="arrays" data-inputs-key="code">
                 <input name="name" value="Элемент 1">
                 <input name="code" value="element_1">
            </div>
            <div data-inputs-to-group="arrays" data-inputs-key="code">
                 <input name="name" value="Элемент 2">
                 <input name="code" value="element_2">
            </div>
         </div>
     </div>
     Получим:
     Array
     (
     [general] => Array
     (
         [name] => Это просто пример
         [desc] => Описание примера
     )

     [data] => Array
     (
     [arrays] => Array
     (
         [element_1] => Array
         (
             [name] => Элемент 1
             [code] => element_1
         )

         [element_2] => Array
         (
             [name] => Элемент 2
             [code] => element_2
         )

     )

     [name] => Массив с данными
     )
     )
     */
    window.getFormData = function (elem,recursive) {

        let form = $(elem);
        let recursive_data;
        let data = {};

        //data-inputs-group
        form.find('[data-inputs-group]:not([data-inputs-group-checked])').each(function (index,group) {

            if( $(group).attr('data-inputs-group-checked') ){
                return;
            }

            $(group).attr('data-inputs-group-checked',true);

            data[$(group).attr('data-inputs-group')] = getFormData(group, true);

        });

        //data-inputs-to-group
        form.find('[data-inputs-to-group]:not([data-inputs-to-group-checked])').each(function (index,group) {

            if( $(group).attr('data-inputs-to-group-checked') ){
                return;
            }

            $(group).attr('data-inputs-to-group-checked',true);

            if( !data[$(group).attr('data-inputs-to-group')] ){
                data[$(group).attr('data-inputs-to-group')] = {};
            }

            recursive_data = getFormData(group, true);

            if( $(group).attr('data-inputs-key') ) {
                data[$(group).attr('data-inputs-to-group')][recursive_data[$(group).attr('data-inputs-key')].toLowerCase()] = recursive_data;
            }else{
                data[$(group).attr('data-inputs-to-group')][ Object.keys(data[$(group).attr('data-inputs-to-group')]).length ] = recursive_data;
            }

        });

        form.find('[name]:not([data-inputs-to-group-checked])').each(function (index,input) {

            let name = $(input).attr('name');

            let clean_name = name;
            if ( clean_name.endsWith('[]') )
                clean_name = clean_name.substring(0, clean_name.length - 2)

            let value = $(input).val();

            if( $(input).attr('data-inputs-to-group-checked') )
                return;

            $(input).attr('data-inputs-to-group-checked',true);

            //Если это чекбокс или radio
            if( $(input).prop('type') === 'checkbox' || $(input).prop('type') === 'radio' ) {

                if ($(input).prop('checked')) {

                    if ( name.endsWith('[]') ) {

                        if( $.isArray(data[clean_name]) ) {

                            data[clean_name].push(value);

                        }else{
                            data[clean_name] = [value];
                        }

                    } else {

                        data[clean_name] = value;

                    }

                } else {

                    if( $(input).attr('data-value-not-checked') )
                        data[$(input).attr('name')] = $(input).attr('data-value-not-checked');

                }


            //Если это мультиселект
            }else if( $(input).prop('type') === 'select-multiple' ){


                if( value.length > 0 ){
                    data[clean_name] = value;
                }else{
                    data[clean_name] = null;
                }



            //Если это любой другой тип
            }else {

                if ( name.endsWith('[]') ) {

                    if( $.isArray(data[clean_name]) ) {

                        data[clean_name].push(value);

                    }else{

                        data[clean_name] = [value];

                    }

                } else {

                    data[clean_name] = value;

                }


            }


        });

        if( recursive !== true ) {
            $('[data-inputs-to-group-checked]').removeAttr('data-inputs-to-group-checked');
            $('[data-inputs-group-checked]').removeAttr('data-inputs-group-checked');
        }

        return data;
    };


    /**
     Установить обработчик отправки формы
     */
    window.submitForm = function (form,success_func,error_func) {

        eventSubmit(form, function (this_form) {
            doSubmitForm(this_form, success_func, error_func)
        });

    };

    /**
     Отправить форму
     */
    window.doSubmitForm = function (form, success_func, error_func) {

        if( isFormLoading() )
            return false;

        formLoadingStart(form);

        window.active_form = form;

        let method = form.attr('data-method');

        if( !form.attr('data-action') ){
            formLoadingEnd(form);
            return showError('Не указан атрибут "data-action".');
        }

        if( !method ){
            formLoadingEnd(form);
            return showError('Не указан атрибут "data-method".');
        }

        request(
            method,
            form.attr('data-action'),
            getFormData(form),
            function (response) {

                formLoadingEnd(form);

                if( success_func ) {

                    success_func(response,form);

                }else{

                    showSuccess("Данные отправлены успешно.");

                }

            },function (error,code) {

                formLoadingEnd(form);

                if( error_func ) {

                    error_func(error, form);

                }else{

                    //Если используется toastr - отчищаем сообщения
                    if( typeof toastr !== "undefined" )
                        toastrClear();

                    showError(error, code);

                }
            }
        );

    }

    /**
     * Очистить форму
     */
    window.resetForm = function (selector) {

        resetFormValidationErrors(selector);
        $(selector).get(0).reset();

    }

    /**
     * Очистить валидационные ошибки формы
     */
    window.resetFormValidationErrors = function (selector) {

        $(selector).removeClass('invalid');
        $(selector + ' .error').empty();

    }


    /**
     * Отобразить валидационные ошибки на форму
     */
    window.bindFormValidationErrors = function (selector, errors) {

        resetFormValidationErrors(selector);
        $(selector).addClass('invalid');

        for (const [key, value] of Object.entries(errors)) {
            $(`${selector} *[data-field-name="${key}"] .error`).text(value);
        }

    }


    /**
     * Начать загрузку
     */
    window.formLoadingStart = function ( form ){

        $('body').addClass('form-loading');

        if( form ) {
            $(form).addClass('form-loading');
            $(form).find('.actions button').prop('disabled', true);
        }

    };


    /**
     * Завершить загрузку
     */
    window.formLoadingEnd = function ( form ){

        $('body').removeClass('form-loading');

        if( form ) {
            $(form).removeClass('form-loading');
            $(form).find('.actions button').prop('disabled', false);
        }

    };


    /**
     * Проверка идет ли сейчас загрузка
     */
    window.isFormLoading = function (){

        return $('body').hasClass('form-loading');

    };


    /**
     * Заблокировать элемент для выполнения запроса
     */
    window.elemStartLoading = function ( elem ){

        $(elem).addClass('elem-loading');

    }


    /**
     * Заблокирован ли элемент для загрузки
     */
    window.isElemLoading = function ( elem ){

        return $(elem).hasClass('elem-loading');

    }


    /**
     * Завершить блокировку элемента
     */
    window.elemEndLoading = function ( elem ){

        $(elem).removeClass('elem-loading');

    }


    /**
     * Получить CSRF токен
     */
    window.getCSRF = function (){

        if( $('meta[name="csrf-token"]').length )
            return $('meta[name="csrf-token"]').attr('content');

        if( $('meta[name="csrf"]').length )
            return $('meta[name="csrf"]').attr('content');

        if( $('input[name="_token"]').length )
            return $('input[name="_token"]:first').val();

        return false;
    };



    /**
     * Установить CSRF токен
     */
    window.setCSRF = function ( CSRF ){

        if( $('meta[name="csrf-token"]').length )
            $('meta[name="csrf-token"]').attr('content', CSRF);

        if( $('meta[name="csrf"]').length )
            $('meta[name="csrf"]').attr('content', CSRF);

        if( $('input[name="_token"]').length )
            $('input[name="_token"]').val(CSRF);

    };



    /**
     * Page is load event
     */
    window.DOMContentLoaded = function (){

        //Запускаем событие загрузки DOM (нужно для некоторых плагинов, например lazy.js)
        window.document.dispatchEvent(new Event("DOMContentLoaded", {
            bubbles: true,
            cancelable: true
        }));

    };



    /**
     Scroll to element
     */
    window.scrollToElem = function (elem, func) {
        $('html, body').animate( {
            scrollTop: $(elem).offset().top-50
        }, 100, function () {
            if( func ) {
                func();
            }
        } );
    };


    /**
        Console.log
     */
    window.log = function ( data, type = 'log' ) {

        if( type === 'error' )
            return console.error(data);

        return console.log(data);

    };




    /**
     *  Trim
     */
    if( !String.prototype.trim ){

        (function() {
            String.prototype.trim = function() {
                return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
            };
        })();

    }


    /**
     * String HashCode
     */
    if( !String.prototype.hashCode ) {

        String.prototype.hashCode = function () {
            let hash = 0, i, chr;
            if (this.length === 0) return hash;
            for (i = 0; i < this.length; i++) {
                chr = this.charCodeAt(i);
                hash = ((hash << 5) - hash) + chr;
                hash |= 0;
            }
            return hash;
        };

    }


    /**
     * Получить ширину скролл бара в браузере
     */
    window.getScrollBrowserWidth = function (){

        if( $('body').height() <= window.innerHeight )
            return 0;

        let div = $('<div>').css({
            position: "absolute",
            top: "0px",
            left: "0px",
            width: "100px",
            height: "100px",
            visibility: "hidden",
            overflow: "scroll"
        });

        $('body').eq(0).append(div);


        let width = div.get(0).offsetWidth - div.get(0).clientWidth;

        div.remove();

        return width;
    }

    /**
     * Включить мелодию при успешном выполнении действия
     */
    window.playSuccessSound = function () {

        const player = new Audio('/assets/guest/sound/success.wav');
        player.play();

    }

    /**
     * Включить мелодию при неуспешном выполнении действия
     */
    window.playFailSound = function () {

        (new Audio('/assets/guest/sound/fail.wav')).play();

    }


    /**
     * Функция копирования текста в буфер обмена
     * @param text
     */
    window.copyTextToClipboard = function (text) {

        let textArea = document.createElement("textarea");
        textArea.value = text;

        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";
        textArea.style.opacity = "0";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        let r = true;

        try {

            if( !document.execCommand('copy') )
                r = false;

        } catch (err) {

            r = false;
            showError('Не удалось скопировать текст.');

        }

        document.body.removeChild(textArea);

        return r;

    }


    /**
     * Список аттрибутов элемента (jQuery элемент)
     */
    window.getAttrList = function( elem ) {

        let obj = {};

        $.each( $(elem)[0].attributes, function() {

            obj[this.name] = this.value;

        });

        return obj;

    }



    /**
     * Получить текст на нужном языке
     */
    window.langText = function ( text, lang ){

        if( typeof text === 'string' )
            return text;

        if( typeof lang === 'undefined' ){

            if( typeof window.current_lang !== 'undefined' ){

                lang = window.current_lang;

            }else{

                if( $('html').attr('lang') ){

                    lang = $('html').attr('lang');

                }else{

                    lang = false;

                }

            }

        }


        if( typeof text[lang] !== 'undefined' )
            return text[lang];


        for( let key in text ){

            return text[key];

        }

        return '';


    }
