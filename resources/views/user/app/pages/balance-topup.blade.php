@section('title', 'Пополнение баланса - SOCNET SMM')
@extends('user.app.layout')

@section('content')
    <div class="balance-topup-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ __('Пополнение баланса') }}</h1>
        </div>

        <!-- Current Balance -->
        <div class="current-balance-section">
            <div class="balance-card">
                <div class="balance-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="balance-content">
                    <div class="balance-label">{{ __('Текущий баланс') }}</div>
                    <div class="balance-value" data-currency="{{ $currCode }}">{{ $symbol }}{{ $converted }}</div>
                </div>
            </div>
        </div>

        <!-- Top-up Form -->
        <div class="topup-form-section">
            <div class="form-container">
                <h2 class="section-title">{{ __('Пополнить баланс') }}</h2>

                <form id="topupForm" class="topup-form">
                    @csrf

                    <!-- Payment Method Selection -->
                    <div class="form-group">
                        <label class="form-label">{{ __('Способ оплаты') }}</label>

                        <!-- Payment Methods Cards Grid -->
                        <div class="payment-methods-grid">
                            @foreach(config('payments.methods', []) as $code => $m)
                                @php($disabled = !empty($m['disabled']))
                                <div class="payment-method-card {{ $disabled ? 'disabled' : '' }}" data-method="{{ $code }}" data-fee="{{ $m['fee'] ?? 0 }}" data-auto="{{ !empty($m['auto']) ? 'true' : 'false' }}" data-pay-currency="{{ $m['pay_currency'] ?? 'usd' }}">
                                    <div class="method-icon">
                                        <img src="{{ asset($m['icon']) }}" alt="{{ $code }}">
                                    </div>
                                    <div class="method-info">
                                        <h4 class="method-name">{{ $m['name'] }}</h4>
                                        <p class="method-description">{{ __($m['description']) }}</p>
                                        <div class="method-fee">Комиссия: {{ number_format((float)($m['fee'] ?? 0), 1) }}%</div>
                                    </div>
                                    <div class="method-status">
                                        @if($disabled)
                                            <i class="fas fa-exclamation-triangle unavailable" title="{{ __('Данный платежный метод временно недоступен') }}"></i>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Hidden select for form submission (commented out but preserved) -->

                        <div class="payment-methods-select custom-select-wrapper">
                            <select id="paymentMethod" class="custom-select payment-method-select"
                                data-placeholder="Выберите способ оплаты">
                                <option value="" data-icon="fas fa-wallet">{{ __('Выберите способ оплаты') }}</option>
                                @foreach(config('payments.methods', []) as $code => $m)
                                    <option value="{{ $code }}" data-fee="{{ $m['fee'] ?? 0 }}" data-auto="{{ !empty($m['auto']) ? 'true' : 'false' }}" data-icon="{{ asset($m['icon']) }}" data-pay-currency="{{ $m['pay_currency'] ?? 'usd' }}" {{ !empty($m['disabled']) ? 'disabled' : '' }}>
                                        {{ $m['name'] }} ({{ __($m['description']) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Hidden input for form submission -->
                        <input type="hidden" id="selectedPaymentMethod" name="paymentMethod" value="">
                    </div>

                    <!-- Amount Input -->
                    <div class="form-group">
                        <label for="amount" class="form-label">{{ __('Сумма пополнения') }}</label>
                        <div class="amount-input-container">
                            <div class="currency-symbol">$</div>
                            <input type="number" id="amount" class="form-input amount-input" placeholder="100"
                                min="10" max="50000" oninput="calculateBonus()" required>
                        </div>
                        <div class="amount-info">
                            <div class="amount-limits">{{ __('Минимум: $10, Максимум: $50,000') }}</div>
                        </div>
                    </div>

                    <!-- Bonus Display -->
                    <div class="bonus-display" id="bonusDisplay" style="display: none;">
                        <div class="bonus-item">
                            <span class="bonus-label">{{ __('Бонус:') }}</span>
                            <span class="bonus-value" id="bonusAmount">$0.00</span>
                        </div>
                        <div class="bonus-item">
                            <span class="bonus-label">{{ __('Комиссия:') }}</span>
                            <span class="bonus-value" id="feeAmount">$0.00</span>
                        </div>
                        <div class="bonus-item total">
                            <span class="bonus-label">{{ __('Сумма к оплате:') }}</span>
                            <span class="bonus-value" id="totalAmount">$0.00</span>
                        </div>
                    </div>

                    <!-- Promo Code Section -->
                    <div class="promo-section">
                        <button type="button" class="promo-btn" data-bs-toggle="modal" data-bs-target="#promoModal">
                            <i class="fas fa-tag"></i>
                            {{ __('Активировать промокод') }}
                        </button>
                        <div class="active-promo" id="activePromo" style="display: none;">
                            <span class="promo-code-text">{{ __('Промокод активирован:') }} <strong
                                    id="activePromoCode"></strong></span>
                            <span class="promo-bonus">{{ __('Бонус:') }} <strong
                                    id="promoBonusAmount">$0.00</strong></span>
                            <button type="button" class="promo-remove" onclick="removePromo()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-submit">
                        <button type="submit" class="btn btn-primary pay-btn" id="payBtn" disabled>
                            <i class="fas fa-credit-card"></i>
                            {{ __('Пополнить баланс') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="history-section">
            <div class="history-container">
                <h2 class="section-title">{{ __('История транзакций') }}</h2>

                <div class="table-container">
                    <table class="transactions-table" id="transactionsTable">
                        <thead>
                            <tr>
                                <th class="col-date">{{ __('Дата') }}</th>
                                <th class="col-amount">{{ __('Сумма') }}</th>
                                <th class="col-method">{{ __('Метод') }}</th>
                                <th class="col-status">{{ __('Статус') }}</th>
                                <th class="col-actions">{{ __('Действия') }}</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTableBody">
                            <!-- Будет заполнено JavaScript -->
                        </tbody>
                    </table>

                    <!-- Empty State -->
                    <div class="empty-state" id="emptyState" style="display: none;">
                        <div class="empty-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <h3 class="empty-title">{{ __('Нет транзакций') }}</h3>
                        <p class="empty-description">
                            {{ __('Ваши транзакции будут отображаться здесь после первого пополнения') }}</p>
                    </div>

                    <!-- Pagination for Transactions -->
                    <div class="pagination-container" id="transactionsPagination">
                        <!-- Pagination component will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <!-- Promo Code Modal -->
    <div class="modal fade balance-topup-modal" id="promoModal" tabindex="-1" aria-labelledby="promoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promoModalLabel">
                        <i class="fas fa-tag"></i>
                        {{ __('Активировать промокод') }}
                    </h5>
                </div>
                <div class="modal-body">
                    <form id="promoForm">
                        <div class="form-group">
                            <label for="promoCode" class="form-label">{{ __('Промокод') }}</label>
                            <input type="text" id="promoCode" class="form-input"
                                placeholder="{{ __('Введите промокод') }}" required>
                        </div>
                        <div class="promo-examples">
                            <small class="text-muted">{{ __('Примеры: BONUS10, WELCOME50, SAVE20') }}</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                        {{ __('Отмена') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="activatePromoBtn" onclick="activatePromo()">
                        <i class="fas fa-check"></i>
                        {{ __('Активировать') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Payment Instructions Modal -->
    <div class="modal fade balance-topup-modal" id="manualPaymentModal" tabindex="-1"
        aria-labelledby="manualPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualPaymentModalLabel">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Инструкции по оплате') }}
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="payment-instructions">
                        <div class="instruction-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h6>{{ __('Переведите точную сумму') }}</h6>
                                <p class="payment-amount">{{ __('Сумма к оплате:') }} <strong
                                        id="paymentAmountDisplay">$0.00</strong></p>
                            </div>
                        </div>

                        <div class="instruction-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h6>{{ __('На указанные реквизиты') }}</h6>
                                <div class="payment-details" id="paymentDetails">
                                    <!-- Payment details will be populated by JS -->
                                </div>
                            </div>
                        </div>

                        <div class="instruction-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h6>{{ __('Подтвердите оплату') }}</h6>
                                <p>{{ __('После перевода нажмите кнопку "Оплатил" ниже') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-clock"></i>
                        {{ __('Обработка ручных платежей может занять до 24 часов') }}
                    </div>

                    <!-- Receipt Upload -->
                    <div class="receipt-upload">
                        <label class="form-label" for="receiptFile">{{ __('Подтверждение оплаты (чек)') }}</label>

                        <div class="receipt-dropzone" id="receiptDropzone">
                            <div class="dropzone-icon">
                                <i class="fas fa-file-upload"></i>
                            </div>
                            <div class="dropzone-text">
                                <strong>{{ __('Перетащите файл сюда') }}</strong>
                                <span>{{ __('или нажмите для выбора') }}</span>
                            </div>
                        </div>

                        <input type="file" id="receiptFile" accept=".jpg,.jpeg,.png,.pdf" style="display: none;">

                        <div class="receipt-file-info" id="receiptFileInfo" style="display: none;">
                            <div class="file-meta">
                                <i class="fas fa-paperclip"></i>
                                <span id="receiptFileName"></span>
                                <button type="button" class="receipt-remove" id="removeReceipt" aria-label="{{ __('Удалить файл') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="receipt-preview" id="receiptPreview" style="display: none;"></div>
                        </div>

                        <small>{{ __('Поддерживаемые форматы: JPG, PNG, PDF. До 10 МБ.') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                        {{ __('Отмена') }}
                    </button>
                    <button type="button" class="btn btn-success" id="confirmManualPayment"
                        onclick="confirmManualPayment()">
                        <i class="fas fa-check"></i>
                        {{ __('Я оплатил') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="{{ mix('assets/user/js/pages/balance.js') }}"></script>
    <script>
        window.SocnetApp = window.SocnetApp || {};
        SocnetApp.currency = {
            base: 'usd',
            symbols: @json(array_map(fn($c) => $c['symbol'] ?? '$', config('settings.currency.available', []))),
            rates: @json(config('settings.currency.rates', []))
        };
        SocnetApp.payments = {
            methods: @json(collect(config('payments.methods', []))->map(fn($m) => ['name' => $m['name'] ?? ''])->toArray())
        };
    </script>
@endpush
