/**
 * Balance Top-up Page Manager
 * Управление страницей пополнения баланса
 */
class BalanceTopupManager {
    constructor() {
        this.currentAmount = 0;
        this.currentMethod = null;
        this.currentFee = 0;
        this.currentBonus = 0;
        this.activePromo = null;
        this.promoBonus = 0;
        this.isUpdatingSelect = false; // Flag to prevent infinite recursion

        // Transactions loaded from backend
        this.transactions = [];

        // Pagination settings for transactions
        this.transactionsPagination = null;
        this.transactionsCurrentPage = 1;
        this.transactionsPerPage = 10;

        // Available promo codes
        this.promoCodes = {
            'BONUS10': { type: 'fixed', value: 10, description: 'Бонус $10' },
            'WELCOME50': { type: 'fixed', value: 50, description: 'Приветственный бонус $50' },
            'SAVE20': { type: 'percent', value: 20, description: 'Скидка 20%' },
            'NEWUSER': { type: 'fixed', value: 25, description: 'Для новых пользователей $25' },
            'VIP5': { type: 'percent', value: 5, description: 'VIP скидка 5%' }
        };

        this.init();
    }

    init() {
        try {
            this.bindEvents();
            this.fetchTransactions();
            this.initializeForm();
            this.initReceiptUpload();
            this.initializeCustomSelects();
            this.initializePaymentMethodSelect();
        } catch (error) {
        }
    }

    // Helpers for currency formatting and conversion
    getCurrentPayCurrency() {
        if (this.currentMethod && this.currentMethod.payCurrency) {
            return String(this.currentMethod.payCurrency).toLowerCase();
        }
        return 'usd';
    }

    getRate(code) {
        try {
            const rates = (window.SocnetApp && SocnetApp.currency && SocnetApp.currency.rates) ? SocnetApp.currency.rates : {};
            const rate = parseFloat(rates[String(code).toLowerCase()] ?? 1);
            return isNaN(rate) || rate <= 0 ? 1 : rate;
        } catch (e) { return 1; }
    }

    getSymbol(code) {
        try {
            const symbols = (window.SocnetApp && SocnetApp.currency && SocnetApp.currency.symbols) ? SocnetApp.currency.symbols : {};
            return symbols[String(code).toLowerCase()] || '';
        } catch (e) { return ''; }
    }

    getPrecision(code) {
        const c = String(code).toLowerCase();
        if (c === 'btc' || c === 'eth') return 8;
        return 2;
    }

    formatCurrency(amount, code, withSymbol = true) {
        const precision = this.getPrecision(code);
        const fixed = (parseFloat(amount) || 0).toFixed(precision);
        const symbol = withSymbol ? this.getSymbol(code) : '';
        if (symbol) return `${symbol}${fixed}`;
        return `${fixed} ${String(code).toUpperCase()}`;
    }

    // Compose display for amount to pay in method currency with approx USD
    composePayDisplay(totalUsd) {
        const payCode = this.getCurrentPayCurrency();
        const methodAmount = totalUsd * this.getRate(payCode);
        const methodStr = this.formatCurrency(methodAmount, payCode, true);
        const usdStr = `$${(totalUsd || 0).toFixed(2)}`;
        if (payCode === 'usd') return usdStr;
        return `${methodStr} (≈ ${usdStr})`;
    }

    initializeCustomSelects() {
        // Initialize custom selects if the component is available
        if (typeof CustomSelect !== 'undefined') {
        }

        if (typeof CustomSelect !== 'undefined' && typeof CustomSelect.initializeAll === 'function') {
            try {
                CustomSelect.initializeAll();

                // Check if instances were created
                setTimeout(() => {
                    if (CustomSelect.selects) {
                        if (Array.isArray(CustomSelect.selects)) {
                        } else if (CustomSelect.selects.size !== undefined) {
                        }
                    }
                    if (CustomSelect.instances) {
                        if (CustomSelect.instances.size !== undefined) {
                        }
                    }
                }, 100);
            } catch (error) {
            }
        } else {
            // Fallback: try to initialize after a short delay
            setTimeout(() => {
                if (typeof CustomSelect !== 'undefined' && typeof CustomSelect.initializeAll === 'function') {
                    try {
                        CustomSelect.initializeAll();
                    } catch (error) {
                    }
                }
            }, 100);
        }
    }

