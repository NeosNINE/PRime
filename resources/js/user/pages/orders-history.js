/**
 * Orders History Page Manager
 * Управление страницей истории заказов
 */
class OrdersHistoryManager {
    constructor() {
        this.currentFilters = {
            status: 'all',
            sortDate: 'date',
            sortDirection: 'asc', // 'asc' или 'desc' - по умолчанию по возрастанию
            dateFrom: null,
            dateTo: null
        };

        this.dateRangePicker = null;
        this.currentPage = 1;
        this.totalPages = 8;
        this.ordersPerPage = 20;

        // Sample orders data (в реальном приложении будет загружаться с сервера)
        this.sampleOrders = this.generateSampleOrders();
        this.filteredOrders = [...this.sampleOrders];

        this.init();

        // Устанавливаем правильную иконку при инициализации
        this.updateSortDirectionIcon();
    }

    init() {
        console.log('OrdersHistoryManager: Initializing...');

        this.bindEvents();

        // Initialize custom selects
        this.initializeCustomSelects();

        // Initialize our new date range picker (placeholder first)
        this.initializeCustomDateRangePicker();

        this.loadOrders();
        this.updatePagination();

        // Устанавливаем правильную иконку после инициализации
        this.updateSortDirectionIcon();

        console.log('OrdersHistoryManager: Initialized successfully');
    }

    bindEvents() {
        // Filter events
        $('.orders-history-page #statusFilter').on('change', () => {
            this.currentFilters.status = $('.orders-history-page #statusFilter').val();
            this.applyFilters();
        });

        $('.orders-history-page #sortDate').on('change', () => {
            this.currentFilters.sortDate = $('.orders-history-page #sortDate').val();
            this.applyFilters();
        });

        // Sort direction button
        $('.orders-history-page #sortDirection').on('click', () => {
            console.log('Sort direction button clicked. Current:', this.currentFilters.sortDirection);

            this.currentFilters.sortDirection = this.currentFilters.sortDirection === 'desc' ? 'asc' : 'desc';

            console.log('Sort direction changed to:', this.currentFilters.sortDirection);

            this.updateSortDirectionIcon();
            this.applyFilters();
        });

        $('.orders-history-page #applyFilters').on('click', () => {
            this.applyFilters();
        });

        // Clear all filters button
        $('.orders-history-page #clearAllFilters').on('click', () => {
            this.clearAllFilters();
        });

        // Initialize pagination component
        this.initializePagination();

        // Order ID click events (copy to clipboard)
        $('.orders-history-page').on('click', '.order-id-link', (e) => {
            const orderId = $(e.target).data('id');
            this.copyOrderId(orderId);
        });

        // Modal events
        $('.orders-history-page').on('click', '.details-btn', (e) => {
            const orderId = $(e.currentTarget).data('order-id');
            this.showOrderDetails(orderId);
        });

        // Cancel order events
        $('.orders-history-page').on('click', '.cancel-btn', (e) => {
            const orderId = $(e.currentTarget).data('order-id');
            this.showCancelOrderModal(orderId);
        });

        $('.orders-history-modal #confirmCancelOrder').on('click', () => {
            const orderId = $('.orders-history-modal #cancelOrderId').text().replace('#', '');
            this.cancelOrder(orderId);
        });

        // Copy button in modal
        $('.orders-history-modal').on('click', '.copy-btn', (e) => {
            const targetId = $(e.currentTarget).data('copy-target');
            const text = $('.orders-history-modal #' + targetId).text();
            this.copyToClipboard(text);
        });

        // Copy buttons in table
        $('.orders-history-page').on('click', '.copy-btn', (e) => {
            e.preventDefault();
            e.stopPropagation();

            // Hide tooltip immediately on click
            const tooltip = bootstrap.Tooltip.getInstance(e.currentTarget);
            if (tooltip) {
                tooltip.hide();
            }

            const textToCopy = $(e.currentTarget).data('copy');
            this.copyToClipboard(textToCopy);
            this.showCopySuccess($(e.currentTarget));
        });

                // Initialize Bootstrap tooltips for copy buttons
        this.initializeTooltips();
    }

