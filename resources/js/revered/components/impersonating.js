
    /**
     * Был произведен ВХОД за другого пользователя. Показываем заглушку
     */
    window.impersonateStart = function (){

        if( $('#impersonatingModal').length )
            return false;

        let modal_html =    '<div class="modal fade" id="impersonatingModal" tabindex="-1" aria-labelledby="impersonatingModalLabel" aria-hidden="true">' +
                                '<div class="modal-dialog modal-dialog-centered">' +
                                    '<div class="modal-content">' +
                                        '<div class="modal-body text-center">' +
                                            '<h5>Вы авторизованы за другого пользователя</h5>' +
                                            '<p>Для безопасности в это время панель администратора заблокирована. Чтобы продолжить, Вам необходимо выйти из авторизации другого пользователя.</p>' +
                                            '<button class="btn btn-primary" data-impersonate-end>Завершить авторизацию и разблокировать панель</button>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>';

        $('body').append(modal_html);

        let modal = $('#impersonatingModal');

        window.impersonate_modal = new bootstrap.Modal(modal,{ backdrop: 'static', keyboard: false });

        //Событие открытие окна (запуск пока окна, ещё не видно пользователю)
        modal[0].addEventListener('show.bs.modal', function (e) {

            //Для шапки проставляем padding, чтобы она не прыгала во время открытия модальных окон
            setPaddingRightHeader();

        });

        //Событие закрытия окна
        modal[0].addEventListener('hidden.bs.modal', function (e) {

            //Для шапки проставляем padding, чтобы она не прыгала во время закрытия модальных окон
            clearPaddingRightHeader();

        });

        window.impersonate_modal.show();

    }


    /**
     * Был произведен ВХОД за админа. Показываем окно информации об этом
     */
    window.impersonateAnotherAdminStart = function(){

        if( $('.logged_in_another_admin').length )
            return false;

        //Убираем окно авторизации за юзера, если оно есть
        if( $('#impersonatingModal').length ){
            $('#impersonatingModal').remove();
            window.impersonate_modal.hide();
        }

        let html = '<div class="logged_in_another_admin">' +
                        '<p>Вы авторизованы под другим админом.</p>' +
                        '<button class="btn btn-primary" data-impersonate-end>Завершить авторизацию</button>' +
                    '</div>';

        $('body').append(html);

    }


    /**
     * Был произведен ВЫХОД за другого пользователя. Убираем заглушку
     */
    window.impersonateEnd = function ( doAjax = false ){

        if( !$('#impersonatingModal').length && !$('.logged_in_another_admin').length )
            return false;

        if( doAjax )
            request('POST', route( window.section_type + '.users.leave_impersonation'), {}, false, false, false);

        if( $('#impersonatingModal').length ){
            $('#impersonatingModal').remove();
            window.impersonate_modal.hide();
        }

        //Обновляем страницу
        if( $('.logged_in_another_admin').length ){

            pageLoadingStart();

            $('.logged_in_another_admin').fadeOut();

            setTimeout(function(){
                window.location.reload();
            }, 1000);

        }

    }


    /**
     * Начало авторизации за другого пользователя (клик по кнопке)
     */
    eventClick('[data-impersonate-start]', function (e) {

        impersonateStart();

    }, true, false);



    /**
     * Нажатие на кнопку завершить авторизацию за другого пользователя
     */
    eventClick('[data-impersonate-end]', function (e) {

        impersonateEnd(true);

    });


