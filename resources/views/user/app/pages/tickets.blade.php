@section('title', 'Поддержка - SOCNET SMM')
@extends('user.app.layout')

@section('content')
    <div class="tickets-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">{{ __('Поддержка') }}</h1>
            </div>
            <div class="header-actions">
                <button type="button" class="btn btn-primary" id="createTicketBtn">
                    <i class="fas fa-plus"></i>
                    {{ __('Создать тикет') }}
                </button>
            </div>
        </div>

        <!-- Tickets List Section -->
        <div class="tickets-list-section" id="ticketsListSection">
            <!-- Search and Filters -->
            <div class="search-filters-section">
                <div class="search-container">
                    <div class="search-input-toggle">
                        <input type="text" id="ticketSearch" class="search-input"
                            placeholder="{{ __('Поиск по номеру тикета или сообщению') }}">
                        <div class="search-button">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div class="search-results" id="ticketSearchResults"></div>
                </div>

                <div class="filters-container">
                    <div class="filter-group">
                        <label for="statusFilter" class="filter-label">{{ __('Статус') }}</label>
                        <div class="custom-select-wrapper">
                            <select id="statusFilter" class="custom-select" data-placeholder="Выберите статус">
                                <option value="all">{{ __('Все статусы') }}</option>
                                <option value="open">{{ __('Открыт') }}</option>
                                <option value="answered">{{ __('Отвечен') }}</option>
                                <option value="closed">{{ __('Закрыт') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="categoryFilter" class="filter-label">{{ __('Категория') }}</label>
                        <div class="custom-select-wrapper">
                            <select id="categoryFilter" class="custom-select" data-placeholder="Выберите категорию">
                                <option value="all">{{ __('Все категории') }}</option>
                                <option value="order">{{ __('Проблема с заказом') }}</option>
                                <option value="payment">{{ __('Проблема с платежом') }}</option>
                                <option value="general">{{ __('Общий вопрос') }}</option>
                                <option value="technical">{{ __('Техническая проблема') }}</option>
                            </select>
                        </div>
                    </div>

                    <button type="button" class="clear-filters-btn" id="clearTicketFilters">
                        <i class="fas fa-times"></i>
                        {{ __('Очистить') }}
                    </button>
                </div>
            </div>

            <!-- Tickets Stats -->
            <div class="tickets-stats">
                <span class="tickets-count">{{ __('Найдено тикетов:') }} <strong id="ticketsCount">0</strong></span>
            </div>

            <!-- Tickets Table -->
            <div class="table-container">
                <table class="tickets-table" id="ticketsTable">
                    <thead>
                        <tr>
                            <th class="col-ticket-id">{{ __('№ Тикета') }}</th>
                            <th class="col-category">{{ __('Категория') }}</th>
                            <th class="col-subject">{{ __('Тема') }}</th>
                            <th class="col-status">{{ __('Статус') }}</th>
                            <th class="col-last-reply">{{ __('Последний ответ') }}</th>
                            <th class="col-date">{{ __('Дата создания') }}</th>
                            <th class="col-actions">{{ __('Действия') }}</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTableBody">
                        <!-- Tickets will be loaded here by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div class="empty-state" id="ticketsEmptyState" style="display: none;">
                <div class="empty-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="empty-title">{{ __('Тикеты не найдены') }}</h3>
                <p class="empty-description">{{ __('У вас пока нет обращений в поддержку. Создайте первый тикет!') }}</p>
                <button class="btn btn-primary" id="createFirstTicketBtn">
                    <i class="fas fa-plus"></i>
                    {{ __('Создать первый тикет') }}
                </button>
            </div>

            <!-- Loading State -->
            <div class="loading-state" id="ticketsLoadingState">
                <div class="loading-spinner"></div>
                <p>{{ __('Загрузка тикетов...') }}</p>
            </div>

            <!-- Pagination -->
            <!-- Pagination for Tickets -->
            <div class="pagination-container" id="ticketsPagination">
                <!-- Pagination component will be rendered here -->
            </div>
        </div>

        <!-- Ticket Chat Section -->
        <div class="ticket-chat-section" id="ticketChatSection" style="display: none;">
            <div class="chat-header">
                <div class="chat-header-content">
                    <button type="button" class="back-to-list-btn" id="backToListBtn">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('К тикетам') }}
                    </button>
                    <div class="chat-ticket-info">
                        <h2 class="chat-ticket-title" id="chatTicketTitle">{{ __('Тикет #123') }}</h2>
                        <div class="chat-ticket-meta">
                            <span class="ticket-status-badge" id="chatTicketStatus">Open</span>
                            <span class="ticket-category" id="chatTicketCategory">Проблема с заказом</span>
                            <span class="ticket-date" id="chatTicketDate">02.08.2025</span>
                        </div>
                    </div>
                </div>
                <div class="chat-header-actions">
                    <button type="button" class="btn btn-outline btn-close-ticket" id="closeTicketBtn">
                        <i class="fas fa-times"></i>
                        {{ __('Закрыть тикет') }}
                    </button>
                </div>
            </div>

            <div class="chat-container">
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be loaded here -->
                </div>
            </div>

            <div class="chat-input-section">
                <form id="chatMessageForm" class="chat-form">
                    <div class="chat-input-container">
                        <div class="file-attachment-area" id="fileAttachmentArea" style="display: none;">
                            <div class="attached-files" id="attachedFiles"></div>
                        </div>
                        <div class="chat-input-wrapper">
                            <textarea id="chatMessageInput" class="chat-input" placeholder="{{ __('Введите ваше сообщение...') }}"
                                rows="3"></textarea>
                            <div class="chat-input-actions">
                                <label for="chatFileInput" class="file-attach-btn" title="{{ __('Прикрепить файл') }}">
                                    <i class="fas fa-paperclip"></i>
                                    <input type="file" id="chatFileInput" multiple
                                        accept=".jpg,.jpeg,.png,.pdf,.txt,.doc,.docx" style="display: none;">
                                </label>
                                <button type="submit" class="send-message-btn" id="sendMessageBtn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <!-- Create Ticket Modal -->
    <div class="modal fade tickets-modal" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTicketModalLabel">
                        <i class="fas fa-ticket-alt"></i>
                        {{ __('Создать новый тикет') }}
                    </h5>
                </div>
                <form id="createTicketForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="ticketCategory" class="form-label">{{ __('Категория') }}</label>
                            <div class="custom-select-wrapper">
                                <select id="ticketCategory" class="custom-select" data-placeholder="Выберите категорию"
                                    required>
                                    <option value="">{{ __('Выберите категорию') }}</option>
                                    <option value="order">{{ __('Проблема с заказом') }}</option>
                                    <option value="payment">{{ __('Проблема с платежом') }}</option>
                                    <option value="general">{{ __('Общий вопрос') }}</option>
                                    <option value="technical">{{ __('Техническая проблема') }}</option>
                                </select>
                            </div>
                        </div>



                        <div class="form-group">
                            <label for="ticketSubject" class="form-label">{{ __('Тема тикета') }}</label>
                            <input type="text" id="ticketSubject" class="form-input"
                                placeholder="{{ __('Кратко опишите проблему') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="ticketMessage" class="form-label">{{ __('Описание проблемы') }}</label>
                            <textarea id="ticketMessage" class="form-textarea" rows="6"
                                placeholder="{{ __('Подробно опишите вашу проблему...') }}" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="ticketFiles" class="form-label">{{ __('Прикрепить файлы') }}</label>
                            <div class="file-upload-area">
                                <input type="file" id="ticketFiles" multiple
                                    accept=".jpg,.jpeg,.png,.pdf,.txt,.doc,.docx" hidden>
                                <div class="file-upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>{{ __('Перетащите файлы сюда или нажмите для выбора') }}</span>
                                    <small>{{ __('Поддерживаются: JPG, PNG, PDF, TXT, DOC, DOCX (макс. 10MB)') }}</small>
                                </div>
                                <div class="selected-files" id="selectedTicketFiles"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                            {{ __('Отмена') }}
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitTicketBtn">
                            <i class="fas fa-paper-plane"></i>
                            {{ __('Создать тикет') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Close Ticket Confirmation Modal -->
    <div class="modal fade tickets-modal" id="closeTicketModal" tabindex="-1" aria-labelledby="closeTicketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="closeTicketModalLabel">
                        <i class="fas fa-times-circle"></i>
                        {{ __('Закрыть тикет') }}
                    </h5>
                </div>
                <div class="modal-body">
                    <p>{{ __('Вы уверены, что хотите закрыть этот тикет? После закрытия вы не сможете добавлять новые сообщения.') }}
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                        {{ __('Отмена') }}
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmCloseTicketBtn">
                        <i class="fas fa-times"></i>
                        {{ __('Закрыть тикет') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ mix('assets/user/js/pages/tickets.js') }}"></script>
@endpush
