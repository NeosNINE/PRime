/**
 * Services Page Manager
 * Управление страницей сервисов
 */
class ServicesManager {
    constructor() {
        this.services = [];
        this.filteredServices = [];
        this.currentFilters = {
            search: '',
            network: 'all',
            category: 'all',
            sort: 'id',
            sortDirection: 'asc' // 'asc' или 'desc'
        };
        this.currentPage = 1;
        this.itemsPerPage = 20;
        this.searchTimeout = null;
        this.pagination = null;

        this.init();
    }

    init() {
        console.log('ServicesManager: Initializing...');

        this.generateSampleServices();
        this.bindEvents();
        this.initializePagination();
        this.applyFilters();

        // Initialize social network card selection based on current filters
        this.initializeSocialNetworkCardSelection();

        console.log('ServicesManager: Initialized successfully');
    }

    // Generate sample services data
    generateSampleServices() {
        const networks = [
            { id: 'instagram', name: 'Instagram', icon: 'fab fa-instagram' },
            { id: 'youtube', name: 'YouTube', icon: 'fab fa-youtube' },
            { id: 'tiktok', name: 'TikTok', icon: 'fab fa-tiktok' },
            { id: 'telegram', name: 'Telegram', icon: 'fab fa-telegram' },
            { id: 'facebook', name: 'Facebook', icon: 'fab fa-facebook' },
            { id: 'twitter', name: 'Twitter', icon: 'fab fa-twitter' }
        ];

        const categories = [
            { id: 'likes', name: 'Лайки', icon: 'fas fa-heart' },
            { id: 'followers', name: 'Подписчики', icon: 'fas fa-users' },
            { id: 'views', name: 'Просмотры', icon: 'fas fa-eye' },
            { id: 'comments', name: 'Комментарии', icon: 'fas fa-comment' },
            { id: 'shares', name: 'Репосты', icon: 'fas fa-share' },
            { id: 'saves', name: 'Сохранения', icon: 'fas fa-bookmark' }
        ];

        this.services = [];
        let serviceId = 1;

        networks.forEach(network => {
            categories.forEach(category => {
                // Generate 2-4 services per network-category combination
                const servicesCount = Math.floor(Math.random() * 3) + 2;

                for (let i = 0; i < servicesCount; i++) {
                    const basePrice = Math.random() * 5 + 0.5; // $0.5 - $5.5
                    const minQuantity = [50, 100, 500, 1000][Math.floor(Math.random() * 4)];
                    const maxQuantity = minQuantity * (Math.floor(Math.random() * 20) + 10);
                    const executionTime = [`0-1 час`, `1-6 часов`, `6-24 часа`, `1-3 дня`][Math.floor(Math.random() * 4)];

                    const qualityTypes = ['Fast', 'High Quality', 'Premium', 'Real', 'Bot', 'Mixed'];
                    const speedTypes = ['Instant', 'Fast', 'Medium', 'Slow'];
                    const guaranteeTypes = ['30 days', '60 days', '90 days', 'Lifetime', 'No guarantee'];

                    const quality = qualityTypes[Math.floor(Math.random() * qualityTypes.length)];
                    const speed = speedTypes[Math.floor(Math.random() * speedTypes.length)];
                    const guarantee = guaranteeTypes[Math.floor(Math.random() * guaranteeTypes.length)];

                    this.services.push({
                        id: serviceId++,
                        name: `${network.name} ${category.name} [${quality}] [${speed}]`,
                        description: `${quality} ${category.name.toLowerCase()} for ${network.name} with ${guarantee} guarantee`,
                        network: network.id,
                        networkName: network.name,
                        networkIcon: network.icon,
                        category: category.id,
                        categoryName: category.name,
                        categoryIcon: category.icon,
                        price: parseFloat(basePrice.toFixed(2)),
                        minQuantity: minQuantity,
                        maxQuantity: maxQuantity,
                        executionTime: executionTime,
                        refill: Math.random() > 0.3, // 70% chance of refill
                        quality: quality,
                        speed: speed,
                        guarantee: guarantee,
                        popular: Math.random() > 0.8, // 20% chance of being popular
                        new: Math.random() > 0.9, // 10% chance of being new
                        createdAt: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000) // Random date within last 30 days
                    });
                }
            });
        });

