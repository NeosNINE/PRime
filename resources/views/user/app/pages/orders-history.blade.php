@section('title', 'История заказов - SOCNET SMM')
@extends('user.app.layout')

@section('content')
    <div class="orders-history-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ __('История заказов') }}</h1>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-container">
                <!-- Status Filter -->
                <div class="filter-group">
                    <label for="statusFilter" class="filter-label">{{ __('Статус') }}</label>
                    <div class="custom-select-wrapper">
                        <select id="statusFilter" class="custom-select">
                            <option value="all">{{ __('Все статусы') }}</option>
                            <option value="pending">{{ __('В ожидании') }}</option>
                            <option value="in_progress">{{ __('В процессе') }}</option>
                            <option value="completed">{{ __('Завершено') }}</option>
                            <option value="cancelled">{{ __('Отменено') }}</option>
                            <option value="partial">{{ __('Частично') }}</option>
                        </select>
                    </div>

                </div>

                <!-- Sort by Date -->
                <div class="filter-group">
                    <label for="sortDate" class="filter-label">{{ __('Сортировка') }}</label>
                    <div class="sort-controls">
                        <div class="custom-select-wrapper">
                            <select id="sortDate" class="custom-select">
                                <option value="date">{{ __('По дате заказа') }}</option>
                                <option value="id">{{ __('По ID заказа') }}</option>
                                <option value="price">{{ __('По стоимости') }}</option>
                                <option value="status">{{ __('По статусу') }}</option>
                            </select>
                        </div>
                        <button type="button" id="sortDirection" class="sort-direction-btn" title="{{ __('Изменить направление сортировки') }}">
                            <i class="fa-solid fa-arrow-up"></i>
                        </button>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div class="filter-group date-range">
                    <label class="filter-label">{{ __('Период') }}</label>
                    <div class="date-inputs">
                        <div class="date-input-wrapper">
                            <input type="text" id="dateRange" class="filter-input"
                                placeholder="{{ __('Выберите период') }}" autocomplete="off" readonly>
                            <i class="fas fa-calendar-alt date-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Apply Filter Button -->
                <div class="filter-group">
                    <button id="applyFilters" class="btn btn-primary filter-btn">
                        <i class="fas fa-filter"></i>
                        {{ __('Применить') }}
                    </button>
                </div>

                <!-- Clear All Filters Button -->
                <div class="filter-group">
                    <button id="clearAllFilters" class="clear-btn">
                        <i class="fas fa-times"></i>
                        {{ __('Очистить') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="orders-table-section">
            <div class="table-container">
                <table class="orders-history-table" id="ordersTable">
                    <thead>
                        <tr>
                            <th class="col-id">{{ __('ID') }}</th>
                            <th class="col-service">{{ __('Услуга') }}</th>
                            <th class="col-link">{{ __('Ссылка') }}</th>
                            <th class="col-quantity">{{ __('Количество') }}</th>
                            <th class="col-status">{{ __('Статус') }}</th>
                            <th class="col-amount">{{ __('Сумма') }}</th>
                            <th class="col-actions">{{ __('Действия') }}</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <!-- Будет создан JavaScript -->
                    </tbody>
                </table>

                <!-- Empty State -->
                <div class="empty-state" id="emptyState" style="display: none;">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3 class="empty-title">{{ __('Заказы не найдены') }}</h3>
                    <p class="empty-description">
                        {{ __('По выбранным фильтрам заказы не найдены. Попробуйте изменить критерии поиска.') }}</p>
                    <a href="{{ route('user.orders') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ __('Создать заказ') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Global Pagination Component -->
        <div class="pagination-container" id="ordersPagination"></div>
    </div>

@endsection

@section('footer')
    <!-- Order Details Modal -->
    <div class="modal fade orders-history-modal" id="orderDetailsModal" tabindex="-1"
        aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Детали заказа') }} <span id="modalOrderId">#12345</span>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="order-details-grid">
                        <div class="detail-group">
                            <label class="detail-label">{{ __('ID заказа') }}</label>
                            <div class="detail-value">
                                <span id="detailOrderId">#12345</span>
                                <button class="copy-btn" data-copy-target="detailOrderId" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <div class="detail-group">
                            <label class="detail-label">{{ __('Услуга') }}</label>
                            <div class="detail-value">
                                <span id="detailService">Instagram Likes</span>
                            </div>
                        </div>

                                                 <div class="detail-group">
                             <label class="detail-label">{{ __('Ссылка') }}</label>
                             <div class="detail-value link-value">
                                 <a id="detailLink" href="#" target="_blank">
                                     <i class="fas fa-external-link-alt"></i>
                                     <span class="link-text">https://instagram.com/p/example</span>
                                 </a>
                                 <button class="copy-btn" data-copy-target="detailLink" data-bs-toggle="tooltip"
                                     data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                                     <i class="fas fa-copy"></i>
                                 </button>
                             </div>
                         </div>

                        <div class="detail-group">
                            <label class="detail-label">{{ __('Количество') }}</label>
                            <div class="detail-value">
                                <span id="detailQuantity">1,000</span>
                            </div>
                        </div>

                        <div class="detail-group">
                            <label class="detail-label">{{ __('Стоимость') }}</label>
                            <div class="detail-value">
                                <span id="detailAmount">$12.50</span>
                            </div>
                        </div>

                        <div class="detail-group">
                            <label class="detail-label">{{ __('Статус') }}</label>
                            <div class="detail-value">
                                <span id="detailStatus"
                                    class="status-badge status-completed">{{ __('Завершено') }}</span>
                            </div>
                        </div>

                        <div class="detail-group">
                            <label class="detail-label">{{ __('Дата создания') }}</label>
                            <div class="detail-value">
                                <span id="detailDate">30.01.2025 14:25</span>
                            </div>
                        </div>

                        <div class="detail-group">
                            <label class="detail-label">{{ __('Дата завершения') }}</label>
                            <div class="detail-value">
                                <span id="detailCompletedDate">30.01.2025 16:45</span>
                            </div>
                        </div>

                        <!-- Drip Feed Info (если есть) -->
                        <div class="detail-group drip-feed-info" id="dripFeedInfo" style="display: none;">
                            <label class="detail-label">{{ __('Drip Feed') }}</label>
                            <div class="detail-value">
                                <div class="drip-details">
                                    <span class="drip-runs">{{ __('Количество запусков') }}: <strong
                                            id="detailDripRuns">5</strong></span>
                                    <span class="drip-interval">{{ __('Интервал') }}: <strong id="detailDripInterval">60
                                            минут</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="detail-group full-width">
                            <label class="detail-label">{{ __('Примечания') }}</label>
                            <div class="detail-value">
                                <span id="detailNotes"
                                    class="text-muted">{{ __('Нет дополнительных примечаний') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="modalRepeatBtn" href="#" class="btn btn-primary">
                        <i class="fas fa-redo"></i>
                        {{ __('Повторить заказ') }}
                    </a>
                    <a id="modalSpeedUpBtn" href="#" class="btn btn-primary">
                        <i class="fas fa-rocket"></i>
                        {{ __('Ускорить') }}
                    </a>
                    <a id="modalReportBtn" href="#" class="btn btn-outline">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ __('Сообщить о проблеме') }}
                    </a>
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                        {{ __('Закрыть') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Confirmation Modal -->
    <div class="modal fade orders-history-modal" id="cancelOrderModal" tabindex="-1"
        aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        {{ __('Отмена заказа') }}
                    </h5>
                </div>
                <div class="modal-body">
                    <p>{{ __('Вы действительно хотите отменить заказ') }} <strong id="cancelOrderId">#12343</strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Заказы можно отменить только в течение 2 минут после создания.') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                        {{ __('Отмена') }}
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmCancelOrder">
                        <i class="fas fa-times"></i>
                        {{ __('Отменить заказ') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ mix('assets/user/js/pages/orders-history.js') }}"></script>
@endpush
