// Orders Page JavaScript
class OrdersManager {
    constructor() {
        this.services = [];
        this.selectedService = null;
        this.massOrders = [];
        this.currentBalance = 1250.00; // Should come from backend
        this.selectedDateFrom = null;
        this.selectedDateTo = null;
        this.datepicker = null;
        this.categories = {
            instagram: ['followers', 'likes', 'views', 'comments', 'saves'],
            youtube: ['subscribers', 'views', 'likes', 'comments', 'shares'],
            tiktok: ['followers', 'likes', 'views', 'shares'],
            telegram: ['subscribers', 'views', 'members'],
            facebook: ['likes', 'followers', 'shares', 'comments'],
            twitter: ['followers', 'likes', 'retweets', 'comments'],
            spotify: ['followers', 'plays', 'saves', 'monthly_listeners'],
            discord: ['members', 'boosters', 'invites', 'activity']
        };

        this.init();
    }

    init() {
        this.loadSampleServices();
        this.setupEventListeners();
        this.initializeTabs();
        this.setDefaultDates();
        this.initializeCustomSelects();
        this.initializeTooltips();
        console.log('Orders Manager initialized');
    }

    // Load sample services data
    loadSampleServices() {
        this.services = [
            {
                id: 1,
                name: 'Instagram подписчики [Высокое качество] [Гарантия 30 дней]',
                category: 'followers',
                network: 'instagram',
                min: 100,
                max: 10000,
                rate: 0.85,
                time: '0-1 час',
                refill: true,
                description: 'Высококачественные подписчики с гарантией 30 дней'
            },
            {
                id: 2,
                name: 'YouTube просмотры [Быстрый старт] [Реальные]',
                category: 'views',
                network: 'youtube',
                min: 1000,
                max: 100000,
                rate: 0.12,
                time: '0-12 часов',
                refill: false,
                description: 'Быстрые просмотры с реальных аккаунтов'
            },
            {
                id: 3,
                name: 'TikTok лайки [Реальные пользователи] [Медленная скорость]',
                category: 'likes',
                network: 'tiktok',
                min: 50,
                max: 5000,
                rate: 0.45,
                time: '1-6 часов',
                refill: true,
                description: 'Лайки от реальных пользователей TikTok'
            },
            {
                id: 4,
                name: 'Telegram подписчики канала [Премиум] [Гарантия]',
                category: 'subscribers',
                network: 'telegram',
                min: 100,
                max: 50000,
                rate: 1.25,
                time: '0-2 часа',
                refill: true,
                description: 'Премиум подписчики с гарантией'
            },
            {
                id: 5,
                name: 'Facebook лайки постов [Безопасно] [Высокое качество]',
                category: 'likes',
                network: 'facebook',
                min: 50,
                max: 20000,
                rate: 0.65,
                time: '0-6 часов',
                refill: false,
                description: 'Безопасные лайки высокого качества'
            },
            {
                id: 6,
                name: 'Spotify подписчики [Реальные пользователи] [Премиум]',
                category: 'followers',
                network: 'spotify',
                min: 100,
                max: 5000,
                rate: 2.50,
                time: '1-3 дня',
                refill: true,
                description: 'Реальные подписчики для Spotify артистов'
            },
            {
                id: 7,
                name: 'Discord участники сервера [Активные] [Безопасно]',
                category: 'members',
                network: 'discord',
                min: 50,
                max: 10000,
                rate: 1.85,
                time: '0-6 часов',
                refill: false,
                description: 'Активные участники для Discord серверов'
            }
        ];
    }

    // Get selected social network from cards
    getSelectedNetwork() {
        const selectedCard = document.querySelector('.social-network-card.selected');
        return selectedCard ? selectedCard.dataset.network : null;
    }