    initializePagination() {
        // Wait for PaginationComponent to be available
        if (typeof window.PaginationComponent !== 'undefined') {
            this.paginationComponent = new PaginationComponent('#ordersPagination', {
                currentPage: this.currentPage,
                totalPages: this.totalPages,
                totalItems: this.filteredOrders.length,
                itemsPerPage: this.ordersPerPage,
                showInfo: true,
                showPerPage: true,
                perPageOptions: [20, 50, 100],
                onPageChange: (page, pagination) => {
                    this.currentPage = page;
                    this.loadOrders();
                },
                onPerPageChange: (itemsPerPage, pagination) => {
                    this.ordersPerPage = itemsPerPage;
                    this.currentPage = 1; // Reset to first page
                    this.applyFilters();
                }
            });
        } else {
            // Fallback: try to initialize after a short delay
            setTimeout(() => this.initializePagination(), 100);
        }
    }

    waitForAirDatepicker() {
        // Check if AirDatepicker is already available
        if (typeof window.AirDatepicker !== 'undefined' && typeof window.AirDatepicker === 'function') {
            this.initializeDateRangePicker();
                return;
            }

        // Wait for AirDatepicker to load
        const checkAirDatepicker = setInterval(() => {
            if (typeof window.AirDatepicker !== 'undefined' && typeof window.AirDatepicker === 'function') {
                clearInterval(checkAirDatepicker);
                this.initializeDateRangePicker();
            }
        }, 100);

        // Fallback after 10 seconds
        setTimeout(() => {
            clearInterval(checkAirDatepicker);
            if (typeof window.AirDatepicker === 'undefined') {
                console.error('AirDatepicker failed to load after 10 seconds');
                // Try to show a fallback or error message
                this.showDatePickerError();
            }
        }, 10000);
    }

    showDatePickerError() {
        const dateInput = document.getElementById('dateRange');
        if (dateInput) {
            dateInput.placeholder = 'Ошибка загрузки календаря';
            dateInput.disabled = true;
            dateInput.style.opacity = '0.6';
        }
    }

    clearAllFilters() {
        // Set flag to prevent duplicate notification
        this.isClearingFilters = true;

        // Clear date range
        if (this.dateRangePicker && typeof this.dateRangePicker.clearSelection === 'function') {
            this.dateRangePicker.clearSelection();
        }

        // Reset all filters to default values
        this.currentFilters = {
            status: 'all',
            sortDate: 'date',
            sortDirection: 'asc',
            dateFrom: null,
            dateTo: null
        };

        // Reset orders per page
        this.ordersPerPage = 20;

        // Update pagination component if it exists
        if (this.paginationComponent) {
            this.paginationComponent.setData({
                itemsPerPage: this.ordersPerPage
            });
        }

        // Reset form elements
        $('#statusFilter').val('all');
        $('#sortDate').val('date');
        $('#dateRange').val('').attr('placeholder', 'Выберите период');

        // Update custom select displays
        this.updateCustomSelectDisplay('#statusFilter', 'all');
        this.updateCustomSelectDisplay('#sortDate', 'date');

        // Reset sort direction
        this.currentFilters.sortDirection = 'asc';
        this.updateSortDirectionIcon();

        // Apply cleared filters
        this.applyFilters();

        // Show appropriate notification for clearing filters
        SocnetApp.notifications.showSuccess('Все фильтры сброшены');

        console.log('All filters cleared');
    }

