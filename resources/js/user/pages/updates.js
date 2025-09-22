/**
 * Updates Page Manager
 * Управление страницей обновлений
 */
class UpdatesManager {
    constructor() {
        this.updates = [];
        this.currentPage = 1;
        this.itemsPerPage = 20;

        this.init();
    }

    init() {
        console.log('UpdatesManager: Initializing...');

        this.generateSampleUpdates();
        this.bindEvents();
        this.renderUpdates();

        console.log('UpdatesManager: Initialized successfully');
    }

    // Generate sample updates data
    generateSampleUpdates() {
        // Разрешаем только 3 типа: новая услуга, изменение цены, удаление услуги
        const updateTypes = [
            { id: 'new', name: 'Новая услуга', icon: 'fas fa-plus-circle' },
            { id: 'price', name: 'Изменение цены', icon: 'fas fa-tag' },
            { id: 'removal', name: 'Удаление услуги', icon: 'fas fa-minus-circle' }
        ];
        const allowedTypes = new Set(updateTypes.map(t => t.id));

        const sampleUpdates = [
            {
                date: new Date('2025-08-02'),
                type: 'new',
                title: 'Добавлена услуга Instagram Likes Pro',
                serviceDescriptions: {
                    ru: 'Премиальная услуга для быстрого получения лайков в Instagram с высокой скоростью и гарантией качества.',
                    en: 'Premium Instagram likes with high speed and quality guarantee.'
                }
            },
            {
                date: new Date('2025-08-01'),
                type: 'price',
                title: 'Цена услуги YouTube Views снижена',
                description: 'Стоимость услуги "YouTube Views Standard" снижена с $2.00 до $1.50 за 1000 просмотров.'
            },
            {
                date: new Date('2025-07-30'),
                type: 'removal',
                title: 'Услуга TikTok Comments удалена',
                serviceDescriptions: {
                    ru: 'Описание услуги комментариев для TikTok. Временно недоступна из-за изменений API.',
                    en: 'TikTok comments service. Temporarily unavailable due to API changes.'
                }
            },
            {
                date: new Date('2025-07-22'),
                type: 'new',
                title: 'Добавлены услуги для Telegram каналов',
                serviceDescriptions: {
                    ru: 'Новые услуги для продвижения Telegram-каналов: подписчики, просмотры постов и реакции.',
                    en: 'New services for Telegram channels: subscribers, post views and reactions.'
                }
            },
            {
                date: new Date('2025-07-20'),
                type: 'price',
                title: 'Специальная цена на Facebook лайки',
                description: 'До конца месяца действует скидка 25% на все услуги накрутки лайков для Facebook постов.'
            },
            {
                date: new Date('2025-07-15'),
                type: 'new',
                title: 'Запущены услуги для LinkedIn',
                serviceDescriptions: {
                    ru: 'Теперь доступны услуги продвижения в LinkedIn: лайки, подписчики и просмотры профиля.',
                    en: 'New LinkedIn services: likes, followers and profile views.'
                }
            },
            {
                date: new Date('2025-07-08'),
                type: 'price',
                title: 'Снижены цены на Twitter услуги',
                description: 'Стоимость всех услуг для Twitter снижена на 15% в связи с улучшением поставщиков.'
            },
            {
                date: new Date('2025-07-05'),
                type: 'new',
                title: 'Добавлена поддержка Threads',
                serviceDescriptions: {
                    ru: 'Первые услуги для Threads: подписчики и лайки.',
                    en: 'First services for Threads: followers and likes.'
                }
            },
            {
                date: new Date('2025-07-03'),
                type: 'removal',
                title: 'Приостановлены услуги для Clubhouse',
                serviceDescriptions: {
                    ru: 'Все услуги для Clubhouse временно приостановлены из-за низкого спроса.',
                    en: 'All Clubhouse services temporarily suspended due to low demand.'
                }
            },
        ];

        this.updates = sampleUpdates
            .filter(u => allowedTypes.has(u.type))
            .map((update, index) => ({
                id: index + 1,
                ...update,
                typeData: updateTypes.find(t => t.id === update.type)
            }));

        // Sort by date (newest first)
        this.updates.sort((a, b) => b.date - a.date);

        console.log(`Generated ${this.updates.length} sample updates`);
    }