        // Sort by ID by default
        this.services.sort((a, b) => a.id - b.id);
        console.log(`Generated ${this.services.length} sample services`);
    }

    // Initialize pagination component
    initializePagination() {
        const paginationContainer = document.getElementById('servicesPagination');
        if (paginationContainer && typeof initializePagination === 'function') {
            this.pagination = initializePagination(paginationContainer, {
                currentPage: this.currentPage,
                totalPages: Math.ceil(this.filteredServices.length / this.itemsPerPage),
                totalItems: this.filteredServices.length,
                itemsPerPage: this.itemsPerPage,
                showInfo: true,
                showPerPage: true,
                perPageOptions: [20, 50, 100],
                onPageChange: (page) => {
                    this.currentPage = page;
                    this.renderServices();
                    this.scrollToTop();
                },
                onPerPageChange: (itemsPerPage) => {
                    this.itemsPerPage = itemsPerPage;
                    this.currentPage = 1;
                    this.updatePagination();
                    this.renderServices();
                    this.scrollToTop();
                }
            });
        }
    }

    // Scroll to top of services section
    scrollToTop() {
        const servicesSection = document.querySelector('.services-cards-section');
        if (servicesSection) {
            servicesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Update sort direction icon
    updateSortDirectionIcon() {
        const directionBtn = document.getElementById('sortDirection');
        if (directionBtn) {
            const icon = directionBtn.querySelector('i');
            if (icon) {
                icon.className = this.currentFilters.sortDirection === 'asc'
                    ? 'fa-solid fa-arrow-up'
                    : 'fa-solid fa-arrow-down';
            }
        }
    }

    // Bind event listeners
    bindEvents() {
        // Search input
        $('.services-page #serviceSearch').on('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentFilters.search = e.target.value.toLowerCase();
                this.applyFilters();
                this.updateSearchResults();
            }, 300);
        });

        // Search input focus/blur for results
        $('.services-page #serviceSearch').on('focus', () => {
            if ($('.services-page #serviceSearch').val().length > 0) {
                this.updateSearchResults();
            }
        });

        $(document).on('click', (e) => {
            if (!$(e.target).closest('.services-page .search-input-wrapper').length) {
                $('.services-page .search-results').removeClass('show');
            }
        });

        // Social network card selection
        $('.services-page .social-network-card').on('click', (e) => {
            const card = e.currentTarget;
            const network = card.dataset.network;

            // Remove selection from all cards
            $('.services-page .social-network-card').removeClass('selected');

            // Add selection to clicked card
            $(card).addClass('selected');

            // Update select value and trigger change event
            const networkSelect = $('.services-page #socialNetworkFilter')[0];
            networkSelect.value = network;

            // Update custom select display using CustomSelect API
            if (window.CustomSelect && typeof window.CustomSelect.setValue === 'function') {
                window.CustomSelect.setValue(networkSelect, network);
                console.log('CustomSelect updated to:', network);
            } else {
                // Fallback: manually update custom select display
                this.updateCustomSelectDisplayManually(networkSelect, network);
                console.log('Manual custom select update to:', network);
            }

            // Update filters and apply
            this.currentFilters.network = network;
            this.applyFilters();
        });

        // Social network custom select
        $('.services-page #socialNetworkFilter').on('change', (e) => {
            const network = e.target.value;

            // Update card selection
            if (network === 'all') {
                $('.services-page .social-network-card').removeClass('selected');
            } else {
                $('.services-page .social-network-card').removeClass('selected');
                $(`.services-page .social-network-card[data-network="${network}"]`).addClass('selected');
            }

            this.currentFilters.network = network;
            this.applyFilters();
        });

        // Category filter
        $('.services-page #categoryFilter').on('change', (e) => {
            this.currentFilters.category = e.target.value;
            this.applyFilters();
        });

        // Sort filter
        $('.services-page #sortFilter').on('change', (e) => {
            this.currentFilters.sort = e.target.value;
            this.applyFilters();
        });

        // Sort direction button
        $('.services-page #sortDirection').on('click', () => {
            this.currentFilters.sortDirection = this.currentFilters.sortDirection === 'asc' ? 'desc' : 'asc';
            this.updateSortDirectionIcon();
            this.applyFilters();
        });

        // Clear filters
        $('.services-page #clearFilters').on('click', () => {
            this.clearFilters();
        });

        // Initialize social network card selection based on current filters
        this.initializeSocialNetworkCardSelection();

        // Service row clicks (for table rows)
        $('.services-page').on('click', 'tbody tr', (e) => {
            if (!$(e.target).closest('.details-btn, .buy-btn').length) {
                const serviceId = $(e.currentTarget).data('service-id');
                this.showServiceDetails(serviceId);
            }
        });

        // Details buttons
        $('.services-page').on('click', '.details-btn', (e) => {
            e.stopPropagation();
            const serviceId = $(e.currentTarget).data('service-id');
            this.showServiceDetails(serviceId);
        });

        // Buy buttons
        $('.services-page').on('click', '.buy-btn', (e) => {
            e.stopPropagation();
            const serviceId = $(e.currentTarget).data('service-id');
            this.redirectToOrder(serviceId);
        });

        // Search result clicks
        $('.services-page').on('click', '.search-result-item', (e) => {
            const serviceId = $(e.currentTarget).data('service-id');
            const service = this.services.find(s => s.id === serviceId);
            if (service) {
                $('.services-page #serviceSearch').val(service.name);
                this.currentFilters.search = service.name.toLowerCase();
                $('.services-page .search-results').removeClass('show');
                this.applyFilters();
            }
        });

        // Modal order button
        $('.services-modal #orderServiceBtn').on('click', () => {
            const serviceId = $('.services-modal #orderServiceBtn').data('service-id');
            if (serviceId) {
                this.redirectToOrder(serviceId);
            }
        });


    }

    // Apply current filters
    applyFilters() {
        this.filteredServices = this.services.filter(service => {
            // Search filter
            if (this.currentFilters.search) {
                const searchTerm = this.currentFilters.search;
                const matchesSearch =
                    service.name.toLowerCase().includes(searchTerm) ||
                    service.id.toString().includes(searchTerm) ||
                    service.description.toLowerCase().includes(searchTerm) ||
                    service.networkName.toLowerCase().includes(searchTerm) ||
                    service.categoryName.toLowerCase().includes(searchTerm);

                if (!matchesSearch) return false;
            }

            // Network filter
            if (this.currentFilters.network !== 'all' && service.network !== this.currentFilters.network) {
                return false;
            }

            // Category filter
            if (this.currentFilters.category !== 'all' && service.category !== this.currentFilters.category) {
                return false;
            }

            return true;
        });

        // Apply sorting
        this.sortServices();

        // Reset to first page
        this.currentPage = 1;

        // Update UI
        this.renderServices();
        this.updatePagination();
        this.updateServicesCount();
    }

    // Sort filtered services
    sortServices() {
        const direction = this.currentFilters.sortDirection === 'asc' ? 1 : -1;

        switch (this.currentFilters.sort) {
            case 'newest':
                this.filteredServices.sort((a, b) => {
                    const result = a.createdAt - b.createdAt;
                    return direction === 1 ? result : -result;
                });
                break;
            case 'price':
                this.filteredServices.sort((a, b) => {
                    const result = a.price - b.price;
                    return direction === 1 ? result : -result;
                });
                break;
            case 'name':
                this.filteredServices.sort((a, b) => {
                    const result = a.name.localeCompare(b.name);
                    return direction === 1 ? result : -result;
                });
                break;
            case 'id':
            default:
                this.filteredServices.sort((a, b) => {
                    const result = a.id - b.id;
                    return direction === 1 ? result : -result;
                });
                break;
        }
    }

    // Update search results dropdown
    updateSearchResults() {
        const searchTerm = $('.services-page #serviceSearch').val().toLowerCase();
        const resultsContainer = $('.services-page .search-results');

        if (searchTerm.length < 2) {
            resultsContainer.removeClass('show');
            return;
        }

        const matchingServices = this.services.filter(service =>
            service.name.toLowerCase().includes(searchTerm) ||
            service.id.toString().includes(searchTerm) ||
            service.networkName.toLowerCase().includes(searchTerm) ||
            service.categoryName.toLowerCase().includes(searchTerm)
        ).slice(0, 8); // Show max 8 results

        if (matchingServices.length === 0) {
            resultsContainer.html(`
                <div class="search-result-item">
                    <div class="service-info">
                        <div class="service-name">Услуги не найдены</div>
                    </div>
                </div>
            `).addClass('show');
            return;
        }

        const resultsHtml = matchingServices.map(service => `
            <div class="search-result-item" data-service-id="${service.id}">
                <div class="service-id">#${service.id}</div>
                <div class="service-info">
                    <div class="service-name">${service.name}</div>
                    <div class="service-meta">
                        <span>$${service.price.toFixed(2)}/1K</span>
                        <span>${service.minQuantity} - ${service.maxQuantity}</span>
                        <span>${service.executionTime}</span>
                    </div>
                </div>
                <div class="service-network ${service.network}">
                    <i class="${service.networkIcon}"></i>
                </div>
            </div>
        `).join('');

        resultsContainer.html(resultsHtml).addClass('show');
    }

    // Render services cards
    renderServices() {
        const container = document.getElementById('servicesCardsContainer');
        const loadingState = $('.services-page #servicesLoading');
        const noResultsState = $('.services-page #servicesNoResults');

        // Show loading
        loadingState.show();
        container.innerHTML = '';
        noResultsState.hide();

        // Simulate loading delay
        setTimeout(() => {
            loadingState.hide();

            if (this.filteredServices.length === 0) {
                noResultsState.show();
                // Hide pagination when no services
                if (this.pagination) {
                    $('.services-pagination').hide();
                }
                return;
            }

            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            const servicesToShow = this.filteredServices.slice(startIndex, endIndex);

            this.renderCards(servicesToShow);

            // Update pagination and show it
            this.updatePagination();
            if (this.pagination) {
                $('.services-pagination').show();
            }
        }, 300);
    }

    // Render service cards grouped by category
    renderCards(services) {
        const container = document.getElementById('servicesCardsContainer');

        // Group services by category
        const groupedServices = this.groupServicesByCategory(services);

        container.innerHTML = '';

        Object.entries(groupedServices).forEach(([groupKey, groupData]) => {
            // Create category section
            const categorySection = document.createElement('div');
            categorySection.className = 'services-category-section';

            // Category header with network icon and name
            const categoryHeader = document.createElement('div');
            categoryHeader.className = 'services-category-header';

            const networkIcon = this.getNetworkIcon(groupData.network);
            const networkName = this.getNetworkDisplayName(groupData.network);
            const categoryName = this.getCategoryDisplayName(groupData.category);

            categoryHeader.innerHTML = `
                <div class="category-header-content">
                    <div class="category-title-wrapper">
                        <i class="${networkIcon}"></i>
                        <h3 class="category-title">${networkName} – ${categoryName}</h3>
                        </div>
                    <span class="category-count">${groupData.services.length} услуг</span>
                    </div>
            `;

            // Services grid
            const servicesGrid = document.createElement('div');
            servicesGrid.className = 'services-grid';
            servicesGrid.innerHTML = groupData.services.map(service => this.createServiceCard(service)).join('');

            categorySection.appendChild(categoryHeader);
            categorySection.appendChild(servicesGrid);
            container.appendChild(categorySection);
        });
    }

    // Group services by network and category
    groupServicesByCategory(services) {
        return services.reduce((groups, service) => {
            // Create unique key combining network and category
            const groupKey = `${service.network}-${service.category}`;
            if (!groups[groupKey]) {
                groups[groupKey] = {
                    network: service.network,
                    category: service.category,
                    services: []
                };
            }
            groups[groupKey].services.push(service);
            return groups;
        }, {});
    }

    // Get network display name
    getNetworkDisplayName(network) {
        const displayNames = {
            instagram: 'Instagram',
            youtube: 'YouTube',
            tiktok: 'TikTok',
            telegram: 'Telegram',
            facebook: 'Facebook',
            twitter: 'Twitter'
        };
        return displayNames[network] || network.charAt(0).toUpperCase() + network.slice(1);
    }

    // Get category display name
    getCategoryDisplayName(category) {
        const displayNames = {
            followers: 'Подписчики',
            likes: 'Лайки',
            views: 'Просмотры',
            comments: 'Комментарии',
            shares: 'Репосты',
            saves: 'Сохранения'
        };
        return displayNames[category] || category.charAt(0).toUpperCase() + category.slice(1);
    }

    // Get network icon
    getNetworkIcon(network) {
        const icons = {
            instagram: 'fab fa-instagram',
            youtube: 'fab fa-youtube',
            tiktok: 'fab fa-tiktok',
            telegram: 'fab fa-telegram',
            facebook: 'fab fa-facebook',
            twitter: 'fab fa-twitter'
        };
        return icons[network] || 'fas fa-globe';
    }

    // Create service card HTML
    createServiceCard(service) {
        return `
            <div class="service-card" data-service-id="${service.id}">
                <!-- Первая строка: ID + заголовок слева, цена справа -->
                <div class="service-row-top">
                    <div class="service-main-info">
                        <span class="service-id">#${service.id}</span>
                        <h4 class="service-title">${service.name}</h4>
                    </div>
                    <div class="service-price">$${service.price.toFixed(2)} за 1000</div>
                </div>

                <!-- Вторая строка: детали слева, кнопки справа -->
                <div class="service-row-bottom">
                    <div class="service-details">
                        <span class="service-detail-item">
                            <i class="${service.networkIcon}"></i>
                            ${this.getNetworkDisplayName(service.network)}
                        </span>
                        <span class="service-detail-item">
                            <i class="${service.categoryIcon}"></i>
                            ${this.getCategoryDisplayName(service.category)}
                        </span>
                        <span class="service-detail-item">
                            <i class="fas fa-clock"></i>
                            ${service.executionTime}
                        </span>
                        <span class="service-detail-item">
                            <i class="fas fa-layer-group"></i>
                            ${service.minQuantity.toLocaleString()} - ${service.maxQuantity.toLocaleString()}
                        </span>
                        <span class="service-detail-item">
                            ${service.refill
                                ? '<i class="fas fa-check-circle" style="color: var(--accent-success)"></i> Восстановление'
                                : '<i class="fas fa-times-circle" style="color: var(--accent-danger)"></i> Без восстановления'
                            }
                        </span>
                    </div>
                    <div class="service-actions">
                        <button class="btn-description" onclick="showServiceDetails(${service.id})">
                            <i class="fas fa-info-circle"></i>
                            Описание
                </button>
                        <a href="#" class="btn-buy" onclick="orderService(${service.id}); return false;">
                            <i class="fas fa-shopping-cart"></i>
                            Купить
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    // Update pagination component
    updatePagination() {
        if (this.pagination) {
            this.pagination.setData({
                currentPage: this.currentPage,
                totalPages: Math.ceil(this.filteredServices.length / this.itemsPerPage),
                totalItems: this.filteredServices.length,
                itemsPerPage: this.itemsPerPage
            });
        }
    }

    // Update services count
    updateServicesCount() {
        $('.services-page #servicesCount').text(this.filteredServices.length);
    }

    // Clear all filters
    clearFilters() {
        this.currentFilters = {
            search: '',
            network: 'all',
            category: 'all',
            sort: 'id',
            sortDirection: 'asc'
        };

        // Reset UI
        $('.services-page #serviceSearch').val('');

        // Reset custom selects properly
        const socialNetworkSelect = document.getElementById('socialNetworkFilter');
        const categorySelect = document.getElementById('categoryFilter');
        const sortSelect = document.getElementById('sortFilter');

        if (window.CustomSelect) {
            if (socialNetworkSelect) {
                window.CustomSelect.setValue(socialNetworkSelect, 'all');
            }
            if (categorySelect) {
                window.CustomSelect.setValue(categorySelect, 'all');
            }
            if (sortSelect) {
                window.CustomSelect.setValue(sortSelect, 'id');
            }
        } else {
            // Fallback for non-custom selects
            $('.services-page #socialNetworkFilter').val('all').trigger('change');
            $('.services-page #categoryFilter').val('all');
            $('.services-page #sortFilter').val('id');
        }

        // Reset social network card selection
        $('.services-page .social-network-card').removeClass('selected');

        // Reset sort direction
        this.currentFilters.sortDirection = 'asc';
        this.updateSortDirectionIcon();

        $('.services-page .search-results').removeClass('show');

        this.applyFilters();

        // Show notification
        if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
            SocnetApp.notifications.showSuccess('Фильтры очищены');
        }
    }

    // Show service details modal
    showServiceDetails(serviceId) {
        const service = this.services.find(s => s.id === serviceId);
        if (!service) return;

        const modalContent = `
            <div class="service-details-modal">
                <div class="service-layout">
                    <!-- Left side: Service info -->
                    <div class="service-info-section">
                        <!-- Category above name -->
                        <div class="service-category">
                            <i class="${service.categoryIcon}"></i>
                            <span>${service.categoryName}</span>
                </div>

                        <!-- Service name -->
                <h3 class="service-name-modal">${service.name}</h3>

                        <!-- Service description -->
                <div class="service-description-modal">
                    <p>${service.description}</p>
                        </div>
                </div>

                    <!-- Right side: Social network -->
                    <div class="service-network-section">
                        <div class="service-network-large ${service.network}">
                            <i class="${service.networkIcon}"></i>
                            <span>${service.networkName}</span>
                        </div>
                        <div class="service-id">#${service.id}</div>
                        </div>
                    </div>

                <!-- Key parameters in a row -->
                <div class="service-parameters-row">
                    <div class="parameter-item">
                        <div class="parameter-icon">
                            <i class="fas fa-sort-amount-up"></i>
                        </div>
                        <div class="parameter-content">
                            <div class="parameter-label">Количество</div>
                            <div class="parameter-value">${service.minQuantity.toLocaleString()} - ${service.maxQuantity.toLocaleString()}</div>
                        </div>
                    </div>

                    <div class="parameter-item">
                        <div class="parameter-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="parameter-content">
                            <div class="parameter-label">Время выполнения</div>
                            <div class="parameter-value">${service.executionTime}</div>
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
        `;

        $('.services-modal #serviceDetailsContent').html(modalContent);
        $('.services-modal #orderServiceBtn').data('service-id', serviceId);

        // Set service price in footer
        $('.services-modal #servicePriceDisplay').text(`$${service.price.toFixed(2)} за 1000`);

        const modal = new bootstrap.Modal(document.getElementById('serviceDetailsModal'));
        modal.show();
    }

    // Show loading state
    showLoading() {
        $('.services-page #servicesLoading').show();
    }

    // Hide loading state
    hideLoading() {
        $('.services-page #servicesLoading').hide();
    }

    // Show no results state
    showNoResults() {
        $('.services-page #servicesNoResults').show();
    }

    // Hide no results state
    hideNoResults() {
        $('.services-page #servicesNoResults').hide();
    }

    // Redirect to order page
    redirectToOrder(serviceId) {
        const service = this.services.find(s => s.id === serviceId);
        if (!service) return;

        // Close any open modals
        const openModal = bootstrap.Modal.getInstance(document.getElementById('serviceDetailsModal'));
        if (openModal) {
            openModal.hide();
        }

        // Show notification
        if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
            SocnetApp.notifications.showInfo(`Переход к заказу услуги: ${service.name}`);
        }

        // In real application, this would redirect to orders page with pre-filled data
        // For now, just log the action
        console.log('Redirecting to order page with service:', service);

        // Simulate redirect
        setTimeout(() => {
            // window.location.href = `/user/orders?service=${serviceId}`;
            if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
                SocnetApp.notifications.showSuccess('Функция будет доступна после интеграции со страницы заказов');
            }
        }, 500);
    }

    // Initialize social network card selection based on current filters
    initializeSocialNetworkCardSelection() {
        if (this.currentFilters.network && this.currentFilters.network !== 'all') {
            const card = document.querySelector(`.services-page .social-network-card[data-network="${this.currentFilters.network}"]`);
            if (card) {
                card.classList.add('selected');
                console.log('Initialized card selection for:', this.currentFilters.network);
            }
        }
    }

    // Manually update custom select display (fallback method)
    updateCustomSelectDisplayManually(selectElement, value) {
        const wrapper = selectElement.closest('.custom-select-wrapper');
        if (wrapper) {
            // Find the display element
            const display = wrapper.querySelector('.custom-select-display');
            if (display) {
                // Find the selected option
                const selectedOption = selectElement.querySelector(`option[value="${value}"]`);
                if (selectedOption) {
                    // Update display text
                    const displayText = selectedOption.textContent;
                    display.textContent = displayText;

                    // Update selected state in dropdown
                    const options = wrapper.querySelectorAll('.custom-select-option');
                    options.forEach(opt => opt.classList.remove('selected'));
                    const selectedOpt = wrapper.querySelector(`.custom-select-option[data-value="${value}"]`);
                    if (selectedOpt) {
                        selectedOpt.classList.add('selected');
                    }
                }
            }
        }
    }
}

// Global function for clearing filters (used in empty state)
window.clearAllFilters = function () {
    if (window.servicesManager) {
        window.servicesManager.clearFilters();
    }
};

// Global function for showing service details (used in service cards)
window.showServiceDetails = function (serviceId) {
    if (window.servicesManager) {
        window.servicesManager.showServiceDetails(serviceId);
    }
};

// Global function for ordering service (used in service cards)
window.orderService = function (serviceId) {
    if (window.servicesManager) {
        window.servicesManager.redirectToOrder(serviceId);
    }
};

// Initialize when document is ready
$(document).ready(() => {
    window.servicesManager = new ServicesManager();
    console.log('Services page loaded');
});

// Integration with main SocnetApp
if (typeof SocnetApp !== 'undefined') {
    SocnetApp.services = {
        manager: null,
        init() {
            this.manager = new ServicesManager();
            return this.manager;
        }
    };

    console.log('Services module integrated with SocnetApp');
}
