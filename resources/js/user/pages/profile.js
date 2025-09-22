/**
 * Profile Page Manager
 * Управление страницей профиля пользователя
 */
class ProfileManager {
    constructor() {
        this.tfaEnabled = false; // Фактическое состояние 2FA
        this.tfaInProcess = false; // Состояние "в процессе" (включения/выключения)
        this.tfaProcessType = null; // Тип процесса: 'enable' или 'disable'
        this.tfaSecret = null; // Секрет для включения 2FA
        this.tfaOtpauth = null; // otpauth URI
        this.avatarFile = null;
        this.currentTab = 'email';
        this.init();
    }

    init() {
        console.log('ProfileManager: Initializing...');
        this.bindEvents();
        this.loadTfaStatus();
        console.log('ProfileManager: Initialized successfully');
    }

    // Bind event listeners
    bindEvents() {
        // Tab switching
        $('.profile-page').on('click', '.tab-btn', (e) => {
            const tab = $(e.currentTarget).data('tab');
            this.switchTab(tab);
        });

        // Avatar upload
        $('.profile-page #avatarInput').on('change', (e) => {
            const file = e.target.files[0];
            const ok = this.handleAvatarUpload(file);
            if (ok) {
                this.uploadAvatar(file);
            }
        });

        // Form submissions
        $('.profile-page #emailForm').on('submit', (e) => {
            e.preventDefault();
            this.handleEmailChange();
        });

        $('.profile-page #passwordForm').on('submit', (e) => {
            e.preventDefault();
            this.handlePasswordChange();
        });

        $('.profile-page #tfaSetupForm').on('submit', (e) => {
            e.preventDefault();
            this.handleTfaSetup();
        });

        $('.profile-page #tfaDisableForm').on('submit', (e) => {
            e.preventDefault();
            this.handleTfaDisable();
        });

        // 2FA toggle
        $('.profile-page #tfaToggle').on('change', (e) => {
            this.handleTfaToggle(e.target.checked);
        });

        // Modal events
        $('.profile-modal').on('hidden.bs.modal', () => {
            console.log('Profile modal closed');
        });

        // TFA code input formatting
        $('.profile-page .tfa-code-input').on('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 6) {
                value = value.substring(0, 6);
            }
            e.target.value = value;
        });
    }

    // Switch between tabs
    switchTab(tabName) {
        if (this.currentTab === tabName) return;

        // Update tab buttons
        $('.profile-page .tab-btn').removeClass('active');
        $(`.profile-page .tab-btn[data-tab="${tabName}"]`).addClass('active');

        // Update tab content
        $('.profile-page .tab-content').removeClass('active');
        $('.profile-page #' + tabName + '-tab').addClass('active');

        this.currentTab = tabName;
    }

    // Handle avatar upload and preview
    handleAvatarUpload(file) {
        if (!file) return false;

        // Validate file type
        const allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
        if (!allowedTypes.includes(file.type)) {
            this.showNotification('error', 'Пожалуйста, выберите изображение в формате PNG или JPG');
            return false;
        }

        // Validate file size (max 5MB)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            this.showNotification('error', 'Размер файла не должен превышать 5MB');
            return false;
        }

        this.avatarFile = file;

        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            const avatarImage = $('.profile-page #avatarImage');
            const avatarPlaceholder = $('.profile-page #avatarPlaceholder');

            avatarImage.attr('src', e.target.result);
            avatarImage.show();
            avatarPlaceholder.hide();
        };
        reader.readAsDataURL(file);

        return true;
    }

    // Upload avatar to server
    uploadAvatar(file) {
        const formData = new FormData();
        formData.append('avatar', file);
        // CSRF для Laravel (поддержка обоих meta-имен)
        const csrf = $('meta[name="csrf-token"]').attr('content') || $('meta[name="csrf"]').attr('content');
        if (csrf) {
            formData.append('_token', csrf);
        }

        const $btn = $('.profile-page .avatar-upload-btn');
        $btn.prop('disabled', true).addClass('loading');

        $.ajax({
            url: '/user/profile/avatar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {},
            success: (response) => {
                try {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res && res.success && res.avatar_url) {
                        // Обновляем аватар в UI
                        const avatarImage = $('.profile-page #avatarImage');
                        const avatarPlaceholder = $('.profile-page #avatarPlaceholder');
                        avatarImage.attr('src', res.avatar_url).show();
                        avatarPlaceholder.hide();
                        this.showNotification('success', res.message || 'Аватар успешно обновлён');
                    } else {
                        this.showNotification('error', (res && res.message) || 'Не удалось загрузить аватар');
                    }
                } catch (_) {
                    this.showNotification('success', 'Аватар успешно обновлён');
                }
            },
            error: (xhr) => {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data && data.errors && data.errors.avatar) {
                        this.showNotification('error', data.errors.avatar[0]);
                    } else {
                        this.showNotification('error', 'Ошибка загрузки аватара');
                    }
                } catch (_) {
                    this.showNotification('error', 'Ошибка загрузки аватара');
                }
            },
            complete: () => {
                $btn.prop('disabled', false).removeClass('loading');
            }
        });
    }

    // Handle email change
    handleEmailChange() {
        const currentEmail = $('.profile-page #currentEmail').val();
        const newEmail = $('.profile-page #newEmail').val().trim();
        const currentPassword = $('.profile-page #currentPasswordEmail').val();

        // Validation
        if (!this.validateEmail(newEmail)) {
            this.showNotification('error', 'Пожалуйста, введите корректный email адрес');
            return;
        }

        if (newEmail === currentEmail) {
            this.showNotification('error', 'Новый email должен отличаться от текущего');
            return;
        }

        if (!currentPassword) {
            this.showNotification('error', 'Пожалуйста, введите текущий пароль');
            return;
        }

        // Запрос на смену Email
        const data = {
            email: newEmail,
            password: currentPassword
        };

        // Показываем загрузку на кнопке
        const $btn = $('.profile-page #emailForm .btn.btn-primary');
        $btn.prop('disabled', true).addClass('loading');

        post('/user/profile/email', data, (response) => {
            try {
                const res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res && res.success) {
                    $('.profile-page #currentEmail').val(res.email);
                    $('.profile-page .user-email').text(res.email);
                    $('.profile-page #newEmail').val('');
                    $('.profile-page #currentPasswordEmail').val('');
                    this.showNotification('success', res.message || 'Email успешно изменен');
                } else {
                    this.showNotification('error', (res && res.message) || 'Не удалось изменить email');
                }
            } catch (e) {
                this.showNotification('success', 'Email успешно изменен');
                $('.profile-page #currentEmail').val(newEmail);
                $('.profile-page .user-email').text(newEmail);
                $('.profile-page #newEmail').val('');
                $('.profile-page #currentPasswordEmail').val('');
            }
            // вернуть кнопку
            $btn.prop('disabled', false).removeClass('loading');
        }, (xhr) => {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data && data.errors) {
                    if (data.errors.email) this.showNotification('error', data.errors.email[0]);
                    if (data.errors.password) this.showNotification('error', data.errors.password[0]);
                } else {
                    this.showNotification('error', 'Ошибка при изменении email');
                }
            } catch (_) {
                this.showNotification('error', 'Ошибка при изменении email');
            }
            $btn.prop('disabled', false).removeClass('loading');
        });
    }

    // Show email confirmation modal
    showEmailConfirmationModal(newEmail) {
        const modal = $('.profile-modal#emailConfirmModal');
        modal.find('.modal-body p').text(`Мы отправили код подтверждения на ${newEmail}`);

        if (typeof bootstrap !== 'undefined') {
            const modalInstance = new bootstrap.Modal(modal[0]);
            modalInstance.show();
        }
    }

    // Confirm email change
    confirmEmailChange() {
        const confirmationCode = $('.profile-modal #emailConfirmCode').val().trim();

        if (!confirmationCode || confirmationCode.length !== 6) {
            this.showNotification('error', 'Пожалуйста, введите 6-значный код подтверждения');
            return;
        }

        // Simulate API call
        setTimeout(() => {
            const newEmail = $('.profile-page #newEmail').val();
            $('.profile-page #currentEmail').val(newEmail);
            $('.profile-page #newEmail').val('');
            $('.profile-page #currentPasswordEmail').val('');

            this.hideModal('emailConfirmModal');
            this.showNotification('success', 'Email успешно изменен');
        }, 500);
    }

    // Handle password change
    handlePasswordChange() {
        const currentPassword = $('.profile-page #currentPassword').val();
        const newPassword = $('.profile-page #newPassword').val();
        const confirmPassword = $('.profile-page #confirmPassword').val();

        // Validation
        if (!currentPassword) {
            this.showNotification('error', 'Пожалуйста, введите текущий пароль');
            return;
        }

        if (newPassword.length < 8) {
            this.showNotification('error', 'Новый пароль должен содержать минимум 8 символов');
            return;
        }

        if (newPassword !== confirmPassword) {
            this.showNotification('error', 'Пароли не совпадают');
            return;
        }

        if (newPassword === currentPassword) {
            this.showNotification('error', 'Новый пароль должен отличаться от текущего');
            return;
        }

        // Отправляем изменение пароля
        this.submitPasswordChange();
    }

    // Show password confirmation modal
    showPasswordConfirmationModal() {
        const modal = $('.profile-modal#passwordConfirmModal');

        if (typeof bootstrap !== 'undefined') {
            const modalInstance = new bootstrap.Modal(modal[0]);
            modalInstance.show();
        }
    }

    // Confirm password change
    confirmPasswordChange() {
        this.submitPasswordChange();
    }

    // Submit password change to backend
    submitPasswordChange() {
        const currentPassword = $('.profile-page #currentPassword').val();
        const newPassword = $('.profile-page #newPassword').val();
        const confirmPassword = $('.profile-page #confirmPassword').val();

        if (newPassword !== confirmPassword) {
            this.showNotification('error', 'Пароли не совпадают');
            return;
        }

        const $btn = $('.profile-page #passwordForm .btn.btn-primary');
        $btn.prop('disabled', true).addClass('loading');

        post('/user/profile/password', {
            current_password: currentPassword,
            new_password: newPassword
        }, (response) => {
            try {
                const res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res && res.success) {
                    $('.profile-page #currentPassword').val('');
                    $('.profile-page #newPassword').val('');
                    $('.profile-page #confirmPassword').val('');
                    this.showNotification('success', res.message || 'Пароль успешно изменён');
                } else {
                    this.showNotification('error', (res && res.message) || 'Не удалось изменить пароль');
                }
            } catch (_) {
                this.showNotification('success', 'Пароль успешно изменён');
                $('.profile-page #currentPassword').val('');
                $('.profile-page #newPassword').val('');
                $('.profile-page #confirmPassword').val('');
            }
            $btn.prop('disabled', false).removeClass('loading');
        }, (xhr) => {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data && data.errors) {
                    if (data.errors.current_password) this.showNotification('error', data.errors.current_password[0]);
                    if (data.errors.password) this.showNotification('error', data.errors.password[0]);
                } else {
                    this.showNotification('error', 'Ошибка при изменении пароля');
                }
            } catch (_) {
                this.showNotification('error', 'Ошибка при изменении пароля');
            }
            $btn.prop('disabled', false).removeClass('loading');
        });
    }

    // Handle 2FA toggle
    handleTfaToggle(enabled) {
        // Если 2FA уже в процессе, не позволяем менять состояние
        if (this.tfaInProcess) {
            // Возвращаем тумблер в предыдущее состояние
            $('.profile-page #tfaToggle').prop('checked', this.tfaEnabled);
            return;
        }

        // Определяем тип процесса
        if (enabled && !this.tfaEnabled) {
            // Пытаемся включить 2FA
            this.tfaInProcess = true;
            this.tfaProcessType = 'enable';
            this.showTfaSetup();
        } else if (!enabled && this.tfaEnabled) {
            // Пытаемся отключить 2FA
            this.tfaInProcess = true;
            this.tfaProcessType = 'disable';
            this.showTfaDisable();
        } else {
            // Возвращаем тумблер в предыдущее состояние
            $('.profile-page #tfaToggle').prop('checked', this.tfaEnabled);
            return;
        }

        this.updateTfaStatus();
    }

    // Show 2FA setup section
    showTfaSetup() {
        $('.profile-page #tfaSetupSection').show();
        $('.profile-page #tfaDisableSection').hide();

        // Запрашиваем секрет и otpauth у сервера
        this.requestTfaSetup();
    }

    // Show 2FA disable section
    showTfaDisable() {
        $('.profile-page #tfaSetupSection').hide();
        $('.profile-page #tfaDisableSection').show();
    }

    // Cancel 2FA process (reset to previous state)
    cancelTfaProcess() {
        this.tfaInProcess = false;
        this.tfaProcessType = null;

        // Возвращаем тумблер в предыдущее состояние
        $('.profile-page #tfaToggle').prop('checked', this.tfaEnabled);

        // Скрываем все секции
        $('.profile-page #tfaSetupSection').hide();
        $('.profile-page #tfaDisableSection').hide();

        this.updateTfaStatus();
    }

    // Update 2FA status display
    updateTfaStatus() {
        const statusIcon = $('.profile-page #tfaStatusIcon');
        const statusTitle = $('.profile-page #tfaStatusTitle');
        const statusDescription = $('.profile-page #tfaStatusDescription');

        if (this.tfaInProcess) {
            // Показываем промежуточное состояние
            if (this.tfaProcessType === 'enable') {
                statusIcon.addClass('enabled');
                statusTitle.text('2FA включается...');
                statusDescription.text('Введите код подтверждения для завершения настройки');
            } else if (this.tfaProcessType === 'disable') {
                statusIcon.addClass('enabled');
                statusTitle.text('2FA включена');
                statusDescription.text('Введите код подтверждения для отключения');
            }
        } else if (this.tfaEnabled) {
            // 2FA включена и подтверждена
            statusIcon.addClass('enabled');
            statusTitle.text('2FA включена');
            statusDescription.text('Ваш аккаунт защищен двухфакторной аутентификацией');
        } else {
            // 2FA отключена
            statusIcon.removeClass('enabled');
            statusTitle.text('2FA отключена');
            statusDescription.text('Для дополнительной безопасности рекомендуем включить двухфакторную аутентификацию');
        }
    }

    // Handle 2FA setup
    handleTfaSetup() {
        const tfaCode = $('.profile-page #tfaCode').val().trim();

        if (!tfaCode || tfaCode.length !== 6) {
            this.showNotification('error', 'Пожалуйста, введите 6-значный код из Google Authenticator');
            return;
        }

        const payload = { secret: this.tfaSecret, code: tfaCode };
        const $btns = $('.profile-page #tfaSetupForm .btn');
        $btns.prop('disabled', true);

        post('/user/profile/2fa/enable', payload, (resp) => {
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (res && res.success) {
                    $('.profile-page #tfaCode').val('');
                    $('.profile-page #tfaSetupSection').hide();
                    this.tfaEnabled = true;
                    this.tfaInProcess = false;
                    this.tfaProcessType = null;
                    $('.profile-page #tfaToggle').prop('checked', true);
                    this.updateTfaStatus();
                    this.showNotification('success', res.message || '2FA успешно включена');
                } else {
                    this.showNotification('error', (res && res.message) || 'Не удалось включить 2FA');
                }
            } catch (_) {
                this.showNotification('success', '2FA успешно включена');
                $('.profile-page #tfaSetupSection').hide();
                this.tfaEnabled = true;
                this.tfaInProcess = false;
                this.tfaProcessType = null;
                $('.profile-page #tfaToggle').prop('checked', true);
                this.updateTfaStatus();
            }
            $btns.prop('disabled', false);
        }, (xhr) => {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data && data.errors && data.errors.code) {
                    this.showNotification('error', data.errors.code[0]);
                } else {
                    this.showNotification('error', 'Ошибка включения 2FA');
                }
            } catch (_) {
                this.showNotification('error', 'Ошибка включения 2FA');
            }
            $btns.prop('disabled', false);
        });
    }

    // Handle 2FA disable
    handleTfaDisable() {
        const tfaCode = $('.profile-page #tfaDisableCode').val().trim();

        if (!tfaCode || tfaCode.length !== 6) {
            this.showNotification('error', 'Пожалуйста, введите 6-значный код из Google Authenticator');
            return;
        }

        const payload = { code: tfaCode };
        const $btns = $('.profile-page #tfaDisableForm .btn');
        $btns.prop('disabled', true);

        post('/user/profile/2fa/disable', payload, (resp) => {
            $('.profile-page #tfaDisableCode').val('');
            $('.profile-page #tfaDisableSection').hide();
            this.tfaEnabled = false;
            this.tfaInProcess = false;
            this.tfaProcessType = null;
            $('.profile-page #tfaToggle').prop('checked', false);
            this.updateTfaStatus();
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                this.showNotification('success', (res && res.message) || '2FA отключена');
            } catch (_) {
                this.showNotification('success', '2FA отключена');
            }
            $btns.prop('disabled', false);
        }, (xhr) => {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data && data.errors && data.errors.code) {
                    this.showNotification('error', data.errors.code[0]);
                } else {
                    this.showNotification('error', 'Ошибка отключения 2FA');
                }
            } catch (_) {
                this.showNotification('error', 'Ошибка отключения 2FA');
            }
            $btns.prop('disabled', false);
        });
    }

    // Copy manual key to clipboard
    copyManualKey() {
        const manualKey = $('.profile-page #manualKey').val();

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(manualKey)
                .then(() => {
                    this.showNotification('success', 'Ключ скопирован в буфер обмена');
                })
                .catch(() => {
                    this.fallbackCopyMethod(manualKey);
                });
        } else {
            this.fallbackCopyMethod(manualKey);
        }
    }

    // Fallback copy method
    fallbackCopyMethod(text) {
        try {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);

            textarea.select();
            textarea.setSelectionRange(0, 99999);

            const successful = document.execCommand('copy');
            document.body.removeChild(textarea);

            if (successful) {
                this.showNotification('success', 'Ключ скопирован в буфер обмена');
            } else {
                this.showNotification('error', 'Не удалось скопировать ключ');
            }
        } catch (err) {
            console.error('Copy failed:', err);
            this.showNotification('error', 'Не удалось скопировать ключ');
        }
    }

    // Toggle password visibility
    togglePassword(inputId) {
        const input = $('.profile-page #' + inputId);
        const button = input.next('.password-toggle');
        const icon = button.find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }

    // Logout function
    logout() {
        if (confirm('Вы действительно хотите выйти?')) {
            // In real app, this would redirect to logout endpoint
            // window.location.href = '/logout';
            this.showNotification('info', 'Выход из системы...');
        }
    }

    // Hide modal
    hideModal(modalId) {
        const modal = $('.profile-modal#' + modalId);
        if (typeof bootstrap !== 'undefined') {
            const modalInstance = bootstrap.Modal.getInstance(modal[0]);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    }

    // Email validation
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // ===== Helpers for 2FA API =====
    loadTfaStatus() {
        get('/user/profile/2fa/status', {}, (resp) => {
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                this.tfaEnabled = !!(res && res.enabled);
                $('.profile-page #tfaToggle').prop('checked', this.tfaEnabled);
                this.updateTfaStatus();
            } catch (_) {
                // ignore
            }
        });
    }

    requestTfaSetup() {
        post('/user/profile/2fa/setup', {}, (resp) => {
            try {
                const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if (!res || !res.success) return;
                this.tfaSecret = res.secret;
                this.tfaOtpauth = res.otpauth;

                // Показываем секрет
                $('.profile-page #manualKey').text(this.tfaSecret);

                // Обновляем QR
                const qrImg = $('.profile-page .qr-code img');
                if (qrImg.length) {
                    // Используем надёжный QR API
                    const url = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(this.tfaOtpauth);
                    qrImg.attr('src', url);
                }
            } catch (_) {}
        });
    }

    // Show notification using SocnetApp notifications
    showNotification(type, message) {
        if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
            // Предпочитаем "умные" тосты с защитой от дублей
            if (typeof SocnetApp.notifications.showSmartToast === 'function') {
                SocnetApp.notifications.showSmartToast(type, message, undefined, true);
            } else if (type === 'success' && typeof SocnetApp.notifications.showSuccess === 'function') {
                SocnetApp.notifications.showSuccess(message);
            } else if (type === 'error' && typeof SocnetApp.notifications.showError === 'function') {
                SocnetApp.notifications.showError(message);
            } else if (type === 'info' && typeof SocnetApp.notifications.showInfo === 'function') {
                SocnetApp.notifications.showInfo(message);
            } else if (typeof SocnetApp.notifications.showInfo === 'function') {
                SocnetApp.notifications.showInfo(message);
            }
        } else {
            // Fallback to browser alert if SocnetApp is not available
            if (type === 'error') {
                alert(message);
            }
        }
    }
}