    initializeCustomSelects() {
        // Initialize custom selects if CustomSelect is available
        if (typeof CustomSelect !== 'undefined' && CustomSelect.initializeAll) {
            CustomSelect.initializeAll();
        } else {
            // Fallback: try to initialize after a short delay
            setTimeout(() => {
                if (typeof CustomSelect !== 'undefined' && CustomSelect.initializeAll) {
                    CustomSelect.initializeAll();
                }
            }, 100);
        }
    }

    // Initialize new custom date range picker
    initializeCustomDateRangePicker() {
        const $dateRangeInput = $('.orders-history-page #dateRange');
        if (!$dateRangeInput.length) return;

        // Create picker with no preselected dates and both calendars on current month
        this.dateRangePicker = window.createDateRangePicker({
            startDate: null,
            endDate: null,
            locale: 'ru',
            format: 'DD.MM.YYYY',
            showClearButton: false
        });

        this.dateRangePicker
            .onApply(({ startDate, endDate, startDateString, endDateString }) => {
                this.currentFilters.dateFrom = startDate;
                this.currentFilters.dateTo = endDate;
                $dateRangeInput.val(`${startDateString} - ${endDateString}`);
                this.applyFilters();
            })
            .onClear(() => {
                this.currentFilters.dateFrom = null;
                this.currentFilters.dateTo = null;
                $dateRangeInput.val('');
                this.applyFilters();
            });

        // Open picker on input click
        $dateRangeInput.on('click', () => this.dateRangePicker.open());
    }

    updateCustomSelectDisplay(selector, value) {
        const $select = $(selector);
        const $customSelect = $select.closest('.custom-select-wrapper');

        if ($customSelect.length) {
            // Find the display element
            const $display = $customSelect.find('.custom-select-display');
            if ($display.length) {
                // Find the selected option text
                const selectedOption = $select.find(`option[value="${value}"]`);
                if (selectedOption.length) {
                    $display.find('.custom-select-text').text(selectedOption.text());
                }
            }
        }
    }

    initializeDateRangePicker() {
        try {
            // Check if AirDatepicker is available
            if (typeof window.AirDatepicker === 'undefined' || typeof window.AirDatepicker !== 'function') {
                console.warn('AirDatepicker not available yet, retrying...');
                setTimeout(() => this.initializeDateRangePicker(), 500);
                return;
            }

            console.log('Initializing AirDatepicker...');

            this.dateRangePicker = new window.AirDatepicker('#dateRange', {
                range: true,
                multipleDates: true,
                multipleDatesSeparator: ' - ',
                dateFormat: 'dd.MM.yyyy',
                locale: {
                    days: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
                    daysShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                    daysMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                    months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                    monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                    today: 'Сегодня',
                    clear: 'Очистить',
                    dateFormat: 'dd.mm.yyyy',
                    timeFormat: 'hh:ii',
                    firstDay: 1
                },
                onSelect: ({ date, datepicker }) => {
                    if (date && date.length === 2) {
                        this.currentFilters.dateFrom = date[0];
                        this.currentFilters.dateTo = date[1];
                        console.log('Date range selected:', date[0], 'to', date[1]);
                    } else {
                        this.currentFilters.dateFrom = null;
                        this.currentFilters.dateTo = null;
                        console.log('Date range cleared');
                    }
                }
            });

            console.log('AirDatepicker initialized successfully');
        } catch (error) {
            console.error('Error initializing AirDatepicker:', error);
            this.showDatePickerError();
        }
    }