    // Initialize payment method select with proper data
    initializePaymentMethodSelect() {
        // Set initial value to empty
        this.isUpdatingSelect = true; // Set flag to prevent recursion
        $('.balance-topup-page #paymentMethod').val('');

        // Ensure the select is properly initialized
        if (typeof CustomSelect !== 'undefined') {
            // Reinitialize the select to ensure proper display
            setTimeout(() => {
                try {
                    const selectElement = $('.balance-topup-page #paymentMethod')[0];
                    let customSelectInstance = null;

                    // Try to find instance in different properties
                    if (CustomSelect.selects && Array.isArray(CustomSelect.selects)) {
                        for (const selectInstance of CustomSelect.selects) {
                            if (selectInstance && selectInstance.element === selectElement) {
                                customSelectInstance = selectInstance;
                                break;
                            }
                        }
                    } else if (CustomSelect.selects && CustomSelect.selects.has && CustomSelect.selects.has(selectElement)) {
                        customSelectInstance = CustomSelect.selects.get(selectElement);
                    } else if (CustomSelect.instances && CustomSelect.instances.has && CustomSelect.instances.has(selectElement)) {
                        customSelectInstance = CustomSelect.instances.get(selectElement);
                    }

                    if (customSelectInstance && typeof customSelectInstance.setValue === 'function') {
                        customSelectInstance.setValue('');
                        if (typeof customSelectInstance.updateDisplay === 'function') {
                            customSelectInstance.updateDisplay();
                        }
                    } else {
                        this.updateCustomSelectAlternative('');
                    }
                } catch (error) {
                    console.warn('Failed to update CustomSelect display:', error);
                    this.updateCustomSelectAlternative('');
                } finally {
                    // Reset flag after initialization
                    this.isUpdatingSelect = false;
                }
            }, 200);
        } else {
            // Reset flag if CustomSelect is not available
            setTimeout(() => {
                this.isUpdatingSelect = false;
            }, 100);
        }
    }