// Initialize manager immediately to avoid undefined errors
window.profileManager = null;

// Global functions for HTML onclick handlers
window.togglePassword = function(inputId) {
    if (window.profileManager) {
        window.profileManager.togglePassword(inputId);
    } else {
        console.warn('ProfileManager not initialized yet');
    }
};

window.toggleTFA = function() {
    if (window.profileManager) {
        const checkbox = $('.profile-page #tfaToggle');
        window.profileManager.handleTfaToggle(checkbox.is(':checked'));
    } else {
        console.warn('ProfileManager not initialized yet');
    }
};

window.cancelTfaProcess = function() {
    if (window.profileManager) {
        window.profileManager.cancelTfaProcess();
    } else {
        console.warn('ProfileManager not initialized yet');
    }
};

window.copyManualKey = function() {
    if (window.profileManager) {
        window.profileManager.copyManualKey();
    } else {
        console.warn('ProfileManager not initialized yet');
    }
};

window.confirmEmailChange = function() {
    if (window.profileManager) {
        window.profileManager.confirmEmailChange();
    } else {
        console.warn('ProfileManager not initialized yet');
    }
};

window.confirmPasswordChange = function() {
    if (window.profileManager) {
        window.profileManager.confirmPasswordChange();
    } else {
        console.warn('ProfileManager not initialized yet');
    }
};

window.logout = function() {
    if (window.profileManager) {
        window.profileManager.logout();
    } else {
        console.warn('ProfileManager not initialized yet');
    }
};

// Initialize when document is ready
$(document).ready(() => {
    // Prevent multiple initializations
    if (!window.profileManager) {
        window.profileManager = new ProfileManager();
        console.log('Profile page loaded');
    }
});

// Integration with main SocnetApp
if (typeof SocnetApp !== 'undefined') {
    SocnetApp.profile = {
        manager: null,
        init() {
            // Return existing manager if already initialized
            if (window.profileManager) {
                this.manager = window.profileManager;
                return this.manager;
            }
            // Create new manager only if none exists
            this.manager = new ProfileManager();
            window.profileManager = this.manager;
            return this.manager;
        }
    };

    console.log('Profile module integrated with SocnetApp');
}