    generateSampleOrders() {
        const services = [
            'Instagram Likes', 'YouTube Views', 'TikTok Followers', 'Facebook Likes',
            'Twitter Followers', 'Instagram Comments', 'YouTube Subscribers', 'Telegram Members'
        ];

        const statuses = ['pending', 'in_progress', 'completed', 'cancelled', 'partial'];
        const links = [
            'https://instagram.com/p/example',
            'https://youtube.com/watch?v=example',
            'https://tiktok.com/@example',
            'https://facebook.com/post/example',
            'https://twitter.com/user/status/example'
        ];

        const orders = [];
        for (let i = 1; i <= 156; i++) {
            const orderId = 12000 + i;
            const service = services[Math.floor(Math.random() * services.length)];
            const status = statuses[Math.floor(Math.random() * statuses.length)];
            const link = links[Math.floor(Math.random() * links.length)];
            const quantity = Math.floor(Math.random() * 9000) + 1000;
            const amount = (quantity / 1000 * (Math.random() * 15 + 5)).toFixed(2);

            // Generate random date within last 30 days
            const date = new Date();
            date.setDate(date.getDate() - Math.floor(Math.random() * 30));

            orders.push({
                id: orderId,
                service: service,
                date: date,
                link: link,
                quantity: quantity,
                status: status,
                amount: parseFloat(amount),
                dripFeed: Math.random() > 0.7, // 30% chance
                dripRuns: Math.floor(Math.random() * 8) + 3,
                dripInterval: [15, 30, 60, 120, 240][Math.floor(Math.random() * 5)],
                notes: Math.random() > 0.8 ? 'Дополнительные требования к качеству аудитории' : null
            });
        }

        return orders.sort((a, b) => a.date - b.date); // Sort by date asc by default
    }

    // Sort orders based on current filters
    sortOrders(orders) {
        const direction = this.currentFilters.sortDirection === 'asc' ? 1 : -1;

        switch (this.currentFilters.sortDate) {
            case 'date':
                orders.sort((a, b) => {
                    const result = a.date - b.date;
                    return direction === 1 ? result : -result;
                });
                break;
            case 'id':
                orders.sort((a, b) => {
                    const result = a.id - b.id;
                    return direction === 1 ? result : -result;
                });
                break;
            case 'price':
                orders.sort((a, b) => {
                    const result = a.amount - b.amount;
                    return direction === 1 ? result : -result;
                });
                break;
            case 'status':
                orders.sort((a, b) => {
                    const result = a.status.localeCompare(b.status);
                    return direction === 1 ? result : -result;
                });
                break;
            default:
                orders.sort((a, b) => {
                    const result = a.date - b.date;
                    return direction === 1 ? result : -result;
                });
                break;
        }
    }

        // Update sort direction icon
    updateSortDirectionIcon() {
        const directionBtn = document.getElementById('sortDirection');
        if (directionBtn) {
            const icon = directionBtn.querySelector('i');
            if (icon) {
                // Обновляем иконку в зависимости от направления сортировки
                if (this.currentFilters.sortDirection === 'desc') {
                    icon.className = 'fa-solid fa-arrow-down';
                } else {
                    icon.className = 'fa-solid fa-arrow-up';
                }

                console.log('Icon updated:', this.currentFilters.sortDirection, icon.className);
            }
        }
    }

    applyFilters() {
        console.log('Applying filters:', this.currentFilters);

        let filtered = [...this.sampleOrders];

        // Status filter
        if (this.currentFilters.status !== 'all') {
            filtered = filtered.filter(order => order.status === this.currentFilters.status);
        }

        // Date range filter
        if (this.currentFilters.dateFrom) {
            const fromDate = new Date(this.currentFilters.dateFrom);
            filtered = filtered.filter(order => order.date >= fromDate);
        }

        if (this.currentFilters.dateTo) {
            const toDate = new Date(this.currentFilters.dateTo);
            toDate.setHours(23, 59, 59, 999); // End of day
            filtered = filtered.filter(order => order.date <= toDate);
        }

        // Sort
        this.sortOrders(filtered);

        this.filteredOrders = filtered;
        this.currentPage = 1;
        this.totalPages = Math.ceil(filtered.length / this.ordersPerPage);

        this.loadOrders();
        this.updatePagination();

        // Only show notification if not clearing filters
        if (!this.isClearingFilters) {
        SocnetApp.notifications.showSuccess('Фильтры применены');
        }
        this.isClearingFilters = false;
    }

