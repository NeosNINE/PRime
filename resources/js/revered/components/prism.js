
    /**
     * CSS для той или инной темы
     */
    window.prismThemesCss = {
        'light' : "code[class*=language-],pre[class*=language-]{color:#000;background:0 0;text-shadow:0 1px #fff;font-family:Consolas,Monaco,'Andale Mono','Ubuntu Mono',monospace;font-size:1em;text-align:left;white-space:pre;word-spacing:normal;word-break:normal;word-wrap:normal;line-height:1.5;-moz-tab-size:4;-o-tab-size:4;tab-size:4;-webkit-hyphens:none;-moz-hyphens:none;-ms-hyphens:none;hyphens:none}code[class*=language-] ::-moz-selection,code[class*=language-]::-moz-selection,pre[class*=language-] ::-moz-selection,pre[class*=language-]::-moz-selection{text-shadow:none;background:#b3d4fc}code[class*=language-] ::selection,code[class*=language-]::selection,pre[class*=language-] ::selection,pre[class*=language-]::selection{text-shadow:none;background:#b3d4fc}@media print{code[class*=language-],pre[class*=language-]{text-shadow:none}}pre[class*=language-]{padding:1em;margin:.5em 0;overflow:auto}:not(pre)>code[class*=language-],pre[class*=language-]{background:#f5f2f0}:not(pre)>code[class*=language-]{padding:.1em;border-radius:.3em;white-space:normal}.token.cdata,.token.comment,.token.doctype,.token.prolog{color:#708090}.token.punctuation{color:#999}.token.namespace{opacity:.7}.token.boolean,.token.constant,.token.deleted,.token.number,.token.property,.token.symbol,.token.tag{color:#905}.token.attr-name,.token.builtin,.token.char,.token.inserted,.token.selector,.token.string{color:#690}.language-css .token.string,.style .token.string,.token.entity,.token.operator,.token.url{color:#9a6e3a;background:hsla(0,0%,100%,.5)}.token.atrule,.token.attr-value,.token.keyword{color:#07a}.token.class-name,.token.function{color:#dd4a68}.token.important,.token.regex,.token.variable{color:#e90}.token.bold,.token.important{font-weight:700}.token.italic{font-style:italic}.token.entity{cursor:help}",
        'dark' : "code[class*=language-],pre[class*=language-]{color:#ccc;background:0 0;font-family:Consolas,Monaco,'Andale Mono','Ubuntu Mono',monospace;font-size:1em;text-align:left;white-space:pre;word-spacing:normal;word-break:normal;word-wrap:normal;line-height:1.5;-moz-tab-size:4;-o-tab-size:4;tab-size:4;-webkit-hyphens:none;-moz-hyphens:none;-ms-hyphens:none;hyphens:none}pre[class*=language-]{padding:1em;margin:.5em 0;overflow:auto}:not(pre)>code[class*=language-],pre[class*=language-]{background:#2d2d2d}:not(pre)>code[class*=language-]{padding:.1em;border-radius:.3em;white-space:normal}.token.block-comment,.token.cdata,.token.comment,.token.doctype,.token.prolog{color:#999}.token.punctuation{color:#ccc}.token.attr-name,.token.deleted,.token.namespace,.token.tag{color:#e2777a}.token.function-name{color:#6196cc}.token.boolean,.token.function,.token.number{color:#f08d49}.token.class-name,.token.constant,.token.property,.token.symbol{color:#f8c555}.token.atrule,.token.builtin,.token.important,.token.keyword,.token.selector{color:#cc99cd}.token.attr-value,.token.char,.token.regex,.token.string,.token.variable{color:#7ec699}.token.entity,.token.operator,.token.url{color:#67cdcc}.token.bold,.token.important{font-weight:700}.token.italic{font-style:italic}.token.entity{cursor:help}.token.inserted{color:green}"
    };


    /**
     *  Установка Prism JS (просмотр кода)
     */
    window.prismJsSetup = function () {

        let elements = $('[data-prism]').not('.prism-init');
        elements.each(function () {

            //Если родитель default_item - пропускам
            if( $(this).closest('.default_item').length )
                return;


            //Применяем тему
            prismJsApplyTheme();


            $(this).addClass('prism-init');

            let data_start = '';
            if( $(this).attr('data-start') )
                data_start = ' data-start="' + parseInt($(this).attr('data-start')) + '"';

            let data_line = '';
            if( $(this).attr('data-line') )
                data_line = ' data-line="' + parseInt($(this).attr('data-line')) + '"';

            let data_line_offset = '';
            if( data_start )
                data_line_offset = ' data-line-offset="' + parseInt($(this).attr('data-start')) + '"';

            let line_numbers = 'line-numbers';
            if( typeof $(this).attr('data-not-line-numbers') !== 'undefined' )
                line_numbers = '';

            let lang = 'php';
            if( $(this).attr('data-lang') )
                lang = $(this).attr('data-lang');

            let code_elem = '<pre class="' + line_numbers + '" '+ data_start + data_line + data_line_offset +'><code class="language-' + lang + '">' + $(this).html() + '</code></pre>';
            $(this).html(code_elem);

            Prism.highlightElement($(this).find('code')[0]);

        });

    };


    /**
     * Применить тему для Prism
     */
    window.prismJsApplyTheme = function (){

        if( $('style[data-prism-theme="'+ getCurrentTheme() +'"]').length )
            return false;

        $('style[data-prism-theme]').remove();

        let css_theme_code = prismThemesCss[getCurrentTheme()];
        $('head').append('<style data-prism-theme="'+ getCurrentTheme() +'">'+ css_theme_code +'</style>');

    }
