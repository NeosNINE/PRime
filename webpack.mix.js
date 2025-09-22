const mix = require('laravel-mix');
const fs = require("fs");
const path = require('path');

//Получить список файлов директории
let getFiles = function (dir){

    let files = [];

    if( !fs.existsSync(dir) )
        return files;

    fs.readdirSync(dir).forEach(file => {

        const absolute = path.join(dir, file);

        if (fs.statSync(absolute).isDirectory()){

            return getFiles(absolute).map(function (filepath){
                files.push(filepath);
            });

        }else{

            return files.push(absolute);

        }

    });

    return files;

};


//Обработать все файлы директории (JS)
let allInDir_JS = function (dir, output_dir) {

    let output_path = '';

    getFiles(dir).map(function (filepath) {

        filepath = filepath.replace(/\\/g, '/');

        if( path.dirname(filepath) !== dir ){

            output_path = output_dir + filepath.replace(dir, '');

        }else{

            output_path = output_dir;

        }

        mix.js(filepath, output_path);

    });

};

//Обработать все файлы директории (SASS)
let allInDir_SASS = function (dir, output_dir) {

    let output_path = '';

    getFiles(dir).map(function (filepath) {

        filepath = filepath.replace(/\\/g, '/');

        if( path.dirname(filepath) !== dir ){

            output_path = output_dir + filepath.replace(dir, '').replace('.scss', '.css');

        }else{

            output_path = output_dir;

        }

        mix.sass(filepath, output_path);

    });

};


/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */


/*
    Guest
 */
mix.js('resources/js/guest/app.js', 'public/assets/guest/js');
mix.js('resources/js/guest/libs.js', 'public/assets/guest/js');
mix.sass('resources/css/guest/app.scss', 'public/assets/guest/css');
mix.sass('resources/css/guest/libs.scss', 'public/assets/guest/css');
allInDir_JS('resources/js/guest/pages', 'public/assets/guest/js/pages');
allInDir_SASS('resources/css/guest/pages', 'public/assets/guest/css/pages');
allInDir_JS('resources/js/guest/components', 'public/assets/guest/js/components');
allInDir_SASS('resources/css/guest/components', 'public/assets/guest/css/components');



/*
    User
 */
mix.js('resources/js/user/app.js', 'public/assets/user/js');
mix.js('resources/js/user/libs.js', 'public/assets/user/js');
mix.sass('resources/css/user/app.scss', 'public/assets/user/css');
mix.sass('resources/css/user/libs.scss', 'public/assets/user/css');
allInDir_JS('resources/js/user/pages', 'public/assets/user/js/pages');
allInDir_SASS('resources/css/user/pages', 'public/assets/user/css/pages');
allInDir_JS('resources/js/user/components', 'public/assets/user/js/components');
allInDir_SASS('resources/css/user/components', 'public/assets/user/css/components');



/*
    Admin
 */
mix.js('resources/js/admin/app.js', 'public/assets/admin/js');
mix.js('resources/js/admin/libs.js', 'public/assets/admin/js');
mix.sass('resources/css/admin/app.scss', 'public/assets/admin/css');
mix.sass('resources/css/admin/libs.scss', 'public/assets/admin/css');
allInDir_JS('resources/js/admin/pages', 'public/assets/admin/js/pages');
allInDir_SASS('resources/css/admin/pages', 'public/assets/admin/css/pages');
allInDir_SASS('resources/css/admin/themes', 'public/assets/admin/css/themes');

//Ставим версию для всех файлов
mix.version();
