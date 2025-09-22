

    window.sortableSetup = function (){

        if( $('.table-sortable:not(.table-sortable-init)').length > 0 ){

            $('.table-sortable:not(.table-sortable-init)').each(function (key,elem){

                $(elem).addClass('table-sortable-init');

                if( $(elem).attr('data-sortable-type') === 'swap' ){


                    $(elem).find('tbody').sortable({
                        handle:'.handle-sortable',
                        swap: true,
                        swapClass:'sortable-highlight',
                        onEnd: function (evt) {



                            let item = $(evt.item);
                            sortableRequest(item.closest('.table-sortable').attr('data-sortable-url'), item, {
                                'type': 'swap',
                                'id_from': item.closest('.table-sortable').find('tbody tr:eq('+evt.newIndex+')').attr('data-id'),
                                'id_to': item.closest('.table-sortable').find('tbody tr:eq('+evt.oldIndex+')').attr('data-id')
                            });

                        }
                    });


                }else{

                    $(elem).find('tbody').sortable({
                        handle:'.handle-sortable',
                        onEnd: function (evt) {

                            let item = $(evt.item);
                            sortableRequest(item.closest('.table-sortable').attr('data-sortable-url'), item, {
                                'this_id': item.attr('data-id'),
                                'prev_id': item.prev().attr('data-id'),
                                'next_id': item.next().attr('data-id')
                            });

                        }
                    });

                }


            });

        }

    };



    window.sortableRequest = function (url, elem, data){

        request('PUT', url, data, function (response){

            //alert(response);

        });

    };
