// News Page JavaScript - Related News Slider
document.addEventListener('DOMContentLoaded', function() {
    console.log('News page loaded');

    // Initialize Related News Swiper
    function initRelatedNewsSwiper() {
        const swiperElement = document.querySelector('.related-news-slider');
        if (!swiperElement) {
            console.log('Related news slider element not found');
            return;
        }

        // Проверяем доступность Swiper
        if (typeof window.Swiper === 'undefined') {
            console.log('Swiper not available yet, retrying in 500ms...');
            setTimeout(initRelatedNewsSwiper, 500);
            return;
        }

        console.log('Initializing Related News Swiper...');

        try {
            const relatedNewsSlider = new window.Swiper('.related-news-slider', {
                // Подключаем модули из глобального объекта
                modules: [window.SwiperModules.Navigation, window.SwiperModules.Pagination, window.SwiperModules.Autoplay],

                // Основные настройки
                slidesPerView: 1,
                spaceBetween: 16,

                // Responsive breakpoints
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 24,
                    },
                    1200: {
                        slidesPerView: 3,
                        spaceBetween: 24,
                    }
                },

                // Навигация
                navigation: {
                    nextEl: '.related-news-next',
                    prevEl: '.related-news-prev',
                },

                // Пагинация
                pagination: {
                    el: '.related-news-pagination',
                    clickable: true,
                },

                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },

                // Эффекты
                loop: true,
                speed: 600,
                grabCursor: true,
            });

            // Сохраняем экземпляр
            if (typeof window.swiperInstances === 'undefined') {
                window.swiperInstances = {};
            }
            window.swiperInstances.relatedNewsSlider = relatedNewsSlider;

        } catch (error) {
            console.error('❌ Error initializing Related News Swiper:', error);
        }
    }

    // Запускаем инициализацию
    initRelatedNewsSwiper();

    // News Pagination Manager - now using the pagination component
    class NewsPagination {
        constructor() {
            this.currentPage = 1;
            this.totalPages = 8; // В реальном приложении это будет приходить с сервера
            this.itemsPerPage = 12;
            this.totalItems = 96; // В реальном приложении это будет приходить с сервера

            // Pagination component instance
            this.pagination = null;

            this.resizeTimeout = null;

            this.init();
        }

        init() {
            this.initializePagination();
            this.setupResponsivePerPageListener();
            console.log('News pagination initialized');
        }

        // Initialize pagination component
        initializePagination() {
            const paginationContainer = document.getElementById('newsPagination');
            if (paginationContainer && typeof initializePagination === 'function') {
                const perPageOptions = this.getPerPageOptions();
                this.pagination = initializePagination(paginationContainer, {
                    currentPage: this.currentPage,
                    totalPages: this.totalPages,
                    totalItems: this.totalItems,
                    itemsPerPage: this.itemsPerPage,
                    showInfo: true,
                    showPerPage: true,
                    perPageOptions: perPageOptions,
                    onPageChange: (page) => {
                        this.currentPage = page;
                        this.goToPage(page);
                    },
                    onPerPageChange: (itemsPerPage) => {
                        this.itemsPerPage = itemsPerPage;
                        this.currentPage = 1;
                        this.updatePaginationData();
                        this.goToPage(1);
                    }
                });
            }
        }

        // Опции per-page в зависимости от ширины экрана
        getPerPageOptions() {
            const width = window.innerWidth;
            if (width < 768) { // Мобильные
                return [3, 6, 12];
            }
            if (width < 1200) { // Планшеты
                return [3, 6, 12];
            }
            // Десктоп
            return [6, 12, 24];
        }

        // Обновление опций при изменении ширины экрана (только для страницы новостей)
        setupResponsivePerPageListener() {
            window.addEventListener('resize', () => {
                clearTimeout(this.resizeTimeout);
                this.resizeTimeout = setTimeout(() => this.handlePerPageResponsiveChange(), 200);
            });
        }

        handlePerPageResponsiveChange() {
            if (!this.pagination) return;

            const newOptions = this.getPerPageOptions();
            const currentOptions = this.pagination.options.perPageOptions || [];

            // Проверяем, изменился ли набор опций
            const changed = newOptions.length !== currentOptions.length || newOptions.some((v, i) => v !== currentOptions[i]);
            if (!changed) return;

            // Сохраняем текущий per-page, при необходимости подгоняем под допустимые значения
            let newItemsPerPage = this.itemsPerPage;
            if (!newOptions.includes(newItemsPerPage)) {
                // Выбираем ближайшее допустимое значение (по умолчанию максимальное из списка)
                newItemsPerPage = newOptions[newOptions.length - 1];
                this.itemsPerPage = newItemsPerPage;
            }

            // Обновляем опции компонента и перерисовываем селект
            this.pagination.options.perPageOptions = newOptions;
            this.pagination.options.itemsPerPage = this.itemsPerPage;
            this.pagination.render();

            // Также обновляем отображаемую информацию
            this.updatePaginationData();
        }

        // Update pagination data
        updatePaginationData() {
            if (this.pagination) {
                this.pagination.setData({
                    currentPage: this.currentPage,
                    totalPages: this.totalPages,
                    totalItems: this.totalItems,
                    itemsPerPage: this.itemsPerPage
                });
            }
        }

        // Pagination events are now handled by the pagination component

        goToPage(page) {
            if (page < 1 || page > this.totalPages || page === this.currentPage) {
                return;
            }

            // В реальном приложении здесь был бы AJAX запрос
            this.currentPage = page;
            this.updatePaginationData();
            this.scrollToTop();

            // Имитация загрузки
            this.showLoading();
            setTimeout(() => {
                this.hideLoading();
            }, 500);
        }

        // Pagination display is now handled by the pagination component

        // Method to update pagination when data changes
        updatePaginationFromData(data) {
            if (data.totalItems !== undefined) {
                this.totalItems = data.totalItems;
            }
            if (data.itemsPerPage !== undefined) {
                this.itemsPerPage = data.itemsPerPage;
            }
            if (data.currentPage !== undefined) {
                this.currentPage = data.currentPage;
            }

            // Recalculate total pages
            this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

            // Update pagination component
            this.updatePaginationData();
        }



        showLoading() {
            const newsGrid = document.querySelector('.news-list-grid');
            if (newsGrid) {
                newsGrid.style.opacity = '0.5';
                newsGrid.style.pointerEvents = 'none';
            }
        }

        hideLoading() {
            const newsGrid = document.querySelector('.news-list-grid');
            if (newsGrid) {
                newsGrid.style.opacity = '1';
                newsGrid.style.pointerEvents = 'auto';
            }
        }

        scrollToTop() {
            const newsSection = document.querySelector('.news-list-section');
            if (newsSection) {
                newsSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    }

    // Initialize pagination
    const newsPagination = new NewsPagination();
});