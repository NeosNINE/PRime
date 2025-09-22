// Auth Component - Authentication Modal Management
// ================================================

const AuthModal = {
    // Configuration
    config: {
        recaptchaSiteKey: '6LcXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', // Replace with real key
        passwordMinLength: 8,
        debounceDelay: 300
    },

    // State
    state: {
        currentModal: null,
        tempAuthToken: null,
        isSubmitting: false,
        previousUrl: null // для сохранения предыдущего URL
    },

    // Initialize
    init() {
        this.bindEvents();
        this.initPasswordToggles();
        this.initPasswordStrength();
        this.initTwoFactorInput();
        this.loadRecaptcha();
        console.log('Auth Modal initialized');
    },

    // Event Bindings
    bindEvents() {
        // Form submissions
        $(document).on('submit', '#loginForm', (e) => this.handleLogin(e));
        $(document).on('submit', '#registerForm', (e) => this.handleRegister(e));
        $(document).on('submit', '#resetPasswordForm', (e) => this.handleResetPassword(e));
        $(document).on('submit', '#twoFactorForm', (e) => this.handleTwoFactor(e));
        $(document).on('submit', '#forgotPasswordForm', (e) => this.handleForgotPassword(e));

        // Google OAuth buttons
        $(document).on('click', '#loginWithGoogle', (e) => this.handleGoogleAuth(e, 'login'));
        $(document).on('click', '#registerWithGoogle', (e) => this.handleGoogleAuth(e, 'register'));

        // Auth buttons with data attributes
        $(document).on('click', '[data-auth-open]', (e) => this.handleAuthButtonClick(e));

        // Обработчик восстановления пароля осуществляется через универсальный [data-auth-open]

        // Modal events
        $('.modal').on('shown.bs.modal', (e) => this.onModalShown(e));
        $('.modal').on('hidden.bs.modal', (e) => this.onModalHidden(e));

        // Real-time validation
        $(document).on('input', '.auth-form-input', (e) => this.validateFieldOnInput(e));
        $(document).on('blur', '.auth-form-input', (e) => this.validateField(e.target));

        // Password confirmation matching
        $(document).on('input', '#registerPasswordConfirm', (e) => this.validatePasswordMatch());

        // Terms checkbox
        $(document).on('change', '#acceptTerms', (e) => this.toggleSubmitButton());

        // Remember scroll position
        $(document).on('scroll', '.auth-terms-content, .auth-privacy-content', (e) => {
            localStorage.setItem('modal-scroll-' + e.target.closest('.modal').id, e.target.scrollTop);
        });
    },

    // Password Toggle Functionality
    initPasswordToggles() {
        $(document).on('click', '.auth-form-toggle-password', function (e) {
            e.preventDefault();
            const targetId = $(this).data('target');
            const $input = $('#' + targetId);
            const $icon = $(this).find('i');

            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    },

    // Password Strength Indicator
    initPasswordStrength() {
        let debounceTimer;

        $(document).on('input', '#registerPassword', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                this.updatePasswordStrength(e.target.value);
            }, this.config.debounceDelay);
        });
    },

    // 2FA Input Enhancement
    initTwoFactorInput() {
        const $twoFactorInput = $('#twoFactorCode');

        // Auto-focus on modal show
        $('#twoFactorModal').on('shown.bs.modal', () => {
            $twoFactorInput.focus();
        });

        // Auto-format input (only numbers, max 6 digits)
        $twoFactorInput.on('input', (e) => {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            value = value.substring(0, 6); // Limit to 6 characters

            // Update input value
            e.target.value = value;

            // Auto-submit when 6 digits are entered
            if (value.length === 6) {
                setTimeout(() => {
                    $('#twoFactorForm').submit();
                }, 300); // Small delay for better UX
            }
        });

        // Handle paste events
        $twoFactorInput.on('paste', (e) => {
            e.preventDefault();
            const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            const cleanValue = pastedText.replace(/\D/g, '').substring(0, 6);
            e.target.value = cleanValue;

            // Auto-submit if 6 digits pasted
            if (cleanValue.length === 6) {
                setTimeout(() => {
                    $('#twoFactorForm').submit();
                }, 300);
            }
        });

        // Handle keydown for better UX
        $twoFactorInput.on('keydown', (e) => {
            // Allow: backspace, delete, tab, escape, enter, arrows
            const allowed = [8, 9, 27, 13, 46, 37, 39];
            if (allowed.includes(e.keyCode)) return;

            // Allow Ctrl/Meta combos: A,C,V,X (select all, copy, paste, cut)
            if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].includes(e.keyCode)) return;

            // Only digits (top row 0-9 and numpad 0-9)
            const isTopRowDigit = e.keyCode >= 48 && e.keyCode <= 57;
            const isNumpadDigit = e.keyCode >= 96 && e.keyCode <= 105;
            if (e.shiftKey || (!isTopRowDigit && !isNumpadDigit)) {
                e.preventDefault();
            }
        });
    },

    updatePasswordStrength(password) {
        const $container = $('#passwordStrength');
        const $fill = $container.find('.strength-fill');
        const $text = $container.find('.strength-text');

        if (!password) {
            $container.removeClass('show');
            return;
        }

        $container.addClass('show');

        const strength = this.calculatePasswordStrength(password);
        $fill.removeClass('weak fair good strong').addClass(strength.level);
        $text.text(strength.text);
    },

    calculatePasswordStrength(password) {
        let score = 0;

        // Length check
        if (password.length >= 8) score += 1;
        if (password.length >= 12) score += 1;

        // Character variety
        if (/[a-z]/.test(password)) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[0-9]/.test(password)) score += 1;
        if (/[^A-Za-z0-9]/.test(password)) score += 1;

        const levels = {
            0: { level: 'weak', text: 'Очень слабый' },
            1: { level: 'weak', text: 'Слабый' },
            2: { level: 'fair', text: 'Средний' },
            3: { level: 'fair', text: 'Хороший' },
            4: { level: 'good', text: 'Сильный' },
            5: { level: 'strong', text: 'Очень сильный' },
            6: { level: 'strong', text: 'Превосходный' }
        };

        return levels[score] || levels[0];
    },

    // Load reCAPTCHA
    loadRecaptcha() {
        // reCAPTCHA is already loaded in the layout
        // This is a placeholder for additional configuration if needed
        if (typeof grecaptcha !== 'undefined') {
            console.log('reCAPTCHA loaded');
        }
    },

    // Form Handlers
    async handleLogin(e) {
        e.preventDefault();

        if (this.state.isSubmitting) return;

        const form = e.target;
        const formData = new FormData(form);

        // Validate form
        if (!this.validateLoginForm(form)) {
            return;
        }

        this.setSubmitting('loginSubmit', true);

        try {
            // Convert FormData to object for request function
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }

            // Use revered request function (callback-based). Do NOT unset loader here immediately.
            request('POST', '/login', data, (response, textStatus, xhr) => {
                // Success: redirect to provided URL or fallback to profile
                this.setSubmitting('loginSubmit', false);
                try {
                    const res = xhr && xhr.responseJSON ? xhr.responseJSON : (typeof response === 'string' ? JSON.parse(response) : response);
                    if (res && res.require_2fa) {
                        // Обновим meta csrf, если пришёл новый токен
                        if (res.csrf) {
                            let meta = document.querySelector('meta[name="csrf-token"]');
                            if (meta) meta.setAttribute('content', res.csrf);
                        }
                        this.showTwoFactorModal();
                        return;
                    }
                    if (res && res.redirect) {
                        window.location.href = res.redirect;
                        return;
                    }
                } catch (_) {}
                window.location.href = '/user/profile';
            }, (xhr) => {
                // Error: stop loader and show validation errors
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data && data.require_2fa) {
                        this.showTwoFactorModal();
                        return;
                    }
                    this.handleLoginErrors(data.errors || {});
                } catch (e) {
                    this.showError('loginPasswordError', 'Произошла ошибка при входе');
                } finally {
                    this.setSubmitting('loginSubmit', false);
                }
            });

        } catch (error) {
            console.error('Login error:', error);
            this.showError('loginPasswordError', 'Произошла ошибка при входе');
            this.setSubmitting('loginSubmit', false);
        }
    },

    // Handle login validation errors
    handleLoginErrors(errors) {
        // Clear previous errors
        this.clearErrors(['loginEmailError', 'loginPasswordError']);

        // Show specific field errors
        if (errors.email) {
            this.showError('loginEmailError', errors.email[0]);
        }
        if (errors.password) {
            this.showError('loginPasswordError', errors.password[0]);
        }

        // Show general error if no specific field errors
        if (!errors.email && !errors.password) {
            this.showError('loginPasswordError', 'Неверный email или пароль');
        }
    },

    // Show 2FA Modal
    showTwoFactorModal() {
        // Generate temporary token
        this.state.tempAuthToken = 'temp_token_' + Date.now();

        // Set token in hidden input
        $('#tempToken').val(this.state.tempAuthToken);

        // Hide login modal and show 2FA modal
        $('#loginModal').modal('hide');

        // Small delay to ensure smooth transition
        setTimeout(() => {
            $('#twoFactorModal').modal('show');
        }, 150);
    },

    async handleRegister(e) {
        e.preventDefault();

        if (this.state.isSubmitting) return;

        const form = e.target;
        const formData = new FormData(form);

        // Validate form
        if (!this.validateRegisterForm(form)) {
            return;
        }

        this.setSubmitting('registerSubmit', true);

        try {
            // Prepare data for request
            const data = {
                login: formData.get('login') || '',
                email: formData.get('email') || '',
                password: formData.get('password') || '',
                password_confirmation: formData.get('password_confirmation') || ''
            };

            // Send AJAX request to backend
            request('POST', '/register', data, (response, textStatus, xhr) => {
                // Success: redirect if backend provided URL, else fallback to profile
                this.setSubmitting('registerSubmit', false);
                try {
                    const res = xhr && xhr.responseJSON ? xhr.responseJSON : (typeof response === 'string' ? JSON.parse(response) : response);
                    if (res && res.redirect) {
                        window.location.href = res.redirect;
                        return;
                    }
                } catch (_) {}
                window.location.href = '/user/profile';
            }, (xhr) => {
                // Error: stop loader and show validation errors
                try {
                    const data = JSON.parse(xhr.responseText);
                    this.handleRegisterErrors(data.errors || {});
                } catch (e) {
                    this.showError('registerEmailError', 'Произошла ошибка при регистрации');
                } finally {
                    this.setSubmitting('registerSubmit', false);
                }
            });

        } catch (error) {
            console.error('Register error:', error);
            this.showError('registerEmailError', 'Произошла ошибка при регистрации');
            this.setSubmitting('registerSubmit', false);
        }
    },

    // Handle register validation errors from backend
    handleRegisterErrors(errors) {
        this.clearErrors(['registerLoginError', 'registerEmailError', 'registerPasswordError', 'registerPasswordConfirmError', 'registerTermsError']);

        if (errors.login) {
            this.showError('registerLoginError', errors.login[0]);
        }
        if (errors.email) {
            this.showError('registerEmailError', errors.email[0]);
        }
        if (errors.password) {
            this.showError('registerPasswordError', errors.password[0]);
        }
        if (errors.password_confirmation) {
            this.showError('registerPasswordConfirmError', errors.password_confirmation[0]);
        }
    },

    async handleTwoFactor(e) {
        e.preventDefault();

        if (this.state.isSubmitting) return;

        const form = e.target;
        const formData = new FormData(form);

        // Validate 2FA code
        const code = formData.get('code');
        if (!this.validate2FACode(code)) {
            this.showError('twoFactorCodeError', 'Введите 6-значный код');
            return;
        }

        this.setSubmitting('twoFactorSubmit', true);

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const payload = { code, ajax: true, _token: csrf };

            $.ajax({
                url: '/login/2fa-verify',
                type: 'POST',
                data: payload,
                headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {},
                success: (response, textStatus, xhr) => {
                    this.setSubmitting('twoFactorSubmit', false);
                    try {
                        const res = xhr && xhr.responseJSON ? xhr.responseJSON : (typeof response === 'string' ? JSON.parse(response) : response);
                        if (res && res.success && res.redirect) {
                            window.location.href = res.redirect;
                            return;
                        }
                    } catch (_) {}
                    window.location.reload();
                },
                error: (xhr) => {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (data && data.errors && data.errors.code) {
                            this.showError('twoFactorCodeError', data.errors.code[0]);
                        } else {
                            this.showError('twoFactorCodeError', 'Неверный код');
                        }
                    } catch (_) {
                        this.showError('twoFactorCodeError', 'Неверный код');
                    } finally {
                        this.setSubmitting('twoFactorSubmit', false);
                    }
                }
            });
        } catch (error) {
            this.handleAuthError(error, '2fa');
            this.setSubmitting('twoFactorSubmit', false);
        }
    },

    async handleForgotPassword(e) {
        e.preventDefault();

        if (this.state.isSubmitting) return;

        const form = e.target;
        const formData = new FormData(form);

        // Validate email (for password recovery, usually only email is accepted)
        if (!this.validateEmail(formData.get('email'))) {
            this.showError('forgotEmailError', 'Введите корректный email');
            return;
        }

        this.setSubmitting('forgotPasswordSubmit', true);

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const payload = {
                email: formData.get('email'),
                ajax: true,
                _token: csrf
            };

            $.ajax({
                url: '/forgot-password',
                type: 'POST',
                data: payload,
                headers: { 'X-CSRF-TOKEN': csrf },
                success: () => {
                    this.setSubmitting('forgotPasswordSubmit', false);
                    alert('Ссылка для восстановления пароля отправлена на ваш email');
                    $('#forgotPasswordModal').modal('hide');
                },
                error: (xhr) => {
                    try {
                        const resp = xhr.responseJSON || JSON.parse(xhr.responseText);
                        if (resp && resp.errors && resp.errors.email) {
                            this.showError('forgotEmailError', resp.errors.email[0]);
                        } else if (resp && resp.message) {
                            this.showError('forgotEmailError', resp.message);
                        } else {
                            this.showError('forgotEmailError', 'Не удалось отправить ссылку. Попробуйте позже');
                        }
                    } catch (_) {
                        this.showError('forgotEmailError', 'Не удалось отправить ссылку. Попробуйте позже');
                    }
                },
                complete: () => {
                    this.setSubmitting('forgotPasswordSubmit', false);
                }
            });

        } catch (error) {
            this.showError('forgotEmailError', 'Произошла ошибка');
            this.setSubmitting('forgotPasswordSubmit', false);
        }
    },

    // Reset Password (page) via AJAX
    async handleResetPassword(e) {
        e.preventDefault();

        if (this.state.isSubmitting) return;

        const form = e.target;
        const $form = $(form);
        const token = $form.find('input[name="token"]').val();
        const email = $form.find('#resetEmail').val();
        const password = $form.find('#password').val();
        const password_confirmation = $form.find('#password_confirmation').val();

        // Clear previous errors
        this.clearErrors(['resetEmailError', 'resetPasswordError', 'resetPasswordConfirmError']);

        // Simple client validation
        let valid = true;
        if (!password || password.length < 8) {
            this.showError('resetPasswordError', 'Минимум 8 символов');
            valid = false;
        }
        if (password !== password_confirmation) {
            this.showError('resetPasswordConfirmError', 'Пароли не совпадают');
            valid = false;
        }
        if (!valid) return;

        this.setSubmitting('resetSubmit', true);

        // Build payload with CSRF
        const csrf = (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || '';
        const data = {
            token,
            email,
            password,
            password_confirmation,
            ajax: true,
            _token: csrf,
        };

        $.ajax({
            url: '/reset-password',
            type: 'POST',
            data,
            headers: { 'X-CSRF-TOKEN': csrf },
            success: (resp) => {
                // Laravel по умолчанию редиректит на login с flash status
                if (resp && resp.redirect) {
                    window.location.href = resp.redirect;
                } else {
                    window.location.href = '/login';
                }
            },
            error: (xhr) => {
                try {
                    const r = xhr.responseJSON || JSON.parse(xhr.responseText);
                    const errs = r && r.errors ? r.errors : {};
                    if (errs.email) this.showError('resetEmailError', errs.email[0]);
                    if (errs.password) this.showError('resetPasswordError', errs.password[0]);
                    if (errs.password_confirmation) this.showError('resetPasswordConfirmError', errs.password_confirmation[0]);

                    if (!errs.email && !errs.password && !errs.password_confirmation && r && r.message) {
                        this.showError('resetPasswordError', r.message);
                    }
                } catch (_) {
                    this.showError('resetPasswordError', 'Не удалось сбросить пароль');
                }
            },
            complete: () => {
                this.setSubmitting('resetSubmit', false);
            }
        });
    },

    // Google OAuth Handler
    handleGoogleAuth(e, type) {
        e.preventDefault();

        const intent = type === 'register' ? 'register' : 'login';
        window.location.href = `/auth/google/redirect?intent=${encodeURIComponent(intent)}`;
    },

    // Validation Methods
    validateLoginForm(form) {
        let isValid = true;

        const email = form.querySelector('#loginEmail').value;
        const password = form.querySelector('#loginPassword').value;

        // Clear previous errors
        this.clearErrors(['loginEmailError', 'loginPasswordError']);

        // Login or Email validation
        if (!this.validateLoginOrEmail(email)) {
            this.showError('loginEmailError', 'Введите корректный логин или email');
            isValid = false;
        }

        // Password validation
        if (!password) {
            this.showError('loginPasswordError', 'Введите пароль');
            isValid = false;
        }

        return isValid;
    },

    validateRegisterForm(form) {
        let isValid = true;

        const login = form.querySelector('#registerLogin').value;
        const email = form.querySelector('#registerEmail').value;
        const password = form.querySelector('#registerPassword').value;
        const passwordConfirm = form.querySelector('#registerPasswordConfirm').value;
        const terms = form.querySelector('#acceptTerms').checked;

        // Clear previous errors
        this.clearErrors(['registerLoginError', 'registerEmailError', 'registerPasswordError', 'registerPasswordConfirmError', 'registerTermsError']);

        // Login validation
        if (!this.validateLogin(login)) {
            this.showError('registerLoginError', 'Логин должен содержать 3-30 символов (буквы, цифры, точка, тире, подчеркивание)');
            isValid = false;
        }

        // Email validation
        if (!this.validateEmail(email)) {
            this.showError('registerEmailError', 'Введите корректный email');
            isValid = false;
        }

        // Password validation
        if (!this.validatePassword(password)) {
            this.showError('registerPasswordError', 'Пароль должен содержать минимум 8 символов, включая букву и цифру');
            isValid = false;
        }

        // Password confirmation
        if (password !== passwordConfirm) {
            this.showError('registerPasswordConfirmError', 'Пароли не совпадают');
            isValid = false;
        }

        // Terms acceptance
        if (!terms) {
            this.showError('registerTermsError', 'Необходимо принять условия использования');
            isValid = false;
        }

        return isValid;
    },

    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    validateLoginOrEmail(value) {
        // Check if it's an email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailRegex.test(value)) {
            return true;
        }

        // Check if it's a valid login (alphanumeric, underscore, dot, hyphen, 3-30 chars)
        const loginRegex = /^[a-zA-Z0-9._-]{3,30}$/;
        return loginRegex.test(value);
    },

    validateLogin(login) {
        // Login validation: alphanumeric, underscore, dot, hyphen, 3-30 chars
        const loginRegex = /^[a-zA-Z0-9._-]{3,30}$/;
        return loginRegex.test(login);
    },

    validatePassword(password) {
        return password.length >= this.config.passwordMinLength &&
            /[a-zA-Z]/.test(password) &&
            /[0-9]/.test(password);
    },

    validate2FACode(code) {
        return /^\d{6}$/.test(code);
    },

    validatePasswordMatch() {
        const password = $('#registerPassword').val();
        const passwordConfirm = $('#registerPasswordConfirm').val();

        if (passwordConfirm && password !== passwordConfirm) {
            this.showError('registerPasswordConfirmError', 'Пароли не совпадают');
            $('#registerPasswordConfirm').addClass('is-invalid');
        } else {
            this.hideError('registerPasswordConfirmError');
            $('#registerPasswordConfirm').removeClass('is-invalid').addClass('is-valid');
        }
    },

    validateField(field) {
        const $field = $(field);
        const value = $field.val();
        let isValid = true;
        let errorMessage = '';

        // Handle specific field IDs first
        if (field.id === 'loginEmail') {
            isValid = this.validateLoginOrEmail(value);
            errorMessage = 'Введите корректный логин или email';
        } else if (field.id === 'registerLogin') {
            isValid = this.validateLogin(value);
            errorMessage = 'Логин должен содержать 3-30 символов (буквы, цифры, точка, тире, подчеркивание)';
        } else {
            // Handle by field type
            switch (field.type) {
                case 'email':
                    isValid = this.validateEmail(value);
                    errorMessage = 'Введите корректный email';
                    break;
                case 'text':
                    // For text fields that should be validated as login or email
                    if (field.id === 'loginEmail') {
                        isValid = this.validateLoginOrEmail(value);
                        errorMessage = 'Введите корректный логин или email';
                    } else if (field.id === 'twoFactorCode') {
                        isValid = this.validate2FACode(value);
                        errorMessage = 'Введите 6-значный код аутентификации';
                    } else {
                        // Generic text validation
                        isValid = value.length > 0;
                        errorMessage = 'Заполните это поле';
                    }
                    break;
                case 'password':
                    if (field.id === 'registerPassword') {
                        isValid = this.validatePassword(value);
                        errorMessage = 'Пароль должен содержать минимум 8 символов, включая букву и цифру';
                    } else {
                        isValid = value.length > 0;
                        errorMessage = 'Введите пароль';
                    }
                    break;
            }
        }

        // Update field appearance
        if (value) {
            if (isValid) {
                $field.removeClass('is-invalid').addClass('is-valid');
            } else {
                $field.removeClass('is-valid').addClass('is-invalid');
            }
        } else {
            $field.removeClass('is-valid is-invalid');
        }

        return isValid;
    },

    validateFieldOnInput(e) {
        // Debounced validation on input
        clearTimeout(e.target.validationTimer);
        e.target.validationTimer = setTimeout(() => {
            this.validateField(e.target);
        }, this.config.debounceDelay);
    },

    // Utility Methods
    showError(errorId, message) {
        const $error = $('#' + errorId);
        $error.text(message).addClass('show');
    },

    hideError(errorId) {
        const $error = $('#' + errorId);
        $error.removeClass('show');
    },

    clearErrors(errorIds) {
        errorIds.forEach(id => this.hideError(id));
    },

    setSubmitting(buttonId, isSubmitting) {
        const $button = $('#' + buttonId);

        if (isSubmitting) {
            $button.addClass('loading').prop('disabled', true);
            this.state.isSubmitting = true;
        } else {
            $button.removeClass('loading').prop('disabled', false);
            this.state.isSubmitting = false;
        }
    },

    getRecaptchaResponse() {
        // In a real application, get the actual reCAPTCHA response
        // return grecaptcha.getResponse();

        // For demo purposes, return a dummy response
        return 'demo_recaptcha_response';
    },

    toggleSubmitButton() {
        const termsAccepted = $('#acceptTerms').is(':checked');
        $('#registerSubmit').prop('disabled', !termsAccepted);
    },

    // Modal Event Handlers
    onModalShown(e) {
        const modalId = e.target.id;
        this.state.currentModal = modalId;

        // Focus first input
        setTimeout(() => {
            $(e.target).find('.auth-form-input:first').focus();
        }, 100);

        // Restore scroll position for content modals
        if (modalId === 'termsModal' || modalId === 'privacyModal') {
            const scrollPos = localStorage.getItem('modal-scroll-' + modalId);
            if (scrollPos) {
                $(e.target).find('.auth-terms-content, .auth-privacy-content').scrollTop(scrollPos);
            }
        }
    },

    onModalHidden(e) {
        const modalId = e.target.id;

        // Reset form if it's an auth modal
        if (['loginModal', 'registerModal', 'twoFactorModal', 'forgotPasswordModal'].includes(modalId)) {
            this.resetForm(modalId);
        }

        this.state.currentModal = null;

        // Возвращаем предыдущий URL или переходим на главную
        if ((modalId === 'loginModal' && window.location.pathname === '/login') ||
            (modalId === 'registerModal' && window.location.pathname === '/register') ||
            (modalId === 'forgotPasswordModal' && (window.location.pathname === '/forgot' || window.location.pathname === '/forgot-password'))) {

            const urlToRestore = this.state.previousUrl || '/';
            history.replaceState(null, '', urlToRestore);

            // Очищаем сохраненный URL
            this.state.previousUrl = null;
        }
    },

    resetForm(modalId) {
        const $modal = $('#' + modalId);
        const $form = $modal.find('.auth-form');

        // Reset form fields
        $form[0].reset();

        // Clear validation states
        $form.find('.auth-form-input').removeClass('is-valid is-invalid');
        $form.find('.auth-form-error').removeClass('show');
        $form.find('.auth-password-strength').removeClass('show');

        // Reset submit button
        const submitBtn = $form.find('.auth-form-submit');
        submitBtn.removeClass('loading').prop('disabled', false);
    },

    // API Simulation
    async simulateApiCall(endpoint, data) {
        // Simulate network delay
        await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 1000));

        // Simulate different responses based on email for demo
        if (endpoint.includes('login') && data.email === 'error@example.com') {
            throw new Error('Неверный email или пароль');
        }

        if (endpoint.includes('register') && data.email === 'exists@example.com') {
            throw new Error('Пользователь с таким email уже существует');
        }

        if (endpoint.includes('2fa') && data.code !== '123456') {
            throw new Error('Неверный код аутентификации');
        }

        console.log(`API Call to ${endpoint}:`, data);
        return { success: true };
    },

    handleSuccessfulAuth(redirectUrl) {
        // Show success message
        alert('Авторизация успешна! Перенаправление...');

        // Close all modals
        $('.modal').modal('hide');

        // Redirect (in a real app, you might want to reload the page or update the UI)
        setTimeout(() => {
            console.log(`Redirecting to: ${redirectUrl}`);
            // window.location.href = redirectUrl;
        }, 1000);
    },

    handleAuthError(error, context) {
        console.error(`Auth error (${context}):`, error);

        // Map error to appropriate field
        const errorMappings = {
            login: 'loginPasswordError',
            register: 'registerEmailError',
            '2fa': 'twoFactorCodeError',
            forgot: 'forgotEmailError'
        };

        const errorField = errorMappings[context];
        if (errorField) {
            this.showError(errorField, error.message || 'Произошла ошибка');
        }
    },

    // Public methods to open modals programmatically
    openLoginModal() {
        const loginModal = document.getElementById('loginModal');
        if (loginModal) {
            // Закрыть все другие модалки перед открытием
            this.closeAllModalsExcept(loginModal);
            const modal = new bootstrap.Modal(loginModal);
            modal.show();
        }
    },

    openRegisterModal() {
        const registerModal = document.getElementById('registerModal');
        if (registerModal) {
            // Закрыть все другие модалки перед открытием
            this.closeAllModalsExcept(registerModal);
            const modal = new bootstrap.Modal(registerModal);
            modal.show();
        }
    },

    openForgotModal() {
        const forgotModal = document.getElementById('forgotPasswordModal');
        if (forgotModal) {
            // Закрыть все другие модалки перед открытием
            this.closeAllModalsExcept(forgotModal);
            const modal = new bootstrap.Modal(forgotModal);
            modal.show();
        }
    },

    // Handle auth buttons with data attributes
    handleAuthButtonClick(e) {
        e.preventDefault();

        const authType = $(e.target).data('auth-open');

        // Сохраняем текущий URL перед изменением
        const currentPath = window.location.pathname + window.location.search;
        const authPaths = ['/login', '/register', '/forgot', '/forgot-password'];
        if (!this.state.previousUrl || !authPaths.includes(currentPath)) {
            this.state.previousUrl = currentPath;
        }

        if (authType === 'login') {
            // Изменяем URL на /login
            history.pushState(null, '', '/login');
            this.openLoginModal();
        } else if (authType === 'register') {
            // Изменяем URL на /register
            history.pushState(null, '', '/register');
            this.openRegisterModal();
        } else if (authType === 'forgot') {
            // Изменяем URL на /forgot-password
            history.pushState(null, '', '/forgot-password');
            this.openForgotModal();
        }
    },



    // Auto-open modals based on current URL
    autoOpenModalByUrl() {
        const currentPath = window.location.pathname;
        const urlParams = new URLSearchParams(window.location.search);

        // Check if we're on login page
        if (currentPath === '/login') {
            // Wait a bit for Bootstrap to be ready
            setTimeout(() => {
                this.openLoginModal();
            }, 100);
        }

        // Check if we're on register page
        if (currentPath === '/register') {
            // Wait a bit for Bootstrap to be ready
            setTimeout(() => {
                this.openRegisterModal();
            }, 100);
        }

        // Check if we're on forgot password page (both /forgot and /forgot-password)
        if (currentPath === '/forgot' || currentPath === '/forgot-password') {
            // Wait a bit for Bootstrap to be ready
            setTimeout(() => {
                this.openForgotModal();
            }, 100);
        }

        // Если вернулись из Google OAuth и требуется 2FA
        if (urlParams.get('two_factor') === '1') {
            setTimeout(() => {
                this.showTwoFactorModal();
            }, 150);
        }
    }
};

// Закрыть все открытые модалки, кроме targetEl (если передан)
AuthModal.closeAllModalsExcept = function (targetEl) {
    const openModals = document.querySelectorAll('.modal.show');
    openModals.forEach((el) => {
        if (!targetEl || el !== targetEl) {
            const instance = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            instance.hide();
        }
    });
};

// Auto-initialize when DOM is ready
$(document).ready(() => {
    AuthModal.init();

    // Auto-open modals based on URL
    AuthModal.autoOpenModalByUrl();
});

// Export for external access
window.AuthModal = AuthModal;
