
// Import date range picker component
import './components/date-range-picker.js';
import './components/header.js';

/**
 *  Выход из личного кабинета
 */
eventClick('.link-logout', function (elem) {

    $('.form-logout').submit();

});



/**
 * Каждые 5 секунд получаем информацию с backend для USER
 */
setInterval(function () {

    //Параметры, которые передаем на сервер
    let request_data = {

    };


    //Выполняем запрос
    request('GET', '/user/refresh-data', request_data, function (response) {

        //Обновляем CSRF token
        if (response.csrf_token)
            setCSRF(response.csrf_token);



        //Что-то пошло не так
    }, function (error, code) {

        console.error(code + " : " + error);

    });

}, 5000);

// Import jQuery if not globally available
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// Import Pagination Component
import './components/pagination.js';
import Notifications from './components/notifications.js';

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

        bindEvents() {
            // Language switcher
            $(document).on('click', '[data-lang]', function (e) {
                e.preventDefault();
                const lang = $(this).data('lang');
                console.log('Switching to language:', lang);
                // Implementation for language switching would go here
                $('.dropdown-menu').removeClass('active');
            });

            // Currency switcher (handled in setupBalanceSelector for visual update)
            $(document).on('click', '[data-currency]', function (e) {
                e.preventDefault();
                const currency = $(this).data('currency');
                // If needed, send currency preference to backend here
            });
        },

        // Header-specific helpers moved to Header module
    },

    // Notifications management
    notifications: {
        init() {
            this.bindEvents();
            this.updateBadge();
            this.initToastSystem();
        },

        bindEvents() {
            // Обработка клика на иконку уведомлений
            $(document).on('click', '.notifications-btn', function (e) {
                // Простая анимация клика
                const $icon = $(this).find('.fas.fa-bell');
                $icon.addClass('animate__animated animate__swing');

                setTimeout(() => {
                    $icon.removeClass('animate__animated animate__swing');
                }, 1000);
            });
        },

        // Обновление бейджа уведомлений
        updateBadge(count = null) {
            const $badge = $('.notification-badge');

            if (count !== null) {
                $badge.attr('data-count', count).text(count);
                if (count > 0) {
                    $badge.show();
                } else {
                    $badge.hide();
                }
            }
        },

        // Получение количества уведомлений (заглушка)
        getNotificationCount() {
            // В реальном приложении здесь будет AJAX запрос
            return parseInt($('.notification-badge').attr('data-count')) || 0;
        },

        // Уменьшение счетчика при просмотре уведомлений
        markAsRead(decrease = 1) {
            const currentCount = this.getNotificationCount();
            const newCount = Math.max(0, currentCount - decrease);
            this.updateBadge(newCount);
        },

        // Initialize toast notification system
        initToastSystem() {
            this.toastContainer = null;
            this.toastIdCounter = 1;
            this.toasts = [];
            this.ensureContainer();
        },

        // Ensure notification container exists
        ensureContainer() {
            let container = $('#notificationContainer');
            if (container.length === 0) {
                $('body').append('<div id="notificationContainer" class="notification-container"></div>');
                container = $('#notificationContainer');
            }
            this.toastContainer = container;
        },

        // Show success notification
        showSuccess(message, duration = 5000) {
            this.showToast('success', message, duration);
        },

        // Show error notification
        showError(message, duration = 5000) {
            this.showToast('error', message, duration);
        },

        // Show warning notification
        showWarning(message, duration = 5000) {
            this.showToast('warning', message, duration);
        },

        // Show info notification
        showInfo(message, duration = 5000) {
            this.showToast('info', message, duration);
        },

        // Main toast notification function
        showToast(type, message, duration = 5000) {
            this.ensureContainer();

            // Check if we already have too many notifications
            const maxNotifications = 4;
            const currentNotifications = this.toastContainer.find('.notification').length;

            if (currentNotifications >= maxNotifications) {
                // Remove the oldest notification to make room
                const oldestToast = this.toastContainer.find('.notification').first();
                if (oldestToast.length) {
                    const oldestId = oldestToast.attr('id');
                    this.removeToast(oldestId);
                }
            }

            const toastId = `notification-${this.toastIdCounter++}`;

            // Icon based on type (using SVG icons like in screenshots)
            const icons = {
                success: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M7 10L9 12L13 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`,
                error: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M10 6L10 10M10 14L10 14.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`,
                warning: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 2L18 18H2L10 2Z" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M10 8L10 12M10 16L10 16.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`,
                info: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M10 6L10 10M10 14L10 14.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`
            };

            // Create toast HTML
            const toastHtml = `
                <div class="notification ${type}" id="${toastId}">
                    <div class="notification-icon">
                        ${icons[type]}
                    </div>
                    <div class="notification-content">
                        <div class="notification-message">${message}</div>
                    </div>
                    <button type="button" class="notification-close" onclick="SocnetApp.notifications.removeToast('${toastId}')">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            `;

            // Add to container
            this.toastContainer.append(toastHtml);

            // Show with animation
            const $toast = $(`#${toastId}`);
            setTimeout(() => {
                $toast.addClass('show');
            }, 50);

            // Auto remove after duration
            if (duration > 0) {
                setTimeout(() => {
                    this.removeToast(toastId);
                }, duration);
            }

            // Track toast
            this.toasts.push({
                id: toastId,
                type: type,
                timestamp: Date.now()
            });

        },

        // Remove specific toast
        removeToast(toastId) {
            const $toast = $(`#${toastId}`);
            if ($toast.length) {
                $toast.addClass('hide');

                // Clear timeout if exists
                if (this.notificationTimeouts && this.notificationTimeouts[toastId]) {
                    clearTimeout(this.notificationTimeouts[toastId]);
                    delete this.notificationTimeouts[toastId];
                }

                setTimeout(() => {
                    $toast.remove();

                    // Remove from tracking
                    this.toasts = this.toasts.filter(t => t.id !== toastId);
                }, 300);
            }
        },

        // Clear all toast notifications
        clearAllToasts() {
            $('.notification').addClass('hide');

            setTimeout(() => {
                if (this.toastContainer) {
                    this.toastContainer.empty();
                }
                this.toasts = [];
            }, 300);
        },

        // Smart notification system - prevents spam
        showSmartToast(type, message, duration = 5000, preventDuplicate = true) {
            // Check if we already have the same message
            if (preventDuplicate) {
                const existingNotification = this.toastContainer.find('.notification-message').filter(function () {
                    return $(this).text() === message;
                });

                if (existingNotification.length > 0) {
                    // Update existing notification instead of creating new one
                    const $notification = existingNotification.closest('.notification');
                    $notification.removeClass('success error warning info').addClass(type);

                    // Reset timer for existing notification
                    const notificationId = $notification.attr('id');
                    this.resetNotificationTimer(notificationId, duration);

                    console.log(`Updated existing notification: [${type.toUpperCase()}] ${message}`);
                    return;
                }
            }

            // Show new notification
            this.showToast(type, message, duration);
        },

        // Reset notification timer
        resetNotificationTimer(notificationId, duration) {
            // Clear existing timeout if any
            if (this.notificationTimeouts && this.notificationTimeouts[notificationId]) {
                clearTimeout(this.notificationTimeouts[notificationId]);
            }

            // Set new timeout
            if (duration > 0) {
                if (!this.notificationTimeouts) {
                    this.notificationTimeouts = {};
                }

                this.notificationTimeouts[notificationId] = setTimeout(() => {
                    this.removeToast(notificationId);
                    delete this.notificationTimeouts[notificationId];
                }, duration);
            }
        }
    },

    // Profile management
    profile: {
        init() {
            this.bindEvents();
        },

        bindEvents() {
            // Обработка клика на выход
            // $(document).on('click', '.logout-option', function (e) {
            //     e.preventDefault();

            //     // Показываем подтверждение
            //     if (confirm('Вы действительно хотите выйти?')) {
            //         console.log('Logging out user...');

            //         // В реальном приложении здесь будет:
            //         // window.location.href = '/logout';
            //         // или AJAX запрос для выхода

            //         // Пока что просто закрываем dropdown
            //         $('.dropdown-menu').removeClass('active');
            //         $('.control-dropdown').removeClass('active');
            //     }
            // });

            // Обработка кликов на остальные опции профиля
            $(document).on('click', '.profile-option:not(.logout-option)', function (e) {
                e.preventDefault();

                const optionText = $(this).find('span').text();
                console.log('Profile option clicked:', optionText);

                // Закрываем dropdown
                $('.dropdown-menu').removeClass('active');
                $('.control-dropdown').removeClass('active');

                // В реальном приложении здесь будет навигация к соответствующим страницам
            });
        },

        // Обновление имени пользователя
        updateUserName(name) {
            $('.profile-name').text(name);
            console.log('User name updated to:', name);
        },

        // Получение текущего имени пользователя
        getUserName() {
            return $('.profile-name').text();
        }
    },

    // Sidebar management
    sidebar: {
        init() {
            this.bindEvents();
            this.handleActiveStates();
        },

        bindEvents() {
            // Обработка мобильного sidebar (если добавить кнопку открытия)
            $(document).on('click', '.sidebar-toggle', function (e) {
                e.preventDefault();
                this.toggleMobileSidebar();
            }.bind(this));

            // Закрытие sidebar при клике на overlay
            $(document).on('click', '.sidebar-overlay', function () {
                this.closeMobileSidebar();
            }.bind(this));

            // Закрытие sidebar при нажатии Escape
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    this.closeMobileSidebar();
                }
            }.bind(this));
        },

        // Управление активными состояниями
        handleActiveStates() {
            // Устанавливаем активную ссылку по URL (в будущем)
            const currentPath = window.location.pathname;
            // Здесь можно добавить логику для установки активной ссылки по URL
        },

        // Переключение мобильного sidebar
        toggleMobileSidebar() {
            const $sidebar = $('.sidebar');
            const $overlay = $('.sidebar-overlay');

            if ($sidebar.hasClass('sidebar-open')) {
                this.closeMobileSidebar();
            } else {
                this.openMobileSidebar();
            }
        },

        // Открытие мобильного sidebar
        openMobileSidebar() {
            $('.sidebar').addClass('sidebar-open');
            $('.sidebar-overlay').addClass('active');
            $('body').addClass('sidebar-mobile-open');
            console.log('Mobile sidebar opened');
        },

        // Закрытие мобильного sidebar
        closeMobileSidebar() {
            $('.sidebar').removeClass('sidebar-open');
            $('.sidebar-overlay').removeClass('active');
            $('body').removeClass('sidebar-mobile-open');
            console.log('Mobile sidebar closed');
        },

        // Обновление бейджа (например, для тикетов)
        updateBadge(linkText, count) {
            $('.sidebar-nav_link').each(function () {
                if ($(this).find('.sidebar-nav_text').text().trim() === linkText) {
                    let $badge = $(this).find('.sidebar-nav_badge');

                    if (count > 0) {
                        if ($badge.length === 0) {
                            // Создаем бейдж если его нет
                            $badge = $('<span class="sidebar-nav_badge"></span>');
                            $(this).append($badge);
                        }
                        $badge.text(count);
                    } else {
                        // Убираем бейдж если count = 0
                        $badge.remove();
                    }

                    console.log(`Badge updated for ${linkText}:`, count);
                    return false;
                }
            });
        }
    },

    // Counters animation
    counters: {
        init() {
            this.setupCounters();
        },

        setupCounters() {
            // Исключаем бейдж уведомлений из плавной анимации чисел
            const counters = Array.from(document.querySelectorAll('[data-count]')).filter(el =>
                !el.classList.contains('notification-badge') && el.id !== 'notificationsBadge'
            );

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
            return function (...args) {
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
                $('img[data-src]').each(function () {
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

                    $('img[data-src]').each(function () {
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
            $(document).on('click', '.btn-primary', function () {
                const buttonText = $(this).text().trim();
                console.log('Primary button clicked:', buttonText);
            });

            // Track form submissions
            $(document).on('submit', 'form', function () {
                const formId = $(this).attr('id') || 'unknown-form';
                console.log('Form submitted:', formId);
            });

            // Track external links
            $(document).on('click', 'a[href^="http"]', function () {
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

    // Bootstrap tooltips management
    tooltips: {
        init() {
            this.initializeTooltips();
            this.bindDynamicTooltips();
            console.log('Tooltips initialized');
        },

        initializeTooltips() {
            // Initialize all tooltips on page load
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
                    return new bootstrap.Tooltip(tooltipTriggerEl, {
                        trigger: 'hover focus'
                    });
                });
                console.log(`Initialized ${tooltipList.length} tooltips`);
            } else {
                console.warn('Bootstrap is not loaded, tooltips will not work');
            }
        },

        bindDynamicTooltips() {
            // Auto-initialize tooltips for dynamically added content
            const observer = new MutationObserver((mutations) => {
                let shouldReinit = false;
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                const hasTooltips = node.querySelector && node.querySelector('[data-bs-toggle="tooltip"]');
                                const isTooltip = node.getAttribute && node.getAttribute('data-bs-toggle') === 'tooltip';
                                if (hasTooltips || isTooltip) {
                                    shouldReinit = true;
                                }
                            }
                        });
                    }
                });

                if (shouldReinit) {
                    // Small delay to ensure DOM is ready
                    this.initializeTooltips();
                }
            });

            // Observe the entire document for changes
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },

        // Method to manually reinitialize tooltips
        reinitialize() {
            this.initializeTooltips();
        }
    },

    // Bootstrap popovers management
    popovers: {
        init() {
            this.initializePopovers();
            this.bindDynamicPopovers();
            console.log('Popovers initialized');
        },

        initializePopovers() {
            // Initialize all popovers on page load
            if (typeof bootstrap !== 'undefined') {
                const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
                const popoverList = [...popoverTriggerList].map(popoverTriggerEl => {
                    return new bootstrap.Popover(popoverTriggerEl, {
                        trigger: 'hover',
                        html: true,
                        placement: 'top'
                    });
                });
                console.log(`Initialized ${popoverList.length} popovers`);
            } else {
                console.warn('Bootstrap is not loaded, popovers will not work');
            }
        },

        bindDynamicPopovers() {
            // Auto-initialize popovers for dynamically added content
            const observer = new MutationObserver((mutations) => {
                let shouldReinit = false;
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                const hasPopovers = node.querySelector && node.querySelector('[data-bs-toggle="popover"]');
                                const isPopover = node.getAttribute && node.getAttribute('data-bs-toggle') === 'popover';
                                if (hasPopovers || isPopover) {
                                    shouldReinit = true;
                                }
                            }
                        });
                    }
                });

                if (shouldReinit) {
                    // Small delay to ensure DOM is ready
                    this.initializePopovers();
                }
            });

            // Observe the entire document for changes
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },

        // Method to manually reinitialize popovers
        reinitialize() {
            this.initializePopovers();
        }
    },

    // Initialize all modules
    init() {
        $(document).ready(() => {
            console.log('SocnetApp starting initialization...');

            this.theme.init();
            this.navigation.init();
            this.notifications.init();
            Notifications.init();
            this.tooltips.init();
            this.popovers.init();
            this.profile.init();
            this.sidebar.init();

            this.counters.init();
            this.performance.init();
            this.analytics.init();

            console.log('SocnetApp initialized successfully');
        });
    }
};

