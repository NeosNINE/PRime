
    /**
     * Всплывающее сообщение
     */
    window.toastr = function ( msg, style = 'info', live_time = 3000, max_count = 3 ) {

        if( !$('#window-toasts').length )
            $('body').append('<div id="window-toasts" class="toast-container position-fixed bottom-0 end-0 p-3"></div>');

        let id = 'toast-' + _.uniqueId();

        let btn_close = '';

        //Если время жизни больше 6 сек, то кнопку закрытия показываем
        if( live_time > 5000 )
            btn_close = '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>';

        let html = '<div id="' + id + '" class="toast toast-' + style + '" data-bs-delay="' + live_time + '" role="alert" aria-live="assertive" aria-atomic="true">' +
                '<div class="d-flex">' +
                    '<div class="toast-body">' +
                        msg +
                    '</div>' +
                    btn_close +
                '</div>' +
            '</div>'

        $('#window-toasts').append(html);


        let toast_elem = document.getElementById(id);
        toast_elem.addEventListener('hidden.bs.toast', () => {
            $('#'+id).remove();
        });

        let toast = new bootstrap.Toast(toast_elem);
        toast.show();


        //Если больше 4 toast элементов - то тогда убираем первые, чтобы сильно не "засорять" экран
        if( $('#window-toasts .toast').length > max_count )
            $('#window-toasts .toast:first').remove();

    }


    /**
     * Отчистить все toasts
     */
    window.toastrClear = function (){

        $('#window-toasts').html('');

    }
