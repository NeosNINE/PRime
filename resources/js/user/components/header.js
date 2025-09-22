// Header module: all header-related behaviors moved from app.js

// Uses global jQuery ($) and Bootstrap if available

const Header = {
    init() {
        this.setupMobileMenu();
        this.setupDropdowns();
        this.setupBalanceSelector();
    },

    setupMobileMenu() {
        const toggle = $('.navbar-toggle');
        const menu = $('.navbar-menu');

        toggle.on('click', () => {
            menu.toggleClass('active');
            toggle.toggleClass('active');
        });

        // Close mobile menu when clicking outside
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.navbar').length) {
                menu.removeClass('active');
                toggle.removeClass('active');
            }
        });

        // Close mobile menu on window resize
        $(window).on('resize', () => {
            if ($(window).width() > 1023) {
                menu.removeClass('active');
                toggle.removeClass('active');
            }
        });
    },

    setupDropdowns() {
        // Toggle generic header dropdowns
        $(document).on('click', '[data-toggle="dropdown"]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const $button = $(this);
            const $dropdown = $button.closest('.control-dropdown');
            const $menu = $dropdown.find('.dropdown-menu');

            if ($dropdown.length === 0 || $menu.length === 0)
                return;

            // Close others
            $('.dropdown-menu').not($menu).removeClass('active');
            $('.control-dropdown').not($dropdown).removeClass('active');

            // Toggle current
            $dropdown.toggleClass('active');
            $menu.toggleClass('active');
        });

        // Close on outside click / ESC
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.control-dropdown').length) {
                $('.dropdown-menu').removeClass('active');
                $('.control-dropdown').removeClass('active');
            }
        });

        $(document).on('keydown', (e) => {
            if (e.key === 'Escape') {
                $('.dropdown-menu').removeClass('active');
                $('.control-dropdown').removeClass('active');
            }
        });
    },

    setupBalanceSelector() {
        // Open/close balance dropdown
        $(document).on('click', '.balance-btn[data-toggle="dropdown"]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const $button = $(this);
            const $dropdown = $button.closest('.balance-selector');
            const $menu = $dropdown.find('.balance-dropdown-menu');

            if ($dropdown.length === 0 || $menu.length === 0)
                return;

            // Close others
            $('.dropdown-menu').not($menu).removeClass('active');
            $('.control-dropdown').not($dropdown).removeClass('active');
            $('.balance-btn').not($button).removeClass('active');

            // Toggle current
            $dropdown.toggleClass('active');
            $menu.toggleClass('active');
            $button.toggleClass('active');
        });

        // Helper to update amount formatting without overwriting value source
        function renderBalanceAmount(amount, currencySymbol, currencyCode) {
            const $balanceBtn = $('.balance-btn');
            const $balanceAmount = $balanceBtn.find('.balance-amount');
            const $balanceIcon = $balanceBtn.find('.balance-icon');
            if ($balanceAmount.length && $balanceIcon.length) {
                $balanceIcon.attr('data-currency', currencyCode).text(currencySymbol);
                $balanceAmount.attr('data-currency', currencyCode).text(`${Number(amount || 0).toFixed(2)}`);
            }
        }

        // Expose global updater used by balance page
        window.updateHeaderBalance = function(balanceUsd){
            // Keep current currency symbol from DOM
            const $balanceBtn = $('.balance-btn');
            const currencySymbol = $balanceBtn.find('.balance-icon').text() || '$';
            const currencyCode = $balanceBtn.find('.balance-icon').attr('data-currency') || 'usd';
            // Пробуем подтянуть конвертированный баланс с backend
            get('/user/refresh-data', {}, (resp) => {
                try{
                    const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                    if (res && res.balance) {
                        const { amount, code, symbol } = res.balance;
                        renderBalanceAmount(Number(amount || 0), symbol || currencySymbol, code || currencyCode);
                        return;
                    }
                }catch(e){}
                // fallback: показать USD как есть
                renderBalanceAmount(Number(balanceUsd || 0), currencySymbol, currencyCode);
            }, () => {
                renderBalanceAmount(Number(balanceUsd || 0), currencySymbol, currencyCode);
            });
        };

        // Choose currency
        $(document).on('click', '.balance-option[data-currency]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const $option = $(this);
            const currency = $option.data('currency');
            const currencySymbol = $option.find('.currency-icon').text();
            const optionAmount = $option.find('.currency-amount').text();
            const $balanceBtn = $('.balance-btn');
            const $balanceIcon = $balanceBtn.find('.balance-icon');
            const $balanceAmount = $balanceBtn.find('.balance-amount');

            // Обновим только символ и data-currency, сумму берем с backend/refresh
            $balanceIcon.attr('data-currency', currency).text(currencySymbol);
            // Мгновенно отрисуем сумму из выбранного пункта, чтобы не ждать AJAX
            if ($balanceAmount.length && optionAmount) {
                $balanceAmount.attr('data-currency', currency).text(optionAmount);
            }
            $('.balance-option').removeClass('active');
            $option.addClass('active');

            // Persist selection on backend
            post('/currency/set', { currency }, () => {}, () => {});

            // Close dropdown
            $('.balance-dropdown-menu').removeClass('active');
            $('.balance-selector').removeClass('active');
            $('.balance-btn').removeClass('active');

            // Санкционируем обновление баланса с backend (refresh-data)
            get('/user/refresh-data', {}, (resp) => {
                try{
                    const res = typeof resp === 'string' ? JSON.parse(resp) : resp;
                    if (res && res.balance) {
                        const { amount, code, symbol } = res.balance;
                        renderBalanceAmount(Number(amount || 0), symbol || currencySymbol, code || currency);
                    } else if (res && typeof res.balance_usd !== 'undefined') {
                        renderBalanceAmount(Number(res.balance_usd || 0), currencySymbol, currency);
                    }
                }catch(e){}
            });
        });

        // Close when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.balance-selector').length) {
                $('.balance-dropdown-menu').removeClass('active');
                $('.balance-selector').removeClass('active');
                $('.balance-btn').removeClass('active');
            }
        });
    },
};

// Auto-init on DOM ready (safe even if called twice)
$(document).ready(() => {
    try { Header.init(); } catch (e) { console.warn('Header init error', e); }
});

eventClick('#confirmLogoutBtn', function () {

    $('.form-logout').submit();

});

window.Header = Header;

export default Header;