    loadOrders() {
        const tbody = $('.orders-history-page #ordersTableBody');
        const emptyState = $('.orders-history-page #emptyState');

        // Calculate pagination
        const startIndex = (this.currentPage - 1) * this.ordersPerPage;
        const endIndex = startIndex + this.ordersPerPage;
        const pageOrders = this.filteredOrders.slice(startIndex, endIndex);

        if (pageOrders.length === 0) {
            tbody.empty();
            $('.orders-history-page #ordersTable').hide();
            emptyState.show();
            return;
        }

        $('.orders-history-page #ordersTable').show();
        emptyState.hide();

        tbody.empty();

        pageOrders.forEach(order => {
            const row = this.createOrderRow(order);
            tbody.append(row);
        });

        // Initialize tooltips for new copy buttons
        this.initializeTooltips();

        console.log(`Loaded ${pageOrders.length} orders for page ${this.currentPage}`);
    }

    createOrderRow(order) {
        const statusClass = `status-${order.status.replace('_', '-')}`;
        const statusText = this.getStatusText(order.status);
        const formattedDate = this.formatDate(order.date);
        const formattedTime = this.formatTime(order.date);
        const formattedLink = this.formatLink(order.link);
        const formattedQuantity = order.quantity.toLocaleString();
        const formattedAmount = `$${order.amount.toFixed(2)}`;

        // Determine available actions based on status and time
        const actions = this.getAvailableActions(order);

        return $(`
            <tr data-order-id="${order.id}" data-status="${order.status}" data-date="${order.date.toISOString().split('T')[0]}">
                <td class="order-id-cell">
                     <div class="order-date-info">
                         <span class="order-date">${formattedDate}</span>
                         <span class="order-time">${formattedTime}</span>
                     </div>
                     <div class="copyable-content">
                    <span class="order-id-link" data-id="${order.id}">#${order.id}</span>
                         <button class="copy-btn" data-copy="${order.id}" data-bs-toggle="tooltip" data-bs-placement="top" title="Копировать ID">
                             <i class="fas fa-copy"></i>
                         </button>
                     </div>
                     ${order.dripFeed ? `
                          <div class="dripfeed-indicator" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="DripFeed: ${order.dripRuns} ${this.getDripRunsText(order.dripRuns)} <br> каждые ${order.dripInterval} ${this.getDripIntervalText(order.dripInterval)}">
                              <i class="fas fa-tint"></i>
                              <span>DripFeed</span>
                          </div>
                      ` : ''}
                </td>
                <td class="service-cell">
                    <span class="service-name">${order.service}</span>
                </td>
                <td class="link-cell">
                    <div class="copyable-content">
                    <a href="${order.link}" target="_blank" class="order-link">
                        <i class="fas fa-external-link-alt"></i>
                        <span>${formattedLink}</span>
                    </a>
                        <button class="copy-btn" data-copy="${order.link}" data-bs-toggle="tooltip" data-bs-placement="top" title="Копировать ссылку">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </td>
                <td class="quantity-cell">
                    <span class="quantity">${formattedQuantity}</span>
                </td>
                <td class="status-cell">
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </td>
                <td class="amount-cell">
                    <span class="amount">${formattedAmount}</span>
                </td>
                <td class="actions-cell">
                    <div class="action-buttons">
                        ${actions}
                    </div>
                </td>
            </tr>
        `);
    }

    getStatusText(status) {
        const statusMap = {
            'pending': 'В ожидании',
            'in_progress': 'В процессе',
            'completed': 'Завершено',
            'cancelled': 'Отменено',
            'partial': 'Частично'
        };
        return statusMap[status] || status;
    }

    getDripRunsText(runs) {
        if (runs === 1) return 'запуск';
        if (runs >= 2 && runs <= 4) return 'запуска';
        return 'запусков';
    }

