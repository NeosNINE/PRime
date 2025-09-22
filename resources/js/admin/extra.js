

    /**
     * Обновить информацию (подтягивается с backend)
     */
    window.refreshInfoFromBackend = function(){


        //Параметры, которые передаем на сервер
        let request_data = {
            'last_client_event_id' : window.last_client_event_id
        };


        //Выполняем запрос
        request('GET', route('admin.get_information_for_client'), request_data, function (response, textStatus, xhr) {

            if( checkAjaxRedirects(response) )
                return false;


            //Обновляем CSRF token
            if( response.csrf_token )
                setCSRF(response.csrf_token);


            //Проставляем последний ID события для клиента
            if( response.last_client_event_id )
                window.last_client_event_id = response.last_client_event_id;


            //Перебираем и запускаем все события
            if( response.new_client_events ) {

                response.new_client_events.forEach(function (event) {

                    if (checkIfCanEventDispatch(event.event_name, event.data))
                        eventDispatch(event.event_name, event.data, false);

                });

            }



            //Проставляем navigation count в навигацию
            if( response.navigation_counts ) {

                for (let count_key in response.navigation_counts) {

                    let count = response.navigation_counts[count_key];

                    $('[data-navigation-count-key="' + count_key + '"]').each(function (i, e) {

                        if (count > 0) {

                            $(e).addClass('active').text(count);

                        } else {

                            $(e).removeClass('active').text('0');

                        }

                    });

                }

            }


            //Если авторизован за другого юзера
            if( response.isImpersonating ){

                //Здесь запускаем именно авторизацию за админа, т.к. этот код выполниться только если за админа авторизован. Если за обычного юзера - то ошибка 420 будет
                impersonateAnotherAdminStart();

            }else{

                //Убираем окно входа за другого пользователя. Иногда это окно может быть открыто и его нужно убрать в этот момент
                impersonateEnd();

            }




        //Что-то пошло не так
        }, function (error, code) {

            //Если это ошибка авторизации за другого юзера
            if( error.status === 420 ){

                impersonateStart();

            }else{

                console.error(code + " : " + error);

            }

        });

    }



    /**
     * Каждые 3 секунды получаем информацию с backend
     */
    setInterval(function (){

        refreshInfoFromBackend();

    },3000);


    /**
     * Загрузить шаблон Blade
     */
    window.loadHTMLView = function( blade_key, data = {}, callback = null ){

        if( callback == null ) {
            callback = data;
            data = {};
        }

        data.blade_key = blade_key;

        request('GET', route('admin.load_html_view'), data, function (html){

            let promise = new Promise((resolve, reject) => {

                resolve(
                    callback === null ? true : callback(html)
                );

            });

            promise.then(function (){
               pageInit();
            });

        });

    }
