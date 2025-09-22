// Guest Header module: mobile menu and dropdown behaviors

const GuestHeader = {
    init() {
        this.setupMobileMenu();
        this.setupDropdowns();
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
        $(document).on('click', '[data-toggle="dropdown"]', function(e) {
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
};

$(document).ready(() => {
    try { GuestHeader.init(); } catch (e) { console.warn('GuestHeader init error', e); }
});

window.GuestHeader = GuestHeader;

export default GuestHeader;


