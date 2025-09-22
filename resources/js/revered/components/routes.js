

    /**
     * Получить URL по названию Route (функция аналогичная хелперу Laravel)
     * первый аргумент route_name - название route (пример: admin.users.browse)
     * последующие аргументы, то что нужно вставить в URL placeholder
     * Пример: route('admin.users.edit', user.id);
     */
    window.route = function (){

        let route_name = arguments[0];
        if( typeof route_name === 'undefined' )
            return console.error('Укажите первым аргументом название route.');

        let route_path = window.routes[route_name];
        if( typeof route_path === 'undefined' )
            return console.error('Не найден route с названием "' + route_name + '"');


        //Преобразуем URL, чтобы AJAX правильно отправлял запрос на нужный адрес
        if( route_path.substr(0, 1) !== '/' && route_path.substr(0, 4) !== 'http' )
            route_path = '/' + route_path;


        let route_args = route_path.match(/\{(.*?)}/g);
        if( route_args ){

            let func_arguments_index = 1; //Начинаем с первого, так как index = 0 это route_name в аргументах
            let func_current_argument;

            route_args.forEach( (arg) => {

                func_current_argument = arguments[func_arguments_index];

                //Если аргумент необязательный
                if( arg.substr(arg.length - 2) === '?}' ){


                    //Если аргумент указан
                    if( typeof func_current_argument !== 'undefined' ){

                        route_path = route_path.replace(arg, func_current_argument);

                    //Если не указан
                    }else{

                        route_path = route_path.replace('/' + arg, '');

                    }


                //Аргумент обязательный
                }else{

                    //Если аргумент не указан
                    if( typeof func_current_argument === 'undefined' )
                        return console.error('Argument ' + arg + ' required in path ' + route_path);

                    route_path = route_path.replace(arg, func_current_argument);

                }

                func_arguments_index++;

            });

        }

        return route_path;

    };
