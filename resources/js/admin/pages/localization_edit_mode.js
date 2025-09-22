

    //Если мод включен
    if( $('body.localization_edit_mode').length && !$('body').hasClass('localization_edit_mode_js_ready') ) {

        $('body.localization_edit_mode').addClass('localization_edit_mode_js_ready');

        //Добавляем в шапку необходимые стили
        let admin_local_edit_style = '<style>';
        admin_local_edit_style += '.admin-local-edit {' +
            'font-family: inherit;' +
            'font-size: inherit;' +
            'font-style: inherit;' +
            'font-weight: inherit;' +
            'color: inherit;' +
            'opacity: inherit;' +
            'display: inline;' +
            'background: inherit;' +
            'text-shadow: inherit;' +
            'margin: initial !important;' +
            'padding: initial !important;' +
            'position: initial !important;' +
            '}';
        admin_local_edit_style += '.admin-local-edit-alt {' +
            'color: #0035FD !important;' +
            'cursor: zoom-in;' +
            '}';
        admin_local_edit_style += '.admin-local-edit-alt * {' +
            'color: #0035FD !important;' +
            'cursor: zoom-in;' +
            '}';
        admin_local_edit_style += '.admin-local-edit::after, .admin-local-edit::before {}';
        admin_local_edit_style += '</style>';

        $('head').append(admin_local_edit_style);


        //Сперва нам нужно отчистить локаль из некоторых аттрибутов тегов
        let data_attribute;
        let tags = $('[placeholder*="{{local="]');
        tags.each(function (index, item){

            data_attribute = $(item).attr('placeholder');
            data_attribute = clearLocals(data_attribute);
            $(item).attr('placeholder', data_attribute);

        });



        let body_html = $('body').html();
        let new_body_html = handlerLocals( body_html );


        $('body').html(new_body_html);


        //Отчищаем title
        $('title').text( clearLocals($('title').text()) );


        //Кликаем на локаль с нажатым alt
        $('body').on('click','.admin-local-edit-alt', function (event){

            event.preventDefault();

            let local_key = $(this).attr('data-key');

            window.open('/admin/localization?key=' + local_key, '_blank');

        });


        //Если направляют на текст локали
        $('body').on('mousemove','.admin-local-edit', function (event){

            $(this).addClass('admin-local-edit-hover');

            if( event.altKey ){
                $('.admin-local-edit-hover').addClass('admin-local-edit-alt');
            }

        });


        //Если уводит направление с локали
        $('body').on('mouseout','.admin-local-edit', function (event){

            $(this).removeClass('admin-local-edit-hover');
            $(this).removeClass('admin-local-edit-alt');

        });


        //Нажатие ALT
        $(window).keydown(function( event ){

            if( event.keyCode === 18 ){

                $('.admin-local-edit-hover').addClass('admin-local-edit-alt');

            }

        });


        //Отпускание нажатия ALT
        $(window).keyup(function( event ){

            if( event.keyCode === 18 ){

                $('.admin-local-edit-hover').removeClass('admin-local-edit-alt');

            }

        });


    }

    handlerLocals("{{local=guest.courses.a1_page_text}}<div class=\"page-description-title\">What is the YouLang communication course?</div>\n" +
        "<div class=\"page-text\"><strong>Communication course from YouLang</strong> A new modern educational complex for foreign students who are starting to learn Russian from scratch.<br /><br />All course materials are available online in PDF format and are suitable for both group classes and individual work in a real or virtual learning space. <br /><br /></div>\n" +
        "<div class=\"page-description-title\">What does the A1 course consist of?</div>\n" +
        "<div class=\"page-text\">The A1 course consists of <strong>7 thematic blocks</strong>, each of which includes <strong>3 lessons</strong>, united by a common theme.<br /><br />The course meets the basic principles of a communication-oriented methodology: by performing a variety of communicative tasks and games, students form the skills of oral and written speech, listening and reading in an atmosphere of natural educational communication. The presentation of educational material is based on the principles of clarity, cyclicity and the absence of an intermediary language. <br /><br /></div>{{/local}}\n");

    //Найти в HTML коде все локализации, обработать и вернуть HTML
    function handlerLocals( html ){

        let all_local_text = html.matchAll(/\{\{local=(.*?)}}(.*?)\{\{\/local}}/gs);
        all_local_text = Array.from(all_local_text);
        console.log(all_local_text);

        let text_with_tag;

        all_local_text.forEach(function(item, i, arr) {

            text_with_tag = '<i class="admin-local-edit" data-key="' + item[1] + '">' + item[2] + '</i>';
            html = html.replace(item[0], text_with_tag);

        });

        return html;
    }


    //Найти в HTML коде все локализации и отчистить их
    function clearLocals( text ){

        let all_local_text = text.matchAll(/\{\{local=(.*?)}}(.*?)\{\{\/local}}/gs);
        all_local_text = Array.from(all_local_text);

        let text_without_tag;

        all_local_text.forEach(function(item, i, arr) {

            text_without_tag = item[2];
            text = text.replace(item[0], text_without_tag);

        });

        return text;
    }
