

    /**
     * Клик по какой-то команде Console из списка
     */
    eventClick('[data-console-btn]', function(e){

        document.body.scrollTop = document.documentElement.scrollTop = 0;
        $('[name="console-command"]').val($(e).attr('data-console-btn')).focus();

    });



    /**
     * Обработка вывода логов
     */
    function logAceEnable(){

        let id;
        let i = 0;
        let editor = {};
        let elements = $('[data-logs-ace]').not('.ace-init');
        elements.each(function (){

            i++;

            $(this).addClass('ace-init');

            id = 'aceJS-' + i + '-' + Math.random().toString(36).replace(/[^a-z]+/g, '');
            $(this).attr('id',id);

            editor[i] = ace.edit(id, {
                mode: { path:"ace/mode/php", inline:true },
                selectionStyle: "line",
                autoScrollEditorIntoView: true,
                minLines:1,
                maxLines:2000,
                readOnly: true,
                highlightActiveLine: true,
                highlightGutterLine: false,
                printMarginColumn: false,
                firstLineNumber: parseInt($(this).attr('data-first-line')),
            });

            editor[i].setTheme("ace/theme/crimson_editor");
            editor[i].moveCursorTo(8,0);

        });

    }


    /**
     * Запустить Queue Worker
     */
    eventClick('[data-queue-start]', function (){

        toastr('Queue worker start successfully.', 'success');

        request('POST', route('admin.dev_tools.queue_start'), {}, function (){

        }, function (){

        }, false);

    });


    /**
     * Запустить Artisan команду (form)
     */
    eventSubmit('.console-command-form', function (form){

        let command = form.find('[name="console-command"]').val();

        request('POST', route('admin.dev_tools.console_command_run'), { command: command }, function (response){

            showOffcanvasWithContent(response, { 'data-size':'large'} );

        }, function ( error, i, code ){

            showOffcanvasWithContent( getErrorMessage(error, code), { 'data-size':'large'} );

        });

    });



    /**
     * Запускается каждый раз при загрузке страницы
     */
    initialize(function(){

        logAceEnable();

    });
