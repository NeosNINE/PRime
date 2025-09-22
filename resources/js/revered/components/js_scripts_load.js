

    /**
     * Обработать src (убираем GET параметры)
     */
    function cleanJsSrc( src ){

        if( typeof src === 'undefined' )
            return false;

        return src.split('.js?')[0];

    }


    /**
     * Сканируем все подключенные JS скрипты
     */
    window.loaded_js_scripts = [];
    $('script').each(function(index, script) {

        if( $(script).attr('src') )
            loaded_js_scripts.push( cleanJsSrc($(script).attr('src')) );

    });


    /**
     * Подключить все JS скрипты
     */
    window.current_inject_script = '';
    window.injectScripts = function (){

        let script = $('load-js:first');

        if( !script.length )
            return false;

        let src = $(script).attr('src');
        let clean_src = cleanJsSrc(src);

        if( !clean_src )
            return console.error('Не удалось загрузить JS <load-js> - не указан аттрибут src.');

        $(script).remove();

        if( loaded_js_scripts.indexOf(clean_src) === -1 ){

            loaded_js_scripts.push(clean_src);
            window.current_inject_script = clean_src;

            let s = document.createElement('script');
            s.src = src;

            //Когда скрипт загружен - запускаем инициализацию
            s.onload = function () {
                runJS(clean_src);
            };

            //Если ошибка загрузки скрипта
            s.onerror = function (){
                showError('Не удалось загрузить JS: ' + src);
            };

            //Вставляем скрипт в DOM для загрузки
            document.body.append(s);


        //Если JS скрипт уже был подгружен
        }else{

            runJS(clean_src);

        }

    };


    /**
     * Запустить JS скрипт (запускает finalize и initialize)
     */
    window.runJS = function ( src ){

        //Сделано через setTimeout для синхронной загрузки, иначе не работает
        setTimeout(function () {

            runFinalize(src);

            setTimeout(function () {

                runInitialize(src);

                setTimeout(function () {

                    injectScripts();

                });

            });

        });

    }


    /**
     * Добавить функцию инициализации (запускает функцию при загрузке страницы)
     */
    window.initialize_functions = new Map();
    window.initialize = function ( func ){

        initialize_functions.set(current_inject_script, func);

    };


    /**
     * Запустить функцию инициализации
     */
    window.already_initialized = new Map();
    window.runInitialize = function ( src ){

        if( initialize_functions.get(src) ){

            initialize_functions.get(src)();
            already_initialized.set(src, true);

        }

    };


    /**
     * Добавить функцию финализации (запускает функцию при загрузке страницы, перед инициализацией)
     */
    window.finalize_functions = new Map();
    window.finalize = function ( func ){

        finalize_functions.set(current_inject_script, func);

    };


    /**
     * Запустить функцию инициализации
     */
    window.runFinalize = function ( src ){

        if( finalize_functions.get(src) && already_initialized.get(src) )
            finalize_functions.get(src)();

    };
