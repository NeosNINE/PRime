@extends('guest.app.layout')

@section('title', 'SOCNET SMM - Лучшая SMM панель')
@section('description',
    'SOCNET SMM — надёжная и сверхбыстрая платформа для продвижения в социальных сетях по доступным
    ценам.')

@section('content')
    <!-- Block 1: Hero -->
    <section class="hero-section particles-bg" id="hero">
        <div class="container">
            <div class="section-badge">
                <i class="fas fa-star"></i>
                <span>SOCNET SMM. Лучшая SMM панель</span>
            </div>

            <div class="hero-content">
                <h1 class="hero-title">
                    Ваш короткий путь к успеху в соцсетях
                </h1>

                <p class="hero-description">
                    SOCNET SMM — надёжная и сверхбыстрая платформа для продвижения в социальных сетях по доступным ценам.
                    Увеличивайте охваты, покоряйте новую аудиторию и превращайте лайки в реальный успех — просто, быстро и
                    выгодно вместе с нами!
                </p>

                <div class="hero-actions">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg register-btn">
                        <i class="fas fa-rocket"></i>
                        Зарегистрироваться
                    </a>

                    <a href="{{ route('register') }}" class="bonus-badge">
                        <div class="bonus-arrows">
                            <span>&laquo;</span>
                        </div>
                        <i class="fas fa-gift"></i>
                        <span>Присоединяйся к нам и забери бонус в 1$</span>
                    </a>
                </div>
            </div>

            <!-- Enhanced Background Icons with Social Media Theme -->
            <div class="hero-bg-icons">
                <!-- Основные иконки активности -->
                <div class="bg-icon like-icon"><i class="fas fa-heart"></i></div>
                <div class="bg-icon arrow-icon"><i class="fas fa-arrow-up"></i></div>
                <div class="bg-icon chart-icon"><i class="fas fa-chart-line"></i></div>
                <div class="bg-icon play-icon"><i class="fas fa-play"></i></div>
                <div class="bg-icon share-icon"><i class="fas fa-share"></i></div>

                <!-- Социальные сети -->
                <div class="bg-icon instagram-icon"><i class="fab fa-instagram"></i></div>
                <div class="bg-icon telegram-icon"><i class="fab fa-telegram"></i></div>
                <div class="bg-icon youtube-icon"><i class="fab fa-youtube"></i></div>
                <div class="bg-icon facebook-icon"><i class="fab fa-facebook"></i></div>
                <div class="bg-icon tiktok-icon"><i class="fab fa-tiktok"></i></div>
                <div class="bg-icon twitter-icon"><i class="fab fa-twitter"></i></div>
                <div class="bg-icon discord-icon"><i class="fab fa-discord"></i></div>
                <div class="bg-icon spotify-icon"><i class="fab fa-spotify"></i></div>

                <!-- Дополнительные иконки активности -->
                <div class="bg-icon thumbs-up-icon"><i class="fas fa-thumbs-up"></i></div>
                <div class="bg-icon comment-icon"><i class="fas fa-comment"></i></div>
                <div class="bg-icon repost-icon"><i class="fas fa-retweet"></i></div>
                <div class="bg-icon crown-icon"><i class="fas fa-crown"></i></div>
                <div class="bg-icon rocket-icon"><i class="fas fa-rocket"></i></div>
            </div>

            <!-- Floating Social Notifications -->
            <div class="hero-social-notifications">
                <div class="social-notification notification-1">
                    <div class="notification-icon"><i class="fas fa-heart"></i></div>
                    <div class="notification-text">+245 лайков</div>
                </div>

                <div class="social-notification notification-2">
                    <div class="notification-icon"><i class="fas fa-users"></i></div>
                    <div class="notification-text">+89 подписчиков</div>
                </div>

                <div class="social-notification notification-3">
                    <div class="notification-icon"><i class="fas fa-comment"></i></div>
                    <div class="notification-text">+32 комментария</div>
                </div>

                <div class="social-notification notification-4">
                    <div class="notification-icon"><i class="fas fa-share"></i></div>
                    <div class="notification-text">+67 репостов</div>
                </div>

                <div class="social-notification notification-5">
                    <div class="notification-icon"><i class="fas fa-play"></i></div>
                    <div class="notification-text">+1.2k просмотров</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Block 2: Statistics -->
    <section class="statistics-section enhanced-gradient-bg" id="statistics">
        <div class="container">
            <div class="section-badge">
                <i class="fas fa-chart-bar"></i>
                <span>Цифры говорят за нас</span>
            </div>

            <div class="section-header">
                <h2 class="section-title">SOCNET SMM в цифрах</h2>
                <p class="section-description">
                    Этих показателей мы достигли благодаря вам — нашим клиентам!
                </p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-number" data-count="4269396">0</div>
                    <div class="stat-label">Заказов</div>
                    <div class="stat-description">Обработали уже миллионы заказов</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="stat-number" data-count="1200">0</div>
                    <div class="stat-label">Услуг</div>
                    <div class="stat-description">Регулярно обновляем список наших услуг и сервисов</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" data-count="40000">0</div>
                    <div class="stat-label">Пользователей</div>
                    <div class="stat-description">Остаемся всегда с вами на одной волне</div>
                </div>
            </div>

            <!-- Background Statistics Icons -->
            <div class="statistics-bg-icons">
                <div class="statistics-bg-icon statistics-icon-1"><i class="fas fa-chart-line"></i></div>
                <div class="statistics-bg-icon statistics-icon-2"><i class="fas fa-chart-bar"></i></div>
                <div class="statistics-bg-icon statistics-icon-3"><i class="fas fa-chart-pie"></i></div>
                <div class="statistics-bg-icon statistics-icon-4"><i class="fas fa-chart-area"></i></div>
                <div class="statistics-bg-icon statistics-icon-5"><i class="fas fa-percentage"></i></div>
                <div class="statistics-bg-icon statistics-icon-6"><i class="fas fa-calculator"></i></div>
                <div class="statistics-bg-icon statistics-icon-7"><i class="fas fa-abacus"></i></div>
                <div class="statistics-bg-icon statistics-icon-8"><i class="fas fa-sort-numeric-up"></i></div>
                <div class="statistics-bg-icon statistics-icon-9"><i class="fas fa-sort-numeric-down"></i></div>
                <div class="statistics-bg-icon statistics-icon-10"><i class="fas fa-infinity"></i></div>
            </div>
        </div>
    </section>

    <!-- Block 3: Social Networks -->
    <section class="social-networks-section pattern-bg section-smooth-wave" id="social-networks">
        <div class="container">
            <div class="section-badge">
                <i class="fas fa-globe"></i>
                <span>Все соцсети под вашим контролем</span>
            </div>

            <div class="section-header">
                <h2 class="section-title">Все ваши соцсети — в одной панели!</h2>
                <p class="section-description">
                    SOCNET SMM охватывает более 20 площадок — от TikTok и YouTube до Telegram и Spotify,
                    помогая вам расти там, где находится ваша целевая аудитория!
                </p>
            </div>

            <div class="platforms-grid">
                <div class="platform-card active" data-platform="telegram">
                    <div class="platform-icon telegram">
                        <i class="fab fa-telegram"></i>
                    </div>
                    <h3 class="platform-title">Telegram</h3>
                </div>

                <div class="platform-card" data-platform="instagram">
                    <div class="platform-icon instagram">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h3 class="platform-title">Instagram</h3>
                </div>

                <div class="platform-card" data-platform="facebook">
                    <div class="platform-icon facebook">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h3 class="platform-title">Facebook</h3>
                </div>

                <div class="platform-card" data-platform="youtube">
                    <div class="platform-icon youtube">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h3 class="platform-title">YouTube</h3>
                </div>

                <div class="platform-card" data-platform="twitter">
                    <div class="platform-icon twitter">
                        <i class="fab fa-twitter"></i>
                    </div>
                    <h3 class="platform-title">Twitter (X)</h3>
                </div>

                <div class="platform-card" data-platform="tiktok">
                    <div class="platform-icon tiktok">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <h3 class="platform-title">TikTok</h3>
                </div>

                <div class="platform-card" data-platform="spotify">
                    <div class="platform-icon spotify">
                        <i class="fab fa-spotify"></i>
                    </div>
                    <h3 class="platform-title">Spotify</h3>
                </div>

                <div class="platform-card" data-platform="discord">
                    <div class="platform-icon discord">
                        <i class="fab fa-discord"></i>
                    </div>
                    <h3 class="platform-title">Discord</h3>
                </div>
            </div>

            <!-- Platform Description Block -->
            <div class="platform-info">
                <div class="platform-info-content">
                    <p class="platform-info-description" id="platform-description">
                        Создайте аудиторию, которая слышит. Мы обеспечим живых участников и просмотры,
                        чтобы канал звучал громче заявлений Павла Дурова.
                    </p>
                </div>
            </div>

            <!-- Background Icons -->
            <div class="social-bg-icons">
                <div class="social-bg-icon social-icon-1"><i class="fab fa-telegram"></i></div>
                <div class="social-bg-icon social-icon-2"><i class="fab fa-instagram"></i></div>
                <div class="social-bg-icon social-icon-3"><i class="fab fa-youtube"></i></div>
                <div class="social-bg-icon social-icon-4"><i class="fab fa-facebook"></i></div>
                <div class="social-bg-icon social-icon-5"><i class="fab fa-twitter"></i></div>
                <div class="social-bg-icon social-icon-6"><i class="fab fa-tiktok"></i></div>
                <div class="social-bg-icon social-icon-7"><i class="fab fa-spotify"></i></div>
                <div class="social-bg-icon social-icon-8"><i class="fab fa-discord"></i></div>
                <div class="social-bg-icon social-icon-9"><i class="fab fa-linkedin"></i></div>
                <div class="social-bg-icon social-icon-10"><i class="fab fa-whatsapp"></i></div>
                <div class="social-bg-icon social-icon-11"><i class="fab fa-pinterest"></i></div>
                <div class="social-bg-icon social-icon-12"><i class="fab fa-snapchat"></i></div>
                <div class="social-bg-icon social-icon-13"><i class="fab fa-twitch"></i></div>
                <div class="social-bg-icon social-icon-14"><i class="fab fa-reddit"></i></div>
            </div>
        </div>
    </section>

    <!-- Block 4: Comparison -->
    <section class="comparison-section enhanced-gradient-bg" id="comparison">
        <div class="container">
            <div class="section-badge">
                <i class="fas fa-trophy"></i>
                <span>Наше преимущество очевидно</span>
            </div>

            <div class="section-header">
                <h2 class="section-title">SOCNET SMM против конкурентов</h2>
            </div>

            <div class="comparison-table">
                <div class="comparison-columns">
                    <div class="competitor-column">
                        <div class="column-icon competitor">
                            <i class="fas fa-times"></i>
                        </div>
                        <h3>Конкуренты</h3>

                        <ul class="comparison-list">
                            <li>
                                <i class="fas fa-times text-danger"></i>
                                <span>Сервисы обновляются редко</span>
                            </li>
                            <li>
                                <i class="fas fa-times text-danger"></i>
                                <span>Медленная или отсутствующая поддержка</span>
                            </li>
                            <li>
                                <i class="fas fa-times text-danger"></i>
                                <span>Невозможно отменить или ускорить зависшие заказы</span>
                            </li>
                            <li>
                                <i class="fas fa-times text-danger"></i>
                                <span>Нет возвратов при отмене</span>
                            </li>
                            <li>
                                <i class="fas fa-times text-danger"></i>
                                <span>Высокие цены и скрытые комиссии</span>
                            </li>
                            <li>
                                <i class="fas fa-times text-danger"></i>
                                <span>Сложная панель, нет API</span>
                            </li>
                        </ul>

                        <div class="result competitor-result">
                            <span class="result-text">Результат ≈ 15% успеха</span>
                            <i class="fas fa-times text-danger"></i>
                            {{--                        <p>Не предлагают заманчивых бонусов</p> --}}
                        </div>
                    </div>

                    <div class="socnet-column">
                        <div class="column-icon socnet">
                            <i class="fas fa-check"></i>
                        </div>
                        <h3>SOCNET SMM</h3>

                        <ul class="comparison-list">
                            <li>
                                <i class="fas fa-check"></i>
                                <span><b>Каталог обновляется каждые 6 часов</b> новыми услугами</span>
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                <span><b>24/7 live-чат и Telegram-бот</b> для мгновенной помощи</span>
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                <span><b>Собственная Drip-Feed-панель</b> + ускорение заказа одним кликом</span>
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                <span>Drip-feed + ускорение одним кликом</span>
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                <span><b>Автовозврат</b> средств на баланс, если заказ отменён</span>
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                <span><b>Минимальные цены от 0,01 $</b> без скрытых платежей</span>
                            </li>
                            <li>
                                <i class="fas fa-check"></i>
                                <span><b>Удобный интерфейс + REST API</b> с веб-хуками</span>
                            </li>
                        </ul>

                        <div class="result socnet-result">
                            <span class="result-text">Результат 99%+ успеха</span>
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Background Comparison Icons -->
            <div class="comparison-bg-icons">
                <div class="comparison-bg-icon comparison-icon-1"><i class="fas fa-trophy"></i></div>
                <div class="comparison-bg-icon comparison-icon-2"><i class="fas fa-star"></i></div>
                <div class="comparison-bg-icon comparison-icon-3"><i class="fas fa-check-circle"></i></div>
                <div class="comparison-bg-icon comparison-icon-4"><i class="fas fa-award"></i></div>
                <div class="comparison-bg-icon comparison-icon-5"><i class="fas fa-medal"></i></div>
                <div class="comparison-bg-icon comparison-icon-6"><i class="fas fa-crown"></i></div>
                <div class="comparison-bg-icon comparison-icon-7"><i class="fas fa-gem"></i></div>
                <div class="comparison-bg-icon comparison-icon-8"><i class="fas fa-rocket"></i></div>
                <div class="comparison-bg-icon comparison-icon-9"><i class="fas fa-bolt"></i></div>
                <div class="comparison-bg-icon comparison-icon-10"><i class="fas fa-fire"></i></div>
            </div>
        </div>
    </section>

    <!-- Block 5: SOCNET STORE -->
    <section class="store-section" id="store">
        <a class="store-section-link" href="https://socnet.store" target="_blank">
            <img class="store-white" src="{{ asset('assets/images/store-white.png') }}" alt="store-banner">
            <img class="store-black" src="{{ asset('assets/images/store-black.png') }}" alt="store-banner">
            <img class="store-white-mob" src="{{ asset('assets/images/store-white-mob.png') }}" alt="store-banner">
            <img class="store-black-mob" src="{{ asset('assets/images/store-black-mob.png') }}" alt="store-banner">
        </a>
    </section>

    <!-- Block 6: Payment Methods -->
    <section class="payment-section enhanced-gradient-bg" id="payments">
        <div class="container">
            <div class="section-badge">
                <i class="fas fa-credit-card"></i>
                <span>Платите так, как вам удобно</span>
            </div>

            <div class="section-header">
                <h2 class="section-title">Платите так, как удобно</h2>
                <p class="section-description">
                    Принимаем все популярные способы оплаты – банковские карты, электронные кошельки, криптовалюту и другие
                    локальные платежные методы по всему миру.
                </p>
            </div>

            <div class="payment-methods">
                <div class="payment-method">
                    <div class="payment-logo">
                        <img src="{{ asset('assets/images/payment/cryptomus.png') }}" alt="cryptomus">
                    </div>
                    <span>Криптовалюта</span>
                </div>
                <div class="payment-method">
                    <div class="payment-logo">
                        <img src="{{ asset('assets/images/payment/paypal.png') }}" alt="paypal">
                    </div>
                    <span>PayPal</span>
                </div>
                <div class="payment-method">
                    <div class="payment-logo">
                        <img src="{{ asset('assets/images/payment/visa.png') }}" alt="visa">
                    </div>
                    <span>VISA</span>
                </div>
                <div class="payment-method">
                    <div class="payment-logo">
                        <img src="{{ asset('assets/images/payment/mastercard.png') }}" alt="mastercard">
                    </div>
                    <span>Mastercard</span>
                </div>
                <div class="payment-method">
                    <div class="payment-logo">
                        <img src="{{ asset('assets/images/payment/mir.png') }}" alt="mir">
                    </div>
                    <span>Мир</span>
                </div>
                <div class="payment-method">
                    <div class="payment-logo">
                        <img src="{{ asset('assets/images/payment/binance.png') }}" alt="binance">
                    </div>
                    <span>BinancePay</span>
                </div>
                <div class="payment-method">
                    <div class="payment-logo">
                        <img src="{{ asset('assets/images/payment/cryptobot.png') }}" alt="cryptobot">
                    </div>
                    <span>CryptoBot</span>
                </div>
                <div class="payment-method">
                    <div class="payment-logo">
                        <img src="{{ asset('assets/images/payment/lolz.png') }}" alt="lolz">
                    </div>
                    <span>LOLZ</span>
                </div>
                <div class="payment-method">
                    <div class="payment-logo">
                        <img src="{{ asset('assets/images/payment/stripe.png') }}" alt="stripe">
                    </div>
                    <span>Stripe</span>
                </div>
            </div>

            <!-- Background Payment Icons -->
            <div class="payment-bg-icons">
                <div class="payment-bg-icon payment-icon-1"><i class="fas fa-credit-card"></i></div>
                <div class="payment-bg-icon payment-icon-2"><i class="fas fa-wallet"></i></div>
                <div class="payment-bg-icon payment-icon-3"><i class="fas fa-bitcoin"></i></div>
                <div class="payment-bg-icon payment-icon-4"><i class="fas fa-coins"></i></div>
                <div class="payment-bg-icon payment-icon-5"><i class="fas fa-money-bill-wave"></i></div>
                <div class="payment-bg-icon payment-icon-6"><i class="fas fa-university"></i></div>
                <div class="payment-bg-icon payment-icon-7"><i class="fas fa-exchange-alt"></i></div>
                <div class="payment-bg-icon payment-icon-8"><i class="fas fa-shield-alt"></i></div>
                <div class="payment-bg-icon payment-icon-9"><i class="fas fa-lock"></i></div>
                <div class="payment-bg-icon payment-icon-10"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </section>

    <!-- Block 7: FAQ -->
    <section class="faq-section pattern-bg" id="faq">
        <div class="container">
            <div class="section-badge">
                <i class="fas fa-question-circle"></i>
                <span>Ответы на все ваши вопросы</span>
            </div>

            <div class="section-header">
                <h2 class="section-title">Часто задаваемые вопросы</h2>
                <p class="section-description">
                    Здесь собраны быстрые ответы на ваши вопросы о SOCNET SMM — прочитайте и оформите свой первый заказ уже
                    сейчас!
                </p>
            </div>

            <div class="faq-accordion">
                <div class="faq-column">

                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Что такое SMM панель?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Это автоматизированная платформа, где вы покупаете подписчиков, лайки, просмотры и другие услуги для своих аккаунтов соцсетей. Заказ обрабатывается без ручного вмешательства.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Какие услуги доступны в SOCNET SMM?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Подписчики, лайки, просмотры, комментарии, участники каналов — более 20 соцсетей, включая
                                Instagram, TikTok, YouTube, Telegram и Spotify.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Безопасно ли пользоваться такими услугами?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Да. Мы применяем методы, которые не приводят к блокировкам; за годы работы у клиентов не было
                                санкций за использование нашей панели.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Как зарегистрироваться?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Нажмите «Регистрация», заполните форму и подтвердите e-mail — на всё уйдёт меньше минуты.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Как пополнить баланс?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Войдите в личный кабинет, нажмите на свой баланс, укажите сумму и оплатите любым удобным
                                платёжным методом.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-column">
                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Как оформить заказ?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Выберите услугу, вставьте ссылку на контент, укажите количество и нажмите «Купить».</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Можно ли перепродавать ваши услуги?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Да. У нас есть цены для реселлеров и полноценный API для интеграции.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Предлагаете ли таргетированные услуги?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Да, можно выбрать страну или демографию и получить именно ту аудиторию, которая нужна.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Почему стоит выбрать SOCNET SMM?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Минимальные цены от $0.01, авто-возвраты, 24/7 поддержка и каталог, который обновляется
                                каждые 6 часов.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" data-toggle="faq">
                            <h3>Что такое магазин SOCNET STORE?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Это наш топовый магазин по продаже аккаунтов социальных сетей, мессенджеров, электронных
                                почт, прокси, VPN-клиентов и других полезных решений для ваших задач.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Background Elements -->
            <div class="faq-bg-elements">
                <div class="faq-bg-icon faq-icon-1">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="faq-bg-icon faq-icon-2">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="faq-bg-icon faq-icon-3">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="faq-bg-icon faq-icon-4">
                    <i class="fas fa-star"></i>
                </div>
                <div class="faq-bg-icon faq-icon-5">
                    <i class="fas fa-search"></i>
                </div>
                <div class="faq-bg-icon faq-icon-6">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="faq-bg-icon faq-icon-7">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="faq-bg-icon faq-icon-8">
                    <i class="fas fa-magic"></i>
                </div>
                <div class="faq-bg-icon faq-icon-9">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="faq-bg-icon faq-icon-10">
                    <i class="fas fa-gem"></i>
                </div>
                <div class="faq-bg-icon faq-icon-11">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                <div class="faq-bg-icon faq-icon-12">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Block 8: News -->
    <section class="news-section enhanced-gradient-bg" id="news">
        <!-- Decorative Background Icons -->
        <div class="news-bg-icons">
            <i class="fas fa-newspaper news-bg-icon news-icon-1"></i>
            <i class="fas fa-rss news-bg-icon news-icon-2"></i>
            <i class="fas fa-book-open news-bg-icon news-icon-3"></i>
            <i class="fas fa-bullhorn news-bg-icon news-icon-4"></i>
            <i class="fas fa-globe news-bg-icon news-icon-5"></i>
            <i class="fas fa-pen-fancy news-bg-icon news-icon-6"></i>
            <i class="fas fa-scroll news-bg-icon news-icon-7"></i>
            <i class="fas fa-feather-alt news-bg-icon news-icon-8"></i>
            <i class="fas fa-comment-dots news-bg-icon news-icon-9"></i>
            <i class="fas fa-star news-bg-icon news-icon-10"></i>
            <i class="fas fa-heart news-bg-icon news-icon-11"></i>
            <i class="fas fa-bookmark news-bg-icon news-icon-12"></i>
        </div>

        <div class="container">
            <div class="section-badge">
                <i class="fas fa-newspaper"></i>
                <span>Будьте в курсе обновлений</span>
            </div>

            <div class="section-header">
                <h2 class="section-title">Новости и блог</h2>
                <p class="section-description">
                    Читайте свежие новости о SOCNET SMM, обновления сервиса и полезные советы из первого источника.
                </p>
            </div>

            <!-- Swiper Navigation (на уровне контейнера) -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>

            <div class="news-swiper swiper">
                <div class="swiper-wrapper">

                    @foreach ($newsList as $news)
                        <div class="swiper-slide">
                            <a href="{{ route('news.show', $news['slug']) }}" class="news-list-card">
                                <div class="news-list-image">
                                    <img src="{{ asset($news['image']) }}" alt="{{ $news['title'] }}">
                                </div>
                                <div class="news-list-content">
                                    <div class="news-list-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($news['date'])->format('d.m.Y') }}
                                    </div>
                                    <h3 class="news-list-title">{{ $news['title'] }}</h3>
                                    <p class="news-list-excerpt">{{ $news['excerpt'] }}</p>
                                    {{-- <span class="news-list-link">
                                    <i class="fas fa-arrow-right"></i>
                                    Читать далее
                                </span> --}}
                                </div>
                            </a>
                        </div>
                    @endforeach

                </div>

                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>
            </div>

            <div class="news-all-link">
                <a href="{{ route('news.index') }}" class="btn-news-all">
                    <i class="fas fa-newspaper"></i>
                    Читать все новости
                </a>
            </div>
        </div>
    </section>

    <!-- Block 9: Reviews -->
    <section class="reviews-section particles-bg" id="reviews">
        <div class="container">
            <div class="section-badge">
                <i class="fas fa-star"></i>
                <span>Реальное мнение клиентов</span>
            </div>

            <div class="section-header">
                <h2 class="section-title">Отзывы и мнения клиентов</h2>
                <p class="section-description">
                    Реальные истории успеха пользователей SOCNET SMM — узнайте, как панель помогает им расти каждый день.
                </p>
            </div>

            <div class="reviews-grid">
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">
                                <img src="{{ asset('assets/images/avatar.jpg') }}" alt="Katrina D'Souza">
                            </div>
                            <div>
                                <div class="reviewer-name">Katrina D'Souza</div>
                                <div class="review-date">2 месяца назад</div>
                            </div>
                        </div>
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="review-text">
                        Тестировал подписчиков в Instagram: запуск < 10 минут, доставка моментальная. Отличное решение для
                            агентств! </p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">
                                <img src="{{ asset('assets/images/avatar-2.jpg') }}" alt="Katrina D'Souza">
                            </div>
                            <div>
                                <div class="reviewer-name">Ali Dawoud</div>
                                <div class="review-date">1 неделю назад</div>
                            </div>
                        </div>
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="review-text">
                        Лучшая панель, что пробовала: цены супер, интерфейс простой, услуги постоянно обновляются. Так
                        держать!
                    </p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">
                                <img src="{{ asset('assets/images/avatar-3.jpg') }}" alt="Katrina D'Souza">
                            </div>
                            <div>
                                <div class="reviewer-name">Jessica Walker</div>
                                <div class="review-date">позавчера</div>
                            </div>
                        </div>
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="review-text">
                        С новым YouTube-каналом набрал 1 000 подписчиков за сутки через Drip-Feed. SOCNET SMM реально
                        ускоряет рост.
                    </p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">SI</div>
                            <div>
                                <div class="reviewer-name">Sergey Ivanov</div>
                                <div class="review-date">1 день назад</div>
                            </div>
                        </div>
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="review-text">
                        Веду пять аккаунтов инфлюенсеров; через API SOCNET SMM автоматизировал заказы и снизил расходы на 40
                        %. Метрики держатся стабильно.
                    </p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">LW</div>
                            <div>
                                <div class="reviewer-name">Li Wei</div>
                                <div class="review-date">3 дня назад</div>
                            </div>
                        </div>
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="review-text">
                        Управляю 5 аккаунтами, через API автоматизировал всё и сэкономил 40%.
                    </p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">MK</div>
                            <div>
                                <div class="reviewer-name">Maria Kowalski</div>
                                <div class="review-date">1 год назад</div>
                            </div>
                        </div>
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="review-text">
                        Отличная поддержка и качественные услуги. Рекомендую всем блогерам!
                    </p>
                </div>
            </div>

            <!-- Reviews Background Elements -->
            <div class="reviews-bg-elements">
                <div class="reviews-bg-icon reviews-icon-1">
                    <i class="fas fa-quote-left"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-2">
                    <i class="fas fa-star"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-3">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-4">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-5">
                    <i class="fas fa-comment"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-6">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-7">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-8">
                    <i class="fas fa-smile"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-9">
                    <i class="fas fa-users"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-10">
                    <i class="fas fa-award"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-11">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="reviews-bg-icon reviews-icon-12">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>
    </section>


    @guest
        @include('guest.app.components.cta-guest')
    @endguest

@endsection

@section('scripts')
    <script>
        // Page-specific functionality will be handled by the main app.js
        console.log('Homepage loaded successfully');
    </script>
@endsection
