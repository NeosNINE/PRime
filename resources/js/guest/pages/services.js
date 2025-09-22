// Services Page JavaScript

class ServicesManager {
    constructor() {
        this.services = [];
        this.filteredServices = [];
        this.currentPage = 1;
        this.itemsPerPage = 20; // Changed to 20 for better UX
        this.currentView = 'table';
        this.filters = {
            search: '',
            network: 'all',
            category: 'all',
            sort: 'id',
            sortDirection: 'asc' // 'asc' или 'desc' - по умолчанию по возрастанию
        };

        // Pagination component instance
        this.pagination = null;

        // Predefined categories to expose in the Category selector
        this.availableCategories = {
            followers: 'Подписчики',
            likes: 'Лайки',
            views: 'Просмотры',
            comments: 'Комментарии',
            shares: 'Репосты',
            saves: 'Сохранения',
            subscribers: 'Подписчики',
            members: 'Участники',
            retweets: 'Ретвиты',
            plays: 'Воспроизведения',
            monthly_listeners: 'Месячные слушатели',
            boosters: 'Бустеры',
            invites: 'Приглашения',
            activity: 'Активность'
        };

        this.init();
    }

    init() {
        this.loadSampleServices();
        this.setupEventListeners();
        // Initialize category select options based on predefined list
        // Defer to ensure CustomSelect has initialized
        setTimeout(() => this.updateCategorySelectOptions(this.filters.network), 0);
        this.applyFiltersAndSort();
        this.initializePagination();
        this.renderServices();

        // Устанавливаем правильную иконку при инициализации
        this.updateSortDirectionIcon();

        // Initialize social network card selection based on current filters
        this.initializeSocialNetworkCardSelection();

        // Log CustomSelect availability
        console.log('CustomSelect available:', !!window.CustomSelect);
        if (window.CustomSelect) {
            console.log('CustomSelect methods:', Object.keys(window.CustomSelect));
        }
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
                    this.renderServices();
                    this.scrollToTop();
                }
            });
        }
    }

    // Sample services data - In real application this would come from API
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
                description: 'Высококачественные подписчики для Instagram с гарантией безопасности. Реальные пользователи, постепенная накрутка, без блокировок.'
            },
            {
                id: 2,
                name: 'YouTube просмотры [Быстрый старт] [Офферы]',
                category: 'views',
                network: 'youtube',
                min: 1000,
                max: 100000,
                rate: 0.12,
                time: '0-12 часов',
                refill: false,
                description: 'Быстрые просмотры для YouTube видео. Идеально для запуска новых каналов и увеличения охвата.'
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
                description: 'TikTok лайки от реальных пользователей. Медленная скорость для максимальной безопасности аккаунта.'
            },
            {
                id: 4,
                name: 'Telegram подписчики канала [Премиум] [Гарантия]',
                category: 'followers',
                network: 'telegram',
                min: 100,
                max: 50000,
                rate: 1.25,
                time: '0-2 часа',
                refill: true,
                description: 'Премиум подписчики для Telegram каналов. Высокое качество, гарантия результата, быстрая доставка.'
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
                description: 'Безопасные лайки для Facebook постов. Высокое качество, органический рост, без риска блокировки.'
            },
            {
                id: 6,
                name: 'Twitter подписчики [Органические] [Без списаний]',
                category: 'followers',
                network: 'twitter',
                min: 50,
                max: 15000,
                rate: 1.85,
                time: '1-12 часов',
                refill: true,
                description: 'Органические подписчики для Twitter. Реальные пользователи, без списаний, стабильный рост.'
            },
            {
                id: 7,
                name: 'Instagram лайки [Мгновенный старт] [Дешевые]',
                category: 'likes',
                network: 'instagram',
                min: 100,
                max: 10000,
                rate: 0.25,
                time: '0-30 минут',
                refill: false,
                description: 'Мгновенные лайки для Instagram постов. Быстрый старт, доступная цена, идеально для начинающих.'
            },
            {
                id: 8,
                name: 'YouTube подписчики канала [Реальные] [Медленно]',
                category: 'followers',
                network: 'youtube',
                min: 10,
                max: 5000,
                rate: 2.45,
                time: '0-24 часа',
                refill: true,
                description: 'Реальные подписчики для YouTube каналов. Медленная доставка для максимальной безопасности.'
            },
            {
                id: 9,
                name: 'TikTok просмотры [Быстро] [Качественные]',
                category: 'views',
                network: 'tiktok',
                min: 1000,
                max: 500000,
                rate: 0.08,
                time: '0-1 час',
                refill: false,
                description: 'Быстрые просмотры для TikTok видео. Качественные просмотры, мгновенный старт.'
            },
            {
                id: 10,
                name: 'Instagram комментарии [Русские] [Случайные]',
                category: 'comments',
                network: 'instagram',
                min: 5,
                max: 500,
                rate: 8.50,
                time: '0-6 часов',
                refill: false,
                description: 'Русские комментарии для Instagram постов. Случайные комментарии, естественное взаимодействие.'
            },
            {
                id: 11,
                name: 'Spotify месячные слушатели [Премиум] [Гарантия]',
                category: 'monthly_listeners',
                network: 'spotify',
                min: 100,
                max: 10000,
                rate: 3.25,
                time: '0-24 часа',
                refill: true,
                description: 'Премиум месячные слушатели для Spotify треков. Высокое качество, гарантия результата.'
            },
            {
                id: 12,
                name: 'Discord участники сервера [Реальные] [Безопасно]',
                category: 'members',
                network: 'discord',
                min: 50,
                max: 5000,
                rate: 2.75,
                time: '0-12 часов',
                refill: true,
                description: 'Реальные участники для Discord серверов. Безопасное увеличение, стабильный рост.'
            }
        ];

        // Generate more sample services for pagination demo
        for (let i = 13; i <= 1247; i++) {
            const networks = ['instagram', 'youtube', 'tiktok', 'telegram', 'facebook', 'twitter', 'spotify', 'discord'];
            const categories = ['followers', 'likes', 'views', 'comments', 'shares', 'saves', 'subscribers', 'members', 'retweets', 'plays', 'monthly_listeners', 'boosters', 'invites', 'activity'];
            const network = networks[Math.floor(Math.random() * networks.length)];
            const category = categories[Math.floor(Math.random() * categories.length)];

            this.services.push({
                id: i,
                name: `${network.charAt(0).toUpperCase() + network.slice(1)} ${this.getCategoryName(category)} [Качество] [Гарантия]`,
                category: category,
                network: network,
                min: Math.floor(Math.random() * 500) + 10,
                max: Math.floor(Math.random() * 100000) + 1000,
                rate: (Math.random() * 10).toFixed(2),
                time: this.getRandomTime(),
                refill: Math.random() > 0.5,
                description: this.getRandomDescription(network, category)
            });
        }
    }

    getCategoryName(category) {
        const names = {
            followers: 'подписчики',
            likes: 'лайки',
            views: 'просмотры',
            comments: 'комментарии',
            shares: 'репосты',
            saves: 'сохранения',
            subscribers: 'подписчики',
            members: 'участники',
            retweets: 'ретвиты',
            plays: 'воспроизведения',
            monthly_listeners: 'месячные слушатели',
            boosters: 'бустеры',
            invites: 'приглашения',
            activity: 'активность'
        };
        return names[category] || category;
    }

    getRandomTime() {
        const times = ['0-30 минут', '0-1 час', '0-6 часов', '0-12 часов', '0-24 часа', '1-3 дня'];
        return times[Math.floor(Math.random() * times.length)];
    }

    getRandomDescription(network, category) {
        const descriptions = [
            'Высококачественная услуга с гарантией результата. Подходит для быстрого роста показателей.',
            'Безопасный способ увеличения активности. Постепенная накрутка без блокировок.',
            'Премиум услуга с реальными пользователями. Долгосрочный эффект и стабильность.',
            'Экономичный вариант для начинающих. Быстрый старт и хорошее соотношение цена/качество.',
            'Профессиональная накрутка от опытных специалистов. Индивидуальный подход.',
            'Качественная услуга для продвижения в социальных сетях. Гарантированный результат.',
            'Безопасная накрутка с использованием современных технологий. Без риска для аккаунта.',
            'Эффективная услуга для увеличения популярности. Быстрая доставка и отличное качество.'
        ];
        return descriptions[Math.floor(Math.random() * descriptions.length)];
    }

    setupEventListeners() {
        // Social network card selection
        document.querySelectorAll('.social-network-card').forEach(card => {
            card.addEventListener('click', () => {
                // Remove selection from all cards
                document.querySelectorAll('.social-network-card').forEach(c => c.classList.remove('selected'));

                // Add selection to clicked card
                card.classList.add('selected');

                // Update select value using CustomSelect API
                const network = card.dataset.network;
                const networkSelect = document.getElementById('socialNetworkFilter');

                // Update native select value
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

                console.log('Card clicked:', network, 'Select value set to:', networkSelect.value);

                // Trigger change event to ensure all listeners are notified
                const changeEvent = new Event('change', { bubbles: true });
                networkSelect.dispatchEvent(changeEvent);
            });
        });

        // Social network select change
        document.getElementById('socialNetworkFilter').addEventListener('change', (e) => {
            const network = e.target.value;
            console.log('Select changed to:', network);

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

            // Update filters and reapply
            this.filters.network = network;
            this.filters.category = 'all';
            this.updateCategorySelectOptions(network);
            this.currentPage = 1;
            this.applyFiltersAndSort();
            this.updatePagination();
            this.renderServices();
        });

        // Search input with dropdown results
        const searchInput = document.getElementById('servicesSearch');
        const resultsContainer = document.getElementById('servicesSearchResults');

        const renderSearchResults = () => {
            const term = (searchInput.value || '').toLowerCase().trim();
            if (!term || term.length < 2) {
                resultsContainer.classList.remove('show');
                resultsContainer.innerHTML = '';
                return;
            }

            const matches = this.services.filter(service =>
                service.name.toLowerCase().includes(term) ||
                String(service.id).includes(term) ||
                service.network.toLowerCase().includes(term) ||
                service.category.toLowerCase().includes(term)
            ).slice(0, 8);

            if (matches.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="search-result-item">
                        <div class="service-info">
                            <div class="service-name">Услуги не найдены</div>
                        </div>
                    </div>`;
                resultsContainer.classList.add('show');
                return;
            }

            const html = matches.map(s => `
                <div class="search-result-item" data-service-id="${s.id}">
                    <div class="service-id">#${s.id}</div>
                    <div class="service-info">
                        <div class="service-name">${s.name}</div>
                        <div class="service-meta">
                            <span>$${parseFloat(s.rate).toFixed(2)}/1K</span>
                            <span>${(s.min||0).toLocaleString()} - ${(s.max||0).toLocaleString()}</span>
                            <span>${s.time}</span>
                        </div>
                    </div>
                    <div class="service-network ${s.network}">
                        <i class="${this.getNetworkIcon(s.network)}"></i>
                    </div>
                </div>`).join('');

            resultsContainer.innerHTML = html;
            resultsContainer.classList.add('show');
        };

        searchInput.addEventListener('input', this.debounce((e) => {
            this.filters.search = e.target.value.toLowerCase();
            this.currentPage = 1;
            this.applyFiltersAndSort();
            this.updatePagination();
            this.renderServices();
            renderSearchResults();
        }, 300));

        searchInput.addEventListener('focus', () => {
            renderSearchResults();
        });

        // Click on result item
        document.addEventListener('click', (ev) => {
            const item = ev.target.closest('.search-result-item');
            if (item && resultsContainer.contains(item)) {
                const id = parseInt(item.getAttribute('data-service-id'), 10);
                const svc = this.services.find(x => x.id === id);
                if (svc) {
                    searchInput.value = svc.name;
                    this.filters.search = svc.name.toLowerCase();
                    this.currentPage = 1;
                    this.applyFiltersAndSort();
                    this.updatePagination();
                    this.renderServices();
                }
                resultsContainer.classList.remove('show');
            } else if (!ev.target.closest('.search-input-container')) {
                resultsContainer.classList.remove('show');
            }
        });

        // Social network filter change is already handled above

        // Category filter (custom select)
        const categorySelect = document.getElementById('categoryFilter');
        if (categorySelect) {
            categorySelect.addEventListener('change', (e) => {
                this.filters.category = e.target.value;
                this.currentPage = 1;
                this.applyFiltersAndSort();
                this.updatePagination();
                this.renderServices();
            });

            // Fallback for custom-select UI in case native change doesn't propagate
            const catWrapper = categorySelect.nextElementSibling;
            if (catWrapper && catWrapper.classList.contains('custom-select-container')) {
                catWrapper.addEventListener('click', (ev) => {
                    const opt = ev.target.closest('.custom-select-option');
                    if (opt && catWrapper.contains(opt)) {
                        const value = opt.getAttribute('data-value');
                        if (value && this.filters.category !== value) {
                            this.filters.category = value;
                            this.currentPage = 1;
                            this.applyFiltersAndSort();
                            this.updatePagination();
                            this.renderServices();
                        }
                    }
                });
            }
        }

        // Sort filter (custom select)
        document.getElementById('sortFilter').addEventListener('change', (e) => {
            this.filters.sort = e.target.value;
            this.currentPage = 1;
            this.applyFiltersAndSort();
            this.updatePagination();
            this.renderServices();
        });

        // Sort direction button
        document.getElementById('sortDirection').addEventListener('click', () => {
            console.log('Sort direction button clicked. Current:', this.filters.sortDirection);

            this.filters.sortDirection = this.filters.sortDirection === 'asc' ? 'desc' : 'asc';

            console.log('Sort direction changed to:', this.filters.sortDirection);

            this.updateSortDirectionIcon();
            this.currentPage = 1;
            this.applyFiltersAndSort();
            this.updatePagination();
            this.renderServices();
        });

        // Pagination is now handled by the pagination component

        // Clear filters button
        const clearBtn = document.getElementById('clearFilters');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                // Reset filters state
                this.filters.search = '';
                this.filters.network = 'all';
                this.filters.category = 'all';
                this.filters.sort = 'id';
                this.filters.sortDirection = 'asc';

                // Reset inputs
                const searchInputEl = document.getElementById('servicesSearch');
                if (searchInputEl) searchInputEl.value = '';

                const networkEl = document.getElementById('socialNetworkFilter');
                if (networkEl) {
                    networkEl.value = 'all';
                    if (window.CustomSelect && typeof window.CustomSelect.setValue === 'function') {
                        window.CustomSelect.setValue(networkEl, 'all');
                    }
                }

                // Rebuild category options to defaults and select 'all'
                this.updateCategorySelectOptions('all');
                const categoryEl = document.getElementById('categoryFilter');
                if (categoryEl) {
                    categoryEl.value = 'all';
                    if (window.CustomSelect && typeof window.CustomSelect.setValue === 'function') {
                        window.CustomSelect.setValue(categoryEl, 'all');
                    }
                }

                const sortEl = document.getElementById('sortFilter');
                if (sortEl) {
                    sortEl.value = 'id';
                    if (window.CustomSelect && typeof window.CustomSelect.setValue === 'function') {
                        window.CustomSelect.setValue(sortEl, 'id');
                    }
                }

                // Reset sort direction
                this.filters.sortDirection = 'asc';
                this.updateSortDirectionIcon();

                                                // Reset social network card selection
                        document.querySelectorAll('.social-network-card').forEach(c => c.classList.remove('selected'));

                        // Apply and render
                        this.currentPage = 1;
                        this.applyFiltersAndSort();
                        this.updatePagination();
                        this.renderServices();
                        this.scrollToTop();
            });
        }
    }

    applyFiltersAndSort() {
        // Sync filters from DOM (in case a custom-select didn't propagate change)
        const domCategory = document.getElementById('categoryFilter');
        if (domCategory && domCategory.value && this.filters.category !== domCategory.value) {
            this.filters.category = domCategory.value;
        }
        const domNetwork = document.getElementById('socialNetworkFilter');
        if (domNetwork && domNetwork.value && this.filters.network !== domNetwork.value) {
            this.filters.network = domNetwork.value;
        }

        let filtered = [...this.services];

        // Apply search filter
        if (this.filters.search) {
            filtered = filtered.filter(service =>
                service.name.toLowerCase().includes(this.filters.search) ||
                service.category.toLowerCase().includes(this.filters.search) ||
                service.network.toLowerCase().includes(this.filters.search)
            );
        }

        // Apply network filter
        if (this.filters.network !== 'all') {
            filtered = filtered.filter(service => service.network === this.filters.network);
        }

        // Apply category filter
        if (this.filters.category !== 'all') {
            filtered = filtered.filter(service => service.category === this.filters.category);
        }

        // Apply sorting
        this.sortServices(filtered);

        this.filteredServices = filtered;

        // Update services count
        document.getElementById('servicesCount').textContent = filtered.length.toLocaleString();
    }

    // Sort services based on current filters
    sortServices(services) {
        const direction = this.filters.sortDirection === 'asc' ? 1 : -1;

        switch (this.filters.sort) {
            case 'id':
                services.sort((a, b) => {
                    const result = a.id - b.id;
                    return direction === 1 ? result : -result;
                });
                break;
            case 'newest':
                services.sort((a, b) => {
                    const result = a.id - b.id; // Новые услуги имеют больший ID
                    return direction === 1 ? result : -result;
                });
                break;
            case 'price':
                services.sort((a, b) => {
                    const result = parseFloat(a.rate) - parseFloat(b.rate);
                    return direction === 1 ? result : -result;
                });
                break;
            case 'name':
                services.sort((a, b) => {
                    const result = a.name.localeCompare(b.name);
                    return direction === 1 ? result : -result;
                });
                break;
            default:
                services.sort((a, b) => {
                    const result = a.id - b.id;
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
                if (this.filters.sortDirection === 'asc') {
                    icon.className = 'fa-solid fa-arrow-up';
                } else {
                    icon.className = 'fa-solid fa-arrow-down';
                }

                console.log('Icon updated:', this.filters.sortDirection, icon.className);
            }
        }
    }

    // Build category options depending on selected network
    updateCategorySelectOptions(network) {
        const select = document.getElementById('categoryFilter');
        if (!select) return;

        // Clear existing options
        select.innerHTML = '';

        // Always include "all"
        const allOption = document.createElement('option');
        allOption.value = 'all';
        allOption.textContent = 'Все категории';
        allOption.selected = true;
        select.appendChild(allOption);

        // If a specific network selected, derive categories available for that network
        // Always show the full predefined categories list
        Object.keys(this.availableCategories).forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat;
            opt.textContent = this.getCategoryDisplayName(cat);
            select.appendChild(opt);
        });

        // Category is always enabled to allow filtering across all networks
        select.disabled = false;

        // Reset value to 'all' when options rebuilt
        select.value = 'all';

        // Sync custom select UI if initialized
        if (window.CustomSelect && typeof window.CustomSelect.updateOptions === 'function') {
            const newOptions = [{ value: 'all', text: 'Все категории', selected: true }];
            Object.keys(this.availableCategories).forEach(cat => {
                newOptions.push({ value: cat, text: this.getCategoryDisplayName(cat) });
            });
            window.CustomSelect.updateOptions(select, newOptions);
            if (typeof window.CustomSelect.setValue === 'function') {
                window.CustomSelect.setValue(select, 'all');
            }
        }
    }

    renderServices() {
        this.showLoading();

        // Simulate API delay
        setTimeout(() => {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            const pageServices = this.filteredServices.slice(startIndex, endIndex);

            if (pageServices.length === 0) {
                // Hide loading when no results found
                this.hideLoading();
                this.showNoResults();
                return;
            }

            this.renderCards(pageServices);
            this.hideLoading();
            this.hideNoResults();
        }, 300);
    }

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

    getCategoryDisplayName(category) {
        const displayNames = {
            followers: 'Подписчики',
            likes: 'Лайки',
            views: 'Просмотры',
            comments: 'Комментарии',
            shares: 'Репосты',
            saves: 'Сохранения',
            subscribers: 'Подписчики',
            members: 'Участники',
            retweets: 'Ретвиты',
            plays: 'Воспроизведения',
            monthly_listeners: 'Месячные слушатели',
            boosters: 'Бустеры',
            invites: 'Приглашения',
            activity: 'Активность'
        };
        return displayNames[category] || category.charAt(0).toUpperCase() + category.slice(1);
    }

    createServiceCard(service) {
        const networkIcon = this.getNetworkIcon(service.network);

        return `
            <div class="service-card" data-service-id="${service.id}">
                <!-- Первая строка: ID + заголовок слева, цена справа -->
                <div class="service-row-top">
                    <div class="service-main-info">
                        <span class="service-id">#${service.id}</span>
                        <h4 class="service-title">${service.name}</h4>
                    </div>
                    <div class="service-price">$${service.rate} за 1000</div>
                </div>

                <!-- Вторая строка: детали слева, кнопки справа -->
                <div class="service-row-bottom">
                    <div class="service-details">
                        <span class="service-detail-item">
                            <i class="${this.getNetworkIcon(service.network)}"></i>
                            ${this.getNetworkDisplayName(service.network)}
                        </span>
                        <span class="service-detail-item">
                            <i class="${this.getCategoryIcon(service.category)}"></i>
                            ${this.getCategoryDisplayName(service.category)}
                        </span>
                        <span class="service-detail-item">
                            <i class="fas fa-clock"></i>
                            ${service.time}
                        </span>
                        <span class="service-detail-item">
                            <i class="fas fa-layer-group"></i>
                            ${service.min.toLocaleString()} - ${service.max.toLocaleString()}
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
                        <a href="#" class="btn-buy" onclick="return false;">
                            <i class="fas fa-shopping-cart"></i>
                            Купить
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

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

    getNetworkDisplayName(network) {
        const names = {
            instagram: 'Instagram',
            youtube: 'YouTube',
            tiktok: 'TikTok',
            telegram: 'Telegram',
            facebook: 'Facebook',
            twitter: 'Twitter',
            spotify: 'Spotify',
            discord: 'Discord'
        };
        return names[network] || network.charAt(0).toUpperCase() + network.slice(1);
    }

    getCategoryIcon(category) {
        const categoryIcons = {
            followers: 'fas fa-users',
            likes: 'fas fa-heart',
            views: 'fas fa-eye',
            comments: 'fas fa-comment',
            shares: 'fas fa-share-alt',
            saves: 'fas fa-bookmark',
            subscribers: 'fas fa-users',
            members: 'fas fa-user-friends',
            retweets: 'fas fa-retweet',
            plays: 'fas fa-play',
            monthly_listeners: 'fas fa-headphones',
            boosters: 'fas fa-rocket',
            invites: 'fas fa-envelope',
            activity: 'fas fa-chart-line'
        };
        return categoryIcons[category] || 'fas fa-tag';
    }

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

    showLoading() {
        document.getElementById('servicesLoading').style.display = 'block';
        document.getElementById('servicesCardsContainer').style.opacity = '0.5';
    }

    hideLoading() {
        document.getElementById('servicesLoading').style.display = 'none';
        document.getElementById('servicesCardsContainer').style.opacity = '1';
    }

    showNoResults() {
        document.getElementById('servicesNoResults').style.display = 'block';
        document.getElementById('servicesCardsContainer').style.display = 'none';
    }

    hideNoResults() {
        document.getElementById('servicesNoResults').style.display = 'none';
        document.getElementById('servicesCardsContainer').style.display = 'block';
    }

    scrollToTop() {
        document.querySelector('.services-main-section').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialize social network card selection based on current filters
    initializeSocialNetworkCardSelection() {
        if (this.filters.network && this.filters.network !== 'all') {
            const card = document.querySelector(`.social-network-card[data-network="${this.filters.network}"]`);
            if (card) {
                card.classList.add('selected');
                console.log('Initialized card selection for:', this.filters.network);
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

// Global function for showing service details
window.showServiceDetails = function(serviceId) {
    // Find service data
    const servicesManager = window.currentServicesManager;
    if (!servicesManager) return;

    const service = servicesManager.services.find(s => s.id === serviceId);
    if (!service) return;

    // Populate modal content
    const modalContent = `
        <div class="service-details-modal">
            <div class="service-layout">
                <!-- Left side: Service info -->
                <div class="service-info-section">
                    <!-- Category above name -->
                    <div class="service-category">
                        <i class="fas fa-${service.category === 'followers' ? 'users' : service.category === 'likes' ? 'heart' : service.category === 'views' ? 'eye' : service.category === 'comments' ? 'comment' : service.category === 'shares' ? 'share-alt' : 'bookmark'}"></i>
                        <span>${servicesManager.getCategoryDisplayName(service.category)}</span>
                    </div>

                    <!-- Service name -->
                    <h3 class="service-name-modal">${service.name}</h3>

                    <!-- Service description -->
                    <div class="service-description-modal">
                        <p>${service.description || 'Подробное описание услуги будет загружено здесь.'}</p>
                    </div>
                </div>

                <!-- Right side: Social network -->
                <div class="service-network-section">
                    <div class="service-network-large ${service.network}">
                        <i class="fab fa-${service.network === 'instagram' ? 'instagram' : service.network === 'youtube' ? 'youtube' : service.network === 'tiktok' ? 'tiktok' : service.network === 'telegram' ? 'telegram' : service.network === 'facebook' ? 'facebook' : 'twitter'}"></i>
                        <span>${service.network.charAt(0).toUpperCase() + service.network.slice(1)}</span>
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
                        <div class="parameter-value">${service.min.toLocaleString()} - ${service.max.toLocaleString()}</div>
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
    `;

    // Update modal content
    document.getElementById('serviceDetailsContent').innerHTML = modalContent;

    // Update price display
    document.getElementById('servicePriceDisplay').textContent = `$${service.rate} за 1000`;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('serviceDetailsModal'));
    modal.show();
};



// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.currentServicesManager = new ServicesManager();
});

// Smooth animations on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Add animation classes to elements
    const animatedElements = document.querySelectorAll('.service-card, .services-category-section, .services-controls');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});
