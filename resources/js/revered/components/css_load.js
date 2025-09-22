


    /**
     * Обработать src (убираем GET параметры)
     */
    function cleanCssHref( href ){

        if( typeof href === 'undefined' )
            return false;

        return href.split('.css?')[0];
    }


    /**
     * Сканируем все подключенные CSS
     */
    window.loaded_css = [];
    $('link').each(function(index, css) {

        if( $(css).attr('href') && $(css).attr('rel') === 'stylesheet' )
            loaded_css.push( cleanCssHref($(css).attr('href')) );

    });


    /**
     * Подключить все JS скрипты
     */
    window.injectCss = function (){

        let css = $('load-css:first');

        if( !css.length )
            return false;

        let href = $(css).attr('src');
        let clean_href = cleanCssHref(href);

        if( !clean_href )
            return console.error('Не удалось загрузить CSS <load-css> - не указан аттрибут src.');

        $(css).remove();

        if( loaded_css.indexOf(clean_href) === -1 ){

            loaded_css.push(clean_href);

            let elem = document.createElement('link');
            elem.href = href;
            elem.rel = 'stylesheet';
            elem.type = 'text/css';

            //Если ошибка загрузки скрипта
            elem.onerror = function (){
                showError('Не удалось загрузить CSS: ' + href);
            };

            //Вставляем скрипт в DOM для загрузки
            if( $('head [data-app-css]').length ){

                $('head [data-app-css]').after(elem);

            }else{

                document.head.append(elem);

            }


        }

    };
