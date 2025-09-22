@section('title', 'API Документация - SOCNET SMM')
@extends('user.app.layout')

@section('content')
    <div class="api-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ __('API Документация') }}</h1>
        </div>

        <!-- API Key Management Section -->
        <div class="api-key-section">
            <div class="api-key-card">
                <h2 class="section-title">{{ __('Управление API ключом') }}</h2>
                <p class="section-description">
                    {{ __('Ваш уникальный API ключ для доступа к нашему API. Храните его в безопасности.') }}</p>

                <div class="api-key-container">
                    <div class="api-key-field">
                        <label class="field-label">{{ __('Текущий API ключ') }}</label>
                        <div class="key-input-group">
                            <input type="text" id="apiKey" value="sk_live_abc123xyz789def456ghi" readonly
                                class="api-key-input">
                            <button type="button" class="copy-key-btn" onclick="copyApiKey()">
                                <i class="fas fa-copy"></i>
                                <span class="btn-text">{{ __('Копировать') }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="key-actions">
                        <button type="button" class="generate-key-btn" data-bs-toggle="modal"
                            data-bs-target="#generateKeyModal">
                            <i class="fas fa-sync-alt"></i>
                            <span class="btn-text">{{ __('Сгенерировать новый ключ') }}</span>
                        </button>
                        <small
                            class="key-warning">{{ __('⚠️ Генерация нового ключа сделает текущий недействительным') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Documentation -->
        <div class="api-documentation">

            <!-- Base URL Section -->
            <div class="doc-section">
                <h2 class="section-title">{{ __('Базовая информация') }}</h2>
                <div class="info-block">
                    <div class="info-item">
                        <strong>{{ __('Базовый URL:') }}</strong>
                        <code class="inline-code">{{ url('/api') }}</code>
                    </div>
                    <div class="info-item">
                        <strong>{{ __('Авторизация:') }}</strong>
                        <code class="inline-code">Authorization: Bearer YOUR_API_KEY</code>
                    </div>
                    <div class="info-item">
                        <strong>{{ __('Формат ответа:') }}</strong>
                        <code class="inline-code">application/json</code>
                    </div>
                </div>
            </div>

            <!-- Create Order Endpoint -->
            <div class="doc-section">
                <h2 class="section-title">{{ __('Создание заказа') }}</h2>
                <div class="endpoint-info">
                    <span class="method post">POST</span>
                    <code class="endpoint-url">/api/orders</code>
                </div>
                <p class="section-description">{{ __('Создает новый заказ для указанной услуги.') }}</p>

                <!-- Parameters Table -->
                <h3 class="subsection-title">{{ __('Параметры') }}</h3>
                <div class="params-table-container">
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>{{ __('Параметр') }}</th>
                                <th>{{ __('Тип') }}</th>
                                <th>{{ __('Описание') }}</th>
                                <th>{{ __('Обязательный') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>service_id</code></td>
                                <td><span class="type">integer</span></td>
                                <td>{{ __('ID услуги из каталога') }}</td>
                                <td><span class="required">{{ __('Да') }}</span></td>
                            </tr>
                            <tr>
                                <td><code>link</code></td>
                                <td><span class="type">string</span></td>
                                <td>{{ __('Ссылка на пост/профиль/видео') }}</td>
                                <td><span class="required">{{ __('Да') }}</span></td>
                            </tr>
                            <tr>
                                <td><code>quantity</code></td>
                                <td><span class="type">integer</span></td>
                                <td>{{ __('Количество (лайки, подписчики и т.д.)') }}</td>
                                <td><span class="required">{{ __('Да') }}</span></td>
                            </tr>
                            <tr>
                                <td><code>runs</code></td>
                                <td><span class="type">integer</span></td>
                                <td>{{ __('Количество прогонов (для drip-feed)') }}</td>
                                <td><span class="optional">{{ __('Нет') }}</span></td>
                            </tr>
                            <tr>
                                <td><code>interval</code></td>
                                <td><span class="type">integer</span></td>
                                <td>{{ __('Интервал между прогонами в минутах') }}</td>
                                <td><span class="optional">{{ __('Нет') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Request Example -->
                <h3 class="subsection-title">{{ __('Пример запроса') }}</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-lang">cURL</span>
                        <button class="copy-code-btn" onclick="copyCode(this)" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>curl -X POST "{{ url('/api/orders') }}" \
  -H "Authorization: Bearer sk_live_abc123xyz789def456ghi" \
  -H "Content-Type: application/json" \
  -d '{
    "service_id": 123,
    "link": "https://instagram.com/p/example123",
    "quantity": 1000
  }'</code></pre>
                </div>

                <!-- Response Example -->
                <h3 class="subsection-title">{{ __('Пример ответа') }}</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-lang">JSON</span>
                        <button class="copy-code-btn" onclick="copyCode(this)" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>{
  "success": true,
  "data": {
    "order_id": 456789,
    "status": "Pending",
    "service_id": 123,
    "link": "https://instagram.com/p/example123",
    "quantity": 1000,
    "created_at": "2025-08-02T10:30:00Z"
  }
}</code></pre>
                </div>
            </div>

            <!-- Check Order Status -->
            <div class="doc-section">
                <h2 class="section-title">{{ __('Проверка статуса заказа') }}</h2>
                <div class="endpoint-info">
                    <span class="method get">GET</span>
                    <code class="endpoint-url">/api/orders/{order_id}</code>
                </div>
                <p class="section-description">{{ __('Возвращает текущий статус заказа и дополнительную информацию.') }}
                </p>

                <!-- Status Values -->
                <h3 class="subsection-title">{{ __('Возможные статусы') }}</h3>
                <div class="status-list">
                    <div class="status-item">
                        <span class="status-badge pending">Pending</span>
                        <span class="status-desc">{{ __('Заказ ожидает обработки') }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-badge in-progress">In Progress</span>
                        <span class="status-desc">{{ __('Заказ выполняется') }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-badge completed">Completed</span>
                        <span class="status-desc">{{ __('Заказ успешно выполнен') }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-badge cancelled">Cancelled</span>
                        <span class="status-desc">{{ __('Заказ отменен') }}</span>
                    </div>
                </div>

                <!-- Request Example -->
                <h3 class="subsection-title">{{ __('Пример запроса') }}</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-lang">cURL</span>
                        <button class="copy-code-btn" onclick="copyCode(this)" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>curl -X GET "{{ url('/api/orders/456789') }}" \
  -H "Authorization: Bearer sk_live_abc123xyz789def456ghi"</code></pre>
                </div>

                <!-- Response Example -->
                <h3 class="subsection-title">{{ __('Пример ответа') }}</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-lang">JSON</span>
                        <button class="copy-code-btn" onclick="copyCode(this)" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>{
  "success": true,
  "data": {
    "order_id": 456789,
    "status": "Completed",
    "service_name": "Instagram Likes",
    "link": "https://instagram.com/p/example123",
    "quantity": 1000,
    "start_count": 150,
    "remains": 0,
    "created_at": "2025-08-02T10:30:00Z",
    "completed_at": "2025-08-02T11:45:00Z"
  }
}</code></pre>
                </div>
            </div>

            <!-- Get Services -->
            <div class="doc-section">
                <h2 class="section-title">{{ __('Получение списка услуг') }}</h2>
                <div class="endpoint-info">
                    <span class="method get">GET</span>
                    <code class="endpoint-url">/api/services</code>
                </div>
                <p class="section-description">{{ __('Возвращает список всех доступных услуг с их параметрами.') }}</p>

                <!-- Request Example -->
                <h3 class="subsection-title">{{ __('Пример запроса') }}</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-lang">cURL</span>
                        <button class="copy-code-btn" onclick="copyCode(this)" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>curl -X GET "{{ url('/api/services') }}" \
  -H "Authorization: Bearer sk_live_abc123xyz789def456ghi"</code></pre>
                </div>

                <!-- Response Example -->
                <h3 class="subsection-title">{{ __('Пример ответа') }}</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-lang">JSON</span>
                        <button class="copy-code-btn" onclick="copyCode(this)" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>{
  "success": true,
  "data": [
    {
      "service_id": 123,
      "name": "Instagram Likes",
      "category": "Instagram",
      "type": "Likes",
      "price_per_1000": 2.50,
      "min_quantity": 100,
      "max_quantity": 10000,
      "description": "High quality Instagram likes",
      "refill": true
    }
  ]
}</code></pre>
                </div>
            </div>

            <!-- Get Balance -->
            <div class="doc-section">
                <h2 class="section-title">{{ __('Проверка баланса') }}</h2>
                <div class="endpoint-info">
                    <span class="method get">GET</span>
                    <code class="endpoint-url">/api/balance</code>
                </div>
                <p class="section-description">{{ __('Возвращает текущий баланс аккаунта.') }}</p>

                <!-- Request Example -->
                <h3 class="subsection-title">{{ __('Пример запроса') }}</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-lang">cURL</span>
                        <button class="copy-code-btn" onclick="copyCode(this)" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>curl -X GET "{{ url('/api/balance') }}" \
  -H "Authorization: Bearer sk_live_abc123xyz789def456ghi"</code></pre>
                </div>

                <!-- Response Example -->
                <h3 class="subsection-title">{{ __('Пример ответа') }}</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-lang">JSON</span>
                        <button class="copy-code-btn" onclick="copyCode(this)" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>{
  "success": true,
  "data": {
    "balance": 250.75,
    "currency": "USD"
  }
}</code></pre>
                </div>
            </div>

            <!-- Error Responses -->
            <div class="doc-section">
                <h2 class="section-title">{{ __('Обработка ошибок') }}</h2>
                <p class="section-description">
                    {{ __('При возникновении ошибок API возвращает соответствующие HTTP коды и описание проблемы.') }}</p>

                <!-- Error Codes Table -->
                <h3 class="subsection-title">{{ __('HTTP коды ошибок') }}</h3>
                <div class="params-table-container">
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>{{ __('Код') }}</th>
                                <th>{{ __('Значение') }}</th>
                                <th>{{ __('Описание') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>400</code></td>
                                <td>Bad Request</td>
                                <td>{{ __('Неверные параметры запроса') }}</td>
                            </tr>
                            <tr>
                                <td><code>401</code></td>
                                <td>Unauthorized</td>
                                <td>{{ __('Неверный или отсутствующий API ключ') }}</td>
                            </tr>
                            <tr>
                                <td><code>403</code></td>
                                <td>Forbidden</td>
                                <td>{{ __('Недостаточно прав доступа') }}</td>
                            </tr>
                            <tr>
                                <td><code>404</code></td>
                                <td>Not Found</td>
                                <td>{{ __('Ресурс не найден') }}</td>
                            </tr>
                            <tr>
                                <td><code>429</code></td>
                                <td>Too Many Requests</td>
                                <td>{{ __('Превышен лимит запросов') }}</td>
                            </tr>
                            <tr>
                                <td><code>500</code></td>
                                <td>Internal Server Error</td>
                                <td>{{ __('Внутренняя ошибка сервера') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Error Response Example -->
                <h3 class="subsection-title">{{ __('Пример ответа с ошибкой') }}</h3>
                <div class="code-block">
                    <div class="code-header">
                        <span class="code-lang">JSON</span>
                        <button class="copy-code-btn" onclick="copyCode(this)" data-bs-toggle="tooltip"
                            data-bs-placement="top" data-bs-title="{{ __('Скопировать') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>{
  "success": false,
  "error": {
    "code": 400,
    "message": "Invalid service_id parameter",
    "details": "Service with ID 999 not found"
  }
}</code></pre>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <!-- Generate New API Key Modal -->
    <div class="modal fade api-modal" id="generateKeyModal" tabindex="-1" aria-labelledby="generateKeyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateKeyModalLabel">{{ __('Сгенерировать новый API ключ') }}</h5>
                </div>
                <div class="modal-body">
                    <div class="warning-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>{{ __('Внимание! После генерации нового ключа текущий ключ станет недействительным.') }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline"
                        data-bs-dismiss="modal">{{ __('Отмена') }}</button>
                    <button type="button" class="btn btn-primary"
                        onclick="generateNewApiKey()">{{ __('Сгенерировать новый ключ') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ mix('assets/user/js/pages/api.js') }}"></script>
@endpush
