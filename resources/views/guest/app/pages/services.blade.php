@extends('guest.app.layout')

@section('title', 'Услуги - SOCNET SMM')
@section('description',
    'Полный каталог SMM услуг для продвижения в социальных сетях. Подписчики, лайки, просмотры и
    многое другое.')

@section('content')
    <!-- Hero Section -->
    <section class="services-hero-section">
        <!-- Decorative Background Icons -->
        <div class="services-bg-icons">
            <i class="fas fa-heart services-bg-icon services-icon-1"></i>
            <i class="fas fa-share services-bg-icon services-icon-2"></i>
            <i class="fas fa-thumbs-up services-bg-icon services-icon-3"></i>
            <i class="fas fa-eye services-bg-icon services-icon-4"></i>
            <i class="fas fa-users services-bg-icon services-icon-5"></i>
            <i class="fas fa-chart-line services-bg-icon services-icon-6"></i>
            <i class="fas fa-rocket services-bg-icon services-icon-7"></i>
            <i class="fas fa-star services-bg-icon services-icon-8"></i>
        </div>

        <div class="container">
            <div class="services-hero-content">
                <h1 class="services-hero-title">Услуги SOCNET SMM</h1>
                <p class="services-hero-description">
                    Ознакомьтесь с полным каталогом наших сервисов и услуг для продвижения в ваших социальных сетях.
                    Выберите нужную услугу из более чем 1000+ доступных вариантов.
                </p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-main-section">
        <div class="container">
            <!-- Search and Filters -->
            <div class="services-controls">
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

                <!-- Search Bar -->
                <div class="services-search">
                    <div class="search-input-container">
                        <input type="text" id="servicesSearch" placeholder="Поиск по названию услуги..."
                            class="search-input">
                        <button class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                        <div class="search-results" id="servicesSearchResults"></div>
                    </div>
                </div>

                <!-- Filters and Sort -->
                <div class="services-filters">
                    <div class="services-filters_box">
                        <!-- Social Networks Filter -->
                        <div class="filter-group">
                            <label class="filter-label">Социальная сеть</label>
                            <div class="custom-select-wrapper network-select">
                                <select class="custom-select" id="socialNetworkFilter" data-placeholder="Выберите сеть">
                                    <option value="all" data-icon="fas fa-globe">Все</option>
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

                        <!-- Category Filter (depends on selected network) -->
                        <div class="filter-group">
                            <label class="filter-label">Категория</label>
                            <div class="custom-select-wrapper category-select">
                                <select class="custom-select" id="categoryFilter" data-placeholder="Категория">
                                    <option value="all">Все категории</option>
                                    <option value="followers" data-icon="fas fa-user-plus">Подписчики</option>
                                    <option value="likes" data-icon="fas fa-heart">Лайки</option>
                                    <option value="views" data-icon="fas fa-eye">Просмотры</option>
                                    <option value="comments" data-icon="fas fa-comment">Комментарии</option>
                                    <option value="shares" data-icon="fas fa-share">Репосты</option>
                                    <option value="saves" data-icon="fas fa-bookmark">Сохранения</option>
                                    <option value="subscribers" data-icon="fas fa-users">Подписчики</option>
                                    <option value="members" data-icon="fas fa-user-friends">Участники</option>
                                    <option value="retweets" data-icon="fas fa-retweet">Ретвиты</option>
                                    <option value="plays" data-icon="fas fa-play">Воспроизведения</option>
                                    <option value="monthly_listeners" data-icon="fas fa-headphones">Месячные слушатели</option>
                                    <option value="boosters" data-icon="fas fa-rocket">Бустеры</option>
                                    <option value="invites" data-icon="fas fa-envelope">Приглашения</option>
                                    <option value="activity" data-icon="fas fa-chart-line">Активность</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="filter-group">
                        <label class="filter-label">Сортировка</label>
                        <div class="sort-controls">
                            <div class="custom-select-wrapper">
                                <select class="custom-select" id="sortFilter" data-placeholder="Сортировка">
                                    <option value="id">По ID услуги</option>
                                    <option value="newest">По новизне</option>
                                    <option value="price">По цене услуги</option>
                                    <option value="name">По названию услуги</option>
                                </select>
                            </div>
                            <button type="button" id="sortDirection" class="sort-direction-btn" title="Изменить направление сортировки">
                                <i class="fa-solid fa-arrow-up"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="services-reset">
                    <button type="button" class="clear-filters-btn" id="clearFilters">
                        <i class="fas fa-times"></i>
                        Очистить
                    </button>
                </div>
            </div>

            <!-- Services Count and View Toggle -->
            <div class="services-info-bar">
                <div class="services-count">
                    Найдено: <span id="servicesCount">1,247</span> услуг
                </div>
            </div>

            <!-- Services Cards -->
            <div class="services-cards-container" id="servicesCardsContainer">
                <!-- Service categories will be loaded here by JavaScript -->
            </div>

            <!-- Loading Spinner -->
            <div class="services-loading" id="servicesLoading" style="display: none;">
                <div class="loading-spinner"></div>
                <p>Загрузка услуг...</p>
            </div>

            <!-- No Results -->
            <div class="services-no-results" id="servicesNoResults" style="display: none;">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Услуги не найдены</h3>
                <p>Попробуйте изменить параметры поиска или фильтры</p>
            </div>

            <!-- Pagination -->
            <div class="pagiation-container" id="servicesPagination">
                <!-- Pagination component will be rendered here -->
            </div>
        </div>

        <!-- Background Services Main Icons -->
        <div class="services-main-bg-icons">
            <div class="services-main-bg-icon services-main-icon-1"><i class="fas fa-rocket"></i></div>
            <div class="services-main-bg-icon services-main-icon-2"><i class="fas fa-chart-line"></i></div>
            <div class="services-main-bg-icon services-main-icon-3"><i class="fas fa-users"></i></div>
            <div class="services-main-bg-icon services-main-icon-4"><i class="fas fa-heart"></i></div>
            <div class="services-main-bg-icon services-main-icon-5"><i class="fas fa-eye"></i></div>
            <div class="services-main-bg-icon services-main-icon-6"><i class="fas fa-comment"></i></div>
            <div class="services-main-bg-icon services-main-icon-7"><i class="fas fa-share"></i></div>
            <div class="services-main-bg-icon services-main-icon-8"><i class="fas fa-bullhorn"></i></div>
            <div class="services-main-bg-icon services-main-icon-9"><i class="fas fa-target"></i></div>
            <div class="services-main-bg-icon services-main-icon-10"><i class="fas fa-trending-up"></i></div>
        </div>
    </section>

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

    @guest
        @include('guest.app.components.cta-guest')
    @endguest

@endsection

@section('scripts')
    <script type="text/javascript" src="{{ mix('assets/guest/js/pages/services.js') }}"></script>
@endsection
