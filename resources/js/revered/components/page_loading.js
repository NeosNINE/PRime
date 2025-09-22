
    /**
     *
     *      Переход по ссылкам (AJAX подгрузка контента)
     *
     */
    eventClick('a:not(.not-ajax):not([target=_blank])',function (elem) {

        if( elem.attr('href') === '#' || !elem.attr('href') || elem.hasClass('btn-action') )
            return false;

        //Закрываем навигацию
        window.closeMobileTopNav();

        loadPage(elem.attr('href'), elem);

    });


    /**
     * Переход [data-href]
     */
    eventClick('[data-href]', function (elem, event){

        let elem_target = $(event.target);
        if( elem_target.closest('a').length || elem_target.closest('button').length || elem_target.is('a') || elem_target.is('button') )
            return false;

        if( $(elem).hasClass('not-ajax') ){

            window.location.href = elem.attr('data-href');

        }else{

            loadPage(elem.attr('data-href'), elem);

        }

    });



    /**
     * Если переходят по истории в браузере загружаем нужную страницу
     */
    window.current_location_hash = location.hash;
    window.current_location_pathname = location.pathname + location.search;
    window.addEventListener("popstate", function(event) {

        //Если изменился хеш страницы, но при этом не поменялся адрес - то не загружаем страницу, т.к. возможна неправильная работа модальных окон
        if( current_location_pathname === location.pathname + location.search ){

            //Если запушено модальное окно - то делаем redirectBack(), чтобы вернутся "назад" в модальном окне или закрыть его
            if( modal_page ){

                redirectBack();

            //Если модальное окно не запущено - запускаем
            }else{

                openModalFromHash();

            }


        //Если адрес страницы изменился - загружаем страницу
        }else{

            window.current_location_pathname = location.pathname + location.search;
            loadPage(location.pathname);

        }

    });


    /**
     *  Добавляем в историю переход по ссылке
     */
    window.pushPathName = function (path){

        if( !window.changedPathName && !modal_page ) {
            if (document.location.pathname !== path && document.location.href !== path) {

                window.last_load_page = document.location.pathname;
                history.pushState(null, null, path);
                window.current_location_pathname = location.pathname + location.search;

            }
        }

    }



    /**
     * Дополнительно отслеживаем URL, чтобы отследить редиректы
     */
    window.changePathName = function (path){

        if( modal_page )
            return false;

        path = '/' + path;

        pushPathName(path);
        window.changedPathName = true;

    };


    /**
     *  Функция загрузки страницы
     */
    window.last_load_page = document.location.pathname;
    window.loadPage = function( url, clicked_elem, callback ){

        if( !url ){
            console.log('Can not load page. URL is false.');
            return false;
        }

        if( isPageLoadingNow() )
            return false;

        pageLoadingStart();


        //Убираем ajax = true из URL (мы его добавляем автоматически в ajax request)
        url = url
            .replace('ajax=true&', '')
            .replace('ajax=true', '');


        window.changedPathName = false;

        //Если нельзя загружать контент в модальном окне
        if( clicked_elem && $(clicked_elem).attr('data-open-no-modal') ){
            window.modal_page = false;
            window.modal_page_bootstrap.hide();
        }

        let data;


        //Если сейчас открыто модальное окно
        if( modal_page ){

            //Если переходят на URL, где было открыто модальное окно - закрываем его и обновляем страницу
            let new_pathname = url.replace(/^.*\/\/[^\/]+/, '').split('#')[0];
            if (new_pathname === window.location.pathname) {

                pageLoadingEnd();
                window.modal_page_bootstrap.hide();

                setTimeout(function () {
                    refreshPage();
                }, 10);

                return false;

            }

            data = { 'modal_page' : true };

        }else{

            data = {};

        }


        request('GET', url, data, function (response) {

            if( checkAjaxRedirects(response) )
                return false;

            pushPathName(url);

            //Смотрим куда нам нужно будет скролить страницу
            let scroll_to = 0;
            let scroll_to_elem = 'html';

            //Если был клик на пагинацию
            if( clicked_elem && clicked_elem.parents('ul.pagination').length > 0 ){

                let closest_box = clicked_elem.closest('.box');
                if( closest_box.length > 0 ) {

                    scroll_to = closest_box.offset().top - 80;

                }

            }


            //Если необходимо загрузить страницу в модальное окно
            if( modal_page ){


                //Текущий контент скрываем, чтобы показать его при возврате назад
                modal_page.find('.modal-page-content')
                    .addClass('modal-page-content-hidden')
                    .removeClass('modal-page-content')
                    .after('<div class="modal-page-content"></div>');



                //Если response содержит <body> или <head> то вставляем как iframe
                if( response.search('<body') !== -1 || response.search('<head>') !== -1 ){

                    modal_page.find('.modal-page-content').html('<iframe id="content_iframe_modal"></iframe>');
                    $('#content_iframe_modal')[0].contentWindow.document.write(response);

                    let iframe_height = modal_page.find('.' + getModalType(modal_page) + '-body').height();
                    if( iframe_height < 300 )
                        iframe_height = 300;

                    $('#content_iframe_modal').height( iframe_height );


                //иначе просто вставляем контент в нужное место
                }else{

                    //Вставляем контент
                    modal_page.find('.modal-page-content').html(response);

                }


                //Обновляем атрибут URL у модального окна (сперва необходимо обновить атрибут и только после этого пушить ХЭШ)
                modal_page.attr('data-' + getModalType(modal_page) + '-url', encodeURIComponent(url.replace(/^.*\/\/[^\/]+/, '')) );


                //Для модальных окон обновляем высоту
                if( getModalType(modal_page) === 'modal' )
                    window.modal_page_bootstrap.handleUpdate();


                //Обновляем hash
                history.pushState(null, null, modalHashEncode(modal_page) );


                //Пушим историю перехода в модальном окне для навигации, если это не возвращение назад
                let last_modal = modalHistoryGetLast();
                if( last_modal && last_modal['data-' + getModalType(modal_page) + '-url'] !== url || !last_modal ) {

                    modalHistoryPush();

                }


                //Добавляем кнопку "Назад" в модальное окно
                addModalBackLink();


                //Фокус на первый элемент
                modalFocusFirstElem();



                //Определяем в каком контейнере нам нужно скролить
                scroll_to_elem = getModalType(modal_page) === 'modal' ? '.modal' : '.offcanvas-body';

                //Если скролим в modal - то нужно пересчитывать куда скролим
                if( scroll_to !== 0 && getModalType(modal_page) === 'modal' )
                    scroll_to = scroll_to - modal_page.find('.modal-page-content').offset().top + 160;


                //Если скролим в offcanvas - то нужно пересчитывать куда скролим
                if( scroll_to !== 0 && getModalType(modal_page) === 'offcanvas' )
                    scroll_to = scroll_to - modal_page.find('.modal-page-content').offset().top + 90;


            //Обычная загрузка страницы
            }else{


                //Если response содержит <body> или <head> то вставляем как iframe
                if( response.search('<body') !== -1 || response.search('<head>') !== -1 ){

                    $('#content').html('<iframe id="content_iframe"></iframe>');
                    $('#content_iframe')[0].contentWindow.document.write(response);


                //иначе просто вставляем контент в нужное место
                }else{

                    //Подгружаем контент
                    $('#content').html(response);

                }


            }

            pageLoadingEnd();
            pageInit();


            //Скролим
            if( scroll_to !== false )
                $(scroll_to_elem).scrollTop(scroll_to);


            //Запускаем callback
            if( typeof callback == 'function' )
                callback(response);


        }, function (error,code) {

            pageLoadingEnd();
            showError(error,code);

        }, false);

    };


    /**
     * Проверка ответа response на редиректы
     */
    window.checkAjaxRedirects = function ( ajax_response ){

        if( ajax_response.redirectTo )
            return window.location.href = ajax_response.redirectTo;

        let ajax_loaded_route = $('<div/>').html(ajax_response).find('meta[name=current_route]').attr('content');

        if( ajax_loaded_route === 'login' ){

            pageLoadingEnd();
            return window.location.href = route('login') + '?backTo=' + encodeURIComponent(window.location.pathname + window.location.search);

        }

        return false;
    }



    /**
     * Редирект назад
     */
    window.redirectBack = function( with_update = false ){

        //Если открыто модальное окно-страница
        if( modal_page ){

            let last_modal = modalHistoryGetLast();
            if( last_modal ){

                modal_page_loads.pop(); //Удаляем последнюю запись в истории

                let href = last_modal['data-' + getModalType(modal_page) + '-url'];

                if( typeof href === 'undefined' )
                    return showError('Something went wrong with loading modal/offcanvas window.');

                href = decodeURIComponent(href);


                //Если мы возвращаемся назад с обновлением - то удаляем из HTML сохраненный контент
                if( modal_page.find('.modal-page-content-hidden').length && with_update )
                    modal_page.find('.modal-page-content-hidden:last').remove();



                //Если есть скрытый контент в модальном окне - показываем его (если возвращаемся без обновления)
                if( modal_page.find('.modal-page-content-hidden').length && !with_update ){

                    modal_page.find('.modal-page-content').remove();

                    modal_page.find('.modal-page-content-hidden:last')
                        .removeClass('modal-page-content-hidden')
                        .addClass('modal-page-content');


                    //Добавляем/убираем кнопку "Назад" в модальное окно
                    addModalBackLink();

                    //Фокус на первый элемент
                    modalFocusFirstElem();

                    //Обновляем атрибут URL у модального окна (сперва необходимо обновить атрибут и только после этого пушить ХЭШ)
                    modal_page.attr('data-' + getModalType(modal_page) + '-url', encodeURIComponent(href.replace(/^.*\/\/[^\/]+/, '')) );

                    //Обновляем hash
                    history.pushState(null, null, modalHashEncode(modal_page) );



                //Иначе - загружаем через URL истории
                }else{

                    loadPage( href, false );

                }


            }else{

                modal_page_bootstrap.hide();

            }


        //Если это не модальное окно, а обычная страница
        }else{

            loadPage(window.last_load_page);

        }

    };


    /**
     * Обновить страницу
     */
    window.refreshPage = function ( refresh_modal_if_open = true){

        //Если сейчас открыто модальное окно
        if( modal_page && refresh_modal_if_open ){

            let data = modalHashDecode( location.hash.substr(1) );
            let url = data['data-' + getModalType(modal_page) + '-url'];

            loadPage(url);

        }else{

            loadPage(document.location.pathname + document.location.search);

        }

        //Обновляем так же информацию с backend (например, чтобы count в навигации обновились)
        refreshInfoFromBackend();

    };



    /**
     * Редирект
     */
    window.redirect = function ( url ){

        window.location.href = url;

    }




    /**
     *  Начать загрузку страницы
     */
    window.pageLoadingStart = function () {

        $('body').addClass('page-loading');

        let page_loading_spinner = '<div id="page-loading"><i class="fas fa-spinner fa-spin"></i></div>';

        if( $('#app').length ){

            $('#app').append(page_loading_spinner);

        }else{

            $('body').append(page_loading_spinner);

        }

        $('#page-loading').fadeIn(4000);
    };



    /**
     *  Закончить загрузку страницы
     */
    window.pageLoadingEnd = function () {
        $('#page-loading').remove();
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
     * Проставить фокус в поле поиска если оно есть
     */
    window.setAutoFocusInput = function (){

        let input;

        if( window.modal_page ){

            input = $('.modal-page-content .form-search').find('[autofocus]:first');

        }else{

            input = $('.form-search').find('[autofocus]:first');

        }

        if( !input.length )
            input = $('#content').find('[autofocus]:first');

        if( !input.length )
            input = $('[autofocus]:visible:first');


        if( input.length ){

            //Проверяем находиться ли input в зоне видимости
            let coordinates = input[0].getBoundingClientRect();
            if( $(window).height() <= coordinates.top || coordinates.top < 0 )
                return ;

            let current_val = input.val();
            input.val('')
            input.focus();
            input.val( current_val );

        }

    };


    /**
     * Отчистить Tooltips (bootstrap)
     */
    window.tooltipsClear = function (){

        //Удаляем открытые tooltip`s (потому что иногда они остаются открытыми навсегда, это баг от bootstrap)
        $('.tooltip.bs-tooltip-auto.show').remove();

    };


    /**
     * Включить Tooltips (bootstrap)
     */
    window.tooltipsEnable = function (){

        tooltipsClear();

        let tooltipTriggerList = $('.tooltip-btn:not(.tooltip-enabled), [data-bs-toggle="tooltip"]:not(.tooltip-enabled)');
        tooltipTriggerList.addClass('tooltip-enabled');
        window.tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, { title: $(tooltipTriggerEl).attr('data-bs-title') }));

    };




    /**
     *  Загрузка страницы инициализация скриптов (функция срабатывает при каждой загрузке страницы через ajax)
     */
    window.pageInit = function ( first_load = false ){

        setDataAccessElements();
        injectCss();
        injectScripts();
        interactiveElementsSetup();
        activeInputSetup();
        fileUploadSetup();
        aceJsSetup();
        prismJsSetup();
        select2Setup();
        sortableSetup();
        editorSetup();
        scrollbarsInit();
        chartInit();
        daterangepickerInit();
        iframeResize();
        setInputSearchCssClassById();
        setAutoFocusInput();
        tooltipsEnable();
        scrollAutoLoadEnable();
        loadPageSetDisabledTrRows();

        if( !first_load ){

            setTimeout(function(){

                window.dispatchEvent( new Event("load") );

            }, 100);

        }

    };
    pageInit( true );


    /**
     * Запуск функции pageInit() с timeout, запускается "отложено", чтобы не запустить pageInit() множество раз одновременно.
     * Используются, например, в событиях
     */
    window.doPageInitTimeout = function(){

        if( window.doPageInitTimeoutNow )
            clearTimeout(window.doPageInitTimeoutNow);

        window.doPageInitTimeoutNow = setTimeout( function(){

            pageInit();

        }, 500);

    }


    /**
     * Функция запускается после полной загрузки страницы
     */
    window.fullLoadedPage = function (){

        checkLeftFullHeightNavView();
        actionsFixedInit();

        $('.app-loading').removeClass('app-loading');
        $('.page-loading-opacity').removeClass('page-loading-opacity');


    };


    //Полная загрузка страницы
    window.addEventListener("load", function() {
        fullLoadedPage();
    });
