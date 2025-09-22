
    /**
     *
     *  Запустить интерактивные элементы
     *
     */
    window.interactiveElementsData = {};
    window.interactiveElementsSetup = function () {

        let i = 0;

        //Показать если какое-то поле равняется нужному значению
        $('[data-show-if]').each(function () {

            i++;

            let attr = $(this).attr('data-show-if');


            if( attr.includes('&&') ){

                let parse = attr.split('&&');
                interactiveElementsData[i] = {
                    'logic': '&&',
                    'elem': $(this),
                    'conditions': {},
                    'type': 'show-if'
                };

                parse.forEach(function (val, key){

                    let p = val.trim().split(' ');

                    interactiveElementsData[i]['conditions'][key] = {
                        'input': p[0],
                        'operator': p[1],
                        'value': p[2],
                        'elem': $(this),
                        'type': 'show-if'
                    };

                });




            }else if( attr.includes('||') ){


                let parse = attr.split('||');
                interactiveElementsData[i] = {
                    'logic': '||',
                    'elem': $(this),
                    'conditions': {},
                    'type': 'show-if'
                };

                parse.forEach(function (val, key){

                    let p = val.trim().split(' ');

                    interactiveElementsData[i]['conditions'][key] = {
                        'input': p[0],
                        'operator': p[1],
                        'value': p[2],
                        'elem': $(this),
                        'type': 'show-if'
                    };

                });





            }else{

                let parse = attr.split(' ');

                interactiveElementsData[i] = {
                    'input': parse[0],
                    'operator': parse[1],
                    'value': parse[2],
                    'elem': $(this),
                    'type': 'show-if'
                };

            }

        });

        interactiveCheck();

    };


    eventChangeWithKeyPress('input,select,textarea',function () {
        interactiveCheck();
    });

    eventBlur('input,textarea',function () {
        interactiveCheck();
    });



    /**
     * Запуск проверки элементов
     */
    window.interactiveCheck = function () {

        let data, key, parent, parent_data;
        for( key in interactiveElementsData ){

            data = interactiveElementsData[key];

            parent = data.elem.closest('form');

            if( !parent.length ){
                parent = $('#content');

                if( !parent.length ){
                    parent = $('body');
                }

            }

            parent_data = getFormData(parent);


            if( data['type'] === 'show-if' ){

                if( interactiveOperatorCheck(parent_data, data) ){

                    data['elem'].show();

                }else{

                    data['elem'].hide();

                }

            }

        }

        //Обновляем элементы формы, чтобы они корректно отображались
        aceJsSetup();
        select2Setup();

    };


    /**
     * Проверка оператора
     */
    window.interactiveOperatorCheck = function (parent_data, data ){


        if( data['logic'] === '&&' ){

            let key;
            let _return = true;
            for (key in data['conditions']) {

                if( !interactiveOperatorCheck(parent_data,data['conditions'][key]) ){
                    _return = false;
                }

            }

            return _return;

        }


        if( data['logic'] === '||' ){

            let key;
            let _return = false;
            for (key in data['conditions']) {

                if( interactiveOperatorCheck(parent_data,data['conditions'][key]) ){
                    _return = true;
                }

            }

            return _return;

        }


        if( data['operator'] === '==' ){

            if( data['value'] === 'true' ){

                if( parent_data[data['input']] ){

                    return true;

                }else{

                    return false;

                }

            }else{

                if( parent_data[data['input']] === data['value'] ){

                    return true;

                }else{

                    return false;

                }

            }



        }else if( data['operator'] === '!=' ){

            if( parent_data[data['input']] !== data['value'] ){

                return true;

            }else{

                return false;

            }

        }


        return false;
    };
