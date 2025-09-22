@extends('guest.app.layout')

@section('title', 'Политика конфиденциальности - SOCNET SMM')
@section('description', 'Политика конфиденциальности сервиса SOCNET SMM')

@section('content')

    <!-- Rules Hero Section -->
    {{-- <section class="rules-hero-section">
        <div class="container">
            <div class="rules-hero-content">
                <h1 class="rules-hero-title">{{ __('Политика конфиденциальности') }}</h1>
                <p class="rules-hero-description">
                    {{ __('Ознакомьтесь с общими положениями, правилами оказания услуг, денежной политикой и ответственность нашего сервиса.') }}
                </p>
            </div>
        </div>
    </section> --}}

    <section class="rules-hero-section">
        <div class="container">
            <div class="rules-hero-content">
                <h1 class="rules-hero-title">{{ __('Политика конфиденциальности') }}</h1>
                <p class="rules-hero-description">
                    {{ __('Настоящая Политика описывает, какие персональные данные обрабатывает SOCNET SMM (далее — «Платформа», «мы», «сервис»), с какой целью, как долго хранит и как обеспечивает их защиту. Политика действует в отношении всей информации, получаемой от пользователей сайта socnet.pro (smm.socnet.pro) и связанных с ним сервисов.') }}
                </p>
            </div>
        </div>

        <!-- Background Rules Hero Icons -->
        <div class="rules-hero-bg-icons">
            <div class="rules-hero-bg-icon rules-hero-icon-1"><i class="fas fa-gavel"></i></div>
            <div class="rules-hero-bg-icon rules-hero-icon-2"><i class="fas fa-balance-scale"></i></div>
            <div class="rules-hero-bg-icon rules-hero-icon-3"><i class="fas fa-shield-alt"></i></div>
            <div class="rules-hero-bg-icon rules-hero-icon-4"><i class="fas fa-file-contract"></i></div>
            <div class="rules-hero-bg-icon rules-hero-icon-5"><i class="fas fa-handshake"></i></div>
            <div class="rules-hero-bg-icon rules-hero-icon-6"><i class="fas fa-clipboard-check"></i></div>
        </div>
    </section>

    <!-- Rules Main Section -->
    <section class="rules-main-section">
        <div class="container">

            <!-- Tab Content -->
            <div class="rules-content">
                <div class="rules-content-body">
                    <div class="rules-section">
                        <h3>{{ __('1. Общие положения') }}</h3>
                        <ul class="rules-list">
                            <li>
                                1.1 <b>Правовые основы</b>. При обработке персональных данных мы руководствуемся:
                                <ul>
                                    <li>
                                        — Федеральным законом РФ № 152-ФЗ «О персональных данных»;
                                    </li>
                                    <li>
                                        — GDPR (ЕС);
                                    </li>
                                    <li>
                                        — законами США о защите персональных данных.
                                    </li>
                                </ul>
                            </li>
                            <li>
                                1.2 <b>Согласие пользователя</b>. Используя сайт, создавая аккаунт или размещая заказ, вы
                                подтверждаете, что прочитали и приняли настоящую Политику конфиденциальности.
                            </li>
                            <li>
                                1.3. <b>Изменения</b>. Мы можем вносить изменения без предварительного уведомления клиента.
                                Актуальная версия публикуется на сайте с указанием даты внесений изменений Политики
                                конфиденциальности; продолжение использования сервиса означает ваше согласие с текущими
                                изменениями. Мы незамедлительно уведомим вас о любом нарушении ваших персональных данных,
                                которое может привести к высокому риску для ваших прав и свобод.
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('2. Цели обработки данных') }}</h3>
                        <ul class="rules-list">
                            <li>
                                2.1 Регистрация и идентификация пользователя.
                            </li>
                            <li>
                                2.2 Исполнение заказов, учёт транзакций и начисление бонусов.
                            </li>
                            <li>
                                2.3 Поддержка клиентов, урегулирование споров, обработка обращений.
                            </li>
                            <li>
                                2.4 Улучшение качества сервиса и аналитика трафика.
                            </li>
                            <li>
                                2.5 Маркетинговые рассылки (только при согласии пользователя).
                            </li>
                            <li>
                                2.6 Соблюдение требований законодательства и предотвращение мошенничества.
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('3. Какие данные мы собираем') }}</h3>
                        <ul class="rules-list">
                            <li>
                                3.1 <b>Учётные данные</b>: имя (ник), email, IP-адрес, данные авторизации (хэш-пароль,
                                токены).
                            </li>
                            <li>
                                3.2 <b>Платёжные данные</b>: история пополнений и выводов, суммы пополнений.
                            </li>
                            <li>
                                3.3 <b>Данные заказов</b>: ссылки на соцсети, выбранные услуги, объёмы услуг, статус.
                            </li>
                            <li>
                                3.4 <b>Файлы Cookie и технические логи</b>: тип устройства, браузер, referrer, время
                                посещения, показатели страницы (используем Google Analytics / Yandex Metrica).
                            </li>
                            <li>
                                3.5 <b>Переписка</b>: тикеты, чат с поддержкой, отзывы об услугах.
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('4. Законные основания обработки') }}</h3>
                        <ul class="rules-list">
                            <li>
                                4.1 Исполнение договора оферты (оказание услуг).
                            </li>
                            <li>
                                4.2 Законные интересы Платформы (защита от мошенничества, развитие сервиса).
                            </li>
                            <li>
                                4.3 Согласие субъекта данных (маркетинговые уведомления).
                            </li>
                            <li>
                                4.4 Выполнение обязанностей по закону (налоговые, бухгалтерские и AML-требования).
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('5. Передача и доступ к персональным данным') }}</h3>
                        <ul class="rules-list">
                            <li>
                                5.1 <b>Сотрудникам Платформы</b> — строго по принципу «need-to-know».
                            </li>
                            <li>
                                5.2 <b>Партнёрам-поставщикам услуг</b> (платёжные шлюзы, хостинг, аналитика) — при условии
                                соблюдения ими конфиденциальности и применения адекватных мер защиты.
                            </li>
                            <li>
                                5.3 <b>Государственным органам</b> — только на основании законного запроса.
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('6. Файлы Cookie') }}</h3>
                        <ul class="rules-list">
                            <li>
                                6.1 Используем <b>сессионные</b> cookies (авторизация, выбор языка) и <b>аналитические</b>
                                cookies (Google Analytics, Yandex Metrica).
                            </li>
                            <li>
                                6.2 Пользователь может удалить или заблокировать cookies в настройках браузера; при этом
                                часть функций сайта может стать недоступной.
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('7. Хранение и защита данных') }}</h3>
                        <ul class="rules-list">
                            <li>
                                7.1 Данные хранятся на защищённых серверах в дата-центрах различных стран мира.
                            </li>
                            <li>
                                7.2 Внедрены меры: HTTPS/SSL, шифрование паролей (bcrypt), двухфакторная аутентификация,
                                журналы доступа, регулярные резервные копии.
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('8. Права пользователя') }}</h3>
                        <ul class="rules-list">
                            <li>
                                8.1 Получать информацию о хранимых данных (право доступа).
                            </li>
                            <li>
                                8.2 Требовать исправления неточных или устаревших данных.
                            </li>
                            <li>
                                8.3 Запрашивать удаление конфиденциальной информации (право на забвение), кроме случаев,
                                когда хранение обязательно по закону.
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('9. Детали KYC и AML') }}</h3>
                        <ul class="rules-list">
                            <li>
                                9.1 В отдельных случаях (крупные платежи, партнёрская программа) мы вправе запросить
                                документы для подтверждения личности и источника происхождения средств.
                            </li>
                            <li>
                                9.2 Документы проверяются вручную уполномоченным отделом, хранятся зашифрованно и удаляются
                                сразу же после проверки.
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('10. Ответственность сторон') }}</h3>
                        <ul class="rules-list">
                            <li>
                                10.1 Платформа обеспечивает разумные технические и организационные меры защиты.
                            </li>
                            <li>
                                10.2 Пользователь несет ответственность за сохранение конфиденциальности своей учетной
                                записи и пароля, а также за ограничение доступа к своему компьютеру или устройству, с
                                которого осуществляется вход в личный аккаунт.
                            </li>
                            <li>
                                10.3 В случае утечки по вине пользователя SOCNET SMM не несёт ответственности.
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <h3>{{ __('11. Контакты для связи') }}</h3>
                        <p>
                            По вопросам конфиденциальности, обращениям и отзывам согласия:
                        </p>
                        <ul class="rules-list">
                            <li>
                                <b>Telegram</b>: <a href="https://t.me/socnet_support" target="_blank">@socnet_support</a>
                            </li>
                            <li>
                                <b>WhatsApp</b>: <a href="https://wa.me/79051904467" target="_blank">+79051904467</a>
                            </li>
                            <li>
                                <b>Email</b>: <a href="mailto:help@socnet.pro">help@socnet.pro</a>
                            </li>
                        </ul>
                    </div>

                    <div class="rules-section">
                        <p>
                            Используя сервисы SOCNET SMM, вы подтверждаете, что прочитали и поняли данную Политику
                            конфиденциальности.
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Background Rules Main Icons -->
        <div class="rules-main-bg-icons">
            <div class="rules-main-bg-icon rules-main-icon-1"><i class="fas fa-book"></i></div>
            <div class="rules-main-bg-icon rules-main-icon-2"><i class="fas fa-scroll"></i></div>
            <div class="rules-main-bg-icon rules-main-icon-3"><i class="fas fa-pen-fancy"></i></div>
            <div class="rules-main-bg-icon rules-main-icon-4"><i class="fas fa-lightbulb"></i></div>
            <div class="rules-main-bg-icon rules-main-icon-5"><i class="fas fa-question-circle"></i></div>
            <div class="rules-main-bg-icon rules-main-icon-6"><i class="fas fa-info-circle"></i></div>
            <div class="rules-main-bg-icon rules-main-icon-7"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="rules-main-bg-icon rules-main-icon-8"><i class="fas fa-check-double"></i></div>
        </div>
    </section>

    @guest
        @include('guest.app.components.cta-guest')
    @endguest

@endsection

@section('scripts')
    <script type="text/javascript" src="{{ mix('assets/guest/js/pages/rules.js') }}"></script>
@endsection