// Auto-initialize when script loads
SocnetApp.init();

// Expose to global scope for debugging
window.SocnetApp = SocnetApp;

// Global notification shortcuts for convenience
window.showSuccess = (message, duration) => SocnetApp.notifications.showSuccess(message, duration);
window.showError = (message, duration) => SocnetApp.notifications.showError(message, duration);
window.showWarning = (message, duration) => SocnetApp.notifications.showWarning(message, duration);
window.showInfo = (message, duration) => SocnetApp.notifications.showInfo(message, duration);

// Smart notification functions
window.showSmartSuccess = (message, duration, preventDuplicate) => SocnetApp.notifications.showSmartToast('success', message, duration, preventDuplicate);
window.showSmartError = (message, duration, preventDuplicate) => SocnetApp.notifications.showSmartToast('error', message, duration, preventDuplicate);
window.showSmartWarning = (message, duration, preventDuplicate) => SocnetApp.notifications.showSmartToast('warning', message, duration, preventDuplicate);
window.showSmartInfo = (message, duration, preventDuplicate) => SocnetApp.notifications.showSmartToast('info', message, duration, preventDuplicate);

// Global tooltip shortcuts for convenience
window.initTooltips = () => SocnetApp.tooltips.reinitialize();

// Global popover shortcuts for convenience
window.initPopovers = () => SocnetApp.popovers.reinitialize();

// Global notifications management shortcuts for convenience
window.markAllNotificationsRead = () => Notifications.dropdown.markAllAsRead();
window.addNotification = (notification) => Notifications.dropdown.add(notification);



eventClick('.sidebar_open', function () {
    $('.sidebar').toggleClass('sidebar-open');
});

// Close sidebar when clicking outside
$(document).on('click', function (e) {
    if (!$(e.target).closest('.sidebar, .model-logout .modal-content').length && !$(e.target).closest('.sidebar_open').length) {
        $('.sidebar').removeClass('sidebar-open');
    }
});