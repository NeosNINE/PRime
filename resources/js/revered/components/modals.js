

    /*
     * Здесь обрабатываются Modal и Offcanvas
     */




    /**
     * Глобально модальное-контент устанавливаем в false изначально (то есть окно закрыто)
     */
    window.modal_page = false;
    window.modal_page_bootstrap = false;



    /**
     * Стандартные аттрибуты для Modal (нужно переопределить в HTML, чтобы изменить их)
     */
    const modal_default_attrs = {
        'tabindex' : '-1',
        'aria-hidden' : 'true',
        'data-bs-keyboard' : 'false',
        'data-previous-aria-hidden' : 'true'
    };

    /**
     * Стандартные аттрибуты для Offcanvas (нужно переопределить в HTML, чтобы изменить их)
     */
    const offcanvas_default_attrs = {
        'tabindex' : '-1',
        'aria-hidden' : 'true',
        'data-bs-keyboard' : 'false',
        'data-previous-aria-hidden' : 'true'
    };


    /**
     * Стандартные CSS классы, которые приписываются к .modal-dialog (если Вы указываете классы в HTML, то стандартные перетираются)
     */
    const modal_default_css_classes = 'modal-dialog-centered';


    /**
     * Стандартные CSS классы, которые приписываются к .offcanvas (если Вы указываете классы в HTML, то стандартные перетираются)
     */
    const offcanvas_default_css_classes = 'offcanvas-end';


    /**
        Открыть модальное окно
     */
    eventClick('[data-modal-href], [data-offcanvas-href]', function (elem, event){

        //Если кликают по ссылке или кнопке внутри data-modal-href, то мы не открываем модальное окно
        if( !elem.is('a') && !elem.is('button') ){

            let elem_target = $(event.target);
            if( elem_target.closest('a').length || elem_target.closest('button').length || elem_target.is('a') || elem_target.is('button') || (elem_target.is('td') && elem_target.hasClass('actions')) )
                return true;

        }

        //Если модальное окно уже открыто - загружаем контент в это же окно
        if( window.modal_page ){

            if( elem.attr('data-offcanvas-href') ){

                loadPage( elem.attr('data-offcanvas-href') );

            }else{

                loadPage( elem.attr('data-modal-href') );

            }

        //Если сейчас никакого модального окна не открыто - открываем
        }else{

            modalShow( getAllAttributesForModal(elem) );

        }

        return false;


    }, true, false);



    /**
     * Если идет отправка формы поиска в модальном окне
     */
    eventSubmit('.modal-page-content .form-search', function (form){

        request( form.attr('method'), form.attr('action'), getFormData(form), function (response){

            form.closest('.modal-page-content').html( response );
            setAutoFocusInput();

        });

    });




    /**
     * Получить список всех аттрибутов элемента
     */
    window.getAllAttributesForModal = function ( elem ){

        let data = {};

        //Формируем список всех аттрибутов, чтобы передать окну
        $.each( $(elem)[0].attributes, function ( index, attribute ) {

            //Некоторые аттрибуты игнорируем
            if(
                   attribute.name === 'id'
                || attribute.name === 'class'
                || attribute.name === 'role'
                || attribute.name === 'aria-modal'
                || attribute.name === 'data-id'
                || attribute.name === 'style'
                || attribute.name === 'href'
                || attribute.name === '_target'
            )
                return;

            //Если это аттрибут ссылки окно
            if( attribute.name === 'data-modal-href' || attribute.name === 'data-offcanvas-href' ){

                data[attribute.name] = attribute.value.replace(/^.*\/\/[^\/]+/, '');

            }else{

                data[attribute.name] = attribute.value;

            }

        });

        return data;

    };




    /**
     * Сгенерировать HTML модального окна, если ещё было сгенерировано
     */
    window.modalGenerateHTML = function (data){

        let type = '';
        let attributes = '';

        //Проставляем аттрибуты из HTML
        for( let data_key in data ){

            if( data_key === 'data-offcanvas-href' || data_key === 'data-offcanvas-url' ){

                type = 'offcanvas';
                attributes = attributes + ' data-offcanvas-url="' + encodeURIComponent(data[data_key]) + '"';

            }else if( data_key === 'data-modal-href' || data_key === 'data-modal-url' ){

                type = 'modal';
                attributes = attributes + ' data-modal-url="' + encodeURIComponent(data[data_key]) + '"';

            }else if( data_key === 'type' ){

                type = data[data_key];

            }else{

                attributes = attributes + ' ' + data_key + '="' + data[data_key] + '"';

            }
        }


        if( !type )
            return false;


        //Добавляем стандартные аттрибуты и css classes
        let default_attrs;
        let css_classes = '', default_css_classes;
        if( type === 'modal' ){

            default_attrs = modal_default_attrs;
            default_css_classes = modal_default_css_classes;

        }else{

            default_attrs = offcanvas_default_attrs;
            default_css_classes = offcanvas_default_css_classes;

        }

            for( let data_key in default_attrs ){

                if( typeof data[data_key] === 'undefined' )
                    attributes = attributes + ' ' + data_key + '="' + default_attrs[data_key] + '"';

            }

            css_classes = ( typeof data['data-css-classes'] === 'undefined' ) ? default_css_classes : data['data-css-classes'];

            if( typeof data['data-size'] !== 'undefined' )
                css_classes += ' ' + data['data-size'];



        //Формируем HTML окна
        if( type === 'offcanvas' ){


            $('body').append('<div class="offcanvas '+ css_classes +'" ' + attributes + '>' +
                '<div class="offcanvas-header">' +
                    '<h5 class="offcanvas-title"></h5>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>' +
                '</div>'+
                '<div class="offcanvas-body">' +
                    '<div class="modal-page-content"></div>' +
                '</div>' +
            '</div>');

        }else{

            $('body').append('<div class="modal fade" ' + attributes + '>' +
                '<div class="modal-dialog '+ css_classes +'">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h5 class="modal-title"></h5>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<div class="modal-page-content"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>');

        }


        let modal = $('.' + type + ':last');

        //Событие открытие окна (запуск пока окна, ещё не видно пользователю)
        modal[0].addEventListener('show.bs.' + type, modalShowEvent);

        //Событие открытие окна (когда окно видно пользователю)
        modal[0].addEventListener('shown.bs.' + type, modalShownEvent);

        //Событие закрытия окна
        modal[0].addEventListener('hidden.bs.' + type, modalHiddenEvent);


        return modal;

    }


    /**
     * Функция запускается при открытии окна (запуск пока окна, ещё не видно пользователю)
     */
    window.modalShowEvent = function (){

        //Для шапки проставляем padding, чтобы она не прыгала во время открытия модальных окон
        setPaddingRightHeader();

    }


    /**
     * Функция запускается при открытии окна (когда окно видно пользователю)
     */
    window.modalShownEvent = function (){

        modalFocusFirstElem();
        iframeResize();

    }


    /**
     * Сделать Focus на первый элемент формы, если он есть
     */
    window.modalFocusFirstElem = function () {

        //Делаем focus на первый элемент
        let first_modal_elem = modal_page.find('input:visible:first, select:visible:first, textarea:visible:first').first();

        if( !first_modal_elem.val() )
            first_modal_elem.focus();

    }



    /**
     * Функция запускается при закрытии окна
     */
    window.modalHiddenEvent = function (){

        if( !$(window.modal_page).attr('data-no-push-history') )
            history.pushState(null, null, document.location.pathname + document.location.search);

        //Удаляем из DOM модальное окно
        $(window.modal_page).next('.modal-backdrop').remove();
        $(window.modal_page).remove();


        //Если окна еще остаются
        if( $('.modal.show').length > 0 ){

            window.modal_page = $('.modal.show:last');

            if( getModalType(window.modal_page) === 'offcanvas' ){
                window.modal_page_bootstrap = new bootstrap.Offcanvas(window.modal_page);
            }else{
                window.modal_page_bootstrap = new bootstrap.Modal(window.modal_page);
            }

            window.modal_page_bootstrap._isShown = true;

        }else{

            window.modal_page = false;
            window.modal_page_bootstrap = false;

            //При закрытии окна отчищаем историю окон для навигации
            window.modal_page_loads = [];
            window.modal_page_bootstrap_loads = [];

            //Для шапки проставляем padding, чтобы она не прыгала во время закрытия модальных окон
            clearPaddingRightHeader();

        }

        normalizeManyModalsView();

    }



    /**
     * Показать модальное окно
     */
    window.modal_page_loads = [];
    window.modal_page_bootstrap_loads = [];
    window.modalShow = function (data, callback){

        if( isPageLoadingNow() )
            return false;

        pageLoadingStart();

        let modal = modalGenerateHTML(data);
        let url = decodeURIComponent($(modal).attr('data-' + getModalType(modal) + '-url'));
        let request_data = { 'modal_page' : true };

        //Если указан attribute data-load-in-iframe="true"
        if( modal.attr('data-load-in-iframe') ) {

            modal.find('.modal-page-content').html('<iframe id="content_iframe_modal" src="' + url + '"></iframe>');

            let iframe_height = modal.find('.' + getModalType(modal) + '-body').height();
            if (iframe_height < 300)
                iframe_height = 300;

            $('#content_iframe_modal').height(iframe_height);

            modalFinishLoading( modal, callback );



        }else{

            request('GET', url, request_data, function (response){

                if( checkAjaxRedirects(response) )
                    return false;

                //Если response содержит <body> или <head> то вставляем как iframe
                if( response.search('<body') !== -1 || response.search('<head>') !== -1 ){

                    modal.find('.modal-page-content').html('<iframe id="content_iframe_modal"></iframe>');
                    $('#content_iframe_modal')[0].contentWindow.document.write(response);

                    let iframe_height = modal.find('.' + getModalType(modal) + '-body').height();
                    if( iframe_height < 300 )
                        iframe_height = 300;

                    $('#content_iframe_modal').height(iframe_height);


                //иначе просто вставляем контент в нужное место
                }else{

                    modal.find('.modal-page-content').html(response);

                }

                modalFinishLoading( modal, callback );

            }, function (error){

                showError(error);
                pageLoadingEnd();

            });

        }

    }


    /**
     * Показать модальное окно с определенным текстом
     */
    window.showModalWithContent = function ( content, attributes = {}, type = 'modal' ){

        attributes.type = type;
        attributes['data-no-push-history'] = true;

        let modal = modalGenerateHTML(attributes);
        modal.find('.modal-page-content').html(content);

        modalFinishLoading( modal );

    }



    /**
     * Показать модальное окно с определенным текстом
     */
    window.showOffcanvasWithContent = function ( content, attributes = {} ){

        showModalWithContent( content, attributes, 'offcanvas' );

    }




    /**
     * Finish loading modal content
     */
    window.modalFinishLoading = function( modal, callback ){

        let data = getAllAttributesForModal(modal);

        if( getModalType(modal) === 'offcanvas' ){
            window.modal_page_bootstrap = new bootstrap.Offcanvas(modal);
        }else{
            window.modal_page_bootstrap = new bootstrap.Modal(modal);
        }


        window.modal_page = modal;
        window.modal_page_bootstrap.show();

        if( typeof data['data-modal-url'] !== 'undefined' ||  typeof data['data-offcanvas-url'] !== 'undefined' )
            history.pushState(null, null, modalHashEncode(modal) );

        pageLoadingEnd();

        if( getModalType(modal) === 'offcanvas' ){

            pageInit();

        }else{

            normalizeManyModalsView();

            setTimeout(function(){
                pageInit();
            }, 250);

        }

        modalHistoryPush();
        removeModalBackLink();

        if( callback )
            callback();

    }



    /**
     * Нормализовать отображение одновременно двух и более модальных окон
     */
    window.normalizeManyModalsView = function(){

        if( $('.modal-backdrop').length > 1 ){

            $('.modal-backdrop:not(.modal-backdrop:last)').hide();

            let z_index;
            let i = 0;

            $('.modal').each(function(index, modal){

                i++;

                if( i === 1 ){

                    z_index = $(modal).css('z-index');

                }else{

                    z_index++;
                    $('.modal-backdrop:eq('+ (i-1) +')').css('z-index', z_index);

                    z_index++;
                    $(modal).css('z-index', z_index);

                }

            });

        }else{

            $('.modal-backdrop').show();

        }

    }




    /**
     * Добавить кнопку назад в активное модальное окно
     */
    window.addModalBackLink = function (){

        //Если Назад уже некуда листать, то удаляем кнопку
        if( modal_page_loads.length <= 1 )
            return removeModalBackLink();


        //Добавляем кнопку "Назад"
        $(modal_page).find('.' + getModalType(modal_page) + '-title').html('<a href="#" class="modal-back-link"><i class="fa fa-long-arrow-alt-left"></i> Назад</a>');

    }


    /**
     * Убрать кнопку назад из активного модального окна
     */
    window.removeModalBackLink = function (){

        //Удаляем кнопку "Назад"
        $(modal_page).find('.modal-back-link').remove();

    }


    /**
     * Кликают на кнопку "назад"
     */
    eventClick('.modal-back-link', function (){

        redirectBack();

    });



    /**
     * Получить тип окна (modal или offcanvas)
     */
    window.getModalType = function ( modal ){

        if( $(modal).hasClass('offcanvas') )
            return 'offcanvas';

        return 'modal';

    }



    /**
     * Сохраняем модальные окна для навигации
     */
    window.modalHistoryPush = function (){

        let data = getAllAttributesForModal(modal_page);

        if( typeof data['data-modal-url'] === 'undefined' &&  typeof data['data-offcanvas-url'] === 'undefined' )
            return false;


        //Если уже есть запись этого URL в истории, то пропускаем
        let last = modal_page_loads[(modal_page_loads.length-1)];

        if( typeof last !== 'undefined' ){

            if( getModalType(modal_page) === 'offcanvas' ){

                if( last['data-offcanvas-url'] === data['data-offcanvas-url'] )
                    return false;

            }else{

                if( last['data-modal-url'] === data['data-modal-url'] )
                    return false;

            }

        }

        modal_page_loads.push(data);

    }


    /**
     * Получить историю предпоследнюю запись из истории для redirect-back
     */
    window.modalHistoryGetLast = function (){

        let last = modal_page_loads[(modal_page_loads.length-2)]; // минус 2 потому что нам нужно получить предпоследний, а не последний элемент

        if( last ){

            return last;

        }else{

            return false;

        }

    }



    /**
     * Закрыть все открытые окна
     */
    window.closeOpenModals = function( callback ){

        let interval = setInterval(function (){

            if( !modal_page_bootstrap ){

                let modal = $('.modal.show, .offcanvas.show');
                if( modal.length ){

                    modal = $(modal[0]);

                    window.modal_page = modal;

                    if( getModalType(window.modal_page) === 'offcanvas' ){
                        window.modal_page_bootstrap = new bootstrap.Offcanvas(window.modal_page);
                    }else{
                        window.modal_page_bootstrap = new bootstrap.Modal(window.modal_page);
                    }

                    window.modal_page_bootstrap._isShown = true;

                }

            }

            try {

                modal_page_bootstrap.hide();

            } catch ( e ){

                $('.offcanvas-backdrop.show').remove();
                $('.modal-backdrop.show').remove();

                clearInterval(interval);

                if( typeof callback === 'function' )
                    callback();

            }

        }, 100);

    }




    /**
     * Сформировать URL для ХЭШа
     */
    window.modalHashEncode = function (modal){


        let hash = '#';
        let data = getAllAttributesForModal(modal);

        //Стандартные аттрибуты
        let default_attrs;
        if( getModalType(modal) === 'modal' ){

            default_attrs = modal_default_attrs;

        }else{

            default_attrs = offcanvas_default_attrs;

        }

        for( let data_key in data ){

            //Если это стандартный аттрибут и у него значение стандартное - пропускаем
            if( default_attrs[data_key] === data[data_key] )
                continue;

            if( hash !== '#' )
                hash = hash + '&';

            hash = hash + data_key + '=' + encodeURIComponent( decodeURIComponent(data[data_key]) );

        }


        return hash;


    }


    /**
     * Декодировать URL
     */
    window.modalHashDecode = function (hash){

        let arr = hash.split('&');

        let data = {}, param;

        arr.forEach(function(item, i, arr) {

            param = item.split('=');

            data[param[0]] = decodeURIComponent(param[1]);

        });

        return data;
    }




    /**
     * Открываем модальное окно из ХЭША если есть
     */
    window.openModalFromHash = function (){

        if( !location.hash || window.modal_page )
            return false;

        if( location.hash.substr(0,16) === '#data-modal-url=' || location.hash.substr(0,20) === '#data-offcanvas-url=' ){

            let data = modalHashDecode( location.hash.substr(1) );

            modalShow( data );

        }

    }



    /**
     * Если при загрузке страницы уже есть ХЭШ - открываем модальное окно с нужным URL
     */
    window.addEventListener("load", function() {

        openModalFromHash();

    });



    /**
     * Отслеживаем изменение Hash в строке для открытия модального окна
     */
    window.addEventListener('hashchange', function (){

        if( location.hash.substr(0,16) === '#data-modal-url=' || location.hash.substr(0,20) === '#data-offcanvas-url=' ){

            let hash = location.hash;

            closeOpenModals(function(){

                location.hash = hash;

                openModalFromHash();

            });

        }

    });



    /**
     * Закрываем модальное окно при клике на ESC
     * (пишем отдельную функцию, чтобы модальные окна нормально работали с sweetalert)
     */
    $(document).keydown(function(e) {

        if( e.key === "Escape" ){

            if( modal_page && !$('.swal2-shown:first').length ){

                window.modal_page_bootstrap.hide();

            }else if( $('.swal2-shown:first').length  ){

                swal.close();

            }

        }

    });