    getDripIntervalText(minutes) {
        if (minutes === 1) return 'минуту';
        if (minutes >= 2 && minutes <= 4) return 'минуты';
        return 'минут';
    }

    formatDate(date) {
        return date.toLocaleDateString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    formatTime(date) {
        return date.toLocaleTimeString('ru-RU', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    formatLink(link) {
        // Extract domain and path for display
        try {
            const url = new URL(link);
            const domain = url.hostname.replace('www.', '');
            const path = url.pathname.length > 20 ? url.pathname.substring(0, 20) + '...' : url.pathname;
            return domain + path;
        } catch (e) {
            return link.length > 30 ? link.substring(0, 30) + '...' : link;
        }
    }

    getAvailableActions(order) {
        let actions = [];

        // Details button (always available)
        actions.push(`
            <button class="action-btn details-btn" data-bs-toggle="modal" data-bs-target="#orderDetailsModal" data-order-id="${order.id}">
                <i class="fas fa-info-circle"></i>
                <span class="btn-text">Детали</span>
            </button>
        `);

        // Cancel button (only for pending orders within 2 minutes)
        if (order.status === 'pending') {
            const timeDiff = Date.now() - order.date.getTime();
            if (timeDiff < 2 * 60 * 1000) { // 2 minutes
                actions.push(`
                    <button class="action-btn cancel-btn" data-order-id="${order.id}">
                        <i class="fas fa-times"></i>
                        <span class="btn-text">Отменить</span>
                    </button>
                `);
            }
        }

        // Speed up button (for orders older than 24 hours and not completed)
        if (order.status === 'in_progress' || order.status === 'pending') {
            const timeDiff = Date.now() - order.date.getTime();
            if (timeDiff > 24 * 60 * 60 * 1000) { // 24 hours
                actions.push(`
                    <a href="/tickets/new?order=${order.id}&type=speed" class="action-btn speed-btn">
                        <i class="fas fa-rocket"></i>
                        <span class="btn-text">Ускорить</span>
                    </a>
                `);
            }
        }

        // Report problem button (for all orders)
        actions.push(`
            <a href="/tickets/new?order=${order.id}" class="action-btn report-btn">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="btn-text">Проблема</span>
            </a>
        `);

        // Repeat order button (always available)
        actions.push(`
            <a href="/orders/new?repeat=${order.id}" class="action-btn repeat-btn">
                <i class="fas fa-redo"></i>
                <span class="btn-text">Повторить</span>
            </a>
        `);

        return actions.join('');
    }

    updatePagination() {
        // Update pagination component if it exists
        if (this.paginationComponent) {
            this.paginationComponent.setData({
                currentPage: this.currentPage,
                totalPages: this.totalPages,
                totalItems: this.filteredOrders.length,
                itemsPerPage: this.ordersPerPage
            });
        }
    }

    copyOrderId(orderId) {
        const text = `#${orderId}`;
        this.copyToClipboard(text, 'ID заказа скопирован в буфер обмена');
    }

    copyToClipboard(text, successMessage = 'Скопировано в буфер обмена') {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                SocnetApp.notifications.showSuccess(successMessage);
            }).catch(err => {
                console.error('Failed to copy to clipboard:', err);
                this.fallbackCopyToClipboard(text, successMessage);
            });
        } else {
            this.fallbackCopyToClipboard(text, successMessage);
        }
    }

