require('../bootstrap');

require('moment');
require('../libs/revered-helpers');
require('./components/custom-select');

// Swiper для слайдеров
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

// Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

// Делаем Swiper доступным глобально
window.Swiper = Swiper;
window.SwiperModules = { Navigation, Pagination, Autoplay };

// Bootstrap
window.bootstrap = require('../libs/bootstrap.bundle.js');
