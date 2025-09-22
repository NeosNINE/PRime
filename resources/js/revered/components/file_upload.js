

    /**
        Установить поля загрузки
     */
    window.fileUploadSetup = function () {

        $('[data-file-upload]:not(.input-init)').each(function () {

            $(this).addClass('input-init');
            $(this).closest('div.input').removeClass('no-active').attr('data-always-active', true);

            let accept = '';
            if ($(this).attr('data-type') === 'image' || $(this).attr('data-type') === 'images') {
                accept = 'image/*';
            }

            let multiple = '';
            if ($(this).attr('data-type') === 'images' || $(this).attr('data-type') === 'files') {
                $(this).attr('multiple', true);
                multiple = 'multiple';
            }

            let data_file_upload_html;
            if ($(this).attr('readonly') || $(this).attr('disabled')) {

                data_file_upload_html = '<span class="data-file-upload-result"></span>';

            } else {

                data_file_upload_html = '<input type="file" class="data-file-upload-input" accept="' + accept + '" ' + multiple + '>'
                    + '<span class="data-file-upload-btn btn btn-default"><i class="fa fa-upload"></i> Загрузить</span>'
                    + '<span class="data-file-upload-result"></span>';

            }


            $(this).html(data_file_upload_html);


            let data_file_upload_elem = $(this);
            let data;


            if (data_file_upload_elem.attr('data-value')) {

                if (data_file_upload_elem.attr('data-type') === 'images' || data_file_upload_elem.attr('data-type') === 'files') {

                    try {

                        $.parseJSON(data_file_upload_elem.attr('data-value')).forEach(function (e) {
                            fileUploadSuccess(data_file_upload_elem, e.path, e.name);
                        });

                    } catch (e) {

                        console.log(e);

                    }

                } else if (data_file_upload_elem.attr('data-type') === 'file') {

                    try {

                        data = $.parseJSON(data_file_upload_elem.attr('data-value'));
                        fileUploadSuccess(data_file_upload_elem, data.path, data.name);

                    } catch (e) {

                        console.log(e);

                    }

                } else {
                    fileUploadSuccess(data_file_upload_elem, data_file_upload_elem.attr('data-value'));
                }


            }

        });

    };


    /**
        Открыть поле загрузки файла
     */
    eventClick('.data-file-upload-btn', function (e) {
        $(e).prev('input[type=file]').focus().click();
    });


    /**
     * Показать индикатор загрузки
     */
    function addFileLoading(data_file_upload) {

        const loading_btn = data_file_upload.find('.data-file-upload-btn');

        if (loading_btn.hasClass('loading'))
            return;

        data_file_upload.find('input[type=file]').prop('disabled', true);
        loading_btn.addClass('loading');
        loading_btn.append('<div class="loader"></div>');
    }


    /**
     * Скрыть индикатор закгрузки
     */
    function removeFileLoading(data_file_upload) {
        data_file_upload.find('input[type=file]').prop('disabled', false);
        data_file_upload.find('.data-file-upload-btn').removeClass('loading');
        data_file_upload.find('.data-file-upload-btn .loader').remove();
    }


    /**
        Событие выборка файла
     */
    $('body').on('change', '[data-file-upload] input[type=file]', function () {

        if (this.files.length < 1)
            return false;


        let data_file_upload = $(this).closest('[data-file-upload]');
        addFileLoading(data_file_upload);

        let data = new FormData();

        $.each(this.files, function (key, value) {
            data.append('files[' + key + ']', value);
        });


        //Отчищаем поле выбора файла
        $(this).val('');


        data.append('_token', getCSRF());


        $.ajax({
            url: '/upload-file',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {

                response.forEach(function (e) {

                    if (typeof e.status !== 'undefined' && e.status === 'success') {

                        fileUploadSuccess(data_file_upload, e.file_url, e.file_name);

                    } else {

                        if (typeof e.error !== 'undefined') {
                            showError(e.error);
                        } else {
                            showError(e);
                        }

                    }


                });

            },
            error: function (jqXHR, status, errorThrown) {

                showError(jqXHR, status);

            },
            complete: function (){

                removeFileLoading(data_file_upload);

            }
        });


    });



    /**
       Успешная загрузка файла
     */
    function fileUploadSuccess(data_file_upload, file_url, file_name) {
        if (!file_url || file_url === 'undefined' || file_url === 'NULL')
            return;

        let attributes = '';
        let input_name = data_file_upload.attr('data-input-name');

        if (data_file_upload.attr('multiple')) {
            attributes += ' data-inputs-to-group="' + data_file_upload.attr('data-input-name') + '" ';
        }

        let result_html = '<span class="data-file-upload-item"' + attributes + '>';

        //Если загрузили изображение
        if (data_file_upload.attr('data-type') === 'image' || data_file_upload.attr('data-type') === 'images') {

            result_html += '<img src="' + file_url + '">';

        } else {
            result_html += '<a href="' + file_url + '" target="_blank" class="btn btn-success">' + file_name + '</a>';
        }


        //Если это файл
        if (data_file_upload.attr('data-type') === 'file') {

            let hidden_path = '<input type="hidden" class="input-hidden-file" name="' + input_name + '[path]" value="' + file_url + '">';
            let hidden_name = '<input type="hidden" class="input-hidden-file" name="' + input_name + '[name]" value="' + file_name + '">';
            result_html += '<input type="hidden" name="' + input_name + '[path]" value="' + file_url + '">';
            result_html += '<input type="hidden" name="' + input_name + '[name]" value="' + file_name + '">'; //Если много файлов
            data_file_upload.find('.input-hidden-file').remove();
            data_file_upload.append(hidden_path + hidden_name);

        //Если много файлов
        } else if (data_file_upload.attr('data-type') === 'files') {

            result_html += '<input type="hidden" name="path" value="' + file_url + '">';
            result_html += '<input type="hidden" name="name" value="' + file_name + '">';


        //Если много изображений
        } else if (data_file_upload.attr('data-type') === 'images') {

            result_html += '<input type="hidden" name="path" value="' + file_url + '">';


        //Если другой тип
        } else {
            result_html += '<input type="hidden" name="' + input_name + '" value="' + file_url + '">';
        }


        //Добавляем кнопку для удаления элемента
        if (!data_file_upload.attr('readonly') && !data_file_upload.attr('disabled'))
            result_html += '<span class="data-file-upload-delete"><i class="fa fa-times-circle"></i></span>';

        result_html += '</span>';

        //Если мультизагрузка, то добавляем результат
        if (data_file_upload.attr('multiple')) {

            data_file_upload.find('.data-file-upload-result').append(result_html);

        //Если не мультизагрузка - то обновляем HTML
        } else {

            data_file_upload.find('.data-file-upload-result').html(result_html);

        }


    }


    /**
        Удалить файл
     */
    eventClick('.data-file-upload-delete', function (elem) {

        swalConfirm({
            title: 'Вы уверены, что желаете удалить файл?',
            confirmButtonText: 'Удалить',
            cancelButtonText: 'Отмена'
        }, function (){

            let data_file_upload_block = $(elem).closest('[data-file-upload="true"]');

            //Если это мультизагрузка
            if (data_file_upload_block.attr('data-type') === 'images' || data_file_upload_block.attr('data-type') === 'files') {


                $(elem).closest('.data-file-upload-item').remove();


            //Загрузка одного файла
            } else if (data_file_upload_block.attr('data-type') === 'file') {

                $(elem).closest('.data-file-upload-item').remove();
                data_file_upload_block.find('.input-hidden-file').val('NULL');

            } else {

                $(elem).closest('.data-file-upload-item').hide();
                $(elem).closest('.data-file-upload-item').find('input[name="' + data_file_upload_block.attr('data-input-name') + '"]').val('NULL');

            }
            data_file_upload_block.attr('data-value', '');

            swalSuccess('Файл успешно удален.');

        });

    });
