
    initialize(function (){

        let current_theme_browser = getCurrentTheme();
        let current_theme_input = $('[data-theme-choice] input:checked').val();

        if( current_theme_browser !== current_theme_input )
            setUpTheme(current_theme_input);

    });
