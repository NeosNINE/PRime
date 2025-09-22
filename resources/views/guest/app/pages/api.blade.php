@extends('guest.app.layout')

@section('title', 'API Документация - SOCNET SMM')
@section('description', 'Мощный API для разработчиков. Создавайте заказы, проверяйте статус и пополняйте баланс через
    простые REST API запросы.')

@section('content')
    <!-- Hero Section -->
    <section class="api-hero-section">

        <!-- Decorative Background Icons -->
        <div class="api-bg-icons">
            <i class="fas fa-heart api-bg-icon api-icon-1"></i>
            <i class="fas fa-share api-bg-icon api-icon-2"></i>
            <i class="fas fa-thumbs-up api-bg-icon api-icon-3"></i>
            <i class="fas fa-eye api-bg-icon api-icon-4"></i>
            <i class="fas fa-users api-bg-icon api-icon-5"></i>
            <i class="fas fa-chart-line api-bg-icon api-icon-6"></i>
            <i class="fas fa-rocket api-bg-icon api-icon-7"></i>
            <i class="fas fa-star api-bg-icon api-icon-8"></i>
        </div>

        <div class="container">
            <div class="api-hero-content">
                <h1 class="api-hero-title">API Документация</h1>
                <p class="api-hero-description">
                    Мощный RESTful API для автоматизации ваших SMM заказов.
                    Создавайте заказы, отслеживайте статус и управляйте балансом
                    через простые HTTP запросы.
                </p>

                <div class="api-hero-features">
                    <div class="api-feature">
                        <div class="api-feature-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <span>RESTful API</span>
                    </div>
                    <div class="api-feature">
                        <div class="api-feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <span>Безопасность</span>
                    </div>
                    <div class="api-feature">
                        <div class="api-feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span>24/7 Доступность</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- API Endpoints Section -->
    <section class="api-endpoints-section">
        <!-- Decorative Background Icons for API Section -->
        <div class="api-section-bg-icons">
            <i class="fas fa-code api-section-icon api-section-icon-1"></i>
            <i class="fas fa-database api-section-icon api-section-icon-2"></i>
            <i class="fas fa-server api-section-icon api-section-icon-3"></i>
            <i class="fas fa-key api-section-icon api-section-icon-4"></i>
            <i class="fas fa-lock api-section-icon api-section-icon-5"></i>
            <i class="fas fa-cogs api-section-icon api-section-icon-6"></i>
            <i class="fas fa-terminal api-section-icon api-section-icon-7"></i>
            <i class="fas fa-network-wired api-section-icon api-section-icon-8"></i>
            <i class="fas fa-microchip api-section-icon api-section-icon-9"></i>
            <i class="fas fa-bolt api-section-icon api-section-icon-10"></i>
        </div>

        <div class="container">
            <div class="api-endpoints-grid">

                <!-- Balance Endpoint -->
                <div class="api-endpoint-block">
                    <div class="api-endpoint-header">
                        <span class="api-method">POST</span>
                        <h3 class="api-endpoint-title">Получить баланс</h3>
                    </div>

                    <div class="api-endpoint-info">
                        <h4>Параметры запроса:</h4>
                        <div class="api-parameters">
                            <div class="api-parameter">
                                <span class="api-param-name">key</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">Ваш API ключ</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">action</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">balance</span>
                            </div>
                        </div>
                    </div>

                    <div class="api-code-example">
                        <h4>Пример запроса:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "key": "your_api_key_here",
  "action": "balance"
}</code></pre>
                        </div>
                    </div>

                    <div class="api-response-example">
                        <h4>Пример ответа:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "balance": "100.50",
  "currency": "USD"
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Services Endpoint -->
                <div class="api-endpoint-block">
                    <div class="api-endpoint-header">
                        <span class="api-method">POST</span>
                        <h3 class="api-endpoint-title">Список услуг</h3>
                    </div>

                    <div class="api-endpoint-info">
                        <h4>Параметры запроса:</h4>
                        <div class="api-parameters">
                            <div class="api-parameter">
                                <span class="api-param-name">key</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">Ваш API ключ</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">action</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">services</span>
                            </div>
                        </div>
                    </div>

                    <div class="api-code-example">
                        <h4>Пример запроса:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "key": "your_api_key_here",
  "action": "services"
}</code></pre>
                        </div>
                    </div>

                    <div class="api-response-example">
                        <h4>Пример ответа:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">[
  {
    "service": 1,
    "name": "Instagram Followers",
    "type": "Default",
    "rate": "1.00",
    "min": "10",
    "max": "10000"
  }
]</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Add Order Endpoint -->
                <div class="api-endpoint-block">
                    <div class="api-endpoint-header">
                        <span class="api-method">POST</span>
                        <h3 class="api-endpoint-title">Создать заказ</h3>
                    </div>

                    <div class="api-endpoint-info">
                        <h4>Параметры запроса:</h4>
                        <div class="api-parameters">
                            <div class="api-parameter">
                                <span class="api-param-name">key</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">Ваш API ключ</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">action</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">add</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">service</span>
                                <span class="api-param-type">integer</span>
                                <span class="api-param-desc">ID услуги</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">link</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">Ссылка на пост/профиль</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">quantity</span>
                                <span class="api-param-type">integer</span>
                                <span class="api-param-desc">Количество</span>
                            </div>
                        </div>
                    </div>

                    <div class="api-code-example">
                        <h4>Пример запроса:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "key": "your_api_key_here",
  "action": "add",
  "service": 1,
  "link": "https://instagram.com/p/example",
  "quantity": 1000
}</code></pre>
                        </div>
                    </div>

                    <div class="api-response-example">
                        <h4>Пример ответа:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "order": 12345
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Order Status Endpoint -->
                <div class="api-endpoint-block">
                    <div class="api-endpoint-header">
                        <span class="api-method">POST</span>
                        <h3 class="api-endpoint-title">Статус заказа</h3>
                    </div>

                    <div class="api-endpoint-info">
                        <h4>Параметры запроса:</h4>
                        <div class="api-parameters">
                            <div class="api-parameter">
                                <span class="api-param-name">key</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">Ваш API ключ</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">action</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">status</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">order</span>
                                <span class="api-param-type">integer</span>
                                <span class="api-param-desc">ID заказа</span>
                            </div>
                        </div>
                    </div>

                    <div class="api-code-example">
                        <h4>Пример запроса:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "key": "your_api_key_here",
  "action": "status",
  "order": 12345
}</code></pre>
                        </div>
                    </div>

                    <div class="api-response-example">
                        <h4>Пример ответа:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "charge": "1.00",
  "start_count": "100",
  "status": "Completed",
  "remains": "0"
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Multiple Orders Status -->
                <div class="api-endpoint-block">
                    <div class="api-endpoint-header">
                        <span class="api-method">POST</span>
                        <h3 class="api-endpoint-title">Статус нескольких заказов</h3>
                    </div>

                    <div class="api-endpoint-info">
                        <h4>Параметры запроса:</h4>
                        <div class="api-parameters">
                            <div class="api-parameter">
                                <span class="api-param-name">key</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">Ваш API ключ</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">action</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">status</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">orders</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">ID заказов через запятую</span>
                            </div>
                        </div>
                    </div>

                    <div class="api-code-example">
                        <h4>Пример запроса:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "key": "your_api_key_here",
  "action": "status",
  "orders": "12345,12346,12347"
}</code></pre>
                        </div>
                    </div>

                    <div class="api-response-example">
                        <h4>Пример ответа:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "12345": {
    "charge": "1.00",
    "start_count": "100",
    "status": "Completed",
    "remains": "0"
  }
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Create Refill -->
                <div class="api-endpoint-block">
                    <div class="api-endpoint-header">
                        <span class="api-method">POST</span>
                        <h3 class="api-endpoint-title">Создать пополнение</h3>
                    </div>

                    <div class="api-endpoint-info">
                        <h4>Параметры запроса:</h4>
                        <div class="api-parameters">
                            <div class="api-parameter">
                                <span class="api-param-name">key</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">Ваш API ключ</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">action</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">refill</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">order</span>
                                <span class="api-param-type">integer</span>
                                <span class="api-param-desc">ID заказа для пополнения</span>
                            </div>
                        </div>
                    </div>

                    <div class="api-code-example">
                        <h4>Пример запроса:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "key": "your_api_key_here",
  "action": "refill",
  "order": 12345
}</code></pre>
                        </div>
                    </div>

                    <div class="api-response-example">
                        <h4>Пример ответа:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "refill": 1
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Get Refill Status -->
                <div class="api-endpoint-block">
                    <div class="api-endpoint-header">
                        <span class="api-method">POST</span>
                        <h3 class="api-endpoint-title">Статус пополнения</h3>
                    </div>

                    <div class="api-endpoint-info">
                        <h4>Параметры запроса:</h4>
                        <div class="api-parameters">
                            <div class="api-parameter">
                                <span class="api-param-name">key</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">Ваш API ключ</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">action</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">refill_status</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">refill</span>
                                <span class="api-param-type">integer</span>
                                <span class="api-param-desc">ID пополнения</span>
                            </div>
                        </div>
                    </div>

                    <div class="api-code-example">
                        <h4>Пример запроса:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "key": "your_api_key_here",
  "action": "refill_status",
  "refill": 1
}</code></pre>
                        </div>
                    </div>

                    <div class="api-response-example">
                        <h4>Пример ответа:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "status": "Completed"
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="api-endpoint-block">
                    <div class="api-endpoint-header">
                        <span class="api-method">POST</span>
                        <h3 class="api-endpoint-title">Информация о пользователе</h3>
                    </div>

                    <div class="api-endpoint-info">
                        <h4>Параметры запроса:</h4>
                        <div class="api-parameters">
                            <div class="api-parameter">
                                <span class="api-param-name">key</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">Ваш API ключ</span>
                            </div>
                            <div class="api-parameter">
                                <span class="api-param-name">action</span>
                                <span class="api-param-type">string</span>
                                <span class="api-param-desc">profile</span>
                            </div>
                        </div>
                    </div>

                    <div class="api-code-example">
                        <h4>Пример запроса:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "key": "your_api_key_here",
  "action": "profile"
}</code></pre>
                        </div>
                    </div>

                    <div class="api-response-example">
                        <h4>Пример ответа:</h4>
                        <div class="api-code-block">
                            <pre><code class="language-json">{
  "username": "user@example.com",
  "email": "user@example.com",
  "skype": "example_skype",
  "balance": "100.84",
  "currency": "USD"
}</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- API Key CTA Section -->
    {{-- <section class="api-cta-section">
        <div class="container">
            <div class="api-cta-content">
                <h2 class="api-cta-title">Готовы начать?</h2>
                <p class="api-cta-description">
                    SMM Panel API здесь 24/7, чтобы помочь вашему SMM бизнесу работать и развиваться в любое время.
                </p>
                <a href="{{ route('register') }}" class="btn btn-primary api-cta-button">
                    Получить API ключ
                </a>
                <p class="api-cta-note">
                    При регистрации вы получите ключ бесплатно
                </p>
            </div>
        </div>
    </section> --}}

    @guest
        @include('guest.app.components.cta-guest')
    @endguest

@endsection

@section('scripts')
    <script type="text/javascript" src="{{ mix('assets/guest/js/pages/api.js') }}"></script>
@endsection