    // Backend: fetch transactions (no pagination)
    fetchTransactions() {
        get('/user/balance/transactions', (resp) => {
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (res && res.success) {
                    this.transactions = (res.items || []).map((t) => ({
                        id: t.id || `txn_${Date.now()}`,
                        date: t.created_at ? new Date(t.created_at) : new Date(),
                        amount: parseFloat(t.amount_usd || 0),
                        method: t.method || '',
                        status: t.status || 'completed',
                        statusText: this.getStatusText(t.status || 'completed')
                    }));
                    this.renderTransactions();
                } else {
                    this.transactions = [];
                    this.renderTransactions();
                }
            } catch (e) {
                this.transactions = [];
                this.renderTransactions();
            }
        }, () => {
            this.transactions = [];
            this.renderTransactions();
        });
    }

    // (pagination removed)

    // Initialize Bootstrap tooltips for payment method warning icons
    initializePaymentMethodTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const warningIcons = document.querySelectorAll('.payment-method-card .unavailable');
            warningIcons.forEach(icon => {
                // Destroy existing tooltip if it exists
                const existingTooltip = bootstrap.Tooltip.getInstance(icon);
                if (existingTooltip) {
                    existingTooltip.dispose();
                }

                // Create new tooltip
                new bootstrap.Tooltip(icon, {
                    trigger: 'hover',
                    placement: 'top',
                    html: false,
                    delay: { show: 200, hide: 0 }
                });
            });
        }
    }

    // Select payment method from card
    selectPaymentMethod(cardElement) {
        // Prevent infinite recursion
        if (this.isUpdatingSelect) {
            return;
        }


        // Remove previous selection and checkmarks
        $('.balance-topup-page .payment-method-card').removeClass('selected');

        // Clear checkmarks but preserve warning icons for disabled methods
        $('.balance-topup-page .payment-method-card .method-status').each(function () {
            const $status = $(this);
            const $card = $status.closest('.payment-method-card');

            // If card is disabled, keep the warning icon
            if ($card.hasClass('disabled')) {
                // Keep the warning icon
                return;
            }

            // Clear the status for non-disabled cards
            $status.empty();
        });

        // Add selection to clicked card
        cardElement.addClass('selected');

        // Add checkmark to selected method
        cardElement.find('.method-status').html('<i class="fas fa-check-circle available"></i>');

        // Get method data from card attributes
        const method = cardElement.data('method');
        const fee = parseFloat(cardElement.data('fee')) || 0;
        const auto = cardElement.data('auto') === true;
        const payCurrency = String(cardElement.data('pay-currency') || 'usd').toLowerCase();
        const disabled = cardElement.hasClass('disabled');

        // Update current method
        this.currentMethod = {
            id: method,
            name: cardElement.find('.method-name').text(),
            fee: fee,
            auto: auto,
            disabled: disabled,
            payCurrency: payCurrency
        };

        // Update hidden input for form submission
        $('#selectedPaymentMethod').val(method);

        // Update custom select to match card selection
        this.isUpdatingSelect = true; // Set flag to prevent recursion

        $('.balance-topup-page #paymentMethod').val(method);

        // Update custom select display if it's initialized
        if (typeof CustomSelect !== 'undefined') {
            try {
                const selectElement = $('.balance-topup-page #paymentMethod')[0];

                // Try to find instance in different properties
                let customSelectInstance = null;

                // Method 1: Try CustomSelect.instances (if it exists)
                if (CustomSelect.instances && CustomSelect.instances.has && CustomSelect.instances.has(selectElement)) {
                    customSelectInstance = CustomSelect.instances.get(selectElement);
                }
                // Method 2: Try CustomSelect.selects (based on logs - it's an array)
                else if (CustomSelect.selects && Array.isArray(CustomSelect.selects)) {
                    for (const selectInstance of CustomSelect.selects) {
                        if (selectInstance && selectInstance.element === selectElement) {
                            customSelectInstance = selectInstance;
                            break;
                        }
                    }
                }
                // Method 3: Try to find by iterating through selects (if it's a Map)
                else if (CustomSelect.selects && CustomSelect.selects.has && CustomSelect.selects.has(selectElement)) {
                    customSelectInstance = CustomSelect.selects.get(selectElement);
                }

                if (customSelectInstance && typeof customSelectInstance.setValue === 'function') {
                    customSelectInstance.setValue(method);
                    if (typeof customSelectInstance.updateDisplay === 'function') {
                        customSelectInstance.updateDisplay();
                    }
                } else {
                    // Try to find instance by different methods
                    this.updateCustomSelectAlternative(method);
                }
            } catch (error) {
                console.warn('Failed to update CustomSelect in selectPaymentMethod:', error);
                // Try alternative method
                this.updateCustomSelectAlternative(method);
            }
        } else {
            this.updateCustomSelectAlternative(method);
        }

        // Reset flag after a short delay to allow DOM updates
        setTimeout(() => {
            this.isUpdatingSelect = false;
        }, 100);

        // Update payment info
        this.updatePaymentInfo();

        if (typeof CustomSelect !== 'undefined') {
            const selectElement = $('.balance-topup-page #paymentMethod')[0];
            if (selectElement) {
            }
        }
    }

    // Alternative method to update CustomSelect when direct access fails
    updateCustomSelectAlternative(method) {
        try {
            // Method 1: Try to find instance by selector
            const selectElement = $('.balance-topup-page #paymentMethod')[0];
            if (selectElement) {
                // Try to access CustomSelect through jQuery data
                const $select = $(selectElement);
                const customSelectData = $select.data('custom-select');
                if (customSelectData) {
                    if (typeof customSelectData.setValue === 'function') {
                        customSelectData.setValue(method);
                        return;
                    }
                }

                // Method 2: Try to find instance by iterating through all instances
                if (CustomSelect.selects && Array.isArray(CustomSelect.selects)) {
                    for (const selectInstance of CustomSelect.selects) {
                        if (selectInstance && selectInstance.element) {
                            if (selectInstance.element === selectElement) {
                                if (typeof selectInstance.setValue === 'function') {
                                    selectInstance.setValue(method);
                                    if (typeof selectInstance.updateDisplay === 'function') {
                                        selectInstance.updateDisplay();
                                    }
                                    return;
                                }
                            }
                        }
                    }
                } else if (CustomSelect.selects && CustomSelect.selects.size > 0) {
                    for (const [element, instance] of CustomSelect.selects) {
                        if (element === selectElement) {
                            if (typeof instance.setValue === 'function') {
                                instance.setValue(method);
                                if (typeof instance.updateDisplay === 'function') {
                                    instance.updateDisplay();
                                }
                                return;
                            }
                        }
                    }
                } else if (CustomSelect.instances && CustomSelect.instances.size > 0) {
                    for (const [element, instance] of CustomSelect.instances) {
                        if (element === selectElement) {
                            if (typeof instance.setValue === 'function') {
                                instance.setValue(method);
                                if (typeof instance.updateDisplay === 'function') {
                                    instance.updateDisplay();
                                }
                                return;
                            }
                        }
                    }
                } else {
                }

                // Method 3: Try to trigger change event manually
                $select.val(method);
                $select.trigger('change');
            } else {
            }
        } catch (error) {
        }
    }

    // Clear payment method selection
    clearPaymentMethodSelection() {
        // Prevent infinite recursion
        if (this.isUpdatingSelect) {
            return;
        }

        // Remove selection from all cards
        $('.balance-topup-page .payment-method-card').removeClass('selected');

        // Clear checkmarks but preserve warning icons for disabled methods
        $('.balance-topup-page .payment-method-card .method-status').each(function () {
            const $status = $(this);
            const $card = $status.closest('.payment-method-card');

            // If card is disabled, keep the warning icon
            if ($card.hasClass('disabled')) {
                return;
            }

            // Clear the status for non-disabled cards
            $status.empty();
        });

        // Clear current method
        this.currentMethod = null;

        // Clear hidden input
        $('#selectedPaymentMethod').val('');

        // Clear custom select
        $('.balance-topup-page #paymentMethod').val('');

        // Update custom select display if it's initialized
        this.isUpdatingSelect = true; // Set flag to prevent recursion

        if (typeof CustomSelect !== 'undefined') {
            try {
                const selectElement = $('.balance-topup-page #paymentMethod')[0];
                let customSelectInstance = null;

                // Try to find instance in different properties
                if (CustomSelect.selects && Array.isArray(CustomSelect.selects)) {
                    for (const selectInstance of CustomSelect.selects) {
                        if (selectInstance && selectInstance.element === selectElement) {
                            customSelectInstance = selectInstance;
                            break;
                        }
                    }
                } else if (CustomSelect.selects && CustomSelect.selects.has && CustomSelect.selects.has(selectElement)) {
                    customSelectInstance = CustomSelect.selects.get(selectElement);
                } else if (CustomSelect.instances && CustomSelect.instances.has && CustomSelect.instances.has(selectElement)) {
                    customSelectInstance = CustomSelect.instances.get(selectElement);
                }

                if (customSelectInstance && typeof customSelectInstance.setValue === 'function') {
                    customSelectInstance.setValue('');
                } else {
                    this.updateCustomSelectAlternative('');
                }
            } catch (error) {
                console.warn('Failed to update CustomSelect in clearPaymentMethodSelection:', error);
                this.updateCustomSelectAlternative('');
            }
        } else {
            this.updateCustomSelectAlternative('');
        }

        // Reset flag after a short delay
        setTimeout(() => {
            this.isUpdatingSelect = false;
        }, 100);

        // Update payment info
        this.updatePaymentInfo();
    }

    bindEvents() {
        try {
            // Form submission
            $('.balance-topup-page #topupForm').on('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit();
            });

            // Payment method cards selection
            $('.balance-topup-page').on('click', '.payment-method-card:not(.disabled)', (e) => {
                e.preventDefault();
                this.selectPaymentMethod($(e.currentTarget));
            });

            // Initialize tooltips for unavailable methods
            this.initializePaymentMethodTooltips();

            // Payment method change (for custom select - synchronized with cards)
            $('.balance-topup-page #paymentMethod').on('change', (e) => {
                // Prevent infinite recursion
                if (this.isUpdatingSelect) {
                    return;
                }

                const selectedValue = $(e.target).val();

                if (selectedValue && selectedValue !== '') {
                    // Find corresponding card and select it
                    const cardElement = $(`.balance-topup-page .payment-method-card[data-method="${selectedValue}"]`);
                    if (cardElement.length) {
                        this.selectPaymentMethod(cardElement);
                    } else {
                    }
                } else {
                    // Clear selection
                    this.clearPaymentMethodSelection();
                }
            });

            // Amount input
            $('.balance-topup-page #amount').on('input', () => {
                this.calculateBonus();
            });

            // Promo code activation
            $('.balance-topup-modal #promoForm').on('submit', (e) => {
                e.preventDefault();
                this.activatePromo();
            });

            // Promo code modal events
            $('.balance-topup-modal#promoModal').on('shown.bs.modal', () => {
                $('.balance-topup-modal #promoCode').focus();
            });

            $('.balance-topup-modal#promoModal').on('hidden.bs.modal', () => {
                $('.balance-topup-modal #promoCode').val('');
            });

            // Quick amount buttons (if needed)
            this.createQuickAmountButtons();
        } catch (error) {
            console.error('Failed to bind events:', error);
        }
    }

    initializeForm() {
        // Set default state
        this.updatePayButton();
    }

    createQuickAmountButtons() {
        // Add quick amount buttons to the amount input group if needed
        const quickAmounts = [50, 100, 500, 1000];
        const amountGroup = $('.balance-topup-page .amount-input-container').parent();

        if (amountGroup.find('.quick-amounts').length === 0) {
            const quickAmountsHtml = `
                <div class="quick-amounts">
                    <span class="quick-amounts-label">Быстрый выбор:</span>
                    ${quickAmounts.map(amount => `
                        <button type="button" class="quick-amount-btn" data-amount="${amount}">$${amount}</button>
                    `).join('')}
                </div>
            `;

            amountGroup.append(quickAmountsHtml);

            // Bind events with specific scope
            $('.balance-topup-page').on('click', '.quick-amount-btn', (e) => {
                const amount = $(e.target).data('amount');
                $('.balance-topup-page #amount').val(amount).trigger('input');
            });
        }
    }

    updatePaymentInfo() {
        // Check if we have a selected method from cards
        if (!this.currentMethod) {
            this.currentFee = 0;
            this.calculateBonus();
            this.updatePayButton();
            return;
        }

        // Check if selected method is disabled
        if (this.currentMethod.disabled) {
            this.handleDisabledPaymentMethod();
            return;
        }

        this.currentFee = this.currentMethod.fee;
        this.calculateBonus();
        this.updatePayButton();
    }

    handleDisabledPaymentMethod() {
        // Reset current method
        this.currentMethod = null;
        this.currentFee = 0;

        // Hide bonus display
        $('.balance-topup-page #bonusDisplay').hide();

        // Update pay button to show disabled state
        $('.balance-topup-page #payBtn').prop('disabled', true);
        $('.balance-topup-page #payBtn').html(`
            <i class="fas fa-exclamation-triangle"></i>
            Метод недоступен
        `);

        // Show notification
        SocnetApp.notifications.showWarning('Выбранный платежный метод временно недоступен');
    }

    calculateBonus() {
        const amount = parseFloat($('.balance-topup-page #amount').val()) || 0;
        this.currentAmount = amount;

        if (amount <= 0) {
            $('.balance-topup-page #bonusDisplay').hide();
            this.updatePayButton();
            return;
        }

        // Calculate bonus based on amount
        let bonus = 0;
        if (amount >= 5000) {
            bonus = amount * 0.10; // 10% for $5000+
        } else if (amount >= 1000) {
            bonus = amount * 0.05; // 5% for $1000+
        } else if (amount >= 500) {
            bonus = amount * 0.02; // 2% for $500+
        }

        this.currentBonus = bonus;

        // Calculate fee and total in USD
        const fee = this.currentMethod ? (amount * this.currentMethod.fee / 100) : 0;
        const totalUsd = amount + fee;

        // Update display
        $('.balance-topup-page #bonusAmount').text(`$${bonus.toFixed(2)}`);
        $('.balance-topup-page #feeAmount').text(`$${fee.toFixed(2)}`);
        $('.balance-topup-page #totalAmount').text(this.composePayDisplay(totalUsd));
        $('.balance-topup-page #bonusDisplay').show();

        this.updatePayButton();
    }

    updatePayButton() {
        const hasAmount = this.currentAmount > 0;
        const hasMethod = this.currentMethod && !this.currentMethod.disabled;
        const isValid = hasAmount && hasMethod;

        $('.balance-topup-page #payBtn').prop('disabled', !isValid);

        if (isValid) {
            if (this.currentMethod.auto) {
                const fee = this.currentAmount * this.currentMethod.fee / 100;
                const totalUsd = this.currentAmount + fee;
                const payText = this.composePayDisplay(totalUsd);
                $('.balance-topup-page #payBtn').html(`
                    <i class="fas fa-credit-card"></i>
                    Пополнить ${payText}
                `);
            } else {
                $('.balance-topup-page #payBtn').html(`
                    <i class="fas fa-info-circle"></i>
                    Получить реквизиты
                `);
            }
        } else {
            $('.balance-topup-page #payBtn').html(`
                <i class="fas fa-credit-card"></i>
                Пополнить баланс
            `);
        }
    }

    handleFormSubmit() {
        if (!this.currentMethod || this.currentAmount <= 0) {
            SocnetApp.notifications.showError('Заполните все поля формы');
            return;
        }

        // Additional check for disabled payment methods
        if (this.currentMethod.disabled) {
            SocnetApp.notifications.showError('Выбранный платежный метод недоступен');
            return;
        }

        if (this.currentMethod.disabled) {
            SocnetApp.notifications.showError('Выбранный метод временно недоступен');
            return;
        }

        if (this.currentMethod.auto) {
            this.processAutoPayment();
        } else {
            this.showManualPaymentInstructions();
        }
    }

    processAutoPayment() {
        const totalAmount = this.currentAmount + (this.currentAmount * this.currentMethod.fee / 100);

        // Фейковое пополнение: просто увеличиваем баланс на amount (USD)
        const payload = { amount: this.currentAmount, method: this.currentMethod.id };
        if (this.activePromo) payload.promo_code = this.activePromo;
        post('/user/balance/fake-topup', payload, (resp) => {
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (res && res.success) {
                    SocnetApp.notifications.showSuccess('Баланс пополнен');
                    // Обновим шапку
                    updateHeaderBalance(res.balance_usd);
                } else {
                    SocnetApp.notifications.showError('Не удалось пополнить баланс');
                }
            } catch(e) {}
        }, () => {
            SocnetApp.notifications.showError('Ошибка пополнения');
        });

        // Добавим транзакцию локально
        this.addNewTransaction({
            amount: this.currentAmount,
            method: this.currentMethod.name.split(' ')[0],
            status: 'completed'
        });

        // Сбросим форму
        this.resetForm();
    }

    showManualPaymentInstructions() {
        const totalUsd = this.currentAmount + (this.currentAmount * this.currentMethod.fee / 100);

        $('.balance-topup-modal #paymentAmountDisplay').text(this.composePayDisplay(totalUsd));

        // Set payment details based on method
        let paymentDetails = '';
        const payCode = this.getCurrentPayCurrency();
        const amountMethod = totalUsd * this.getRate(payCode);
        const amountMethodStr = this.formatCurrency(amountMethod, payCode, true);
        const approxUsdStr = `$${totalUsd.toFixed(2)}`;

        if (this.currentMethod.id === 'paypal') {
            paymentDetails = `
                <div class="payment-detail">
                    <strong>PayPal Email:</strong> payments@example.com
                </div>
                <div class="payment-detail">
                    <strong>Сумма:</strong> ${payCode === 'usd' ? approxUsdStr : `${amountMethodStr} (≈ ${approxUsdStr})`}
                </div>
                <div class="payment-detail">
                    <strong>Комментарий:</strong> Balance top-up #${Date.now()}
                </div>
                <div class="alert alert-info mt-2">После перевода нажмите «Я оплатил», заявка поступит администратору.</div>
            `;
        } else if (this.currentMethod.id === 'lolz') {
            paymentDetails = `
                <div class="payment-detail">
                    <strong>Форум:</strong> LOLZTEAM
                </div>
                <div class="payment-detail">
                    <strong>Тема:</strong> Пополнение баланса
                </div>
                <div class="payment-detail">
                    <strong>Ссылка:</strong> https://lolz.guru/threads/12345
                </div>
                <div class="payment-detail">
                    <strong>Сумма:</strong> ${payCode === 'usd' ? approxUsdStr : `${amountMethodStr} (≈ ${approxUsdStr})`}
                </div>
                <div class="payment-detail">
                    <strong>Комментарий:</strong> Balance top-up #${Date.now()}
                </div>
            `;
        } else if (this.currentMethod.id === 'manual_test') {
            paymentDetails = `
                <div class="payment-detail">
                    <strong>Тестовый банковский счет:</strong> 1234 5678 9012 3456
                </div>
                <div class="payment-detail">
                    <strong>Получатель:</strong> ООО "Тестовая Компания"
                </div>
                <div class="payment-detail">
                    <strong>Банк:</strong> Тестовый Банк
                </div>
                <div class="payment-detail">
                    <strong>Сумма:</strong> ${payCode === 'usd' ? approxUsdStr : `${amountMethodStr} (≈ ${approxUsdStr})`}
                </div>
                <div class="payment-detail">
                    <strong>Комментарий:</strong> Balance top-up #${Date.now()}
                </div>
            `;
        }

        $('.balance-topup-modal #paymentDetails').html(paymentDetails);

        const modal = new bootstrap.Modal(document.getElementById('manualPaymentModal'));
        modal.show();
    }

    initReceiptUpload() {
        const dropzone = document.getElementById('receiptDropzone');
        const fileInput = document.getElementById('receiptFile');
        const fileInfo = document.getElementById('receiptFileInfo');
        const fileName = document.getElementById('receiptFileName');
        const preview = document.getElementById('receiptPreview');
        const removeBtn = document.getElementById('removeReceipt');

        if (!dropzone || !fileInput) return;

        const resetReceipt = () => {
            fileInput.value = '';
            if (fileInfo) fileInfo.style.display = 'none';
            if (preview) { preview.style.display = 'none'; preview.innerHTML = ''; }
        };

        const handleFiles = (files) => {
            const file = files && files[0] ? files[0] : null;
            if (!file) return;

            // Validate size (<=10MB)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                SocnetApp.notifications.showError('Файл слишком большой. Максимум 10 МБ');
                return;
            }

            // Validate type
            const allowed = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!allowed.includes(file.type)) {
                SocnetApp.notifications.showError('Недопустимый формат файла. JPG, PNG или PDF');
                return;
            }

            if (fileName) fileName.textContent = file.name;
            if (fileInfo) fileInfo.style.display = '';

            // Preview if image
            if (file.type.startsWith('image/') && preview) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = `<img src="${e.target.result}" alt="receipt" />`;
                    preview.style.display = '';
                };
                reader.readAsDataURL(file);
            } else if (preview) {
                preview.style.display = 'none';
                preview.innerHTML = '';
            }
        };

        dropzone.addEventListener('click', () => fileInput.click());
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                handleFiles(e.dataTransfer.files);
            }
        });

        fileInput.addEventListener('change', (e) => handleFiles(e.target.files));
        if (removeBtn) removeBtn.addEventListener('click', () => resetReceipt());
    }

    confirmManualPayment() {
        // Send pending transaction to backend
        const fileInput = document.getElementById('receiptFile');
        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

        // Build form data to support file upload
        const formData = new FormData();
        formData.append('amount', this.currentAmount);
        formData.append('method', this.currentMethod.id);
        if (fileInput && fileInput.files && fileInput.files[0]) {
            formData.append('receipt', fileInput.files[0]);
        }
        if (this.activePromo) {
            formData.append('promo_code', this.activePromo);
        }

        $.ajax({
            url: '/user/balance/fake-topup',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: Object.assign({}, getCSRF() ? { 'X-CSRF-TOKEN': getCSRF() } : {}),
            success: (resp) => {
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (res && res.success) {
                    SocnetApp.notifications.showSuccess('Заявка на пополнение отправлена на ручную проверку.');
                    // Refresh transactions from backend
                    this.fetchTransactions();
                } else {
                    SocnetApp.notifications.showError('Не удалось отправить заявку.');
                }
            } catch(e) {
                SocnetApp.notifications.showError('Ошибка обработки ответа сервера.');
            }
            },
            error: () => {
                SocnetApp.notifications.showError('Ошибка при отправке заявки.');
            }
        });

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('manualPaymentModal'));
        modal.hide();

        // Reset form
        this.resetForm();

        // Reset receipt UI
        const dropzone = document.getElementById('receiptDropzone');
        const fileInfo = document.getElementById('receiptFileInfo');
        const preview = document.getElementById('receiptPreview');
        const fileInput2 = document.getElementById('receiptFile');
        if (fileInput2) fileInput2.value = '';
        if (fileInfo) fileInfo.style.display = 'none';
        if (preview) { preview.style.display = 'none'; preview.innerHTML = ''; }
    }

    activatePromo() {
        const promoCode = $('.balance-topup-modal #promoCode').val().trim().toUpperCase();

        if (!promoCode) {
            SocnetApp.notifications.showError('Введите промокод');
            return;
        }

        if (this.activePromo) {
            SocnetApp.notifications.showError('Промокод уже активирован');
            return;
        }

        post('/user/promo/apply', { code: promoCode }, (resp) => {
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (!res || !res.success || !res.data) {
                    SocnetApp.notifications.showError('Промокод не найден или недействителен');
                    return;
                }

                this.activePromo = res.data.code;
                this.promoBonus = parseFloat(res.data.bonus_usd || 0) || 0;

                // Update UI
                $('.balance-topup-page #activePromoCode').text(this.activePromo);
                $('.balance-topup-page #promoBonusAmount').text(`$${this.promoBonus.toFixed(2)}`);
                $('.balance-topup-page #activePromo').show();

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('promoModal'));
                modal.hide();

                SocnetApp.notifications.showSuccess('Промокод активирован!');
            } catch (e) {
                SocnetApp.notifications.showError('Ошибка обработки ответа сервера');
            }
        }, () => {
            SocnetApp.notifications.showError('Ошибка связи с сервером');
        });
    }

    removePromo() {
        this.activePromo = null;
        this.promoBonus = 0;
        $('.balance-topup-page #activePromo').hide();

        SocnetApp.notifications.showInfo('Промокод удален');
    }

    addNewTransaction(data) {
        const transaction = {
            id: `txn_${Date.now()}`,
            date: new Date(),
            amount: data.amount,
            method: data.method,
            status: data.status,
            statusText: this.getStatusText(data.status)
        };

        this.transactions.unshift(transaction);
        this.renderTransactions();
    }

    getStatusText(status) {
        const statusMap = {
            'pending': 'В обработке',
            'completed': 'Завершено',
            'failed': 'Неудача'
        };
        return statusMap[status] || status;
    }

    renderTransactions() {
        const tbody = $('.balance-topup-page #transactionsTableBody');
        const emptyState = $('.balance-topup-page #emptyState');

        if (!this.transactions || this.transactions.length === 0) {
            tbody.empty();
            $('.balance-topup-page #transactionsTable').hide();
            emptyState.show();
            return;
        }

        $('.balance-topup-page #transactionsTable').show();
        emptyState.hide();

        tbody.empty();

        this.transactions.forEach(transaction => {
            const row = this.createTransactionRow(transaction);
            tbody.append(row);
        });
    }

    createTransactionRow(transaction) {
        const statusClass = `status-${transaction.status}`;
        const formattedDate = this.formatDate(transaction.date);
        const formattedTime = this.formatTime(transaction.date);

        const methodName = this.getMethodDisplayName(transaction.method);
        return $(`
            <tr data-transaction-id="${transaction.id}">
                <td class="date-cell">
                    <span class="transaction-date">${formattedDate}</span>
                    <span class="transaction-time">${formattedTime}</span>
                </td>
                <td class="amount-cell">
                    <span class="amount">$${transaction.amount.toFixed(2)}</span>
                </td>
                <td class="method-cell">
                    <span class="method-name">${methodName}</span>
                </td>
                <td class="status-cell">
                    <span class="status-badge ${statusClass}">${transaction.statusText}</span>
                </td>
                <td class="actions-cell">
                    <a href="/tickets/new?transaction=${transaction.id}" class="action-btn report-btn">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span class="btn-text">Проблема</span>
                    </a>
                </td>
            </tr>
        `);
    }

    getMethodDisplayName(code) {
        const methodsCfg = (window.SocnetApp && SocnetApp.payments && SocnetApp.payments.methods) ? SocnetApp.payments.methods : {};
        const key = String(code || '').toLowerCase();
        const name = methodsCfg[key] && methodsCfg[key].name ? methodsCfg[key].name : key;
        // Fallback: capitalize if missing config
        return name || (key ? key.charAt(0).toUpperCase() + key.slice(1) : '');
    }

    formatDate(date) {
        return date.toLocaleDateString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    formatTime(date) {
        return date.toLocaleTimeString('ru-RU', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    resetForm() {
        // Prevent infinite recursion
        if (this.isUpdatingSelect) {
            return;
        }

        $('.balance-topup-page #topupForm')[0].reset();
        this.currentAmount = 0;
        this.currentMethod = null;
        this.currentFee = 0;
        this.currentBonus = 0;
        $('.balance-topup-page #bonusDisplay').hide();

        // Reset payment method card selection
        $('.balance-topup-page .payment-method-card').removeClass('selected');

        // Clear checkmarks but preserve warning icons for disabled methods
        $('.balance-topup-page .payment-method-card .method-status').each(function () {
            const $status = $(this);
            const $card = $status.closest('.payment-method-card');

            // If card is disabled, keep the warning icon
            if ($card.hasClass('disabled')) {
                // Keep the warning icon
                return;
            }

            // Clear the status for non-disabled cards
            $status.empty();
        });

        // Reset hidden input
        $('#selectedPaymentMethod').val('');

        // Reset custom select
        $('.balance-topup-page #paymentMethod').val('');

        // Update custom select display if it's initialized
        this.isUpdatingSelect = true; // Set flag to prevent recursion

        if (typeof CustomSelect !== 'undefined') {
            try {
                const selectElement = $('.balance-topup-page #paymentMethod')[0];
                let customSelectInstance = null;

                // Try to find instance in different properties
                if (CustomSelect.selects && Array.isArray(CustomSelect.selects)) {
                    for (const selectInstance of CustomSelect.selects) {
                        if (selectInstance && selectInstance.element === selectElement) {
                            customSelectInstance = selectInstance;
                            break;
                        }
                    }
                } else if (CustomSelect.selects && CustomSelect.selects.has && CustomSelect.selects.has(selectElement)) {
                    customSelectInstance = CustomSelect.selects.get(selectElement);
                } else if (CustomSelect.instances && CustomSelect.instances.has && CustomSelect.instances.has(selectElement)) {
                    customSelectInstance = CustomSelect.instances.get(selectElement);
                }

                if (customSelectInstance && typeof customSelectInstance.setValue === 'function') {
                    customSelectInstance.setValue('');
                } else {
                    this.updateCustomSelectAlternative('');
                }
            } catch (error) {
                console.warn('Failed to update CustomSelect in resetForm:', error);
                this.updateCustomSelectAlternative('');
            }
        } else {
            this.updateCustomSelectAlternative('');
        }

        // Reset flag after a short delay
        setTimeout(() => {
            this.isUpdatingSelect = false;
        }, 100);

        this.updatePayButton();
    }
}

// Global functions for inline event handlers
window.updatePaymentInfo = function () {
    if (window.balanceManager) {
        window.balanceManager.updatePaymentInfo();
    }
};

window.calculateBonus = function () {
    if (window.balanceManager) {
        window.balanceManager.calculateBonus();
    }
};

window.activatePromo = function () {
    if (window.balanceManager) {
        window.balanceManager.activatePromo();
    }
};

window.removePromo = function () {
    if (window.balanceManager) {
        window.balanceManager.removePromo();
    }
};

window.confirmManualPayment = function () {
    if (window.balanceManager) {
        window.balanceManager.confirmManualPayment();
    }
};

// Initialize when document is ready
$(document).ready(() => {
    try {
        window.balanceManager = new BalanceTopupManager();
    } catch (error) {
        console.error('Failed to initialize BalanceTopupManager:', error);
    }
});

// Export for global access
window.BalanceTopupManager = BalanceTopupManager;
