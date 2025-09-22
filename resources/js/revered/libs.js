require('../bootstrap');

window.bootstrap = require('../libs/bootstrap.bundle');
require('moment');
require('overlayscrollbars/js/jquery.overlayScrollbars');
window.swal = require('sweetalert2');
require('select2');
window.select2_en = require('select2/src/js/select2/i18n/en');
window.select2_ru = require('select2/src/js/select2/i18n/ru');
require('../libs/ace/ace');
require('../libs/ace/mode-html');
require('daterangepicker');
require('jquery-sortablejs');
import tinymce from 'tinymce';
require('apexcharts');
window.pluralize = require('pluralize');


window.Prism = require('prismjs');
require('prismjs/components/prism-markup-templating.min');
require('prismjs/components/prism-php.min');
require('prismjs/components/prism-sql.min');
require('prismjs/plugins/line-highlight/prism-line-highlight.min');
require('prismjs/plugins/line-numbers/prism-line-numbers.min');
Prism.manual = true;


require('../libs/revered-helpers');
