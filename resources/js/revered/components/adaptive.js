
    /**
     * Открытие мобильного меню
     */
    window.openMobileTopNav = function (){

        $('#header').addClass('mobile-active');
        $('.mobile-nav-open').after('<div class="mobile-nav-close"></div>');

    }

    /**
     * Закрытие мобильного меню
     */
    window.closeMobileTopNav = function (){

        $('#header .mobile-nav-close').remove();
        $('#header').removeClass('mobile-active');

    }

    /**
     * Клик закрыть меню
     */
    eventClick('.mobile-nav-close', function (){
       window.closeMobileTopNav();
    });


    /**
     * Клик открыть меню
     */
    eventClick('.mobile-nav-open', function (){
        window.openMobileTopNav();
    });


    /**
     * Клик по left-full-height-nav
     */
    eventClick('.left-full-height-nav', function (e){

        $(e).addClass('active');

    });



    /**
     * Клик вне left-full-height-nav
     */
    eventClickOutside('.left-full-height-nav', function (e){

        $(e).removeClass('active');

    });


    /**
     * Если боковое меню не помещается в высоту экрана - то добавить scroll
     */
    window.checkScrollForSidebarNav = function( with_scroll_to_active_link = false ){

        if( $('#sidebar-nav').length ){

            let window_height = $(window).height();
            let logo_height = $('#sidebar-nav div.logo:first').outerHeight(true);

            let sidebar_nav_height = window_height - logo_height - 20;
            let sidebar_nav = $('#sidebar-nav nav.nav');

            sidebar_nav.css('height', sidebar_nav_height);

            if( with_scroll_to_active_link ){

                let active_link = $('#sidebar-nav nav.nav a.active:last');

                if( active_link.length > 0 )
                    sidebar_nav.scrollTop(sidebar_nav.scrollTop() - sidebar_nav.offset().top + active_link.offset().top - 200);

            }

        }

    };
    checkScrollForSidebarNav(true);



    /**
     * Адаптируем left-full-height-nav
     */
    window.checkLeftFullHeightNavView = function(){

        $('.left-full-height-nav').each(function(index, full_height_nav){

            let app_width = $('#app').outerWidth();
            let full_height_nav_width = $(full_height_nav).outerWidth();

            let container = $(full_height_nav).next('.container');
            let container_width = container.outerWidth();

            let container_margin = app_width - container_width;
            let container_margin_half = parseInt(container_margin/2);

            let check = app_width - container_width - container_margin_half;

            if( full_height_nav_width > check - 20 ){

                container.css('padding-left', full_height_nav_width - check + container_margin_half + 15);

            }else{

                container.css('padding-left', 0);

            }

        });

    }


    /**
     * Мобильный вид header (в том числе верхнее меню)
     */
    window.checkMobileHeaderView = function(){

        let width = $('#header .logo').width() + $('#header .nav:not(.over-top-nav):first').width() + $('#header .nav-right').width() + 35;

        if( !$('.mobile-nav-open').length )
            $('#header .nav-right').before('<div class="mobile-nav-open"></div>');

        if( $('#sidebar-nav').length && parseInt($('#sidebar-nav').css('left')) === 0 )
            width -= $('#sidebar-nav').width();

        if( width > $('#header .container:last').width() ){

            $('#header').addClass('mobile-header');

        }else{

            $('#header').removeClass('mobile-header');

        }

    }
    checkMobileHeaderView();


    /**
     * Открытие sidebar navigation
     */
    window.openSideBarNav = function( immediately = false ){

        if( !mobile_sidebar_turn_on )
            return;

        if( !$('#sidebar-nav').length )
            return;

        if( parseInt($('#sidebar-nav').css('left')) === 0 )
            return;

        if( immediately ){

            $('#sidebar-nav').css('left', 0);

        }else{

            $('#sidebar-nav').animate({'left':0});

        }

    }


    /**
     * Закрытие sidebar navigation
     */
    window.closeSideBarNav = function( immediately = false ){

        if( !mobile_sidebar_turn_on )
            return;

        if( immediately ){

            $('#sidebar-nav').css('left', -300);

        }else{

            $('#sidebar-nav').animate({'left':-300});

        }


    }


    /**
     * При клике на Logo открываем sidebar если он есть на мобильных
     */
    eventClick('#header .logo', function(){

        if( !mobile_sidebar_turn_on )
            return;

        if( parseInt($('#sidebar-nav').css('left')) === 0 ){

            closeSideBarNav();

        }else{

            openSideBarNav();

        }

    });


    /**
     * Клик вне side bar
     */
    eventClickOutside('#sidebar-nav', '#header .logo', function (){

        if( !mobile_sidebar_turn_on )
            return;

        closeSideBarNav();

    });


    /**
     * Клик по ссылке внутри side bar
     */
    eventClick('#sidebar-nav a:not(.open-sub-nav)', function (){

        if( !mobile_sidebar_turn_on )
            return;

        closeSideBarNav();

    });


    /**
     * Если есть side bar и он скрыт (на мобильном), то вешаем обработчики
     */
    window.mobile_sidebar_turn_on = false;
    window.checkMobileSideBar = function(){

        if( !$('#sidebar-nav').length )
            return;

        //Mobile
        if( window.innerWidth <= 980 ){

            window.mobile_sidebar_turn_on = true;
            closeSideBarNav(true);


        //Desktop
        }else{

            openSideBarNav(true);
            window.mobile_sidebar_turn_on = false;

        }

    }
    checkMobileSideBar();



    /**
     * Событие изменения окна
     */
    $(window).on('resize', function(){
        checkMobileHeaderView();
        checkMobileSideBar();
        checkLeftFullHeightNavView();
        actionsFixedInit();
        checkScrollForSidebarNav();
    });