    // Setup event listeners
    setupEventListeners() {
        // Social network card selection
        document.querySelectorAll('.social-network-card').forEach(card => {
            card.addEventListener('click', () => {
                // Remove selection from all cards
                document.querySelectorAll('.social-network-card').forEach(c => c.classList.remove('selected'));

                // Add selection to clicked card
                card.classList.add('selected');

                // Update select value
                const network = card.dataset.network;
                $('#socialNetworkSelect').val(network);

                // Update custom select display
                this.updateCustomSelectDisplay('#socialNetworkSelect', network);

                // Force custom select to update its display
                this.reinitializeCustomSelect('#socialNetworkSelect');

                // Update categories and services
                this.updateCategories();
                this.updateServices();
            });
        });

        // Social network select change
        $('#socialNetworkSelect').on('change', (e) => {
            const network = e.target.value;

            if (network === 'all') {
                // Remove selection from all cards
                document.querySelectorAll('.social-network-card').forEach(c => c.classList.remove('selected'));
            } else {
                // Remove selection from all cards
                document.querySelectorAll('.social-network-card').forEach(c => c.classList.remove('selected'));

                // Add selection to corresponding card
                const card = document.querySelector(`.social-network-card[data-network="${network}"]`);
                if (card) {
                    card.classList.add('selected');
                }
            }

            // Update categories and services
            this.updateCategories();
            this.updateServices();
        });

        // Category select change
        $('#categorySelect').on('change', (e) => {
            // Update services based on category selection
            this.updateServices();
        });

        // Tab switching
        $('.orders-page').on('click', '.tab-btn', (e) => {
            this.switchTab($(e.currentTarget).data('tab'));
        });

        // Close search results when clicking outside
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.orders-page .search-container').length) {
                $('.orders-page .search-results').removeClass('show');
            }
        });

        // Global notifications system is already initialized in SocnetApp
    }

    // Initialize tabs
    initializeTabs() {
        this.switchTab('single');
    }

    // Set default dates (start with placeholder only)
    setDefaultDates() {
        // Do not prefill dates – show placeholder first
        this.selectedDateFrom = null;
        this.selectedDateTo = null;

        // Initialize our custom date range picker
        this.initializeDateRangePicker();

        // Ensure input shows placeholder
        this.updateDateRangeDisplay();
    }

    // Switch between tabs
    switchTab(tabName) {
        $('.orders-page .tab-btn').removeClass('active');
        $('.orders-page .tab-content').removeClass('active');

        $('.orders-page .tab-btn[data-tab="' + tabName + '"]').addClass('active');
        $('.orders-page #' + tabName + 'OrderTab').addClass('active');

        console.log(`Switched to ${tabName} tab`);
    }

    // Update categories based on selected social network
    updateCategories() {
        const network = this.getSelectedNetwork();
        const $categorySelect = $('#categorySelect');

        if (network && network !== 'all') {
            // Enable service search when network is selected
            $('.orders-page #serviceSearch').prop('disabled', false);

            // Update category select options
            const availableCategories = this.categories[network] || [];
            $categorySelect.empty().append('<option value="all" data-icon="fas fa-list">Все категории</option>');

            availableCategories.forEach(category => {
                const icon = this.getCategoryIcon(category);
                $categorySelect.append(`<option value="${category}" data-icon="${icon}">${this.getCategoryName(category)}</option>`);
            });

            // Reset category selection and reinitialize custom select
            $categorySelect.val('all');
            this.updateCustomSelectDisplay('#categorySelect', 'all');
            this.reinitializeCustomSelect('#categorySelect');
        } else {
            // Disable service search when no network is selected
            $('.orders-page #serviceSearch').prop('disabled', true);

            // Reset category select to all categories
            $categorySelect.empty().html(`
                <option value="all" data-icon="fas fa-list">Все категории</option>
                <option value="followers" data-icon="fas fa-users">Подписчики</option>
                <option value="likes" data-icon="fas fa-heart">Лайки</option>
                <option value="views" data-icon="fas fa-eye">Просмотры</option>
                <option value="comments" data-icon="fas fa-comment">Комментарии</option>
                <option value="shares" data-icon="fas fa-share-alt">Репосты</option>
                <option value="saves" data-icon="fas fa-bookmark">Сохранения</option>
            `);

            // Reinitialize custom select
            this.reinitializeCustomSelect('#categorySelect');
        }

        // Reset service selection
        this.selectedService = null;
        this.hideServiceInfo();
        this.updateOrderButton();
    }

    // Get localized category name
    getCategoryName(category) {
        const names = {
            followers: 'Подписчики',
            likes: 'Лайки',
            views: 'Просмотры',
            comments: 'Комментарии',
            saves: 'Сохранения',
            subscribers: 'Подписчики',
            shares: 'Репосты',
            members: 'Участники',
            retweets: 'Ретвиты',
            plays: 'Воспроизведения',
            monthly_listeners: 'Месячные слушатели',
            boosters: 'Бустеры',
            invites: 'Приглашения',
            activity: 'Активность'
        };
        return names[category] || category;
    }

    // Get category icon
    getCategoryIcon(category) {
        const icons = {
            followers: 'fas fa-users',
            likes: 'fas fa-heart',
            views: 'fas fa-eye',
            comments: 'fas fa-comment',
            saves: 'fas fa-bookmark',
            subscribers: 'fas fa-users',
            shares: 'fas fa-share-alt',
            members: 'fas fa-user-friends',
            retweets: 'fas fa-retweet',
            plays: 'fas fa-play',
            monthly_listeners: 'fas fa-headphones',
            boosters: 'fas fa-rocket',
            invites: 'fas fa-envelope',
            activity: 'fas fa-chart-line'
        };
        return icons[category] || 'fas fa-tag';
    }

    // Update services based on network and category selection
    updateServices() {
        const network = this.getSelectedNetwork();
        const category = $('#categorySelect').val();

        if (network && network !== 'all') {
            // Enable service search when network is selected
            $('.orders-page #serviceSearch').prop('disabled', false);
        } else {
            // Disable service search when no network is selected
            $('.orders-page #serviceSearch').prop('disabled', true);
        }

        // Reset service selection
        this.selectedService = null;
        this.hideServiceInfo();
        this.updateOrderButton();
    }

    // Search services
    searchServices() {
        const query = $('.orders-page #serviceSearch').val().toLowerCase();
        const network = this.getSelectedNetwork();
        const category = $('#categorySelect').val();

        if (query.length < 2) {
            $('.orders-page #searchResults').removeClass('show');
            return;
        }

        console.log('Search params:', { query, network, category });

        const filteredServices = this.services.filter(service => {
            // Поиск по тексту
            const matchesQuery = service.name.toLowerCase().includes(query) ||
                service.id.toString().includes(query) ||
                service.network.toLowerCase().includes(query);

            // Фильтр по сети
            const matchesNetwork = !network || network === 'all' || service.network === network;

            // Фильтр по категории
            const matchesCategory = !category || category === 'all' || service.category === category;

            // Если есть поисковый запрос, приоритет отдается поиску
            if (matchesQuery) {
                // Применяем фильтры сети и категории
                return matchesNetwork && matchesCategory;
            }

            // Если поиск не совпал, применяем только фильтры
            return matchesNetwork && matchesCategory;
        });

        console.log('Filtered services:', filteredServices);
        this.renderSearchResults(filteredServices, '.orders-page #searchResults');
    }

    // Search services for mass order
    searchMassServices() {
        const query = $('.orders-page #massServiceSearch').val().toLowerCase();

        if (query.length < 2) {
            $('.orders-page #massSearchResults').removeClass('show');
            return;
        }

        const filteredServices = this.services.filter(service => {
            return service.name.toLowerCase().includes(query) ||
                service.id.toString().includes(query);
        });

        this.renderSearchResults(filteredServices, '.orders-page #massSearchResults');
    }

    // Render search results
    renderSearchResults(services, containerSelector) {
        const container = $(containerSelector);
        container.empty();

        if (services.length === 0) {
            container.html('<div class="search-result-item">Услуги не найдены</div>');
        } else {
            services.forEach(service => {
                const item = $(`
                    <div class="search-result-item" data-service-id="${service.id}">
                        <div class="service-id">ID: ${service.id}</div>
                        <div class="service-name">${service.name}</div>
                        <div class="service-rate">$${service.rate}/1000 | Min: ${service.min} | Max: ${service.max}</div>
                    </div>
                `);

                item.on('click', () => {
                    if (containerSelector === '.orders-page #searchResults') {
                        this.selectService(service);
                    } else {
                        this.selectMassService(service);
                    }
                });

                container.append(item);
            });
        }

        container.addClass('show');
    }

    // Select service for single order
    selectService(service) {
        this.selectedService = service;
        $('.orders-page #serviceSearch').val(`${service.id} - ${service.name}`);
        $('.orders-page #searchResults').removeClass('show');

        // Update quantity limits
        $('.orders-page #quantityLimits').text(`Мин: ${service.min} | Макс: ${service.max}`);

        // Show service info
        this.showServiceInfo(service);

        // Calculate cost if quantity is set
        this.calculateCost();

        console.log('Selected service:', service);
    }

    // Select service for mass order
    selectMassService(service) {
        $('.orders-page #massServiceSearch').val(`${service.id} - ${service.name}`);
        $('.orders-page #massSearchResults').removeClass('show');

        // Store selected service for adding to mass order
        window.selectedMassService = service;

        console.log('Selected mass service:', service);
    }

    // Show service information panel
    showServiceInfo(service) {
        const panel = $('.orders-page #serviceInfoPanel');
        const details = $('.orders-page #serviceDetails');

        // Create service-details-modal structure
        details.html(`
            <div class="service-details-modal">
                <div class="service-layout">
                    <!-- Left side: Service info -->
                    <div class="service-info-section">
                        <!-- Service name -->
                        <h3 class="service-name-modal">
                            <div class="service-id">#${service.id}</div>
                            <span>${service.name}</span>
                        </h3>

                        <!-- Service description -->
                        <div class="service-description-modal">
                            <p>${service.description}</p>
                        </div>
                    </div>

                    <!-- Right side: Social network -->
                    <div class="service-network-section">
                        <div class="service-network-large ${service.network}">
                            <i class="${this.getNetworkIcon(service.network)}"></i>
                            <span>${this.getNetworkName(service.network)}</span>
                        </div>
                        <div class="service-category">
                            <i class="${this.getCategoryIcon(service.category)}"></i>
                            <span>${this.getCategoryName(service.category)}</span>
                        </div>
                    </div>
                </div>

                <!-- Key parameters in a row -->
                <div class="service-parameters-row">
                    <div class="parameter-item">
                        <div class="parameter-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="parameter-content">
                            <div class="parameter-label">Цена за 1000</div>
                            <div class="parameter-value">$${service.rate}</div>
                        </div>
                    </div>

                    <div class="parameter-item">
                        <div class="parameter-icon">
                            <i class="fas fa-sort-amount-up"></i>
                        </div>
                        <div class="parameter-content">
                            <div class="parameter-label">Количество</div>
                            <div class="parameter-value">${service.min} - ${service.max}</div>
                        </div>
                    </div>

                    <div class="parameter-item">
                        <div class="parameter-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="parameter-content">
                            <div class="parameter-label">Время выполнения</div>
                            <div class="parameter-value">${service.time}</div>
                        </div>
                    </div>

                    <div class="parameter-item">
                        <div class="parameter-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="parameter-content">
                            <div class="parameter-label">Восстановление</div>
                            <div class="parameter-value ${service.refill ? 'success' : 'danger'}">
                                <i class="fas fa-${service.refill ? 'check' : 'times'}"></i>
                                ${service.refill ? 'Да' : 'Нет'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);

        panel.show();
    }

    // Hide service information panel
    hideServiceInfo() {
        $('.orders-page #serviceInfoPanel').hide();
    }

    // Calculate order cost
    calculateCost() {
        if (!this.selectedService) {
            $('.orders-page #orderCost').text('$0.00');
            this.updateOrderButton();
            return;
        }

        const quantity = parseInt($('.orders-page #quantity').val()) || 0;
        const cost = (quantity / 1000) * this.selectedService.rate;

        $('.orders-page #orderCost').text(`$${cost.toFixed(2)}`);

        // Update order button state
        this.updateOrderButton();
    }

    // Update order button state
    updateOrderButton() {
        const button = $('.orders-page #submitSingleOrder');
        const url = $('.orders-page #orderUrl').val();
        const quantity = parseInt($('.orders-page #quantity').val()) || 0;
        const cost = parseFloat($('.orders-page #orderCost').text().replace('$', ''));

        const isValid = this.selectedService &&
            url &&
            quantity >= this.selectedService.min &&
            quantity <= this.selectedService.max &&
            cost <= this.currentBalance;

        button.prop('disabled', !isValid);

        if (cost > this.currentBalance) {
            button.text('Недостаточно средств');
        } else {
            button.html('<i class="fas fa-shopping-cart"></i> Купить');
        }
    }

    // Toggle drip-feed options
    toggleDripFeed() {
        const enabled = $('.orders-page #dripFeedEnabled').is(':checked');
        const options = $('.orders-page #dripFeedOptions');

        if (enabled) {
            options.slideDown();
        } else {
            options.slideUp();
        }
    }

    // Submit single order
    submitSingleOrder() {
        if (!this.selectedService) {
            SocnetApp.notifications.showError('Пожалуйста, выберите услугу');
            return;
        }

        const orderData = {
            serviceId: this.selectedService.id,
            url: $('.orders-page #orderUrl').val(),
            quantity: parseInt($('.orders-page #quantity').val()),
            dripFeed: $('.orders-page #dripFeedEnabled').is(':checked'),
            dripRuns: parseInt($('.orders-page #dripRuns').val()) || null,
            dripInterval: parseInt($('.orders-page #dripInterval').val()) || null
        };

        // Validate data
        if (!orderData.url || !orderData.quantity) {
            SocnetApp.notifications.showError('Пожалуйста, заполните все обязательные поля');
            return;
        }

        if (orderData.quantity < this.selectedService.min || orderData.quantity > this.selectedService.max) {
            SocnetApp.notifications.showError(`Количество должно быть от ${this.selectedService.min} до ${this.selectedService.max}`);
            return;
        }

        const cost = (orderData.quantity / 1000) * this.selectedService.rate;
        if (cost > this.currentBalance) {
            SocnetApp.notifications.showError('Недостаточно средств на балансе');
            return;
        }

        // Show loading state
        this.showButtonLoading('.orders-page #submitSingleOrder', 'Создание заказа...');

        // Simulate API call
        this.processOrder(orderData);
    }

    // Process order (simulate API call)
    processOrder(orderData) {
        console.log('Processing order:', orderData);

        // Simulate API delay
        setTimeout(() => {
            // Hide loading state
            this.hideButtonLoading('.orders-page #submitSingleOrder');

            // Simulate success/failure
            const success = Math.random() > 0.1; // 90% success rate

            if (success) {
                const orderId = Math.floor(Math.random() * 10000) + 1000;
                SocnetApp.notifications.showSuccess(`Заказ #${orderId} успешно создан!`);
                this.resetSingleOrderForm();
            } else {
                SocnetApp.notifications.showError('Ошибка при создании заказа. Попробуйте снова.');
            }
        }, 1000);
    }

    // Reset single order form
    resetSingleOrderForm() {
        // Reset social network selection
        $('.orders-page #socialNetworkSelect').val('all');
        this.updateCustomSelectDisplay('#socialNetworkSelect', 'all');
        this.reinitializeCustomSelect('#socialNetworkSelect');

        // Reset category selection
        $('.orders-page #categorySelect').val('all');
        this.updateCustomSelectDisplay('#categorySelect', 'all');
        this.reinitializeCustomSelect('#categorySelect');

        // Reset other fields
        $('.orders-page #serviceSearch').val('').prop('disabled', true);
        $('.orders-page #orderUrl').val('');
        $('.orders-page #quantity').val('');
        $('.orders-page #dripFeedEnabled').prop('checked', false);
        $('.orders-page #dripRuns').val('');
        $('.orders-page #dripInterval').val('');
        $('.orders-page #dripFeedOptions').hide();

        // Remove selection from all cards
        document.querySelectorAll('.social-network-card').forEach(c => c.classList.remove('selected'));

        // Reset categories to all available
        this.updateCategories();

        this.selectedService = null;
        this.hideServiceInfo();
        $('.orders-page #orderCost').text('$0.00');
        this.updateOrderButton();

        $('.orders-page #searchResults').removeClass('show');
    }

    // Parse mass orders from textarea
    parseMassOrders() {
        const text = $('.orders-page #massOrderData').val();
        const lines = text.split('\n').filter(line => line.trim());

        this.massOrders = [];

        lines.forEach((line, index) => {
            const parts = line.split('|').map(part => part.trim());

            if (parts.length === 3) {
                const serviceId = parseInt(parts[0]);
                const url = parts[1];
                const quantity = parseInt(parts[2]);

                const service = this.services.find(s => s.id === serviceId);

                if (service && url && quantity) {
                    this.massOrders.push({
                        id: index + 1,
                        service: service,
                        url: url,
                        quantity: quantity,
                        cost: (quantity / 1000) * service.rate,
                        originalLine: line, // Сохраняем оригинальную строку
                        lineIndex: index // Сохраняем индекс строки
                    });
                }
            }
        });

        this.renderMassOrders();
    }

    // Add single order to mass order list
    addMassOrder() {
        const service = window.selectedMassService;
        const url = $('.orders-page #massUrl').val();
        const quantity = parseInt($('.orders-page #massQuantity').val());

        if (!service || !url || !quantity) {
            SocnetApp.notifications.showError('Пожалуйста, заполните все поля');
            return;
        }

        if (quantity < service.min || quantity > service.max) {
            SocnetApp.notifications.showError(`Количество должно быть от ${service.min} до ${service.max}`);
            return;
        }

        const order = {
            id: this.massOrders.length + 1,
            service: service,
            url: url,
            quantity: quantity,
            cost: (quantity / 1000) * service.rate
        };

        this.massOrders.push(order);

        // Clear form
        $('.orders-page #massServiceSearch').val('');
        $('.orders-page #massUrl').val('');
        $('.orders-page #massQuantity').val('');
        window.selectedMassService = null;

        this.renderMassOrders();
    }

    // Render mass orders table
    renderMassOrders() {
        const tbody = $('.orders-page #massOrdersBody');
        tbody.empty();

        if (this.massOrders.length === 0) {
            tbody.html('<tr class="empty-row"><td colspan="5">Список заказов пуст</td></tr>');
            $('.orders-page #totalMassCost').text('$0.00');
            $('.orders-page #submitMassOrders').prop('disabled', true);
            return;
        }

        let totalCost = 0;

        this.massOrders.forEach(order => {
            totalCost += order.cost;

            const row = $(`
                <tr>
                    <td>${order.service.id} - ${order.service.name}</td>
                    <td>${order.url}</td>
                    <td>${order.quantity}</td>
                    <td>$${order.cost.toFixed(2)}</td>
                    <td>
                        <div class="btn-group">
                            <button class="edit-btn" onclick="ordersManager.editMassOrder(${order.lineIndex}, this)" data-bs-toggle="tooltip" data-bs-placement="top" title="Изменить заказ">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="remove-btn" onclick="ordersManager.removeMassOrder(${order.id}, this)" data-bs-toggle="tooltip" data-bs-placement="top" title="Удалить заказ">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);

            tbody.append(row);
        });

        $('.orders-page #totalMassCost').text(`$${totalCost.toFixed(2)}`);
        $('.orders-page #submitMassOrders').prop('disabled', totalCost > this.currentBalance);

        if (totalCost > this.currentBalance) {
            $('.orders-page #submitMassOrders').text('Недостаточно средств');
        } else {
            $('.orders-page #submitMassOrders').html('<i class="fas fa-list-check"></i> Купить все');
        }
    }

    // Remove order from mass order list
    removeMassOrder(orderId, buttonElement) {
        // Скрываем тултип
        this.hideTooltip(buttonElement);

        const orderToRemove = this.massOrders.find(order => order.id === orderId);
        if (orderToRemove) {
            // Удаляем заказ из массива
            this.massOrders = this.massOrders.filter(order => order.id !== orderId);

            // Удаляем соответствующую строку из textarea
            const $textarea = $('.orders-page #massOrderData');
            const text = $textarea.val();
            const lines = text.split('\n');

            // Удаляем строку по индексу
            if (lines[orderToRemove.lineIndex]) {
                lines.splice(orderToRemove.lineIndex, 1);
                const newText = lines.join('\n');
                $textarea.val(newText);

                // Пересчитываем заказы после изменения textarea
                this.parseMassOrders();

                // Показываем уведомление об удалении
                SocnetApp.notifications.showSuccess('Заказ успешно удален');
            }
        }
    }

    // Edit mass order - highlight text in textarea
    editMassOrder(lineIndex, buttonElement) {
        // Скрываем тултип
        this.hideTooltip(buttonElement);

        const $textarea = $('.orders-page #massOrderData');
        const text = $textarea.val();
        const lines = text.split('\n');

        if (lines[lineIndex]) {
            // Устанавливаем фокус на textarea
            $textarea.focus();

            // Находим позицию начала строки
            let startPos = 0;
            for (let i = 0; i < lineIndex; i++) {
                startPos += lines[i].length + 1; // +1 для символа новой строки
            }

            const endPos = startPos + lines[lineIndex].length;

            // Выделяем строку
            $textarea[0].setSelectionRange(startPos, endPos);

            // Прокручиваем к выделенной строке
            const textareaHeight = $textarea[0].scrollHeight;
            const lineHeight = textareaHeight / lines.length;
            const scrollTop = (lineHeight * lineIndex) - ($textarea.height() / 2);
            $textarea.scrollTop(Math.max(0, scrollTop));

            // Показываем уведомление
            SocnetApp.notifications.showInfo('Строка выделена для редактирования');
        }
    }

    // Submit mass orders
    submitMassOrders() {
        if (this.massOrders.length === 0) {
            SocnetApp.notifications.showError('Список заказов пуст');
            return;
        }

        const totalCost = this.massOrders.reduce((sum, order) => sum + order.cost, 0);

        if (totalCost > this.currentBalance) {
            SocnetApp.notifications.showError('Недостаточно средств на балансе');
            return;
        }

        // Show loading state
        this.showButtonLoading('.orders-page #submitMassOrders', 'Создание заказов...');

        // Simulate API call
        console.log('Processing mass orders:', this.massOrders);

        setTimeout(() => {
            // Hide loading state
            this.hideButtonLoading('.orders-page #submitMassOrders');

            const success = Math.random() > 0.1;

            if (success) {
                SocnetApp.notifications.showSuccess(`Создано ${this.massOrders.length} заказов на сумму $${totalCost.toFixed(2)}`);
                this.resetMassOrderForm();
            } else {
                SocnetApp.notifications.showError('Ошибка при создании заказов. Попробуйте снова.');
            }
        }, 1500);
    }

    // Reset mass order form
    resetMassOrderForm() {
        $('.orders-page #massOrderData').val('');
        $('.orders-page #massServiceSearch').val('');
        $('.orders-page #massUrl').val('');
        $('.orders-page #massQuantity').val('');

        // Reset social network selection
        $('.orders-page #socialNetworkSelect').val('all');
        this.updateCustomSelectDisplay('#socialNetworkSelect', 'all');
        this.reinitializeCustomSelect('#socialNetworkSelect');

        // Reset category selection
        $('.orders-page #categorySelect').val('all');
        this.updateCustomSelectDisplay('#categorySelect', 'all');
        this.reinitializeCustomSelect('#categorySelect');

        // Remove selection from all cards
        document.querySelectorAll('.social-network-card').forEach(c => c.classList.remove('selected'));

        // Reset categories to all available
        this.updateCategories();

        this.massOrders = [];
        this.renderMassOrders();

        $('.orders-page #massSearchResults').removeClass('show');
        window.selectedMassService = null;
    }

    // Initialize AirDatePicker with range selection
    initializeDatePicker(defaultFrom, defaultTo) {
        const $dateRangeInput = $('.orders-page #dateRange');

        // Additional safety check - try both global and window references
        const AirDatepickerConstructor = window.AirDatepicker || (typeof AirDatepicker !== 'undefined' ? AirDatepicker : null);

        if (typeof AirDatepickerConstructor !== 'function') {
            console.error('AirDatepicker is not a constructor, using fallback');
            this.fallbackToBasicDateInputs();
            return;
        }

        // Store selected dates for later use
        this.selectedDateFrom = defaultFrom;
        this.selectedDateTo = defaultTo;

        // Update input value
        this.updateDateRangeDisplay();

        // Initialize AirDatePicker using constructor
        const datepicker = new AirDatepickerConstructor($dateRangeInput[0], {
            range: true,
            multipleDates: true,
            multipleDatesSeparator: ' - ',
            dateFormat: 'dd.MM.yyyy',
            locale: {
                days: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
                daysShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                daysMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Иун', 'Иул', 'Авг', 'Сен', 'Окт', 'Ноя', 'Декабрь'],
                today: 'Сегодня',
                clear: 'Очистить',
                dateFormat: 'dd.mm.yyyy',
                timeFormat: 'hh:ii',
                firstDay: 1
            },
            onSelect: (formattedDate, date, inst) => {
                if (date && date.length === 2) {
                    this.selectedDateFrom = date[0];
                    this.selectedDateTo = date[1];
                    this.updateDateRangeDisplay();
                }
            },
            onShow: () => {
                // Set default selection if no dates are selected
                if (!this.selectedDateFrom || !this.selectedDateTo) {
                    const today = new Date();
                    const oneMonthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
                    this.selectedDateFrom = oneMonthAgo;
                    this.selectedDateTo = today;
                }
            }
        });

        // Make icon clickable to open datepicker
        $dateRangeInput.siblings('.date-icon').on('click', () => {
            datepicker.show();
        });

        // Store datepicker instance for later use
        this.datepicker = datepicker;
    }

    // Fallback to basic date inputs if AirDatepicker is not available
    fallbackToBasicDateInputs() {
        const $dateRangeInput = $('.orders-page #dateRange');
        const $dateInputWrapper = $dateRangeInput.closest('.date-input-wrapper');

        // Replace the single input with two date inputs
        $dateInputWrapper.html(`
            <input type="date" id="dateFrom" class="date-input" style="margin-right: 0.5rem;">
            <span style="color: var(--text-muted); margin: 0 0.5rem;">—</span>
            <input type="date" id="dateTo" class="date-input">
        `);

        // Set default values
        const today = new Date();
        const oneMonthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

        $('#dateFrom').val(oneMonthAgo.toISOString().split('T')[0]);
        $('#dateTo').val(today.toISOString().split('T')[0]);

        // Store selected dates
        this.selectedDateFrom = oneMonthAgo;
        this.selectedDateTo = today;

        // Add event listeners for date changes
        $('#dateFrom, #dateTo').on('change', () => {
            const fromDate = new Date($('#dateFrom').val());
            const toDate = new Date($('#dateTo').val());

            if (fromDate && toDate) {
                this.selectedDateFrom = fromDate;
                this.selectedDateTo = toDate;
            }
        });

        console.log('Using fallback date inputs');
    }

    // Initialize custom selects
    initializeCustomSelects() {
        // Wait for CustomSelect to be available
        if (typeof CustomSelect !== 'undefined') {
            CustomSelect.initializeAll();
        } else {
            // Fallback: try to initialize after a short delay
            setTimeout(() => {
                if (typeof CustomSelect !== 'undefined') {
                    CustomSelect.initializeAll();
                } else {
                    console.warn('CustomSelect not available, using default selects');
                }
            }, 100);
        }
    }

    // Initialize tooltips
    initializeTooltips() {
        // Инициализируем Bootstrap tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    trigger: 'hover'
                });
            });
        }
    }

    // Hide tooltip on button click
    hideTooltip(buttonElement) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltip = bootstrap.Tooltip.getInstance(buttonElement);
            if (tooltip) {
                tooltip.hide();
            }
        }
    }

    // Reinitialize custom select after dynamic content changes
    reinitializeCustomSelect(selector) {
        const $select = $(selector);
        const $container = $select.siblings('.custom-select-container');

        // Remove existing custom select container
        if ($container.length) {
            $container.remove();
        }

        // Remove initialization attribute
        $select.removeAttr('data-initialized');

        // Show original select temporarily
        $select.show();

        // Trigger custom select initialization
        if (typeof CustomSelect !== 'undefined' && CustomSelect.createCustomSelect) {
            CustomSelect.createCustomSelect($select[0]);
        }

        // Hide original select again
        $select.hide();
    }

    // Reset custom select display
    resetCustomSelect(selector) {
        const $select = $(selector);
        const $container = $select.siblings('.custom-select-container');

        if ($container.length) {
            const placeholder = $select.attr('data-placeholder') || 'Выберите опцию';

            // Устанавливаем только placeholder без иконки
            $container.find('.custom-select-display').html(`
                <span class="custom-select-text custom-select-placeholder">${placeholder}</span>
            `);
        }
    }

    // Update custom select disabled state
    updateCustomSelectDisabledState(selector, disabled) {
        const $select = $(selector);
        const $container = $select.siblings('.custom-select-container');

        if ($container.length) {
            const $display = $container.find('.custom-select-display');

            if (disabled) {
                $display.addClass('disabled');
                $select.prop('disabled', true);
            } else {
                $display.removeClass('disabled');
                $select.prop('disabled', false);
            }
        }
    }

    // Update custom select display text
    updateCustomSelectDisplay(selector, value) {
        const $select = $(selector);
        const $container = $select.siblings('.custom-select-container');

        if ($container.length) {
            const selectedOption = $select.find(`option[value="${value}"]`);

            if (selectedOption.length) {
                const icon = selectedOption.attr('data-icon');
                const text = selectedOption.text();

                if (icon) {
                    // Обновляем содержимое custom-select-display с иконкой и текстом
                    $container.find('.custom-select-display').html(`
                        <i class="${icon}"></i>
                        <span class="custom-select-text">${text}</span>
                    `);
                } else {
                    // Только текст без иконки
                    $container.find('.custom-select-display').html(`
                        <span class="custom-select-text">${text}</span>
                    `);
                }

                $container.find('.custom-select-text').removeClass('custom-select-placeholder');
            } else {
                const placeholder = $select.attr('data-placeholder') || 'Выберите опцию';
                $container.find('.custom-select-display').html(`
                    <span class="custom-select-text custom-select-placeholder">${placeholder}</span>
                `);
            }
        }
    }

    // Wait for AirDatepicker to be available
    waitForAirDatepicker(callback, maxAttempts = 50) {
        let attempts = 0;

        const checkAirDatepicker = () => {
            attempts++;

            // Check both global AirDatepicker and window.AirDatepicker
            const airDatepicker = window.AirDatepicker || (typeof AirDatepicker !== 'undefined' ? AirDatepicker : null);

            if (airDatepicker && typeof airDatepicker === 'function') {
                console.log('AirDatepicker loaded successfully and is a constructor');
                // Ensure it's available globally
                window.AirDatepicker = airDatepicker;
                callback();
                return;
            }

            if (attempts >= maxAttempts) {
                console.error('AirDatepicker failed to load after maximum attempts, using fallback');
                this.fallbackToBasicDateInputs();
                return;
            }

            // Wait 100ms before next attempt
            setTimeout(checkAirDatepicker, 100);
        };

        checkAirDatepicker();
    }

    // Clear selected dates
    clearDateFilter() {
        this.selectedDateFrom = null;
        this.selectedDateTo = null;
        this.updateDateRangeDisplay();

        // Clear our custom date range picker
        if (this.dateRangePicker && typeof this.dateRangePicker.clearSelection === 'function') {
            this.dateRangePicker.clearSelection();
        }

        SocnetApp.notifications.showSuccess('Фильтр по датам очищен');
    }

    // Initialize custom date range picker
    initializeDateRangePicker() {
        const $dateRangeInput = $('.orders-page #dateRange');
        const $dateIcon = $dateRangeInput.siblings('.date-icon');

        // Create date range picker instance
        this.dateRangePicker = window.createDateRangePicker({
            startDate: null,
            endDate: null,
            locale: 'ru',
            format: 'DD.MM.YYYY',
            showClearButton: false
        });

        // Set up callbacks
        this.dateRangePicker
            .onApply((data) => {
                this.selectedDateFrom = data.startDate;
                this.selectedDateTo = data.endDate;
                this.updateDateRangeDisplay();
                console.log('Date range applied:', data);
            })
            .onClear(() => {
                this.selectedDateFrom = null;
                this.selectedDateTo = null;
                this.updateDateRangeDisplay();
                console.log('Date range cleared');
            });

        // Make input and icon clickable to open date picker
        $dateRangeInput.on('click', () => {
            this.dateRangePicker.open();
        });

        $dateIcon.on('click', () => {
            this.dateRangePicker.open();
        });
    }

    // Update date range display in input
    updateDateRangeDisplay() {
        const $dateRangeInput = $('.orders-page #dateRange');

        if (this.selectedDateFrom && this.selectedDateTo) {
            const fromStr = this.selectedDateFrom.toLocaleDateString('ru-RU');
            const toStr = this.selectedDateTo.toLocaleDateString('ru-RU');
            $dateRangeInput.val(`${fromStr} - ${toStr}`);
        } else if (this.selectedDateFrom) {
            const fromStr = this.selectedDateFrom.toLocaleDateString('ru-RU');
            $dateRangeInput.val(fromStr);
        } else {
            // Пустое значение, чтобы показался placeholder
            $dateRangeInput.val('');
        }
    }

    // Apply date filter
    applyDateFilter() {
        if (!this.selectedDateFrom || !this.selectedDateTo) {
            SocnetApp.notifications.showError('Пожалуйста, выберите даты');
            return;
        }

        if (this.selectedDateFrom > this.selectedDateTo) {
            SocnetApp.notifications.showError('Дата начала не может быть больше даты окончания');
            return;
        }

        const dateFrom = this.selectedDateFrom.toISOString().split('T')[0];
        const dateTo = this.selectedDateTo.toISOString().split('T')[0];

        console.log('Applying date filter:', { dateFrom, dateTo });

        // Here you would typically make an API call to update statistics
        SocnetApp.notifications.showSuccess('Фильтр применен успешно');
    }

    // Show loading state on button
    showButtonLoading(buttonSelector, loadingText = 'Обработка...') {
        const $button = $(buttonSelector);
        const originalContent = $button.html();

        // Store original content for restoration
        $button.data('original-content', originalContent);

        // Set loading state
        $button.prop('disabled', true)
            .addClass('loading')
            .html(`
                   <div class="button-loading">
                       <i class="fas fa-spinner fa-spin"></i>
                       <span>${loadingText}</span>
                   </div>
               `);

        console.log(`Button ${buttonSelector} loading state activated`);
    }

    // Hide loading state on button
    hideButtonLoading(buttonSelector) {
        const $button = $(buttonSelector);
        const originalContent = $button.data('original-content');

        if (originalContent) {
            $button.prop('disabled', false)
                .removeClass('loading')
                .html(originalContent);

            // Remove stored data
            $button.removeData('original-content');
        }

        console.log(`Button ${buttonSelector} loading state deactivated`);
    }

    // Get network icon
    getNetworkIcon(network) {
        const icons = {
            instagram: 'fab fa-instagram',
            youtube: 'fab fa-youtube',
            tiktok: 'fab fa-tiktok',
            telegram: 'fab fa-telegram',
            facebook: 'fab fa-facebook',
            twitter: 'fab fa-twitter',
            spotify: 'fab fa-spotify',
            discord: 'fab fa-discord'
        };
        return icons[network] || 'fas fa-globe';
    }

    // Get network display name
    getNetworkName(network) {
        const displayNames = {
            instagram: 'Instagram',
            youtube: 'YouTube',
            tiktok: 'TikTok',
            telegram: 'Telegram',
            facebook: 'Facebook',
            twitter: 'Twitter',
            spotify: 'Spotify',
            discord: 'Discord'
        };
        return displayNames[network] || network.charAt(0).toUpperCase() + network.slice(1);
    }
}

// Global functions for HTML onclick handlers
// Note: updateCategories and updateServices are now handled automatically by card selection

window.searchServices = function () {
    if (window.ordersManager) {
        window.ordersManager.searchServices();
    }
};

window.searchMassServices = function () {
    if (window.ordersManager) {
        window.ordersManager.searchMassServices();
    }
};

window.calculateCost = function () {
    if (window.ordersManager) {
        window.ordersManager.calculateCost();
    }
};

window.toggleDripFeed = function () {
    if (window.ordersManager) {
        window.ordersManager.toggleDripFeed();
    }
};

window.submitSingleOrder = function () {
    if (window.ordersManager) {
        window.ordersManager.submitSingleOrder();
    }
};

window.parseMassOrders = function () {
    if (window.ordersManager) {
        window.ordersManager.parseMassOrders();
    }
};

window.addMassOrder = function () {
    if (window.ordersManager) {
        window.ordersManager.addMassOrder();
    }
};

window.submitMassOrders = function () {
    if (window.ordersManager) {
        window.ordersManager.submitMassOrders();
    }
};

window.applyDateFilter = function () {
    if (window.ordersManager) {
        window.ordersManager.applyDateFilter();
    }
};

window.clearDateFilter = function () {
    if (window.ordersManager) {
        window.ordersManager.clearDateFilter();
    }
};

// Initialize when document is ready
$(document).ready(function () {
    window.ordersManager = new OrdersManager();
    console.log('Orders page loaded');
});

// Integration with main SocnetApp if it exists
if (typeof SocnetApp !== 'undefined') {
    SocnetApp.orders = {
        manager: null,
        init() {
            this.manager = new OrdersManager();
            return this.manager;
        }
    };

    console.log('Orders module integrated with SocnetApp');
}