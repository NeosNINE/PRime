@section('title', 'Услуги - SOCNET SMM')
@extends('user.app.layout')

@section('content')
    <div class="services-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ __('Услуги') }}</h1>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters-section">
            <!-- Social Networks Cards -->
            <div class="social-networks-section">
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

            <div class="search-container">
                <div class="search-toggle">
                    <input type="text" id="serviceSearch" class="search-input"
                        placeholder="{{ __('Поиск по ID или названию услуги...') }}">
                    <div class="search-button">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="search-results" id="searchResults"></div>
            </div>

            <div class="filters-container">
                <!-- Social Network Filter -->
                <div class="filter-group">
                    <label class="filter-label">{{ __('Социальная сеть') }}</label>
                    <div class="custom-select-wrapper">
                        <select class="custom-select" id="socialNetworkFilter" data-placeholder="Выберите сеть">
                            <option value="all" data-icon="fas fa-globe" selected>Все</option>
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
                </div>

                <!-- Category Filter -->
                <div class="filter-group">
                    <label for="categoryFilter" class="filter-label">{{ __('Категория') }}</label>
                    <div class="custom-select-wrapper">
                        <select id="categoryFilter" class="custom-select">
                            <option value="all">{{ __('Все категории') }}</option>
                            <option value="likes">{{ __('Лайки') }}</option>
                            <option value="followers">{{ __('Подписчики') }}</option>
                            <option value="views">{{ __('Просмотры') }}</option>
                            <option value="comments">{{ __('Комментарии') }}</option>
                            <option value="shares">{{ __('Репосты') }}</option>
                            <option value="saves">{{ __('Сохранения') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Sort Filter -->
                <div class="filter-group">
                    <label for="sortFilter" class="filter-label">{{ __('Сортировка') }}</label>
                    <div class="sort-controls">
                        <div class="custom-select-wrapper">
                            <select id="sortFilter" class="custom-select">
                                <option value="id">{{ __('По ID услуги') }}</option>
                                <option value="newest">{{ __('По новизне') }}</option>
                                <option value="price">{{ __('По цене услуги') }}</option>
                                <option value="name">{{ __('По названию услуги') }}</option>
                            </select>
                        </div>
                        <button type="button" id="sortDirection" class="sort-direction-btn" title="{{ __('Изменить направление сортировки') }}">
                            <i class="fa-solid fa-arrow-up"></i>
                        </button>
                    </div>
                </div>

                <!-- Clear Filters -->
                <div class="filter-group">
                    <button type="button" class="clear-filters-btn" id="clearFilters">
                        <i class="fas fa-times"></i>
                        {{ __('Очистить') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Services Cards -->
        <div class="services-cards-section">
            <div class="services-info-bar">
                <div class="services-count">
                    {{ __('Найдено:') }} <span id="servicesCount">0</span> {{ __('услуг') }}
                </div>
            </div>

            <!-- Services Cards Container -->
            <div class="services-cards-container" id="servicesCardsContainer">
                <!-- Service categories will be loaded here by JavaScript -->
            </div>

            <!-- Loading State -->
            <div class="services-loading" id="servicesLoading" style="display: none;">
                <div class="loading-spinner"></div>
                <p>{{ __('Загрузка услуг...') }}</p>
            </div>

            <!-- No Results -->
            <div class="services-no-results" id="servicesNoResults" style="display: none;">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>{{ __('Услуги не найдены') }}</h3>
                <p>{{ __('Попробуйте изменить параметры поиска или фильтры') }}</p>
                <button class="btn btn-primary" onclick="clearAllFilters()">
                    <i class="fas fa-refresh"></i>
                    {{ __('Сбросить фильтры') }}
                </button>
            </div>
        </div>

        <!-- Pagination -->
        <!-- Pagination for Services -->
        <div class="pagination-container" id="servicesPagination">
            <!-- Pagination component will be rendered here -->
        </div>
    </div>

@endsection

@section('footer')
    <!-- Service Details Modal -->
    <div class="modal fade services-modal" id="serviceDetailsModal" tabindex="-1"
        aria-labelledby="serviceDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceDetailsModalLabel">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Описание услуги') }}
                    </h5>
                </div>
                <div class="modal-body" id="serviceDetailsContent">
                    <!-- Service details will be populated here -->
                </div>
                <div class="modal-footer">
                    <div class="service-price-section">
                        <div class="price-info">
                            <span class="price-label">{{ __('Стоимость:') }}</span>
                            <span class="price-value" id="servicePriceDisplay">$0.00</span>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                            {{ __('Закрыть') }}
                        </button>
                        <button type="button" class="btn btn-primary" id="orderServiceBtn">
                            <i class="fas fa-shopping-cart"></i>
                            {{ __('Купить') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ mix('assets/user/js/pages/services.js') }}"></script>
@endpush
