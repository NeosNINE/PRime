// Import jQuery if not globally available
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// Import auth and header components
import './components/auth';
// import './components/header';
import '../user/components/pagination';

// Main application object
const SocnetApp = {
    // Theme management
    theme: {
        current: localStorage.getItem('theme') || (document.documentElement.getAttribute('data-theme') || 'light'),

        init() {
            this.apply(this.current);
            this.bindEvents();
        },

        toggle() {
            this.current = this.current === 'light' ? 'dark' : 'light';
            this.apply(this.current);
            this.save();
        },

        apply(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            this.current = theme;
            try {
                document.cookie = 'theme=' + encodeURIComponent(theme) + '; path=/; max-age=' + (60*60*24*365);
            } catch(e) {}
        },

        save() {
            localStorage.setItem('theme', this.current);
        },

        bindEvents() {
            $(document).on('click', '[data-toggle="theme"]', (e) => {
                e.preventDefault();
                this.toggle();
            });
        }
    },

    // Navigation management
    navigation: {
        init() {
            this.bindEvents();
            // Header behavior moved to separate module
        },

        // header-specific methods moved to GuestHeader module

        bindEvents() {
            // Language switcher
            $(document).on('click', '[data-lang]', function(e) {
                e.preventDefault();
                const lang = $(this).data('lang');
                console.log('Switching to language:', lang);
                // Implementation for language switching would go here
                $('.dropdown-menu').removeClass('active');
            });

            // Currency switcher
            $(document).on('click', '[data-currency]', function(e) {
                e.preventDefault();
                const currency = $(this).data('currency');
                console.log('Switching to currency:', currency);
                post('/currency/set', { currency }, function(){}, function(){});
                $('.dropdown-menu').removeClass('active');
            });
        }
    },

    // Scroll effects
    scrollEffects: {
        init() {
            this.setupBackToTop();
            this.setupHeaderScroll();
            this.setupScrollAnimations();
        },

        setupBackToTop() {
            const backToTop = $('#backToTop');

            $(window).on('scroll', () => {
                if ($(window).scrollTop() > $(window).height()) {
                    backToTop.addClass('visible');
                } else {
                    backToTop.removeClass('visible');
                }
            });

            backToTop.on('click', (e) => {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
            });
        },

        setupHeaderScroll() {
            const header = $('.header');
            let lastScrollTop = 0;

            $(window).on('scroll', () => {
                const scrollTop = $(window).scrollTop();

                if (scrollTop > 100) {
                    header.addClass('scrolled');
                } else {
                    header.removeClass('scrolled');
                }

                // Hide header on scroll down, show on scroll up
                if (scrollTop > lastScrollTop && scrollTop > 200) {
                    header.addClass('hidden');
                } else {
                    header.removeClass('hidden');
                }

                lastScrollTop = scrollTop;
            });
        },

        setupScrollAnimations() {
            // Intersection Observer for animations
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-fadeInUp');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                // Observe elements for animation
                $('.section-badge, .section-header, .platform-card, .stat-card, .review-card').each(function() {
                    observer.observe(this);
                });
            }
        }
    },

    // Sliders
    sliders: {
        init() {
            this.setupNewsSwiper();
        },

        setupNewsSwiper() {
            const swiperElement = document.querySelector('.news-swiper');
            if (!swiperElement) {
                console.warn('News swiper element not found');
                // Попробуем найти через некоторое время
                setTimeout(() => {
                    const delayedSwiperElement = document.querySelector('.news-swiper');
                    if (delayedSwiperElement) {
                        console.log('Found swiper element after delay, retrying...');
                        this.setupNewsSwiper();
                    }
                }, 500);
                return;
            }

            // Проверяем доступность Swiper
            if (typeof window.Swiper === 'undefined') {
                console.error('Swiper is not loaded');
                return;
            }

            // Проверяем наличие слайдов
            const slides = swiperElement.querySelectorAll('.swiper-slide');
            console.log(`Found ${slides.length} slides in swiper`);

            if (slides.length === 0) {
                console.warn('No slides found in swiper');
                return;
            }

            console.log('Initializing News Swiper...');

            try {
                const swiper = new window.Swiper('.news-swiper', {
                    modules: [window.SwiperModules.Navigation, window.SwiperModules.Pagination, window.SwiperModules.Autoplay],

                                    // Основные параметры
                spaceBetween: 20,
                speed: 600,
                loop: true,
                centeredSlides: false,
                watchOverflow: true,

                    // Автопрокрутка
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },

                                    // Отзывчивость
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                        spaceBetween: 15,
                    },
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                },

                    // Навигация
                    navigation: {
                        nextEl: '.news-section .container .swiper-button-next',
                        prevEl: '.news-section .container .swiper-button-prev',
                        disabledClass: 'swiper-button-disabled',
                    },

                    // Пагинация
                    pagination: {
                        el: '.news-swiper .swiper-pagination',
                        clickable: true,
                        dynamicBullets: false,
                        bulletClass: 'swiper-pagination-bullet',
                        bulletActiveClass: 'swiper-pagination-bullet-active',
                    },

                    // Эффекты
                    effect: 'slide',
                });

                // Сохраняем экземпляр Swiper для возможного использования позже
                if (typeof window.swiperInstances === 'undefined') {
                    window.swiperInstances = {};
                }
                window.swiperInstances.newsSwiper = swiper;

                console.log('Swiper instance created:', swiper);

            } catch (error) {
                console.error('Error initializing Swiper:', error);
            }
        }
    },

    // Counters animation
    counters: {
        init() {
            this.setupCounters();
        },

        setupCounters() {
            const counters = document.querySelectorAll('[data-count]');

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.animateCounter(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });

                counters.forEach(counter => observer.observe(counter));
            } else {
                // Fallback for browsers without IntersectionObserver
                counters.forEach(counter => this.animateCounter(counter));
            }
        },

        animateCounter(element) {
            const target = parseInt(element.getAttribute('data-count'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString();
            }, 16);
        }
    },

    // FAQ accordion
    faq: {
        init() {
            this.bindEvents();
        },

        bindEvents() {
            $(document).on('click', '[data-toggle="faq"]', function(e) {
                e.preventDefault();

                const faqItem = $(this).closest('.faq-item');
                const faqAnswer = faqItem.find('.faq-answer');

                // Close other FAQ items
                $('.faq-item').not(faqItem).removeClass('active');
                $('.faq-item').not(faqItem).find('.faq-answer').slideUp(300);

                // Toggle current FAQ item
                faqItem.toggleClass('active');
                faqAnswer.slideToggle(300);
            });
        }
    },

    // Smooth scrolling
    smoothScroll: {
        init() {
            this.bindEvents();
        },

        bindEvents() {
            $(document).on('click', 'a[href^="#"]', function(e) {
                const href = $(this).attr('href');
                if (href === '#') return;

                e.preventDefault();

                const target = $(href);
                if (target.length) {
                    const headerHeight = $('.header').outerHeight() || 80;
                    $('html, body').animate({
                        scrollTop: target.offset().top - headerHeight
                    }, 800);
                }
            });
        }
    },

    // Platform switching
    platforms: {
        init() {
            this.bindEvents();
            this.initPlatformData();
        },

        initPlatformData() {
            this.platformData = {
                telegram: {
                    title: 'Telegram',
                    description: 'Создайте аудиторию, которая слышит. Мы обеспечим живых участников и просмотры, чтобы канал звучал громче заявлений Павла Дурова.'
                },
                instagram: {
                    title: 'Instagram',
                    description: 'Попадите из ленты — сразу в «Рекомендации». Настоящие подписчики, лайки и просмотры Reels заставят ваши публикации впечатлить даже Роналду.'
                },
                facebook: {
                    title: 'Facebook',
                    description: 'Будьте в каждой ленте. Лайки, друзья и репосты поднимут вашу страницу на уровень империи Марка Цукерберга.'
                },
                youtube: {
                    title: 'YouTube',
                    description: 'Сделайте каждое видео известным событием! Живые просмотры, лайки и подписчики выведут канал в тренды рядом с MrBeast.'
                },
                twitter: {
                    title: 'Twitter (X)',
                    description: 'Будьте в тренде обсуждений! Подписчики и ретвиты поднимут твиты выше ленты Илона Маска.'
                },
                tiktok: {
                    title: 'TikTok',
                    description: 'Станьте вирусным за один свайп. Живые просмотры, лайки и подписчики выведут ваши ролики в рекомендации.'
                },
                spotify: {
                    title: 'Spotify',
                    description: 'Живые прослушивания, сохранения и фолловеры поднимут треки рядом с The Weeknd в топ-плейлистах.'
                },
                discord: {
                    title: 'Discord',
                    description: 'Хотите, чтобы ваш Discord-сервер был активнее, чем у PewDiePie? Мы приведём живых участников и разгоним чаты, чтобы комьюнити гудело круглосуточно.'
                }
            };
        },

        bindEvents() {
            $(document).on('click', '.platform-card', (e) => {
                e.preventDefault();
                const platform = $(e.currentTarget).data('platform');
                this.switchPlatform(platform);
            });
        },

        switchPlatform(platform) {
            // Remove active class from all cards
            $('.platform-card').removeClass('active');

            // Add active class to clicked card
            $(`.platform-card[data-platform="${platform}"]`).addClass('active');

            // Update platform info
            const data = this.platformData[platform];
            if (data) {
                const $description = $('#platform-description');

                // Fade out
                $description.fadeOut(200, () => {
                    // Update content - only description
                    $description.text(data.description);

                    // Fade in
                    $description.fadeIn(200);
                });
            }
        }
    },

    // Form handling
    forms: {
        init() {
            this.setupValidation();
            this.bindEvents();
        },

        setupValidation() {
            // Add custom validation styles
            $('input[required], textarea[required], select[required]').on('blur', function() {
                if (this.checkValidity()) {
                    $(this).removeClass('invalid').addClass('valid');
                } else {
                    $(this).removeClass('valid').addClass('invalid');
                }
            });
        },

        bindEvents() {
            // Handle form submissions
            $(document).on('submit', 'form', function(e) {
                const form = $(this);

                // Basic validation
                const requiredFields = form.find('[required]');
                let isValid = true;

                requiredFields.each(function() {
                    if (!this.checkValidity()) {
                        $(this).addClass('invalid');
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first invalid field
                    // const firstInvalid = form.find('.invalid').first();
                    // if (firstInvalid.length) {
                    //     $('html, body').animate({
                    //         scrollTop: firstInvalid.offset().top - 100
                    //     }, 500);
                    // }
                }
            });
        }
    },

    // Utilities
    utils: {
        // Debounce function
        debounce(func, wait, immediate) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func(...args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func(...args);
            };
        },

        // Throttle function
        throttle(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // Check if element is in viewport
        isInViewport(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        },

        // Format number with locale
        formatNumber(num, locale = 'en-US') {
            return new Intl.NumberFormat(locale).format(num);
        }
    },

    // Performance optimizations
    performance: {
        init() {
            this.lazyLoadImages();
            this.preloadCriticalResources();
        },

        lazyLoadImages() {
            if ('loading' in HTMLImageElement.prototype) {
                // Native lazy loading
                $('img[data-src]').each(function() {
                    this.src = this.dataset.src;
                    this.loading = 'lazy';
                });
            } else {
                // Fallback using Intersection Observer
                if ('IntersectionObserver' in window) {
                    const imageObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const img = entry.target;
                                img.src = img.dataset.src;
                                img.classList.remove('lazy');
                                imageObserver.unobserve(img);
                            }
                        });
                    });

                    $('img[data-src]').each(function() {
                        imageObserver.observe(this);
                    });
                }
            }
        },

        preloadCriticalResources() {
            // Preload critical images and fonts
            const criticalResources = [
                // Add critical resources here
            ];

            criticalResources.forEach(resource => {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.href = resource.url;
                link.as = resource.type;
                document.head.appendChild(link);
            });
        }
    },

    // Analytics and tracking
    analytics: {
        init() {
            this.trackPageView();
            this.bindTrackingEvents();
        },

        trackPageView() {
            // Implement page view tracking
            console.log('Page view tracked:', window.location.pathname);
        },

        bindTrackingEvents() {
            // Track button clicks
            $(document).on('click', '.btn-primary', function() {
                const buttonText = $(this).text().trim();
                console.log('Primary button clicked:', buttonText);
            });

            // Track form submissions
            $(document).on('submit', 'form', function() {
                const formId = $(this).attr('id') || 'unknown-form';
                console.log('Form submitted:', formId);
            });

            // Track external links
            $(document).on('click', 'a[href^="http"]', function() {
                const url = $(this).attr('href');
                console.log('External link clicked:', url);
            });
        }
    },

    // Notifications management
    notifications_manager: {
        notifications: [],
        unreadCount: 0,

        init() {
            this.loadNotifications();
            this.bindEvents();
            this.updateBadge();
            console.log('Notifications manager initialized');
        },

        bindEvents() {
            // Toggle notifications dropdown
            $(document).on('click', '.notifications-btn', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown();
            });

            // Close dropdown when clicking outside
            $(document).on('click', (e) => {
                if (!$(e.target).closest('.notifications-selector').length) {
                    this.closeDropdown();
                }
            });

            // Handle notification click (supports optional link)
            $(document).on('click', '.notification-item', (e) => {
                const $item = $(e.currentTarget);
                const notificationId = $item.data('id');
                const notification = this.notifications.find(n => n.id === notificationId);

                if (!notification) return;

                // Mark as read first
                this.markAsRead(notificationId);

                // Navigate if url is provided
                if (notification.url) {
                    const target = notification.target === '_blank' || notification.openInNewTab ? '_blank' : '_self';
                    window.open(notification.url, target);
                }
            });

            // Handle notification delete button
            $(document).on('click', '.notification-delete', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const $item = $(e.currentTarget).closest('.notification-item');
                const notificationId = $item.data('id');
                this.deleteNotification(notificationId);
            });

            // Handle mark all as read
            $(document).on('click', '.mark-all-read-btn', (e) => {
                e.stopPropagation();
                this.markAllAsRead();
            });
        },

        loadNotifications() {
            // Simulate loading notifications (in real app this would be an API call)
            this.notifications = this.generateSampleNotifications();
            this.unreadCount = this.notifications.filter(n => !n.read).length;
            this.renderNotifications();
        },

        generateSampleNotifications() {
            const now = new Date();
            return [
                {
                    id: 1,
                    type: 'ticket',
                    title: 'Новый ответ в тикете #12345',
                    text: 'Поддержка ответила на ваш запрос о проблеме с заказом. Пожалуйста, проверьте тикет.',
                    time: new Date(now - 5 * 60 * 1000), // 5 minutes ago
                    read: false,
                    icon: 'fas fa-ticket-alt'
                },
                {
                    id: 2,
                    type: 'security',
                    title: 'Вход с нового IP-адреса',
                    text: 'Обнаружен вход в ваш аккаунт с IP 192.168.1.100 (Москва, Россия)',
                    time: new Date(now - 2 * 60 * 60 * 1000), // 2 hours ago
                    read: false,
                    icon: 'fas fa-shield-alt'
                },
                {
                    id: 3,
                    type: 'account',
                    title: 'Email успешно изменен',
                    text: 'Ваш email адрес был успешно изменен на новый адрес',
                    time: new Date(now - 5 * 60 * 60 * 1000), // 5 hours ago
                    read: true,
                    icon: 'fas fa-envelope'
                },
                {
                    id: 4,
                    type: 'api',
                    title: 'API ключ сгенерирован',
                    text: 'Новый API ключ был успешно сгенерирован для вашего аккаунта',
                    time: new Date(now - 1 * 24 * 60 * 60 * 1000), // 1 day ago
                    read: true,
                    icon: 'fas fa-key'
                },
                {
                    id: 5,
                    type: 'security',
                    title: 'Пароль изменен',
                    text: 'Ваш пароль был успешно изменен с IP 192.168.1.100',
                    time: new Date(now - 2 * 24 * 60 * 60 * 1000), // 2 days ago
                    read: true,
                    icon: 'fas fa-lock'
                }
            ];
        },

        renderNotifications() {
            const container = $('#notificationsList');

            if (this.notifications.length === 0) {
                container.html(`
                    <div class="notifications-empty">
                        <div class="empty-icon">
                            <i class="fas fa-bell-slash"></i>
                        </div>
                        <div class="empty-text">Нет уведомлений</div>
                    </div>
                `);
                return;
            }

            // Group notifications by date
            let lastDateKey = null;
            const parts = [];

            this.notifications.forEach(notification => {
                const dateKey = this.getDateLabel(notification.time);

                if (dateKey !== lastDateKey) {
                    lastDateKey = dateKey;
                    parts.push(`
                        <div class="notification-date-separator">
                            <span>${dateKey}</span>
                        </div>
                    `);
                }

                parts.push(this.createNotificationHtml(notification));
            });

            container.html(parts.join(''));
        },

        createNotificationHtml(notification) {
            const timeAgo = this.formatTimeAgo(notification.time);
            const unreadClass = notification.read ? '' : 'unread';
            const statusDot = notification.read ? 'read' : '';
            const statusText = notification.read ? 'прочитано' : 'новое';

            return `
                <div class="notification-item ${unreadClass} ${notification.url ? 'is-link' : ''}" data-id="${notification.id}">
                    <div class="notification-icon type-${notification.type}">
                        <i class="${notification.icon}"></i>
                    </div>
                    <div class="notification-content">
                        <button class="notification-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-text">${notification.text}</div>
                        <div class="notification-meta">
                            <div class="notification-time">${timeAgo}</div>
                            <div class="notification-status">
                                <div class="status-dot ${statusDot}"></div>
                                <div class="status-text">${statusText}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },

        toggleDropdown() {
            const selector = $('.notifications-selector');
            const isActive = selector.hasClass('active');

            // Close all other dropdowns first
            $('.control-dropdown').removeClass('active');

            if (!isActive) {
                selector.addClass('active');
                // Reload notifications when opening
                this.loadNotifications();
            }
        },

        closeDropdown() {
            $('.notifications-selector').removeClass('active');
        },

        markAsRead(notificationId) {
            const notification = this.notifications.find(n => n.id === notificationId);
            if (notification && !notification.read) {
                notification.read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this.updateBadge();
                this.renderNotifications();

                // In real app, send API request to mark as read
                console.log('Marked notification as read:', notificationId);
            }
        },

        markAllAsRead() {
            let marked = 0;
            this.notifications.forEach(notification => {
                if (!notification.read) {
                    notification.read = true;
                    marked++;
                }
            });

            if (marked > 0) {
                this.unreadCount = 0;
                this.updateBadge();
                this.renderNotifications();

                // Show success message
                if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
                    SocnetApp.notifications.showSuccess(`Отмечено как прочитанные: ${marked} уведомлений`);
                }

                console.log('Marked all notifications as read:', marked);
            }
        },

        updateBadge() {
            const badge = $('#notificationsBadge');
            badge.attr('data-count', this.unreadCount);
            badge.text(this.unreadCount);
        },

        addNotification(notification) {
            // Add new notification to the beginning of the list
            notification.id = Date.now(); // Simple ID generation
            notification.time = new Date();
            notification.read = false;

            this.notifications.unshift(notification);
            this.unreadCount++;
            this.updateBadge();

            // If dropdown is open, re-render
            if ($('.notifications-selector').hasClass('active')) {
                this.renderNotifications();
            }

            console.log('New notification added:', notification);
        },

        deleteNotification(notificationId) {
            const index = this.notifications.findIndex(n => n.id === notificationId);
            if (index === -1) return;

            // Adjust unread counter if needed
            if (!this.notifications[index].read) {
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this.updateBadge();
            }

            // Remove from list and re-render
            this.notifications.splice(index, 1);
            this.renderNotifications();

            // Optional feedback
            if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
                SocnetApp.notifications.showInfo('Уведомление удалено');
            }
        },

        formatTimeAgo(date) {
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));

            if (diffInMinutes < 1) {
                return 'только что';
            } else if (diffInMinutes < 60) {
                return `${diffInMinutes} мин назад`;
            } else if (diffInMinutes < 1440) { // 24 hours
                const hours = Math.floor(diffInMinutes / 60);
                return `${hours} ч назад`;
            } else {
                const days = Math.floor(diffInMinutes / 1440);
                return `${days} дн назад`;
            }
        },

        getDateLabel(date) {
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const yesterday = new Date(today.getTime() - 24 * 60 * 60 * 1000);
            const notificationDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());

            if (notificationDate.getTime() === today.getTime()) {
                return 'Сегодня';
            } else if (notificationDate.getTime() === yesterday.getTime()) {
                return 'Вчера';
            } else {
                // Format date for older notifications
                return date.toLocaleDateString('ru-RU', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }
        }
    },

    // Initialize all modules
    init() {
        $(document).ready(() => {
            console.log('SocnetApp starting initialization...');

            this.theme.init();
            this.navigation.init();
            this.scrollEffects.init();

            // Проверяем доступность Swiper перед инициализацией слайдеров
            if (typeof window.Swiper !== 'undefined') {
                console.log('Swiper is available, initializing sliders...');
                this.sliders.init();
            } else {
                console.error('Swiper is not available, sliders will not work');
                // Пытаемся инициализировать через некоторое время
                setTimeout(() => {
                    if (typeof window.Swiper !== 'undefined') {
                        console.log('Swiper became available, initializing sliders delayed...');
                        this.sliders.init();
                    }
                }, 1000);
            }

            this.counters.init();
            this.platforms.init();
            this.faq.init();
            this.smoothScroll.init();
            this.forms.init();
            this.performance.init();
            this.analytics.init();
            this.notifications_manager.init();

            console.log('SocnetApp initialized successfully');
        });
    }
};

// Auto-initialize when script loads
SocnetApp.init();

// Global notifications management shortcuts for convenience
window.markAllNotificationsRead = () => SocnetApp.notifications_manager.markAllAsRead();
window.addNotification = (notification) => SocnetApp.notifications_manager.addNotification(notification);

// Expose to global scope for debugging
window.SocnetApp = SocnetApp;




