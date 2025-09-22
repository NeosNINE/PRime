/**
     * Scrollbar INIT
     */
    window.scrollbarsInit = function (){


        /**
         * Просто проставляем scrollbar
         */
        if( $("[data-scrollbar]:not(.scrollbar-active)").length ){

            $("[data-scrollbar]").overlayScrollbars({
                scrollbars: {
                    autoHide: "move"
                }
            });

            $("[data-scrollbar]").addClass('scrollbar-active');

        }


        /**
         * Проставляем scrollbar и при этом делаем блок на всю высоту экрана (приписывается, как правило, к .box-table или .box)
         */
        if( $("[data-scrollbar-full-height]:not(.scrollbar-active)").length ){

            $("[data-scrollbar-full-height]").overlayScrollbars({
                scrollbars: {
                    autoHide: "move"
                }
            });

            $("[data-scrollbar-full-height]").each(function (index, elem){

                let pagination_block_height = 0;

                if( $(elem).next('.pagination-block').length )
                    pagination_block_height = $(elem).next('.pagination-block').outerHeight();

                let data = elem.getBoundingClientRect();
                let window_height = window.innerHeight;

                $(elem).css('height', window_height - data.top - pagination_block_height - 30);

                $(elem).find('table').addClass('thead-sticky');
                $(elem).find('table th:first').append('<div class="thead-left-border"></div>');

            });

            $("[data-scrollbar-full-height]").addClass('scrollbar-active');

        }

    }




    /**
     * Open sub-nav
     */
    eventClick('.open-sub-nav',function (elem) {

        if( $(elem).closest('#sidebar-nav').length )
            $('#header .sub-nav').hide();



        //Если это клик подменю внутри подменю (второй и более уровень вложенности)
        if( $(elem).closest('.sub-nav').length ) {

            $(elem).closest('.sub-nav').find('.sub-nav').hide();
            $(elem).next('.sub-nav').toggle();

        }else {

            if( $(elem).next('.sub-nav:visible').length ){

                $(elem).closest('.nav').find('.sub-nav').hide();

            }else{

                $(elem).closest('.nav').find('.sub-nav').hide();
                $(elem).next('.sub-nav').toggle();

            }

        }

    });



    /**
     * Close sub-nav
     */
    eventMouseUp(document, function (elem, event){

        if( $(event.target).hasClass('open-sub-nav') || $(event.target).closest('.open-sub-nav').length > 0 )
            return false;

        let container = $("#header .sub-nav");
        if (container.has(event.target).length === 0){}
        container.hide();

    });


    /**
     * Click on link sub-nav
     */
    eventClick('.sub-nav a:not(.not-ajax):not([target=_blank])',function (elem) {

        if( $(elem).hasClass('open-sub-nav') )
            return false;

        let container = $("#header .sub-nav");
        if (container.has(elem.target).length === 0)
            container.hide();


        if( $(elem).closest('[data-change-text="true"]').length ){

            let text = $(elem).find('span.navigation-text').text();
            let icon = $(elem).find('span.navigation-text').prev('i');

            let parent = $(elem).closest('[data-change-text="true"]');

            parent.find('a:first span.navigation-text').text(text);

            if( icon.length && parent.find('a:first span.navigation-text').prev('i').length ){

                parent.find('a:first span.navigation-text').prev('i').attr('class', icon.attr('class'));

            }

        }


    });


    /**
     * Active nav
     */
    eventClick('.nav a:not(.not-ajax):not([target=_blank])',function (elem) {

        if( $(elem).hasClass('open-sub-nav') || $(elem).attr('data-offcanvas-href') || $(elem).attr('data-modal-href') )
            return false;

        $('.nav').find('a.active').removeClass('active');
        $('.nav').find('li.active').removeClass('active');

        if( $(elem).closest('.sub-nav').length )
            $(elem).closest('.sub-nav').closest('li').find('a.open-sub-nav:first').addClass('active');


        $(elem).addClass('active');

        if( $('#sidebar-nav').length )
            $('#sidebar-nav').removeClass('hover');

        let parent_li = $(elem).parents('li');
        parent_li = parent_li[parent_li.length - 1];
        $(parent_li).addClass('active');

        if( $(parent_li).closest('#header') )
            $(parent_li).find('a:first').addClass('active');

    });


    /**
     * Если есть sidebar
     */
    if( $('#sidebar-nav').length ){

        eventMouseEnter('#sidebar-nav', function (elem) {
            $(elem).addClass('hover');
        });

        eventMouseLeave('#sidebar-nav', function (elem) {
            $(elem).removeClass('hover');
        });

    }


    /**
     * Click Logout
     */
    eventClick('a[href="#logout"]', function () {

        $('body').append('<form id="logout-form" class="hide" action="' + route('logout') + '" method="POST"><input type="hidden" name="_token" value="' + getCSRF() + '"></form>');
        $('#logout-form').submit();

    });


    /**
     * Запушен ли таймер проверки видимости элемента для загрузки контента
     */
    window.scroll_auto_load_timer_enable = false;



    /**
     * Включить авто подгруздку сущностей при скролле
     * Необходимо оставлять блок пагинации и атрибут для включения scroll загрузки приписывать в родительский блок пагинации
     */
    window.scrollAutoLoadEnable = function (){

        let count_enabled = 0;

        $('[data-scroll-load]').each(function (index, elem) {

            if( !$(elem).hasClass('data-scroll-load-enabled') ){

                count_enabled++;
                $(elem).addClass('data-scroll-load-enabled');

                $(elem).find('.pagination-block').before(
                    '<div class="scroll-loading-js-mark"></div>' +
                    '<div class="scroll-loading">' +
                        '<div class="spinner-border text-primary" role="status">' +
                            '<span class="visually-hidden">Загрузка...</span>' +
                        '</div>' +
                    '</div>');

            }


        });

        if( count_enabled > 0 && !scroll_auto_load_timer_enable ){

            window.scroll_auto_load_timer_enable = true;

            //Запускаем проверку именно через timer т.к. событие скролла не отлавливается в модальных окнах и др блоках
            setInterval(function (){

                checkScrollAutoLoad();

            }, 300);

        }

    };


    /**
     * Стандартная функция загрузки (если явно не указан аттрибут data-scroll-load-success-func)
     */
    window.defaultScrollAutoLoadFunc = function ( response_html, scroll_load_elem ){

        //Сперва подготавливаем HTML, чтобы выборку нужных данных делать, без подготовки не работает нормально

        response_html = '<div>' + response_html + '</div>';

        let elements_html = '';
        $(response_html).find('[data-scroll-load]').each(function(index, elem){

            elements_html += '<div' + $(elem).html() + '</div>';

        });


        let html = false;

        //Сперва получаемся получить тип модели (чтобы найти нужную таблицу, если их несколько на странице)
        let data_tr_table = $(scroll_load_elem).find('[data-tr-type]').attr('data-tr-type');

        //Если тип указан
        if( typeof data_tr_table !== 'undefined' )
            html = $(elements_html).find('table[data-tr-type="' + data_tr_table + '"]:first').find('tbody').html();


        if( !html )
            html = $(elements_html).find('table:first').find('tbody').html();


        $(scroll_load_elem).find('table:first').find('tbody').append(html);

    };



    /**
     * Event Scroll до конца страницы - подгружаем информацию для объектов, где включен auto scroll
     */
    window.checkScrollAutoLoad = function (){

        $('.data-scroll-load-enabled').each(function (index, elem){

            if( !$(elem).find('.scroll-loading-js-mark').length )
                return;

            //Проверяем, находиться ли конец таблицы в зоне видимости и нам нужно подгружать уже
            let coordinates = $(elem).find('.scroll-loading-js-mark')[0].getBoundingClientRect();
            if( $(window).height() + 200 <= coordinates.top || coordinates.top < 0 )
                return;


            let scroll_loading = $(elem).find('.scroll-loading');

            if( scroll_loading.hasClass('loading-now') || scroll_loading.hasClass('loading-finished') )
                return;

            scroll_loading.addClass('loading-now');

            let paginate_block = $(elem).find('.pagination-block');
            let current_page_number = parseInt(paginate_block.attr('data-current-page'));
            let next_page_number = current_page_number + 1;
            let last_page_number = parseInt(paginate_block.attr('data-last-page'));
            let load_url = paginate_block.attr('data-next-page-url');
            let page_name = paginate_block.attr('data-page-name');

            let success_func = $(elem).attr('data-scroll-load-success-func'); //Должна быть объявлена как глобальная в объекте window

            if( typeof success_func === 'undefined' )
                success_func = 'defaultScrollAutoLoadFunc';

            request('GET', load_url, function (response){

                scroll_loading.removeClass('loading-now');
                paginate_block.attr('data-current-page', next_page_number);

                //Если это была загружена последняя страница
                if( next_page_number === last_page_number ) {

                    scroll_loading.addClass('loading-finished');


                //Была загружена не последняя страница
                }else{

                    let new_load_url = load_url.replace('?' + page_name + '=' + next_page_number, '?' + page_name + '=' + (next_page_number + 1) );
                    new_load_url = new_load_url.replace('&' + page_name + '=' + next_page_number, '&' + page_name + '=' + (next_page_number + 1) );
                    paginate_block.attr('data-next-page-url', new_load_url);

                }


                //Запускаем функцию обработки загруженного контента
                window[success_func](response, elem);



            }, function (error,i,code){

                scroll_loading.removeClass('loading-now');
                showError(error,code);

            });

        });

    }




    /**
     * Btn Action Request (эта функция запускается при клике на .btn-action)
     */
    function btnActionRequest( elem ) {

        pageLoadingStart();

        request(
            $(elem).attr('data-action-method') ?? 'POST',
            $(elem).attr('data-action') ?? $(elem).attr('href'),
            {
                value: $(elem).attr('data-value') ?? null
            },
            function (response) {

                pageLoadingEnd();

                //Если указано событие которое нужно запустить
                if( $(elem).attr('data-success-event') )
                    eventDispatch( $(elem).attr('data-success-event'), $(elem).attr('data-value') ?? response );


                //Если указано, что нужно отчистить пункт меню (кол-во уведомлений) - отчищаем
                if( $(elem).attr('data-clear-navigation') )
                    clearNavigationCountItem($(elem).attr('data-clear-navigation'));


                //Если указано, что нужно изменить кол-во уведомлений в навигации
                if( $(elem).attr('data-change-navigation-key') )
                    changeNavigationCountItem($(elem).attr('data-change-navigation-key'), $(elem).attr('data-change-navigation-count') ?? -1);


                //Если нужно - показываем SweetAlert
                if( $(elem).attr('data-success-text') )
                    swalSuccess($(elem).attr('data-success-text'));


                //Если нужно - показываем SweetAlert response text
                if( $(elem)[0].hasAttribute('data-show-response-swal') )
                    swalSuccess(response);


                //Если нужно - показываем toastr response text
                if( $(elem)[0].hasAttribute('data-show-response-toastr') )
                    toastr(response, 'success');


                //Если нужно - показываем modal response text
                if( $(elem)[0].hasAttribute('data-show-response-modal') )
                    showModalWithContent(response);


                //Если нужно - показываем offcanvas response text
                if( $(elem)[0].hasAttribute('data-show-response-offcanvas') )
                    showOffcanvasWithContent(response);


                //Если нужно - скрываем кнопку
                if( $(elem)[0].hasAttribute('data-success-btn-hide') )
                    $(elem).hide();


                //Если нам не нужно оставить кнопку скрытой - убираем disabled
                if( !$(elem)[0].hasAttribute('data-success-btn-disabled') )
                    $(elem).removeAttr('disabled');



                //Если нужно делаем редирект или обновляем страницу
                if( $(elem).attr('data-success-redirect') ){

                    redirect($(elem).attr('data-success-redirect'));


                }else if( $(elem)[0].hasAttribute('data-success-refresh-page') ){

                    refreshPage();

                }



            },
            function (error,code) {

                pageLoadingEnd();
                $(elem).removeAttr('disabled');

                let error_msg = getErrorMessage( error, code );

                if( $(elem).attr('data-show-error-as-alert') || $(elem)[0].hasAttribute('data-show-error-as-alert') ){

                    alert(error_msg);


                }else if( $(elem).attr('data-show-error-as-toastr') || $(elem)[0].hasAttribute('data-show-error-as-toastr') ){

                    toastr(error_msg, 'error');



                }else if( $(elem).attr('data-show-error-as-modal') || $(elem)[0].hasAttribute('data-show-error-as-modal') ){

                    showModalWithContent(error_msg, getAttrList(elem));



                }else if( $(elem).attr('data-show-error-as-offcanvas') || $(elem)[0].hasAttribute('data-show-error-as-offcanvas') ){

                    showOffcanvasWithContent(error_msg, getAttrList(elem));



                }else{

                    swalError( error_msg );

                }

            }
        );


    }


    /**
     *
     *  Кнопка действия (можно быстро повесить ajax обработчик какого-либо действия на кнопку)
     *
     */
    eventClick('.btn-action', function (elem) {

        if( $(elem).attr('disabled') )
            return false;

        $(elem).attr('disabled', true);

        let params = {};

        if( $(elem).attr('data-confirm-text') )
            params.title = $(elem).attr('data-confirm-text');

        if( $(elem).attr('data-confirm-btn-text') )
            params.confirmButtonText = $(elem).attr('data-confirm-btn-text');

        if( $(elem).attr('data-cancel-btn-text') )
            params.cancelButtonText = $(elem).attr('data-cancel-btn-text');

        if( $(elem).attr('data-confirm-btn-color') )
            params.confirmButtonColor = $(elem).attr('data-cancel-btn-text');

        if( $(elem)[0].hasAttribute('data-confirm') ){

            swalConfirm(params, function(){

                btnActionRequest(elem);

            }, function(){

                $(elem).removeAttr('disabled');

            });

        }else{

            btnActionRequest(elem);

        }

    });




    /**
     * Проверить права доступа (есть ли у роли пользователя доступ)
     */
    window.checkAccess = function( access_key ){

        return window.acceses.indexOf(access_key) !== -1;

    }


    /**
     * Перебрать все элементы с [data-access] и показать те, к которым есть доступ. Удалить те к которым нет доступа
     */
    window.setDataAccessElements = function(){

        $('[data-access]').each(function(i, elem){

            if( checkAccess($(elem).attr('data-access')) ){

                $(elem).removeAttr('data-access');

            }else{

                $(elem).remove();

            }

        });

    }


    /**
     * Скопировать текст в буфер обмена при клике на элемент
     */
    eventClick('[data-copy-text]', function(e){

        if( copyTextToClipboard($(e).text()) )
            showSuccess('Успешно скопировано.');

    });



    /**
     * Направляют мышку на div.input
     */
    eventMouseEnter('.input', function(e) {

        $(e).addClass('hover');

    });



    /**
     * Убирают мышку на div.input
     */
    eventMouseLeave('.input', function(e) {

        $(e).removeClass('hover');

    });


    /**
     * Проставить css class search-by-id если значение начинается на # в input seach
     */
    window.setInputSearchCssClassById = function(){

        $('input[name="search"]').each(function(i, e){

            if( $(e).val().startsWith('#') && $(e).val().substring(1).match(/^\d+$/) ){

                $(e).addClass('search-by-id');

            }else{

                $(e).removeClass('search-by-id');

            }

        });

    };



    /**
     * Изменяют search input
     */
    eventChangeWithKeyPress('input[name="search"]', function() {

        setInputSearchCssClassById();

    });



    /**
     * Отчистить кол-во уведомлений в навигации
     */
    window.clearNavigationCountItem = function( count_key ) {

        $('[data-navigation-count-key="' + count_key + '"]').each(function (i, e) {

            $(e).removeClass('active').text('0');

        });

    }



    /**
     * Прибавить/убрать кол-во уведомлений в навигации
     * если отрицательный change_count - то будет отниматься кол-во, если положительный - прибавляться
     */
    window.changeNavigationCountItem = function( count_key, change_count = -1 ) {

        change_count = parseInt( change_count );
        if( change_count === 0 || typeof change_count === 'undefined' )
            return false;

        let current_count = parseInt($('[data-navigation-count-key="' + count_key + '"]:first').text());
        if( typeof current_count === 'undefined' )
            current_count = 0;


        let set_count;

        if( change_count < 0 ){

            set_count = current_count - Math.abs(change_count);

        }else{

            set_count = current_count + change_count;

        }

        $('[data-navigation-count-key="' + count_key + '"]').each(function (i, e) {

            if (set_count > 0) {

                $(e).addClass('active').text(set_count);

            } else {

                $(e).removeClass('active').text('0');

            }

        });

    }


    /**
     * Устанавливает padding-right для header (это нужно во время открытия модальных окон, чтобы header не прыгал)
     */
    window.setPaddingRightHeader = function(){

        $('#header').css('padding-right', getScrollBrowserWidth() + 'px');

    }

    /**
     * Отчищает padding-right для header (это нужно во время закрытия модальных окон, чтобы header не прыгал)
     */
    window.clearPaddingRightHeader = function(){

        $('#header').css('padding-right', '0');

    }


    /**
     * Action кнопки, которые прикрепляются к низу экрана, если контент большой.
     * Приписываем CSS class actions-fixed к .page-footer или box-footer
     */
    window.actionsFixedInit = function(){

        $('.actions-fixed').each(function(index, elem){

            let parent = $(elem).closest('.modal-page-content');

            if( !parent.length )
                parent = $(elem).closest('.container');

            if( !parent.length )
                parent = $('#app');


            //Если контент помещается в экран
            if( window.outerHeight > parent.outerHeight() ){

                $(elem).removeClass('actions-fixed').removeClass('actions-fixed-init');

            }else{

                setTimeout(function(){

                    let left = parent.offset().left - parseInt(parent.parent().css('padding-left'));

                    $(elem).addClass('actions-fixed-init');
                    $(elem).css('left', left);
                    $(elem).css('width', parent.parent().outerWidth());

                    parent.css('padding-bottom', $(elem).height() + 20);

                }, 300);

            }

        });

    }



    /**
     * Получить текущую тему
     */
    window.getCurrentTheme = function (){

        return $('body').attr('data-bs-theme') ?? 'light';

    }


    /**
     * Установить тему
     */
    window.setUpTheme = function ( theme_key ){

        let theme = window.themes[theme_key];

        if( typeof theme === 'undefined' )
            return toastr('Тема не найдена: ' + theme_key, 'error');

        if( getCurrentTheme() === theme_key )
            return false;

        $('link[data-them-css]').remove();

        $('head').append('<link href="'+ theme.css_path +'" data-them-css="'+ theme_key +'" rel="stylesheet">')
        $('body').attr('data-bs-theme', theme_key);

        localStorage.setItem('current_theme', theme_key); //Local Storage для frontend
        document.cookie = "current_theme; path=/; max-age=-1"; //Сперва отчищаем
        document.cookie = "current_theme="+ theme_key +"; path=/; max-age=999999999"; //Cookie для backend

        if( $('[data-theme-choice] input').length )
            $('[data-theme-choice] input[value="'+ theme_key +'"]').attr('checked', true);


    }


    /**
     * Установить тему из настроек системы, если это необходимо
     */
    window.setUpThemeFromSystemSettings = function ( theme_key ){

        if( typeof window.theme_can_change === 'undefined' || !window.theme_can_change )
            return false;

        //Если в local storage прописана тема, то мы не должны устанавливать тему из настроек системы
        //Но нам нужно проверить, если тема в local storage отличается от той, что сейчас включена, то нам нужно переключить тему
        if( localStorage.getItem('current_theme') ) {

            if( getCurrentTheme() === localStorage.getItem('current_theme') )
                return false;

        }

        setUpTheme(theme_key);

    }


    /**
     * Устанавливаем тему из настроек
     */
    const darkThemeMq = window.matchMedia("(prefers-color-scheme: dark)");
    if (darkThemeMq.matches) {
        setUpThemeFromSystemSettings('dark');
    } else {
        setUpThemeFromSystemSettings('light');
    }
        darkThemeMq.addListener(e => {
            if (e.matches) {
                setUpThemeFromSystemSettings('dark');
            } else {
                setUpThemeFromSystemSettings('light');
            }
        });


    /**
     * Устанавливаем тему переключателями
     */
    eventChange('[data-theme-choice] input', function (e){

        setUpTheme($(e).val());

    });


    /**
     * Добавить управление компонентом (по клику какой кнопки, куда и какой компонент будет загружаться, а так же по какой кнопке удаляется)
     * @param block_elements - класс/id блока, куда нужно подгрузить компонент, например: .items-list
     * @param blade_template_to_load - название blade шаблона, который подгружаем, без .blade.php на конце, например: admin.components.item
     * @param btn_add_element - класс/id кнопки, например: .add-btn
     * @param btn_remove_element - класс/id кнопки, например: .remove-btn
     * @param remove_closest_element - класс/id ближайшего элемента, который будет удаляться по кнопке удаления, например: .item
     */
    window.addControlComponent = function ( block_elements, blade_template_to_load, btn_add_element, btn_remove_element, remove_closest_element, added_callback, removed_callback ){

        eventClick(btn_add_element, function(){

            pageLoadingStart();

            loadHTMLView(blade_template_to_load, function (html){

                pageLoadingEnd();
                $(block_elements).append(html);

                let input = $(block_elements).find(remove_closest_element + ':last').find('input:visible, textarea:visible, select:visible');

                if( input.length ){

                    input = $(input[0]);

                    setTimeout(function (){

                        let current_val = input.val();
                        input.val('')
                        input.focus();
                        input.val( current_val );

                    }, 100);

                }

                if( typeof added_callback == 'function' )
                    added_callback(html);

            });

        }, true);

        eventClick(btn_remove_element, function (e){

            let elem_to_remove = $(e).closest(remove_closest_element);

            if( elem_to_remove.length ){

                let html = elem_to_remove[0].outerHTML;

                elem_to_remove.remove();

                if( typeof removed_callback == 'function' )
                    removed_callback(html);

            }

        }, true);

    }
