
    /**
        Focus
    */
   eventFocus('input,select,textarea',function (elem) {

       if( elem.hasClass('select2-search__field') )
           elem = elem.closest('.select2').prev('.select2');

       if( elem[0].hasAttribute('readonly') || elem[0].hasAttribute('disabled') )
           return;

       let div = $(elem).closest('div.input');
       if( div.length > 0 ) {
           if( !div.attr('data-always-active') ){

               div.addClass('focus');

           }
       }

   });


   /**
        Focus Out
    */
   eventFocusOut('input,select,textarea', function (elem) {

       if( elem.hasClass('select2-search__field') ) {

           elem = elem.closest('.select2').prev('.select2');

           if( !elem.length )
               elem = $('.input.focus:first').find('.select2:first');

       }

       inputChangeActiveState(elem);

   });


   /**
        Input Change
    */
   eventChange('input,select,textarea', function (elem) {

       inputChangeActiveState(elem);


   });


   /**
    * Проставляем правильно активный input или нет
    */
   window.inputChangeActiveState = function (elem) {

       let input = $(elem);
       let timeout = 0;

       if( input.hasClass('select2') )
           timeout = 50;

       setTimeout(function () {

           const siblings = input.siblings('[type=' + input.attr('type') + ']');
           let siblings_empty = true;
           siblings.each(function(){
               if( $(this).val() !== '' && $(this).val().length > 0 ){
                   siblings_empty = false;
                   return false;
               }
           });

           let div = input.closest('div.input');

           if( input.hasClass('select2-search__field') )
               return;

           if( div.length > 0 ) {

               if( !div.attr('data-always-active') ){

                   let select2_container = input.next('.select2-container');

                   if( select2_container.length > 0 ){

                       if( !select2_container.hasClass('select2-container--focus') && !select2_container.hasClass('select2-container--open') )
                           div.removeClass('focus');

                   }else{

                       div.removeClass('focus');

                   }

                   if( input.hasClass('select2-search__field') )
                       input = div.find('select.select2');


                   if( input.is('select') ){

                       if( input.attr('disabled') )
                           input.find('option').prop('disabled', false);

                   }

                   let options_has_empty_value = false;
                   if( input.find('option[value=""]').length )
                       options_has_empty_value = true;

                   if( (input.val() === '' || input.val() === null || input.val().length < 1) && siblings_empty && !options_has_empty_value ){

                       div.addClass('no-active');

                   } else {

                       div.removeClass('no-active');

                   }

               }

           }

       }, timeout);


   }


    /**
     * Select2 setUP
     */
    window.select2Setup = function (){

        let elements = $('select.select2:visible').not('.select2-init').addClass('select2-init');

        elements.each(function(){

            let select = $(this);

            let dropdown_parent = $('body');
            if( select.closest('.modal-page-content').length )
                dropdown_parent = select.closest('.modal-page-content');

            let tags = false;
            if( $(this)[0].hasAttribute('data-tags') )
                tags = true;

            if( select.is('[data-search-model]') ){

                if( !select.attr('data-search-template-result') )
                    showError('You should specify attr data-search-template-result on html elem.');

                let templateForResult = '<div>' + select.attr('data-search-template-result') + '</div>';
                let templateResultItems = templateForResult.match(/(item\.[\w.]+)/gm);

                let templateForSelected = select.attr('data-search-template-selected') ?? select.attr('data-search-template-result');
                let templateSelectedItems = templateForSelected.match(/(item\.[\w.]+)/gm);

                let columns = select.is('[data-search-columns]') ? select.attr('data-search-columns').split(' ') : null; //По каким полям будет идти поиск, по умолчанию по всем полям

                let field_to_option_value = select.is('[data-search-field]') ? select.attr('data-search-field') : 'id'; //Какое поле сущности выводить в option value

                select.select2({
                    tags: tags,
                    language: window.select2_ru,
                    dropdownParent: dropdown_parent,
                    ajax: {
                        url: route('select2.search'),
                        dataType: 'json',
                        cache: true,
                        data: function(params){
                            return {
                                search: params.term || '',
                                page: params.page || 1,
                                columns: columns,
                                model: select.attr('data-search-model'),
                                with_relations: select.attr('data-search-with-relations') //Через запятую связанные модели, которые подгрузить
                            };
                        },
                        processResults: function(data){

                            let items = data.results.data;

                            if( field_to_option_value !== 'id' ){

                                items = $.map(items, function (item) {
                                    item.id = item[field_to_option_value];
                                    return item;
                                });

                            }


                            return {
                                results: items,
                                pagination: {
                                    more: data.results.next_page_url
                                }
                            }
                        },
                        error: function (error,i,code) {

                            if( code !== 'abort' )
                                showError(error,code);

                        }
                    },
                    templateResult: select2OutputFormat(templateForResult, templateResultItems),
                    templateSelection: select2OutputFormat(templateForSelected, templateSelectedItems), //Чтобы нормально работал шаблон для выбранных элементов, изначально не должно быть прописано <option> в <select> без selected
                });

            }else{

                select.select2({
                    tags: tags,
                    language: window.select2_ru,
                    minimumResultsForSearch: 10,
                    dropdownParent: dropdown_parent
                });

            }


        });

        elements.on("select2:select", function (e){

            let div = $(this).closest('div.input');

            if( !div.length )
                div = $('.input.focus:first');

            if( e.params.data.text || $(this).data('ajax-url') ){

                div.removeClass('no-active');

            }else{

                div.addClass('no-active').removeClass('focus');

            }

        });

        elements.on('select2:open', (e) => {

            $(".select2-search__field" ).attr( 'autocomplete', 'new-password' );

            setTimeout(function (){

                if( $('.select2-container--open .select2-search__field').length )
                    $('.select2-container--open .select2-search__field')[0].focus();

            }, 200);

        });


        $(".select2-search__field" ).attr( 'autocomplete', 'new-password' );

    };


    /**
     * Принудительно открываем select2, для того чтобы работало по клику на label
     */
    eventFocus('select.select2-init', function (elem){

        elem.select2('open');

    });
        eventKeyUp(function (event){

            //keyup tab
            if( event.which === 9 ){

                setTimeout(function(){

                    let elem = $('.input.focus');

                    if( !elem.length )
                        return;

                    let select2_container = elem.find('.select2-container');
                    if( select2_container.length > 0 && !select2_container.hasClass('select2-container--open') && select2_container.prev('.select2').closest('.input').hasClass('focus') )
                        select2_container.prev('.select2.select2-init').select2('open');


                }, 100);

            }

        });



   /**
    * Формат вывода данных в select2 ajax search
    */
   function select2OutputFormat( template, templateItems ){

       return function(item){

           if( item.loading )
               return item.text;

           if( item.selected )
               return $.parseHTML(item.text);

           if( item.title === 'preloaded' )
               return item.text;

           if( !template.length )
               return 'Missing result template';

           let result = template;
           let fn = result.matchAll(/_(?<functionName>\w+)\s*\((?<functionArguments>(?:[^()]+)*)?\s*\)/g); //Есть ли функция в шаблоне (прописывается сперва нижнее подчеркивание, например: _langText(item.name)

           let val = item;
           let token_parts;
           let to_replace;

           //Если в шаблоне есть какие-то функции, например: _langText(item.name)
           if( fn ){

               for( let match of fn ) {

                   let fn_name = match.groups.functionName;
                   let fn_args = match.groups.functionArguments;

                   val = item;

                   token_parts = fn_args.split('.');
                   token_parts.forEach(function(token_part, index){

                       if( token_part !== 'item' && index !== 0 )
                           val = val[token_part];

                   });

                   to_replace = '_' + fn_name + '(' + fn_args + ')';
                   val = window[fn_name](val);

                   result = result.replace(to_replace, val);

               }

           }

           if( templateItems ) {

               templateItems.forEach(function (token) {

                   val = item;

                   token_parts = token.split('.');
                   token_parts.forEach(function (token_part, index) {

                       if (token_part !== 'item' && index !== 0)
                           val = val[token_part];

                   });

                   if (val === undefined)
                       val = 'ID: ' + item['id'];

                   result = result.replace(token, val);

               });

           }

           return $.parseHTML(result);

       }

   }


    /**
     * Проставляем active только к нужным элементам + делаем элементы readonly, если есть необходимость
     */
    window.activeInputSetup = function (){

        $('div.input').find('input,select,textarea').each(function (key,item){

            let input = $(item);
            let div = input.closest('div.input');

            const siblings = input.siblings('[type=' + input.attr('type') + ']');
            let siblings_empty = true;
            siblings.each(function(){
                if( $(this).val() !== '' && $(this).val().length > 0 ){
                    siblings_empty = false;
                    return false;
                }
            });

            if( div.hasClass('wysiwyg-editor') )
                div.attr('data-always-active',true);

            if( div.attr('data-input-setup') === 'active' )
                return;

            if( !div.attr('data-always-active') ) {

                if( input.hasClass('select2-search__field') )
                    input = div.find('select.select2');


                if( input.is('select') ){

                    if( input.attr('disabled') ){

                        input.find('option').prop('disabled', false);

                    }

                }

                let options_has_empty_value = false;
                if( input.find('option[value=""]').length )
                    options_has_empty_value = true;

                if( (input.val() === '' || input.val() === null || input.val() && input.val().length < 1) && siblings_empty && !options_has_empty_value ){

                    div.addClass('no-active');

                }else{

                    div.removeClass('no-active');
                    div.attr('data-input-setup','active');

                }

            }

        });


        let inputs_only_read = $('.inputs-only-read');
        inputs_only_read.find('input, textarea').addClass('only-read').attr('readonly', 'true');
        inputs_only_read.find('input[type="radio"], input[type="checkbox"]').attr('disabled', 'true');
        inputs_only_read.find('select').addClass('only-read').attr('disabled', 'true');
        inputs_only_read.find('[data-only-read-hidden]').hide();

    };


    /**
     * Переключаем языки
     */
    eventClick('.language-choose [data-lang]',function (elem){

        let parent = $(elem).closest('.language-choose');
        parent.find('[data-lang]').addClass('no-active').removeClass('active');
        $(elem).removeClass('no-active').addClass('active');

        let elem_to_show, elem_to_hide;
        if( parent.next().hasClass('block-languages') ){

            elem_to_hide = parent.next().children('[data-input-lang]');
            elem_to_show = parent.next().children('[data-input-lang="'+$(elem).attr('data-lang')+'"]');

        }else{

            elem_to_hide = parent.next().find('[data-input-lang]');
            elem_to_show = parent.next().find('[data-input-lang="'+$(elem).attr('data-lang')+'"]');

        }

        elem_to_hide.hide();

        if( elem_to_show.attr('data-input-lang-flex') === 'true' ){

            elem_to_show.css('display', 'flex');

        }else{
            elem_to_show.show();
        }

        iframeResize();

    });
