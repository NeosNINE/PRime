/**
 * Notifications Page Manager
 * Страница со списком уведомлений + пагинация, удаление, кликабельность
 */
class NotificationsPageManager {
    constructor() {
        this.items = [];
        this.currentPage = 1;
        this.itemsPerPage = 20;

        this.init();
    }

    init() {
        console.log('NotificationsPage: Initializing...');
        this.loadData();
        this.bindEvents();
        console.log('NotificationsPage: Initialized');
    }

    // Load data from backend
    loadData() {
        const loading = $('.notifications-page #notificationsLoadingState');
        const container = $('.notifications-page #notificationsContainer');
        const empty = $('.notifications-page #notificationsEmptyState');

        loading.show();
        container.hide();
        empty.hide();

        get('/user/notifications/list', { page: this.currentPage, per_page: this.itemsPerPage }, (resp) => {
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                this.items = Array.isArray(res.items) ? res.items : [];
                // Преобразуем time в Date для отрисовки
                this.items.forEach(n => { n.time = n.time ? new Date(n.time) : new Date(); });
            } catch (e) {
                this.items = [];
            }

            loading.hide();
            this.render();
        }, () => {
            loading.hide();
            this.items = [];
            this.render();
        });
    }

    bindEvents() {
        // Pagination
        $('.notifications-page').on('click', '.pagination-btn', (e) => {
            const page = $(e.currentTarget).data('page');
            if (page && !$(e.currentTarget).hasClass('active')) {
                this.currentPage = parseInt(page);
                this.render();
            }
        });

        // Click-through
        $('.notifications-page').on('click', '.notification-item', (e) => {
            const $item = $(e.currentTarget);
            const id = $item.data('id');
            const item = this.items.find(n => n.id === id);
            if (!item) return;

            // Mark as read in global manager if exists
            if (window.SocnetApp && SocnetApp.notifications_manager) {
                SocnetApp.notifications_manager.markAsRead(id);
            }

            if (item.url) {
                const target = item.target === '_blank' || item.openInNewTab ? '_blank' : '_self';
                window.open(item.url, target);
            }
        });

        // Delete
        $('.notifications-page').on('click', '.notification-delete', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const id = $(e.currentTarget).closest('.notification-item').data('id');
            this.deleteItem(id);
        });

        // Mark all as read
        $('.notifications-page').on('click', '#markAllReadBtn', (e) => {
            e.preventDefault();
            this.markAllAsRead();
        });

        // Delete all notifications
        $('.notifications-page').on('click', '#deleteAllBtn', (e) => {
            e.preventDefault();
            this.deleteAllNotifications();
        });
    }

    render() {
        const container = $('.notifications-page #notificationsContainer');
        const empty = $('.notifications-page #notificationsEmptyState');
        const countEl = $('.notifications-page #notificationsCount');

        if (this.items.length === 0) {
            container.hide();
            empty.show();
            countEl.text('0');
            $('.notifications-page #notificationsPaginationSection').hide();
            return;
        }

        countEl.text(this.items.length);

        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const itemsToShow = this.items.slice(startIndex, endIndex);

        // Group notifications by date
        let lastDateKey = null;
        const parts = [];

        itemsToShow.forEach(n => {
            const dateKey = this.getDateLabel(n.time instanceof Date ? n.time : new Date(n.time));

            if (dateKey !== lastDateKey) {
                lastDateKey = dateKey;
                parts.push(`
                    <div class="notification-date-separator">
                        <span>${dateKey}</span>
                    </div>
                `);
            }

            parts.push(this.createItemHtml(n));
        });

        const html = parts.join('');
        container.html(html).show();

        this.updatePagination();
        this.updateInfo();
    }

    createItemHtml(n) {
        const timeAgo = this.formatTimeAgo(n.time instanceof Date ? n.time : new Date(n.time));
        const unreadClass = n.read ? '' : 'unread';
        const statusDot = n.read ? 'read' : '';
        const statusText = n.read ? 'прочитано' : 'новое';
        const linkClass = n.url ? 'is-link' : '';

        return `
            <div class="notification-item ${unreadClass} ${linkClass}" data-id="${n.id}">
                <div class="notification-icon type-${n.type}">
                    <i class="${n.icon || 'fas fa-info-circle'}"></i>
                </div>
                <div class="notification-content">
                    <button class="notification-delete"><i class="fas fa-trash"></i></button>
                    <div class="notification-title">${n.title || ''}</div>
                    <div class="notification-text">${n.text || ''}</div>
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
    }

    updatePagination() {
        const totalPages = Math.ceil(this.items.length / this.itemsPerPage);
        const section = $('.notifications-page #notificationsPaginationSection');
        const controls = $('.notifications-page #notificationsPaginationControls');

        if (totalPages <= 1) {
            section.hide();
            return;
        }

        let buttonsHtml = '';

        // Prev
        buttonsHtml += `
            <button class="pagination-btn page-btn" data-page="${this.currentPage - 1}" ${this.currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
                Предыдущая
            </button>
        `;

        const maxVisible = 5;
        let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
        let end = Math.min(totalPages, start + maxVisible - 1);
        if (end - start < maxVisible - 1) start = Math.max(1, end - maxVisible + 1);

        for (let i = start; i <= end; i++) {
            buttonsHtml += `
                <button class="pagination-btn ${i === this.currentPage ? 'active' : ''}" data-page="${i}">
                    ${i}
                </button>
            `;
        }

        // Next
        buttonsHtml += `
            <button class="pagination-btn page-btn" data-page="${this.currentPage + 1}" ${this.currentPage === totalPages ? 'disabled' : ''}>
                Следующая
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

        controls.html(buttonsHtml);
        section.show();
    }

    updateInfo() {
        const startItem = (this.currentPage - 1) * this.itemsPerPage + 1;
        const endItem = Math.min(this.currentPage * this.itemsPerPage, this.items.length);
        $('.notifications-page #notificationsPaginationInfo').text(`Показано ${startItem}-${endItem} из ${this.items.length}`);
    }

    deleteItem(id) {
        post('/user/notifications/delete', { id }, () => {
            this.items = this.items.filter(n => n.id === id ? false : true);
            const totalPages = Math.ceil(this.items.length / this.itemsPerPage) || 1;
            if (this.currentPage > totalPages) this.currentPage = totalPages;
            this.render();
        }, () => {
            // fallback локально, если ошибка
            this.items = this.items.filter(n => n.id === id ? false : true);
            const totalPages = Math.ceil(this.items.length / this.itemsPerPage) || 1;
            if (this.currentPage > totalPages) this.currentPage = totalPages;
            this.render();
        });
    }

    formatTimeAgo(date) {
        const now = new Date();
        const diff = Math.floor((now - date) / (1000 * 60));
        if (diff < 1) return 'только что';
        if (diff < 60) return `${diff} мин назад`;
        if (diff < 1440) return `${Math.floor(diff / 60)} ч назад`;
        const days = Math.floor(diff / 1440);
        return `${days} дн назад`;
    }

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

    // Mark all notifications as read
    markAllAsRead() {
        if (this.items.length === 0) {
            if (window.SocnetApp && SocnetApp.notifications) {
                SocnetApp.notifications.showInfo('Нет уведомлений для отметки');
            }
            return;
        }

        // Show confirmation dialog
        if (!confirm('Отметить все уведомления как прочитанные?')) {
            return;
        }

        post('/user/notifications/read-all', {}, () => {
            this.items.forEach(notification => { notification.read = true; });
            this.render();
        }, () => {
            this.items.forEach(notification => { notification.read = true; });
            this.render();
        });

        // Re-render to update UI
        this.render();

        // Show success message
        if (window.SocnetApp && SocnetApp.notifications) {
            SocnetApp.notifications.showSuccess('Все уведомления отмечены как прочитанные');
        }
    }

    // Delete all notifications
    deleteAllNotifications() {
        if (this.items.length === 0) {
            if (window.SocnetApp && SocnetApp.notifications) {
                SocnetApp.notifications.showInfo('Нет уведомлений для удаления');
            }
            return;
        }

        // Show confirmation dialog
        if (!confirm('Вы уверены, что хотите удалить ВСЕ уведомления? Это действие нельзя отменить.')) {
            return;
        }

        post('/user/notifications/delete-all', {}, () => {
            this.items = [];
            this.currentPage = 1;
            this.render();
        }, () => {
            this.items = [];
            this.currentPage = 1;
            this.render();
        });

        // Show success message
        if (window.SocnetApp && SocnetApp.notifications) {
            SocnetApp.notifications.showSuccess('Все уведомления удалены');
        }
    }
}

// Initialize when document is ready
$(document).ready(() => {
    if (document.querySelector('.notifications-page')) {
        window.notificationsPage = new NotificationsPageManager();
    }
});

// Export (optional)
window.NotificationsPageManager = NotificationsPageManager;


