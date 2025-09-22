/**
 * API Page Manager
 * Управление страницей API документации
 */
class ApiManager {
    constructor() {
        this.currentApiKey = 'sk_live_abc123xyz789def456ghi';
        this.init();
    }

    init() {
        console.log('ApiManager: Initializing...');
        this.bindEvents();
        this.initSyntaxHighlighting();
        console.log('ApiManager: Initialized successfully');
    }

    // Bind event listeners
    bindEvents() {
        // Modal events for API key generation
        $('.api-modal#generateKeyModal').on('hidden.bs.modal', () => {
            console.log('Generate key modal closed');
        });

        // Note: onclick handlers are used in HTML for copy functions
        // Removed delegated event handlers to prevent duplicate execution
    }

    // Copy API key to clipboard
    copyApiKey() {
        const apiKeyInput = $('.api-page #apiKey');
        const copyBtn = $('.api-page .copy-key-btn');

        if (!apiKeyInput.length) {
            console.error('API key input not found');
            return;
        }

        const apiKey = apiKeyInput.val();

        // Visual feedback
        copyBtn.css('transform', 'scale(0.95)');
        setTimeout(() => {
            copyBtn.css('transform', '');
        }, 150);

        // Copy to clipboard
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(apiKey)
                .then(() => {
                    this.showNotification('success', 'API ключ скопирован в буфер обмена');
                    console.log('API key copied to clipboard:', apiKey);
                })
                .catch(err => {
                    console.error('Failed to copy API key:', err);
                    this.fallbackCopyMethod(apiKeyInput[0]);
                });
        } else {
            this.fallbackCopyMethod(apiKeyInput[0]);
        }
    }

    // Copy code block content to clipboard
    copyCode(button) {
        const codeBlock = $(button).closest('.code-block');
        const codeElement = codeBlock.find('pre code');

        if (!codeElement.length) {
            console.error('Code element not found');
            return;
        }

        const codeText = codeElement.text();

        // Visual feedback
        $(button).css('transform', 'scale(0.95)');
        setTimeout(() => {
            $(button).css('transform', '');
        }, 150);

        // Copy to clipboard
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(codeText)
                .then(() => {
                    this.showNotification('success', 'Код скопирован в буфер обмена');
                    console.log('Code copied to clipboard');
                })
                .catch(err => {
                    console.error('Failed to copy code:', err);
                    this.showNotification('error', 'Не удалось скопировать код');
                });
        } else {
            // Fallback for older browsers
            this.fallbackCopyText(codeText);
        }
    }

    // Generate new API key (simulation)
    generateNewApiKey() {
        const modal = $('.api-modal#generateKeyModal');
        const apiKeyInput = $('.api-page #apiKey');

        // Generate new fake API key
        const newApiKey = 'sk_live_' + this.generateRandomString(24);

        // Close modal
        if (modal.length && typeof bootstrap !== 'undefined') {
            const modalInstance = bootstrap.Modal.getInstance(modal[0]);
            if (modalInstance) {
                modalInstance.hide();
            }
        }

        // Update the input field
        apiKeyInput.val(newApiKey);
        this.currentApiKey = newApiKey;

        // Show success notification
        this.showNotification('success', 'Новый API ключ успешно сгенерирован');

        console.log('New API key generated:', newApiKey);
    }

    // Generate random string for API key
    generateRandomString(length) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    // Fallback copy method for older browsers
    fallbackCopyMethod(inputElement) {
        try {
            inputElement.select();
            inputElement.setSelectionRange(0, 99999);

            const successful = document.execCommand('copy');
            if (successful) {
                this.showNotification('success', 'API ключ скопирован в буфер обмена');
                console.log('API key copied using fallback method');
            } else {
                throw new Error('Copy command failed');
            }
        } catch (err) {
            console.error('Fallback copy method failed:', err);
            this.showNotification('error', 'Не удалось скопировать ключ автоматически. Скопируйте его вручную');
        }
    }

    // Fallback copy text method
    fallbackCopyText(text) {
        try {
            // Create temporary textarea
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
                this.showNotification('success', 'Код скопирован в буфер обмена');
                console.log('Code copied using fallback method');
            } else {
                throw new Error('Copy command failed');
            }
        } catch (err) {
            console.error('Fallback copy text method failed:', err);
            this.showNotification('error', 'Не удалось скопировать код автоматически');
        }
    }

    // Initialize JSON syntax highlighting
    initSyntaxHighlighting() {
        const codeBlocks = $('.api-page .code-block code');

        codeBlocks.each(function() {
            let html = $(this).html();

            // Highlight JSON syntax
            html = html.replace(/"([^"]+)":/g, '<span class="token property">"$1"</span>:');
            html = html.replace(/:\s*"([^"]+)"/g, ': <span class="token string">"$1"</span>');
            html = html.replace(/:\s*(\d+)/g, ': <span class="token number">$1</span>');
            html = html.replace(/:\s*(true|false)/g, ': <span class="token boolean">$1</span>');
            html = html.replace(/:\s*(null)/g, ': <span class="token null">$1</span>');
            html = html.replace(/([{}[\],])/g, '<span class="token punctuation">$1</span>');

            $(this).html(html);
        });

        console.log('JSON syntax highlighting initialized');
    }

    // Show notification using SocnetApp notifications
    showNotification(type, message) {
        if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
            if (type === 'success') {
                SocnetApp.notifications.showSuccess(message);
            } else if (type === 'error') {
                SocnetApp.notifications.showError(message);
            } else {
                SocnetApp.notifications.showInfo(message);
            }
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }
}

// Initialize manager immediately to avoid undefined errors
window.apiManager = null;

// Global functions for HTML onclick handlers with debouncing
let lastCopyApiKeyCall = 0;
let lastGenerateKeyCall = 0;
const DEBOUNCE_DELAY = 500; // 500ms debounce

window.copyApiKey = function() {
    const now = Date.now();
    if (now - lastCopyApiKeyCall < DEBOUNCE_DELAY) {
        console.log('copyApiKey: Too many calls, skipping');
        return;
    }
    lastCopyApiKeyCall = now;

    if (window.apiManager) {
        window.apiManager.copyApiKey();
    } else {
        console.warn('ApiManager not initialized yet');
    }
};

window.copyCode = function(button) {
    if (window.apiManager) {
        window.apiManager.copyCode(button);
    } else {
        console.warn('ApiManager not initialized yet');
    }
};

window.generateNewApiKey = function() {
    const now = Date.now();
    if (now - lastGenerateKeyCall < DEBOUNCE_DELAY) {
        console.log('generateNewApiKey: Too many calls, skipping');
        return;
    }
    lastGenerateKeyCall = now;

    if (window.apiManager) {
        window.apiManager.generateNewApiKey();
    } else {
        console.warn('ApiManager not initialized yet');
    }
};

// Initialize when document is ready
$(document).ready(() => {
    // Prevent multiple initializations
    if (!window.apiManager) {
        window.apiManager = new ApiManager();
        console.log('API page loaded');
    }
});

// Integration with main SocnetApp
if (typeof SocnetApp !== 'undefined') {
    SocnetApp.api = {
        manager: null,
        init() {
            // Return existing manager if already initialized
            if (window.apiManager) {
                this.manager = window.apiManager;
                return this.manager;
            }
            // Create new manager only if none exists
            this.manager = new ApiManager();
            window.apiManager = this.manager;
            return this.manager;
        }
    };

    console.log('API module integrated with SocnetApp');
}
