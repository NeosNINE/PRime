// Notifications module (dropdown + toasts) for User area
// Structured, readable implementation exported as a single object

const Notifications = {
    // Toast API exposed as SocnetApp.notifications
    toast: {
        container: null,
        timeouts: {},

        init() {
            this.ensureContainer();
        },

        ensureContainer() {
            if (!this.container || !this.container.length) {
                const exist = document.getElementById('notificationContainer');
                if (!exist) {
                    const div = document.createElement('div');
                    div.id = 'notificationContainer';
                    div.className = 'notification-container';
                    document.body.appendChild(div);
                }
                this.container = $('#notificationContainer');
            }
        },

        show(type, message, duration = 5000) {
            this.ensureContainer();
            const id = 'notif-' + Date.now();

            const icons = {
                success: '<i class="fas fa-check"></i>',
                error: '<i class="fas fa-times"></i>',
                warning: '<i class="fas fa-exclamation"></i>',
                info: '<i class="fas fa-info"></i>'
            };

            const html = `
                <div class="notification ${type}" id="${id}">
                    <div class="notification-icon">${icons[type] || icons.info}</div>
                    <div class="notification-content">
                        <div class="notification-message">${message}</div>
                    </div>
                    <button type="button" class="notification-close" data-id="${id}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            this.container.append(html);
            const $el = $('#' + id);
            setTimeout(() => $el.addClass('show'), 10);

            if (duration > 0) {
                this.timeouts[id] = setTimeout(() => this.remove(id), duration);
            }

            $(document).on('click', `.notification-close[data-id="${id}"]`, () => this.remove(id));
        },

        showSuccess(msg, d) { this.show('success', msg, d); },
        showError(msg, d) { this.show('error', msg, d); },
        showWarning(msg, d) { this.show('warning', msg, d); },
        showInfo(msg, d) { this.show('info', msg, d); },

        showSmartToast(type, message, duration = 5000, preventDuplicate = true) {
            this.ensureContainer();
            if (preventDuplicate) {
                const exists = this.container.find('.notification-message').filter(function () {
                    return $(this).text() === message;
                });
                if (exists.length) {
                    const id = exists.closest('.notification').attr('id');
                    this.resetTimer(id, duration);
                    return;
                }
            }
            this.show(type, message, duration);
        },

        resetTimer(id, duration) {
            if (this.timeouts[id]) clearTimeout(this.timeouts[id]);
            if (duration > 0) {
                this.timeouts[id] = setTimeout(() => this.remove(id), duration);
            }
        },

        remove(id) {
            const $el = $('#' + id);
            if (!$el.length) return;
            $el.addClass('hide');
            if (this.timeouts[id]) clearTimeout(this.timeouts[id]);
            setTimeout(() => $el.remove(), 250);
        },

        clear() {
            this.container && this.container.children().remove();
            this.timeouts = {};
        }
    },

    // Dropdown notifications (header bell)
    dropdown: {
        list: [],
        unread: 0,

        init() {
            this.bindEvents();
            this.load();
            this.updateBadge();
        },

        bindEvents() {
            $(document).on('click', '.notifications-btn', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown();
            });

            $(document).on('click', (e) => {
                if (!$(e.target).closest('.notifications-selector').length) {
                    this.closeDropdown();
                }
            });

            $(document).on('click', '.notification-item', (e) => {
                // Не закрываем меню при клике по уведомлению
                e.stopPropagation();
                const $item = $(e.currentTarget);
                const id = $item.data('id');
                this.markAsRead(id);
                // Немедленно убираем визуальные индикаторы непрочитанного
                $item.removeClass('unread');
                $item.find('.status-dot').addClass('read');
                $item.find('.status-text').text('прочитано');
                // Сообщаем бэкенду
                post('/user/notifications/read', { id }, () => {}, () => {});

                const url = $item.data('url');
                if (url) {
                    const target = $item.data('target') || '_self';
                    window.open(url, target);
                }
            });

            $(document).on('click', '.notification-delete', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const id = $(e.currentTarget).closest('.notification-item').data('id');
                // Удаляем на фронте
                this.delete(id);
                // Сообщаем бэкенду
                post('/user/notifications/delete', { id }, () => {}, () => {});
            });

            $(document).on('click', '.mark-all-read-btn', (e) => {
                e.stopPropagation();
                this.markAllAsRead();
                // Сообщаем бэкенду
                post('/user/notifications/read-all', {}, () => {}, () => {});
            });
        },

        load() {
            // Placeholder demo data
            // Начинаем пустым — данные будут поступать с бэкенда (refresh-data)
            this.list = [];
            this.unread = 0;
            this.render();
        },

        render() {
            const $c = $('#notificationsList');
            if (!this.list.length) {
                $c.html('<div class="notifications-empty"><div class="empty-icon"><i class="fas fa-bell-slash"></i></div><div class="empty-text">Нет уведомлений</div></div>');
                return;
            }
            let last = null;
            const out = [];
            // Показываем сначала новые (последние по времени)
            const sorted = [...this.list].sort((a,b) => b.time.getTime() - a.time.getTime());
            sorted.forEach(n => {
                const dk = this.dateLabel(n.time);
                if (dk !== last) {
                    last = dk;
                    out.push(`<div class="notification-date-separator"><span>${dk}</span></div>`);
                }
                const unread = n.read ? '' : 'unread';
                const statusDot = n.read ? 'read' : '';
                const statusText = n.read ? 'прочитано' : 'новое';
                const linkAttrs = n.url ? ` data-url="${n.url}" data-target="${(n.target === '_blank' || n.openInNewTab) ? '_blank' : '_self'}"` : '';
                const linkClass = n.url ? ' is-link' : '';
                out.push(`
                    <div class="notification-item ${unread}${linkClass}" data-id="${n.id}"${linkAttrs}>
                        <div class="notification-icon type-${n.type}"><i class="${n.icon}"></i></div>
                        <div class="notification-content">
                            <button class="notification-delete"><i class="fas fa-trash"></i></button>
                            <div class="notification-title">${n.title}</div>
                            <div class="notification-text">${n.text}</div>
                            <div class="notification-meta">
                                <div class="notification-time">${this.timeAgo(n.time)}</div>
                                <div class="notification-status"><div class="status-dot ${statusDot}"></div><div class="status-text">${statusText}</div></div>
                            </div>
                        </div>
                    </div>
                `);
            });
            $c.html(out.join(''));
        },

        add(n) {
            n.id = n.id || ('tmp-' + Date.now());
            n.time = n.time || new Date();
            n.read = !!n.read;
            this.list.unshift(n);
            if (!n.read) this.unread++;
            this.updateBadge();
            this.render();
        },

        markAsRead(id) {
            const n = this.list.find(x => x.id === id);
            if (n && !n.read) {
                n.read = true;
                this.unread = Math.max(0, this.unread - 1);
                this.updateBadge();
                this.render();
            }
        },

        delete(id) {
            const idx = this.list.findIndex(x => x.id === id);
            if (idx !== -1) {
                const wasUnread = !this.list[idx].read;
                this.list.splice(idx, 1);
                if (wasUnread) this.unread = Math.max(0, this.unread - 1);
                this.updateBadge();
                this.render();
            }
        },

        markAllAsRead() {
            let changed = 0;
            this.list.forEach(n => { if (!n.read) { n.read = true; changed++; } });
            if (changed) {
                this.unread = 0;
                this.updateBadge();
                this.render();
                if (window.SocnetApp && SocnetApp.notifications) {
                    SocnetApp.notifications.showSuccess(`Отмечено как прочитанные: ${changed} уведомлений`);
                }
            }
        },

        toggleDropdown() {
            const el = $('.notifications-selector');
            const isActive = el.hasClass('active');
            $('.control-dropdown').removeClass('active');
            if (!isActive) el.addClass('active');
        },

        closeDropdown() {
            $('.notifications-selector').removeClass('active');
        },

        updateBadge() {
            $('#notificationsBadge').attr('data-count', this.unread).text(this.unread);
        },

        timeAgo(d) {
            const now = Date.now();
            const m = Math.floor((now - d.getTime()) / 60000);
            if (m < 1) return 'только что';
            if (m < 60) return `${m} мин назад`;
            const h = Math.floor(m / 60);
            if (m < 1440) return `${h} ч назад`;
            const days = Math.floor(m / 1440);
            return `${days} дн назад`;
        },

        dateLabel(d) {
            const today = new Date();
            const t0 = new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime();
            const d0 = new Date(d.getFullYear(), d.getMonth(), d.getDate()).getTime();
            if (d0 === t0) return 'Сегодня';
            if (d0 === t0 - 86400000) return 'Вчера';
            return d.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }
    },

    init() {
        this.toast.init();
        this.dropdown.init();
        this.loadOnce();
    },
    lastEventId: 0,
    loadOnce() {
        // Одноразовая загрузка уведомлений при загрузке страницы
        get('/user/refresh-data', { last_event_id: 0 }, (resp) => {
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (!res) return;
                if (res.last_event_id) this.lastEventId = res.last_event_id;
                if (Array.isArray(res.notifications) && res.notifications.length) {
                    res.notifications.forEach(n => {
                        this.dropdown.add({
                            id: n.id,
                            title: n.title,
                            text: n.text,
                            url: n.url,
                            icon: n.icon || 'fas fa-bell',
                            type: n.type || 'info',
                            time: n.time ? new Date(n.time) : new Date(),
                            read: !!n.read === true
                        });
                    });
                }
            } catch (_) {}
        });
    }
};

export default Notifications;


