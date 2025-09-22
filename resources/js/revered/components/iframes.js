
    /**
     *  Подстроить высоту Iframe (сделать height на всю высоту контента)
     */
    function oneIframeResize( iframe ){

        let content = $(iframe).contents().find('body');
        $(iframe).height($(content).outerHeight(true)+50);

    }


    /**
        Подстраиваем высоту iframe автоматически при загрузке
    */
    $('.iframe-default:visible').on('load',function(){
        oneIframeResize(this);
    });


    /**
        Перестроить высоту
    */
    window.iframeResize = function (){

        $('.iframe-default:visible').each(function (index, iframe){
            oneIframeResize(iframe);
        });

    };
