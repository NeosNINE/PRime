

    /**
     * SweetAlert Confirm
     */
    window.swalConfirm = function( params, confirmed_func, canceled_func ){

        //Если параметры не указаны
        if( typeof params === 'function' || typeof params === 'undefined' ){
            canceled_func = confirmed_func;
            confirmed_func = params;
            params = {};
        }

        if( typeof params.text === 'undefined' )
            params.text = false;

        if( typeof params.icon === 'undefined' )
            params.icon = 'question';

        if( typeof params.showCancelButton === 'undefined' )
            params.showCancelButton = true;

        if( typeof params.reverseButtons === 'undefined' )
            params.reverseButtons = true;

        if( typeof params.title === 'undefined' )
            params.title = 'Вы уверены?';

        if( typeof params.confirmButtonText === 'undefined' )
            params.confirmButtonText = 'Подтвердить';

        if( typeof params.cancelButtonText === 'undefined' )
            params.cancelButtonText = 'Отмена';

        if( typeof params.confirmButtonColor === 'undefined' )
            params.confirmButtonColor = '#007FE9';

        params = swalPrepareParams( params );

        swal.fire(params).then((result) => {

            //Если подтвердил действие
            if (result.value) {

                if( typeof confirmed_func == 'function')
                    confirmed_func();


            //Если нажал отмена
            }else{

                if( typeof canceled_func == 'function')
                    canceled_func();

            }


        });

        tooltipsClear();

    }


    /**
     * Показать сообщение SUCCESS. Если timer = true - то сообщение само исчезает, если false - то нужно нажать кнопку "ОК", чтобы оно исчезло
     */
    window.swalSuccess = function( msg, timer = true ){

        let params = {
            icon: 'success',
            title: msg
        };

        if( timer ){

            params.showConfirmButton = false;
            params.timer = 1500;

        }

        params = swalPrepareParams( params );

        swal.fire(params);

        tooltipsClear();

    }


    /**
     * Показать сообщение INFO. Если timer = true - то сообщение само исчезает, если false - то нужно нажать кнопку "ОК", чтобы оно исчезло
     */
    window.swalInfo = function( msg, timer = true ){

        let params = {
            icon: 'info',
            title: msg
        };

        if( timer ){

            params.showConfirmButton = false;
            params.timer = 1500;

        }

        params = swalPrepareParams( params );

        swal.fire(params);

        tooltipsClear();

    }


    /**
     * Показать сообщение ERROR. Если timer = true - то сообщение само исчезает, если false - то нужно нажать кнопку "ОК", чтобы оно исчезло
     * Здесь по умолчанию оставляем timer = false, т.к. чаще всего ожидает такое поведение
     */
    window.swalError = function( msg, timer = false ){

        let params = {
            icon: 'error',
            title: msg
        };

        if( timer ){

            params.showConfirmButton = false;
            params.timer = 1500;

        }

        params = swalPrepareParams( params );

        swal.fire(params);

        tooltipsClear();

    }


    /**
     * Показать сообщение WARNING. Если timer = true - то сообщение само исчезает, если false - то нужно нажать кнопку "ОК", чтобы оно исчезло
     * Здесь по умолчанию оставляем timer = false, т.к. чаще всего ожидает такое поведение
     */
    window.swalWarning = function( msg, timer = false ){

        let params = {
            icon: 'warning',
            title: msg
        };

        if( timer ){

            params.showConfirmButton = false;
            params.timer = 1500;

        }

        params = swalPrepareParams( params );

        swal.fire(params);

        tooltipsClear();

    }


    /**
     * Подготовить параметры для SweetAlert
     */
    function swalPrepareParams( params ){

        params.willOpen = function() {
            setPaddingRightHeader();
        }

        params.willClose = function() {
            clearPaddingRightHeader();
        }

        return params;

    }
