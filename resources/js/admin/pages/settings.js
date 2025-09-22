
    /**
     * Проставить index`ы в название inputs, чтобы на бекенде можно было правильно обрабатывать структуру массивов
     */
    function indexesToFileName( elem, field_name ){

        let keys = getLastKeys( elem );

        keys.forEach( function( key ){

            field_name = field_name.replace('-default-index-', key);

        });

        return field_name;
    }


    /**
     * Получить список last_key вместе со всеми родителями
     */
    function getLastKeys( elem, keys = false ){

        if( !keys )
            keys = [];

        let parent = elem.parent().closest('.settings-item');

        if( !parent.length )
            return keys;

        let key = parent.attr('data-key');
        if( typeof key !== 'undefined' ){

            keys.unshift(key);
            keys = getLastKeys( parent, keys );

        }


        return keys;
    }

    /**
     * Посчитать кол-во элементов .settings-item
     */
    function getCountElements( parent ){

        let setting_items_count = 0;
        $(parent).children('.settings-item').each(function(index, elem){

            if( !$(elem).closest('.default_item').length ){
                setting_items_count++;
            }

        });

        return setting_items_count;
    }

    /**
     * Добавить элемент
     */
    eventClick('.settings_item_array .add_new_elem', function(e){

        let items_block = $(e).closest('.settings_item_array_items');
        let main_block = items_block.closest('.settings_item_array');
        let html = $(e).find('.default_item').html();

        //Удаляем input empty[] если он был
        items_block.find('input[name="empty[]"]').remove();

        let last_key = parseInt( main_block.attr('data-last-key') );
        if( typeof last_key == 'undefined' )
            last_key = 1;

        last_key++;
        main_block.attr('data-last-key', last_key);

        let new_elem = $(html).insertBefore($(e));
        new_elem.attr('data-key', last_key);

        new_elem.find('[data-name]').each(function (i, elem){

            //Если этот элемент является элементом default_item - пропускаем
            if( $(elem).closest('.default_item').length > 0 )
                return;

            let field_name = $(elem).attr('data-name');

            field_name = indexesToFileName( $(elem), field_name );

            let attr_name = 'name';
            if( $(elem).attr('data-file-upload') === 'true' )
                attr_name = 'data-input-name';

            $(elem).attr(attr_name, field_name);
            $(elem).attr('data-name', false);

            //Проверяем, если есть поля с одинаковым name - выдаем ошибку и делаем disabled кнопку сохранить, для безопасности
            if( $('[name="'+field_name+'"]').length >= 2 ){
                showError('Произошла ошибка названия полей. Обратитесь к разработчику.');
                $('.settingsSave .box-footer .btn').addClass('disabled').attr('disabled', true);
            }

        });

        new_elem.find('.input:first').find('input:first, textarea:first, select:first').focus();

        let max_elements = parseInt(main_block.attr('data-max-elements'));
        let setting_items_count = getCountElements(items_block);
        if( setting_items_count >= max_elements ){
            $(e).hide();
        }


        editorSetup();
        aceJsSetup();

    });


    /**
     * Удалить элемент
     */
    eventClick('.settings_item_array .delete_elem', function(e){

        let elem = $(e).closest('.settings-item');
        let elem_name;

        if( elem.find('.input').length > 1 ){

            elem_name = 'элемент';

        }else{

            elem_name = '"' + elem.find('.input:last .label:first').text().trim() + '"';

        }

        swalConfirm({
            title: 'Вы уверены, что желаете удалить '+ elem_name +'?',
            confirmButtonText: 'Удалить',
            cancelButtonText: 'Отмена'
        }, function(){

            let parent_settings_item_array_items = elem.closest('.settings_item_array_items');
            let parent_array_block = elem.closest('.settings_item_array');

            let setting_items_count = getCountElements(parent_settings_item_array_items);

            let min_elements = parseInt(parent_array_block.attr('data-min-elements'));
            if( min_elements >= setting_items_count ){

                return showError('Вы не можете удалить элемент. Минимальное кол-во элементов:&nbsp;' + min_elements);

            }


            elem.remove();
            setting_items_count -= 1;

            //Если родительский элемент имеет manage-key и удалили уже все элементы массива
            if( parent_array_block.attr('data-manage-key') ){

                if( !setting_items_count ){

                    let input_name = parent_array_block.attr('data-manage-key');
                    if( parent_settings_item_array_items.attr('data-input-lang') ){
                        input_name += '|lang|' + parent_settings_item_array_items.attr('data-input-lang');
                    }

                    parent_settings_item_array_items.append('<input type="hidden" name="empty[]" value="'+ input_name +'">')

                }

            }

            //Возвращаем кнопку добавить элемент (она могла быть скрыта из-за максимального кол-ва эелментов)
            let max_elements = parseInt(parent_array_block.attr('data-max-elements'));
            if( max_elements > setting_items_count ){
                parent_settings_item_array_items.children('.add_new_elem').show();
            }

        });

    });


    /**
     * Сохранить настройки
     */
    eventSubmit('.settingsSave', function (form){

        if( isPageLoadingNow() )
            return false;

        pageLoadingStart();

        request('PUT', $(form).attr('data-action'), getFormData(form), function( response ){

                pageLoadingEnd();

                if( response === '1' ){

                    swalSuccess('Успешно сохранено!');

                }else{

                    showError(response);

                }

        },
        function( error ){

            showError(error);
            pageLoadingEnd();

        });

    });


    //Если есть ключи для копирования
    if( $('.input-copy-text').length ) {

        let shown_settings_key_copy_text = false;

        /**
         * Показать ключи для копирования
         */
        function showSettingsKeyCopyText() {

            $('.input-copy-text').removeClass('active');
            $('.input.hover').find('.input-copy-text').addClass('active');

        }


        /**
         * Скрыть ключи для копирования
         */
        function hideSettingsKeyCopyText() {

            $('.input-copy-text').removeClass('active');

        }


        /**
         * Для разработчика показать ключи для копирования
         */
        eventKeyDown(function (event) {

            if (event.shiftKey || event.keyCode === 16) {

                shown_settings_key_copy_text = true;
                showSettingsKeyCopyText();

            }

        });


        /**
         * Для разработчика скрыть ключи для копирования
         */
        eventKeyUp(function (event) {

            if (event.shiftKey || event.keyCode === 16) {

                shown_settings_key_copy_text = false;
                hideSettingsKeyCopyText();

            }

        });


        /**
         * Если зажат shift - показывает ключи для копирования
         */
        eventMouseMove('.page-settings', function () {

            if( shown_settings_key_copy_text )
                showSettingsKeyCopyText();

        });

    }