    fallbackCopyToClipboard(text, successMessage) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            document.execCommand('copy');
            SocnetApp.notifications.showSuccess(successMessage);
        } catch (err) {
            console.error('Fallback copy failed:', err);
            SocnetApp.notifications.showError('Не удалось скопировать в буфер обмена');
        }

        document.body.removeChild(textArea);
    }

    showCopySuccess($button) {
        const originalIcon = $button.find('i').attr('class');

        // Change icon to checkmark
        $button.find('i').attr('class', 'fas fa-check');
        $button.addClass('copy-success');

        // Reset after 2 seconds
        setTimeout(() => {
            $button.find('i').attr('class', originalIcon);
            $button.removeClass('copy-success');
        }, 2000);
    }

    initializeTooltips() {
        // Initialize Bootstrap tooltips for copy buttons
        $('.orders-history-page .copy-btn[data-bs-toggle="tooltip"]').each((index, element) => {
            // Destroy existing tooltip if it exists
            const existingTooltip = bootstrap.Tooltip.getInstance(element);
            if (existingTooltip) {
                existingTooltip.dispose();
            }

            // Create new tooltip
            new bootstrap.Tooltip(element, {
                trigger: 'hover',
                placement: 'top',
                html: false,
                delay: { show: 200, hide: 0 }
            });
        });
    }

    showOrderDetails(orderId) {
        const order = this.sampleOrders.find(o => o.id == orderId);
        if (!order) {
            SocnetApp.notifications.showError('Заказ не найден');
            return;
        }

        // Update modal content
        $('.orders-history-modal #modalOrderId').text(`#${order.id}`);
        $('.orders-history-modal #detailOrderId').text(`#${order.id}`);
        $('.orders-history-modal #detailService').text(order.service);
        $('.orders-history-modal #detailLink').attr('href', order.link);
        $('.orders-history-modal #detailLink .link-text').text(order.link);
        $('.orders-history-modal #detailQuantity').text(order.quantity.toLocaleString());
        $('.orders-history-modal #detailAmount').text(`$${order.amount.toFixed(2)}`);

        const statusClass = `status-${order.status.replace('_', '-')}`;
        const statusText = this.getStatusText(order.status);
        $('.orders-history-modal #detailStatus').attr('class', `status-badge ${statusClass}`).text(statusText);

        $('.orders-history-modal #detailDate').text(`${this.formatDate(order.date)} ${this.formatTime(order.date)}`);

        // Completion date (only for completed orders)
        if (order.status === 'completed') {
            const completedDate = new Date(order.date.getTime() + Math.random() * 24 * 60 * 60 * 1000);
            $('.orders-history-modal #detailCompletedDate').text(`${this.formatDate(completedDate)} ${this.formatTime(completedDate)}`);
        } else {
            $('.orders-history-modal #detailCompletedDate').text('—');
        }

        // Drip feed info
        if (order.dripFeed) {
            $('.orders-history-modal #dripFeedInfo').show();
            $('.orders-history-modal #detailDripRuns').text(order.dripRuns);
            $('.orders-history-modal #detailDripInterval').text(`${order.dripInterval} минут`);
        } else {
            $('.orders-history-modal #dripFeedInfo').hide();
        }

        // Notes
        $('.orders-history-modal #detailNotes').text(order.notes || 'Нет дополнительных примечаний');

        // Update modal action buttons
        $('.orders-history-modal #modalRepeatBtn').attr('href', `/orders/new?repeat=${order.id}`);
        $('.orders-history-modal #modalReportBtn').attr('href', `/tickets/new?order=${order.id}`);
    }

    showCancelOrderModal(orderId) {
        $('.orders-history-modal #cancelOrderId').text(`#${orderId}`);
        const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
        modal.show();
    }

    cancelOrder(orderId) {
        console.log('Cancelling order:', orderId);

        // Simulate API call
        setTimeout(() => {
            // Update order status in sample data
            const order = this.sampleOrders.find(o => o.id == orderId);
            if (order) {
                order.status = 'cancelled';
            }

            // Refresh table
            this.applyFilters();

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal'));
            modal.hide();

            SocnetApp.notifications.showSuccess(`Заказ #${orderId} успешно отменен`);
        }, 1000);
    }
}

// Initialize when document is ready
$(document).ready(() => {
    window.ordersHistoryManager = new OrdersHistoryManager();
});

// Export for global access
window.OrdersHistoryManager = OrdersHistoryManager;