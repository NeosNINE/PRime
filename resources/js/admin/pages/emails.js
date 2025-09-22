
    /**
     * Устанавливаем стандартные события
     */
    setDefaultObjectEvents('email', 'emails', '.emails-table');


    /**
     * Событие ошибки отправки письма
     */
    setEventListener('email.error', function(event, email) {

        $('.btn-resend-emails-all').show();
        objectTableRowUpdate( $('.emails-table tr[data-id="'+ email.id +'"]'), email.html_table_row );

    });


    /**
     * Повторная отправка Email сообщения
     */
    eventClick('.btn-resend-email', function(e){

        request('POST', $(e).attr('data-action'), function( response ){


            if( response.status === 'success' ){

                eventDispatch('email.edit', response);
                swalSuccess('Сообщение успешно отправлено');

            }else{

                eventDispatch('email.error', response);

                if( response.data && response.data.hasOwnProperty('error_msg') ){

                    swalError( response.data.error_msg );

                }else{

                    swalError('Что-то пошло не так');

                }

            }

        });

    });
