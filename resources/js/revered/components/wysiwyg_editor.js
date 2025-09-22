
    tinymce.baseURL = '/assets/libs/tinymce';
    tinymce.suffix = '.min';

    /**
     *
     *  Запустить wysiwyg редактор
     *
     */
    window.editorSetup = function () {

        if( typeof tinymce == 'undefined' )
            return false;


        let elements = $('[data-editor]').not('.wysiwyg-editor-init');
        elements.each(function (index) {

            //Если родитель default_item - пропускам
            if( $(this).closest('.default_item').length ){
                return;
            }

            $(this).addClass('wysiwyg-editor-init');
            $(this).attr('id','wysiwyg-editor-'+Math.random().toString(36).substr(2, 10));

            if( $(this).is('[data-editor-compact]') ){


                tinymce.init({
                    selector: '#' + $(this).attr('id'),
                    theme: "silver",
                    skin: getCurrentTheme() === 'dark' ? 'oxide-dark' : 'oxide',
                    language: 'ru',
                    height: $(this).attr('data-editor-height') ?? 150,
                    navbar: false,
                    plugins: [
                        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "save table contextnav directionality emoticons template paste textcolor"
                    ],
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",
                    image_title: true,
                    automatic_uploads: true,
                    images_upload_url: '/wysiwyg_upload',
                    relative_urls: false,
                    images_upload_base_path: '/',
                    file_picker_types: 'image',
                    file_picker_callback: function (cb, value, meta) {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.onchange = function () {
                            var file = this.files[0];

                            var reader = new FileReader();
                            reader.readAsDataURL(file);
                            reader.onload = function () {
                                var id = 'blobid' + (new Date()).getTime();
                                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                                var base64 = reader.result.split(',')[1];
                                var blobInfo = blobCache.create(id, file, base64);
                                blobCache.add(blobInfo);
                                cb(blobInfo.blobUri(), {title: file.name});
                            };
                        };
                        input.click();
                    }
                });


            }else {

                tinymce.init({
                    selector: '#' + $(this).attr('id'),
                    theme: "silver",
                    skin: getCurrentTheme() === 'dark' ? 'oxide-dark' : 'oxide',
                    language: 'ru',
                    height: $(this).attr('data-editor-height') ?? 550,
                    plugins: [
                        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "save table contextmenu directionality emoticons template paste textcolor"
                    ],
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
                    image_title: true,
                    automatic_uploads: true,
                    images_upload_url: '/wysiwyg_upload',
                    relative_urls: false,
                    images_upload_base_path: '/',
                    file_picker_types: 'image',
                    file_picker_callback: function (cb, value, meta) {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.onchange = function () {
                            var file = this.files[0];

                            var reader = new FileReader();
                            reader.readAsDataURL(file);
                            reader.onload = function () {
                                var id = 'blobid' + (new Date()).getTime();
                                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                                var base64 = reader.result.split(',')[1];
                                var blobInfo = blobCache.create(id, file, base64);
                                blobCache.add(blobInfo);
                                cb(blobInfo.blobUri(), {title: file.name});
                            };
                        };
                        input.click();
                    }
                });


            }


        });

    }