    // Bind event listeners
    bindEvents() {
        // Pagination
        $('.updates-page').on('click', '.pagination-btn', (e) => {
            const page = $(e.currentTarget).data('page');
            if (page && !$(e.currentTarget).hasClass('active')) {
                this.currentPage = parseInt(page);
                this.renderUpdates();
                this.updatePagination();

                // Scroll to top
                $('.updates-page')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // Render updates list
    renderUpdates() {
        const container = $('.updates-page #updatesContainer');
        const loadingState = $('.updates-page #updatesLoadingState');
        const emptyState = $('.updates-page #updatesEmptyState');

        // Show loading
        loadingState.show();
        container.hide();
        emptyState.hide();

        // Simulate loading delay
        setTimeout(() => {
            loadingState.hide();

            if (this.updates.length === 0) {
                emptyState.show();
                return;
            }

            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            const updatesToShow = this.updates.slice(startIndex, endIndex);

            const updatesHtml = updatesToShow.map(update => this.createUpdateItemHtml(update)).join('');
            container.html(updatesHtml);
            container.show();

            this.updatePagination();
            this.updateUpdatesCount();

        }, 300);
    }

    // Create update item HTML (с учётом автогенерации описаний)
    createUpdateItemHtml(update) {
        const dateDay = update.date.getDate().toString().padStart(2, '0');
        const dateMonth = this.getMonthName(update.date.getMonth());

        // Автогенерация описания по типу
        const lang = (window.SocnetApp && SocnetApp.locale) ? SocnetApp.locale : 'ru';
        let description = '';
        if (update.type === 'new') {
            const raw = update.serviceDescriptions && (update.serviceDescriptions[lang] || update.serviceDescriptions['ru'] || update.serviceDescriptions['en']) || '';
            description = this.truncateText(raw, 220);
        } else if (update.type === 'removal') {
            const raw = update.serviceDescriptions && (update.serviceDescriptions[lang] || update.serviceDescriptions['ru'] || update.serviceDescriptions['en']) || '';
            description = raw ? this.truncateText(raw, 180) : '';
        } else if (update.type === 'price') {
            description = update.description || '';
        }

        return `
            <div class="update-item ${update.type === 'removal' ? 'update-removal' : ''}">
                <div class="update-date">
                    <div class="date-day">${dateDay}</div>
                    <div class="date-month">${dateMonth}</div>
                </div>
                <div class="update-content">
                    <div class="update-type type-${update.type}">
                        <i class="${update.typeData.icon}"></i>
                        ${update.typeData.name}
                    </div>
                    <h3 class="update-title">${update.title}</h3>
                    ${description ? `<p class="update-description">${description}</p>` : ''}
                </div>
            </div>
        `;
    }

    // Update pagination
    updatePagination() {
        const totalPages = Math.ceil(this.updates.length / this.itemsPerPage);
        const paginationSection = $('.updates-page #updatesPaginationSection');
        const paginationControls = $('.updates-page #updatesPaginationControls');
        const paginationInfo = $('.updates-page #updatesPaginationInfo');

        if (totalPages <= 1) {
            paginationSection.hide();
            return;
        }

        // Update info
        const startItem = (this.currentPage - 1) * this.itemsPerPage + 1;
        const endItem = Math.min(this.currentPage * this.itemsPerPage, this.updates.length);
        paginationInfo.text(`Показано ${startItem}-${endItem} из ${this.updates.length}`);

        // Generate pagination buttons
        let buttonsHtml = '';

        // Previous button
        buttonsHtml += `
            <button class="pagination-btn" data-page="${this.currentPage - 1}" ${this.currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
                Предыдущая
            </button>
        `;

        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            buttonsHtml += `
                <button class="pagination-btn ${i === this.currentPage ? 'active' : ''}" data-page="${i}">
                    ${i}
                </button>
            `;
        }

        // Next button
        buttonsHtml += `
            <button class="pagination-btn" data-page="${this.currentPage + 1}" ${this.currentPage === totalPages ? 'disabled' : ''}>
                Следующая
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

        paginationControls.html(buttonsHtml);
        paginationSection.show();
    }

    // Update updates count
    updateUpdatesCount() {
        $('.updates-page #updatesCount').text(this.updates.length);
    }

    // Get month name
    getMonthName(monthIndex) {
        const months = [
            'янв', 'фев', 'мар', 'апр', 'май', 'июн',
            'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'
        ];
        return months[monthIndex];
    }

    truncateText(text, maxLen) {
        if (!text) return '';
        const t = text.trim();
        if (t.length <= maxLen) return t;
        return t.slice(0, maxLen).trim() + '…';
    }

    // Show notification
    showNotification(type, message) {
        if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
            if (type === 'success') {
                SocnetApp.notifications.showSuccess(message);
            } else if (type === 'error') {
                SocnetApp.notifications.showError(message);
            } else {
                SocnetApp.notifications.showInfo(message);
            }
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }
}

// Initialize when document is ready
$(document).ready(() => {
    window.updatesManager = new UpdatesManager();
    console.log('Updates page loaded');
});

// Integration with main SocnetApp
if (typeof SocnetApp !== 'undefined') {
    SocnetApp.updates = {
        manager: null,
        init() {
            this.manager = new UpdatesManager();
            return this.manager;
        }
    };

    console.log('Updates module integrated with SocnetApp');
}
