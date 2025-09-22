@section('title', 'Реферальная программа - SOCNET SMM')
@extends('user.app.layout')

@section('content')

<div class="referral-page">
    <!-- Header -->
    <div class="referral-header">
        <h1 class="referral-title">{{ __('Реферальная программа') }}</h1>
    </div>

    <!-- Referral Link Section -->
    <div class="referral-link-section">
        <div class="referral-link-card">
            <h3 class="section-title">{{ __('Ваша реферальная ссылка') }}</h3>
            <div class="referral-link-container">
                <div class="referral-link-input">
                    <input type="text"
                            id="referralLink"
                            value="{{ $referralLink ?? 'https://socnet-smm.com/ref/abc123' }}"
                            readonly
                            class="link-field">
                    <button type="button"
                            class="copy-btn"
                            onclick="copyReferralLink()">
                        <i class="fas fa-copy"></i>
                        <span class="copy-text">{{ __('Копировать') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="referral-stats-section">
        <h3 class="section-title">{{ __('Статистика') }}</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $referralStats['registrations'] ?? '10' }}</div>
                    <div class="stat-label">{{ __('Регистраций') }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">${{ $referralStats['earnings'] ?? '125.00' }}</div>
                    <div class="stat-label">{{ __('Заработано') }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $referralPercent ?? '5' }}%</div>
                    <div class="stat-label">{{ __('Ваш процент') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- How it works Section -->
    <div class="referral-rules-section">
        <h3 class="section-title">{{ __('Правила реферальной системы SOCNET SMM') }}</h3>
        <div class="rules-content">
            <div class="rule-section">
                <h4 class="rule-title">1. Регистрация в программе</h4>
                <div class="rule-items">
                    <p class="rule-item">1.1. Вы принимаете данные правила при создании реферального аккаунта.</p>
                    <p class="rule-item">1.2. Один пользователь — один реферальный аккаунт.</p>
                </div>
            </div>

            <div class="rule-section">
                <h4 class="rule-title">2. Размер и выплата комиссий</h4>
                <div class="rule-items">
                    <p class="rule-item">2.1. Стандартная ставка: 5% от чистой суммы всех заказов привлечённого клиента (ставка отображается в личном кабинете).</p>
                    <p class="rule-item">2.2. Весь заработок с рефералов переводится в автоматическом режиме на баланс вашего реферального аккаунта для покупок услуг в SOCNET SMM.</p>
                    <p class="rule-item">2.3. Саморефералы запрещены: комиссия за собственные платежи не начисляется.</p>
                </div>
            </div>

            <div class="rule-section">
                <h4 class="rule-title">3. Допустимые методы продвижения</h4>
                <div class="rule-items">
                    <p class="rule-item">3.1. Размещение текстовых и графических ссылок на сайте, в соцсетях, e-mail-рассылках.</p>
                    <p class="rule-item">3.2. Собственные баннеры допустимы, если не вводят в заблуждение.</p>
                    <p class="rule-item">3.3. Запрещены спам, массовые нецелевые рассылки и публикации на ресурсах с нелегальным контентом.</p>
                    <p class="rule-item">3.4. Контекстная реклама (PPC) по брендовым ключам «SOCNET SMM» разрешена только с дополнительного согласия с администрацией сервиса.</p>
                </div>
            </div>

            <div class="rule-section">
                <h4 class="rule-title">4. Причины блокировки реферального аккаунта</h4>
                <div class="rule-items">
                    <p class="rule-item">– ложная реклама, заведомо неверные обещания;</p>
                    <p class="rule-item">– нарушение авторских прав и использование чужих торговых марок без разрешения;</p>
                    <p class="rule-item">– подделка трафика, «накрутка» заказов, саморефералы;</p>
                    <p class="rule-item">– любые действия, наносящие репутационный или финансовый ущерб SOCNET SMM.</p>
                </div>
            </div>

            <div class="rule-section">
                <h4 class="rule-title">5. Ограничение ответственности</h4>
                <div class="rule-items">
                    <p class="rule-item">SOCNET SMM не отвечает за косвенные убытки партнера по привлечению рефералов (потерю прибыли, сбои трекинга, работу сторонних платёжных систем).</p>
                </div>
            </div>

            <div class="rule-section">
                <h4 class="rule-title">6. Срок действия и изменение правил</h4>
                <div class="rule-items">
                    <p class="rule-item">6.1. Соглашение действует с момента одобрения заявки и до закрытия вашего аккаунта.</p>
                    <p class="rule-item">6.2. Мы можем обновлять правила: вы автоматически соглашаетесь с обновленными условиями реферальной программы, продолжая пользоваться услугами SOCNET SMM.</p>
                </div>
            </div>

            <div class="rule-section">
                <h4 class="rule-title">7. Ваше согласие</h4>
                <div class="rule-items">
                    <p class="rule-item">Принимая участие в реферальной программе, вы автоматически подтверждаете свое согласие со всеми пунктами действующих правил.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script type="text/javascript" src="{{ mix('assets/user/js/pages/referral.js') }}"></script>
@endpush

@endsection
