
    ace.config.set("basePath", "/assets/libs/ace/");

    /**
     *
     *  Установка Ace JS (редактор кода)
     *
     */
    window.aceJsSetup = function () {

        let id;
        let i = 0;
        let editor = {};
        let elements = $('[data-ace]').not('.ace-init');
        elements.each(function () {

            //Если родитель default_item - пропускам
            if( $(this).closest('.default_item').length ){
                return;
            }

            i++;

            $(this).addClass('ace-init');

            id = 'aceJS-' + i + '-' + Math.random().toString(36).replace(/[^a-z]+/g, '');
            $(this).attr('id',id);

            $(this).after('<input type="hidden" name="'+$(this).attr('name')+'">');
            $(this).next('input').attr('id',id+'-input');

            let mode = "ace/mode/html";
            if( $(this).attr('data-ace-mode') )
                mode = "ace/mode/" + $(this).attr('data-ace-mode');

            editor[i] = ace.edit(id, {
                mode: mode,
                selectionStyle: "text",
                autoScrollEditorIntoView: true,
                minLines:1,
                maxLines:2000
            });

            $('#'+id+'-input').val(editor[i].getValue());

            if( $(this).attr('disabled') || $(this).attr('readonly') ){

                editor[i].setOptions({
                    readOnly: true,
                    highlightActiveLine: false,
                    highlightGutterLine: false
                })

            }else{

                editor[i].on('change', function(this_event, this_editor){
                    $('#'+ $(this_editor.container).attr('id') +'-input').val(this_editor.getValue());
                });

            }


        });

    };
