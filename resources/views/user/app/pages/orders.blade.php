@section('title', 'Создать заказ - SOCNET SMM')
@extends('user.app.layout')

@section('content')
    <div class="orders-page">
        <!-- Statistics Section -->
        <div class="orders-stats-section">
            <h1 class="orders-title">{{ __('Моя статистика') }}</h1>

            <div class="stats-grid">
                <!-- Balance Card -->
                <div class="stat-card balance-card">
                    <div class="stat-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('Текущий баланс') }}</div>
                        <div class="stat-value" data-currency="usd">${{ $balance ?? '1,250.00' }}</div>
                        <a href="{{ route('user.balance-topup') }}" class="stat-action">
                            <i class="fas fa-coins"></i>
                            {{ __('Пополнить баланс') }}
                        </a>
                    </div>
                </div>

                <!-- Spent Amount Card -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('Потрачено всего') }}</div>
                        <div class="stat-value">${{ $totalSpent ?? '5,000.00' }}</div>
                    </div>
                </div>

                <!-- Completed Orders Card -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('Выполнено заказов') }}</div>
                        <div class="stat-value">{{ $completedOrders ?? '150' }}</div>
                    </div>
                </div>

                <!-- Discount Card -->
                <div class="stat-card discount-card">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('Текущая скидка') }}</div>
                        <div class="stat-value">Начинающий ({{ $discountPercent ?? '5' }}%)</div>
                        <div class="stat-progress">
                            <div class="progress-info">{{ __('До следующего уровня: $2,000') }}</div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $discountProgress ?? '60' }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="date-filter-section">
                <div class="date-filter">
                    <label for="dateRange">{{ __('Период:') }}</label>
                    <div class="date-input-wrapper">
                        <input type="text" id="dateRange" class="date-range-input"
                            placeholder="{{ __('Выберите период') }}" autocomplete="off" readonly>
                        <i class="fas fa-calendar-alt date-icon"></i>
                    </div>
                    <button class="filter-btn" onclick="applyDateFilter()">
                        <i class="fas fa-filter"></i>
                        {{ __('Применить') }}
                    </button>
                    <button class="clear-btn" onclick="clearDateFilter()">
                        <i class="fas fa-times"></i>
                        {{ __('Очистить') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Order Creation Section -->
        <div class="order-creation-section">
            <div class="order-tabs">
                <button class="tab-btn active" data-tab="single">
                    <i class="fas fa-plus"></i>
                    {{ __('Одиночный заказ') }}
                </button>
                <button class="tab-btn" data-tab="mass">
                    <i class="fas fa-list"></i>
                    {{ __('Массовый заказ') }}
                </button>
            </div>

            <!-- Single Order Tab -->
            <div class="tab-content active" id="singleOrderTab">
                <div class="order-form-container">
                    <div class="order-form">
                        <!-- Social Network Selection -->
                        <div class="form-group">
                            <div class="social-networks-grid">
                                <div class="social-network-card" data-network="instagram">
                                    <div class="network-icon">
                                        <i class="fab fa-instagram"></i>
                                    </div>
                                    <div class="network-name">Instagram</div>
                                </div>
                                <div class="social-network-card" data-network="youtube">
                                    <div class="network-icon">
                                        <i class="fab fa-youtube"></i>
                                    </div>
                                    <div class="network-name">YouTube</div>
                                </div>
                                <div class="social-network-card" data-network="tiktok">
                                    <div class="network-icon">
                                        <i class="fab fa-tiktok"></i>
                                    </div>
                                    <div class="network-name">TikTok</div>
                                </div>
                                <div class="social-network-card" data-network="telegram">
                                    <div class="network-icon">
                                        <i class="fab fa-telegram"></i>
                                    </div>
                                    <div class="network-name">Telegram</div>
                                </div>
                                <div class="social-network-card" data-network="facebook">
                                    <div class="network-icon">
                                        <i class="fab fa-facebook"></i>
                                    </div>
                                    <div class="network-name">Facebook</div>
                                </div>
                                <div class="social-network-card" data-network="twitter">
                                    <div class="network-icon">
                                        <i class="fab fa-twitter"></i>
                                    </div>
                                    <div class="network-name">Twitter</div>
                                </div>
                                <div class="social-network-card" data-network="spotify">
                                    <div class="network-icon">
                                        <i class="fab fa-spotify"></i>
                                    </div>
                                    <div class="network-name">Spotify</div>
                                </div>
                                <div class="social-network-card" data-network="discord">
                                    <div class="network-icon">
                                        <i class="fab fa-discord"></i>
                                    </div>
                                    <div class="network-name">Discord</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Social Network Select -->
                            <div class="form-group">
                                <label>{{ __('Социальная сеть') }}</label>
                                <select class="form-control custom-select" id="socialNetworkSelect"
                                    data-placeholder="{{ __('Выберите социальную сеть') }}">
                                    <option value="all" data-icon="fas fa-globe">{{ __('Все сети') }}</option>
                                    <option value="instagram" data-icon="fab fa-instagram">Instagram</option>
                                    <option value="youtube" data-icon="fab fa-youtube">YouTube</option>
                                    <option value="tiktok" data-icon="fab fa-tiktok">TikTok</option>
                                    <option value="telegram" data-icon="fab fa-telegram">Telegram</option>
                                    <option value="facebook" data-icon="fab fa-facebook">Facebook</option>
                                    <option value="twitter" data-icon="fab fa-twitter">Twitter</option>
                                    <option value="spotify" data-icon="fab fa-spotify">Spotify</option>
                                    <option value="discord" data-icon="fab fa-discord">Discord</option>
                                </select>
                            </div>

                            <!-- Category Select -->
                            <div class="form-group">
                                <label>{{ __('Категория') }}</label>
                                <select class="form-control custom-select" id="categorySelect"
                                    data-placeholder="{{ __('Выберите категорию') }}">
                                    <option value="all" data-icon="fas fa-list">{{ __('Все категории') }}</option>
                                    <option value="followers" data-icon="fas fa-users">{{ __('Подписчики') }}</option>
                                    <option value="likes" data-icon="fas fa-heart">{{ __('Лайки') }}</option>
                                    <option value="views" data-icon="fas fa-eye">{{ __('Просмотры') }}</option>
                                    <option value="comments" data-icon="fas fa-comment">{{ __('Комментарии') }}</option>
                                    <option value="shares" data-icon="fas fa-share-alt">{{ __('Репосты') }}</option>
                                    <option value="saves" data-icon="fas fa-bookmark">{{ __('Сохранения') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Service Search -->
                        <div class="form-group">
                            <label for="serviceSearch">{{ __('Поиск услуги') }}</label>
                            <div class="search-container">
                                <div class="search-toggle">
                                    <input type="text" id="serviceSearch" class="form-input"
                                        placeholder="{{ __('Поиск по названию или ID услуги…') }}"
                                        oninput="searchServices()">
                                    <div class="search-button">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </div>
                                <div class="search-results" id="searchResults"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- URL Input -->
                            <div class="form-group">
                                <label for="orderUrl">{{ __('Ссылка') }}</label>
                                <input type="url" id="orderUrl" class="form-input"
                                    placeholder="{{ __('https://instagram.com/p/...') }}" required>
                            </div>

                            <!-- Quantity Input -->
                            <div class="form-group">
                                <label for="quantity">{{ __('Количество') }}</label>
                                <input type="number" id="quantity" class="form-input"
                                    placeholder="{{ __('1000') }}" min="1" oninput="calculateCost()" required>
                                <div class="quantity-limits" id="quantityLimits"></div>
                            </div>
                        </div>

                        <!-- Drip-feed Option -->
                        <div class="drip-feed-section">
                            <div class="drip-feed-toggle">
                                <input type="checkbox" id="dripFeedEnabled" onchange="toggleDripFeed()">
                                <label for="dripFeedEnabled">{{ __('Включить Drip-feed') }}</label>
                                <span class="drip-feed-info" data-bs-toggle="popover" data-bs-trigger="hover"
                                    data-bs-html="true"
                                    data-bs-content="<b>DripFeed</b> — это специальный режим, при котором лайки, подписчики, просмотры и другие метрики доставляются не мгновенно, а небольшими порциями с заданной периодичностью. Такой «растянутый» график имитирует естественный рост активности и выглядит безопаснее для алгоритмов соцсетей.<br><br>Вам необходимо указать минимальный интервал между запусками и количество запусков.<br><br>Пример:<br>Вы указали общее количество 1 000 лайков, 10 запусков, интервал 60 минут, значит, каждые 60 минут система добавляет по 100 лайков, пока не выполнит все 10 циклов.">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            </div>

                            <div class="drip-feed-options" id="dripFeedOptions" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="dripRuns">{{ __('Количество запусков') }}</label>
                                        <input type="number" id="dripRuns" class="form-input" placeholder="5"
                                            min="2" max="100">
                                    </div>
                                    <div class="form-group">
                                        <label for="dripInterval">{{ __('Интервал (минуты)') }}</label>
                                        <input type="number" id="dripInterval" class="form-input" placeholder="60"
                                            min="30">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Cost and Submit -->
                        <div class="order-summary">
                            <div class="cost-display">
                                <span class="cost-label">{{ __('Стоимость:') }}</span>
                                <span class="cost-value" id="orderCost">$0.00</span>
                            </div>
                            <button type="button" class="order-btn" id="submitSingleOrder"
                                onclick="submitSingleOrder()" disabled>
                                <i class="fas fa-shopping-cart"></i>
                                {{ __('Купить') }}
                            </button>
                        </div>
                    </div>

                    <!-- Service Info Panel -->
                    <div class="service-info-panel" id="serviceInfoPanel" style="display: none;">
                        <div class="service-details" id="serviceDetails">
                            <!-- Service info will be populated here -->
                        </div>
                    </div>
                    <!-- Service details will be populated here by JavaScript -->
                </div>
            </div>

            <!-- Mass Order Tab -->
            <div class="tab-content" id="massOrderTab">
                <div class="mass-order-container">
                    <div class="mass-order-input">
                        <label for="massOrderData">{{ __('Массовый ввод заказов') }}</label>
                        <textarea id="massOrderData" class="mass-textarea"
                            placeholder="{{ __('Формат: ID услуги | ссылка | количество
Пример:
1 | https://instagram.com/p/example1 | 1000
2 | https://youtube.com/watch?v=example2 | 5000') }}"
                            oninput="parseMassOrders()"></textarea>
                    </div>

                    <div class="mass-order-form">
                        <h3>{{ __('Добавить заказ') }}</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="massServiceSearch">{{ __('Поиск услуги') }}</label>
                                <div class="search-container">
                                    <div class="search-toggle">
                                        <input type="text" id="massServiceSearch" class="form-input"
                                            placeholder="{{ __('Поиск по названию или ID услуги…') }}"
                                            oninput="searchMassServices()">
                                        <div class="search-button">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </div>
                                    <div class="search-results" id="massSearchResults"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="massUrl">{{ __('Ссылка') }}</label>
                                <input type="url" id="massUrl" class="form-input"
                                    placeholder="{{ __('https://...') }}">
                            </div>
                            <div class="form-group">
                                <label for="massQuantity">{{ __('Количество') }}</label>
                                <input type="number" id="massQuantity" class="form-input" placeholder="1000"
                                    min="1">
                            </div>
                            <div class="form-group">
                                <label class="_hidden">add</label>
                                <button type="button" class="add-btn" onclick="addMassOrder()">
                                    <i class="fas fa-plus"></i>
                                    {{ __('Добавить') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mass Orders List -->
                    <div class="mass-orders-list">
                        <h3>{{ __('Список заказов') }}</h3>
                        <div class="orders-table-container">
                            <table class="orders-table" id="massOrdersTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('Услуга') }}</th>
                                        <th>{{ __('Ссылка') }}</th>
                                        <th>{{ __('Количество') }}</th>
                                        <th>{{ __('Стоимость') }}</th>
                                        <th>{{ __('Действия') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="massOrdersBody">
                                    <tr class="empty-row">
                                        <td colspan="6">{{ __('Список заказов пуст') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mass-order-summary">
                            <div class="total-cost">
                                <span class="total-label">{{ __('Общая стоимость:') }}</span>
                                <span class="total-value" id="totalMassCost">$0.00</span>
                            </div>
                            <button type="button" class="order-btn mass-order-btn" id="submitMassOrders"
                                onclick="submitMassOrders()" disabled>
                                <i class="fas fa-list-check"></i>
                                {{ __('Купить все') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @push('js')
        <script type="text/javascript" src="{{ mix('assets/user/js/pages/orders.js') }}"></script>
    @endpush

@endsection
