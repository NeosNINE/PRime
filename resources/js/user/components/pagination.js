/**
 * Global Pagination Component
 * Глобальный компонент пагинации для переиспользования
 */
class PaginationComponent {
    constructor(container, options = {}) {
        this.container = $(container);
        this.options = {
            currentPage: 1,
            totalPages: 1,
            totalItems: 0,
            itemsPerPage: 20,
            showInfo: true,
            showPerPage: false,
            perPageOptions: [20, 50, 100],
            onPageChange: null,
            onPerPageChange: null,
            ...options
        };

        this.init();
    }

    init() {
        this.render();
        this.bindEvents();
    }

    render() {
        const { currentPage, totalPages, totalItems, itemsPerPage, showInfo, showPerPage, perPageOptions } = this.options;

        const startItem = (currentPage - 1) * itemsPerPage + 1;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);

        let html = '';

        if (showPerPage || showInfo) {
            html += `<div class="pagination-box">`;

            if (showPerPage) {
                html += `
                    <div class="pagination-per-page">
                        <label for="paginationPerPage" class="pagination-label form-label">На странице</label>
                        <select id="paginationPerPage" class="custom-select" data-placeholder="Выберите количество">
                            ${perPageOptions.map(option =>
                    `<option value="${option}" ${option === itemsPerPage ? 'selected' : ''}>${option}</option>`
                ).join('')}
                        </select>
                    </div>
                `;
            }

            if (showInfo) {
                html += `
                    <div class="pagination-info">
                        <span>Показано <strong>${startItem}-${endItem}</strong> из <strong>${totalItems}</strong></span>
                    </div>
                `;
            }

            html += `</div>`;
        }

        html += `
            <nav class="pagination-nav">
                <ul class="pagination">
                    ${this.renderPaginationButtons(currentPage, totalPages)}
                </ul>
            </nav>
        `;

        this.container.html(html);

        if (this.options.showPerPage && typeof CustomSelect !== 'undefined' && CustomSelect.initializeAll) {
            setTimeout(() => {
                CustomSelect.initializeAll();
            }, 100);
        }
    }

    renderPaginationButtons(currentPage, totalPages) {
        let html = '';

        const prevDisabled = currentPage === 1 ? 'disabled' : '';
        html += `
            <li class="page-item ${prevDisabled}">
                <a class="page-link page-arrow" href="#" tabindex="-1" title="Предыдущая страница">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;


        if (currentPage <= 3) {
            for (let i = 1; i <= Math.min(3, totalPages); i++) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `
                    <li class="page-item ${activeClass}">
                        <a class="page-link" href="#">${i}</a>
                    </li>
                `;
            }
            if (totalPages > 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                html += `<li class="page-item"><a class="page-link" href="#">${totalPages}</a></li>`;
            }
        } else if (currentPage >= totalPages - 2) {
            html += `<li class="page-item"><a class="page-link" href="#">1</a></li>`;
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            for (let i = Math.max(totalPages - 2, 1); i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `
                    <li class="page-item ${activeClass}">
                        <a class="page-link" href="#">${i}</a>
                    </li>
                `;
            }
        } else {
            html += `<li class="page-item"><a class="page-link" href="#">1</a></li>`;
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;

            for (let i = currentPage - 1; i <= currentPage + 1; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `
                    <li class="page-item ${activeClass}">
                        <a class="page-link" href="#">${i}</a>
                    </li>
                `;
            }

            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="#">${totalPages}</a></li>`;
        }

        const nextDisabled = currentPage === totalPages ? 'disabled' : '';
        html += `
            <li class="page-item ${nextDisabled}">
                <a class="page-link page-arrow" href="#" title="Следующая страница">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

        return html;
    }

    bindEvents() {
        this.container.on('click', '.page-link', (e) => {
            e.preventDefault();
            const $link = $(e.currentTarget);

            if ($link.closest('.page-item').hasClass('disabled')) {
                return;
            }

            if ($link.hasClass('page-arrow')) {
                const $pageItem = $link.closest('.page-item');

                if ($pageItem.is(':first-child')) {
                    if (this.options.currentPage > 1) {
                        this.goToPage(this.options.currentPage - 1);
                    }
                }
                else if ($pageItem.is(':last-child')) {
                    if (this.options.currentPage < this.options.totalPages) {
                        this.goToPage(this.options.currentPage + 1);
                    }
                }
            } else {
                const page = $link.text().trim();
                if (!isNaN(page)) {
                    this.goToPage(parseInt(page));
                }
            }
        });

        if (this.options.showPerPage) {
            this.container.on('change', '#paginationPerPage', (e) => {
                const newPerPage = parseInt($(e.target).val());
                this.changePerPage(newPerPage);
            });
        }
    }

    goToPage(page) {
        if (page >= 1 && page <= this.options.totalPages && page !== this.options.currentPage) {
            this.options.currentPage = page;
            this.updatePaginationOnly();

            if (this.options.onPageChange) {
                this.options.onPageChange(page, this);
            }
        }
    }

    changePerPage(itemsPerPage) {
        if (itemsPerPage !== this.options.itemsPerPage) {
            this.options.itemsPerPage = itemsPerPage;
            this.options.currentPage = 1;

            this.updatePerPageSelector();

            this.updateInfoOnly();

            this.updatePaginationOnly();

            if (this.options.onPerPageChange) {
                this.options.onPerPageChange(itemsPerPage, this);
            }
        }
    }

    setData(data) {

        const oldOptions = { ...this.options };
        this.options = { ...this.options, ...data };

        const pageChanged = oldOptions.currentPage !== this.options.currentPage;
        const totalPagesChanged = oldOptions.totalPages !== this.options.totalPages;
        const totalItemsChanged = oldOptions.totalItems !== this.options.totalItems;
        const itemsPerPageChanged = oldOptions.itemsPerPage !== this.options.itemsPerPage;

        if (pageChanged && !totalItemsChanged && !itemsPerPageChanged) {
            this.updatePaginationOnly();
        } else if (itemsPerPageChanged && !totalItemsChanged) {
            this.updatePerPageSelector();
            this.updateInfoOnly();
            this.updatePaginationOnly();
        } else if (totalItemsChanged && !itemsPerPageChanged) {
            this.updateInfoOnly();
            this.updatePaginationOnly();
        } else if (totalItemsChanged && itemsPerPageChanged) {
            this.render();
        } else if (pageChanged) {
            this.updatePaginationOnly();
        }

        const method = this.getUpdateMethod(pageChanged, totalPagesChanged, totalItemsChanged, itemsPerPageChanged);
    }

    getUpdateMethod(pageChanged, totalPagesChanged, totalItemsChanged, itemsPerPageChanged) {
        if (pageChanged && !totalItemsChanged && !itemsPerPageChanged) {
            return 'updatePaginationOnly';
        } else if (itemsPerPageChanged && !totalItemsChanged) {
            return 'updatePerPageSelector + updateInfoOnly + updatePaginationOnly';
        } else if (totalItemsChanged && !itemsPerPageChanged) {
            return 'updateInfoOnly + updatePaginationOnly';
        } else if (totalItemsChanged && itemsPerPageChanged) {
            return 'render (full)';
        } else if (pageChanged) {
            return 'updatePaginationOnly';
        }
        return 'none';
    }

    update() {
        this.render();
    }

    updatePaginationOnly() {
        const { currentPage, totalPages } = this.options;

        this.container.find('.page-item').removeClass('active');
        const $activePageItem = this.container.find(`.page-item .page-link:contains("${currentPage}")`).closest('.page-item');
        if ($activePageItem.length) {
            $activePageItem.addClass('active');
        }

        const $prevBtn = this.container.find('.page-item:first-child');
        const $nextBtn = this.container.find('.page-item:last-child');

        if (currentPage === 1) {
            $prevBtn.addClass('disabled');
        } else {
            $prevBtn.removeClass('disabled');
        }

        if (currentPage === totalPages) {
            $nextBtn.addClass('disabled');
        } else {
            $nextBtn.removeClass('disabled');
        }

        this.updatePageNumbers(currentPage, totalPages);
    }

    updateInfoOnly() {
        const { currentPage, totalItems, itemsPerPage } = this.options;
        const startItem = (currentPage - 1) * itemsPerPage + 1;
        const endItem = Math.min(currentPage * itemsPerPage, totalItems);


        const $info = this.container.find('.pagination-info span');
        if ($info.length) {
            $info.html(`Показано <strong>${startItem}-${endItem}</strong> из <strong>${totalItems}</strong>`);
        }
    }

    updatePerPageSelector() {
        const { itemsPerPage, perPageOptions } = this.options;

        const $select = this.container.find('#paginationPerPage');
        if ($select.length) {
            $select.val(itemsPerPage);

            this.updateCustomSelectDisplay(itemsPerPage);
        }
    }

    updateCustomSelectDisplay(selectedValue) {
        const $customSelect = this.container.find('.custom-select-wrapper');
        if ($customSelect.length) {
            const $select = $customSelect.find('select');
            const $display = $customSelect.find('.custom-select-display');
            const $text = $display.find('.custom-select-text');

            if ($select.length && $display.length && $text.length) {
                const selectedOption = $select.find(`option[value="${selectedValue}"]`);
                if (selectedOption.length) {
                    $text.text(selectedOption.text());

                    $customSelect.find('.custom-select-option').removeClass('selected');
                    $customSelect.find(`.custom-select-option[data-value="${selectedValue}"]`).addClass('selected');

                    if ($display.hasClass('placeholder')) {
                        $display.removeClass('placeholder');
                    }
                }
            }
        }
    }

    updateActivePageOnly() {
        const { currentPage } = this.options;

        this.container.find('.page-item').removeClass('active');

        const $firstPageItem = this.container.find('.page-item .page-link:contains("1")').closest('.page-item');
        if ($firstPageItem.length) {
            $firstPageItem.addClass('active');
        }

        const $prevBtn = this.container.find('.page-item:first-child');
        const $nextBtn = this.container.find('.page-item:last-child');

        $prevBtn.addClass('disabled');

        if (this.options.totalPages > 1) {
            $nextBtn.removeClass('disabled');
        }
    }

    updatePageNumbers(currentPage, totalPages) {
        const $pagination = this.container.find('.pagination');
        const $pageItems = $pagination.find('.page-item:not(:first-child):not(:last-child)');

        if ($pageItems.length !== this.getVisiblePageCount(currentPage, totalPages)) {
            const pageNumbersHtml = this.renderPageNumbersOnly(currentPage, totalPages);
            $pageItems.remove();
            $pagination.find('.page-item:first-child').after(pageNumbersHtml);
        } else {
            $pageItems.removeClass('active');
            $pageItems.each((index, item) => {
                const $item = $(item);
                const pageNum = parseInt($item.find('.page-link').text());
                if (pageNum === currentPage) {
                    $item.addClass('active');
                }
            });
        }
    }

    renderPageNumbersOnly(currentPage, totalPages) {
        let html = '';

        if (currentPage <= 3) {
            for (let i = 1; i <= Math.min(3, totalPages); i++) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `
                    <li class="page-item ${activeClass}">
                        <a class="page-link" href="#">${i}</a>
                    </li>
                `;
            }
            if (totalPages > 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                html += `<li class="page-item"><a class="page-link" href="#">${totalPages}</a></li>`;
            }
        } else if (currentPage >= totalPages - 2) {
            html += `<li class="page-item"><a class="page-link" href="#">1</a></li>`;
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            for (let i = Math.max(totalPages - 2, 1); i <= totalPages; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `
                    <li class="page-item ${activeClass}">
                        <a class="page-link" href="#">${i}</a>
                    </li>
                `;
            }
        } else {
            html += `<li class="page-item"><a class="page-link" href="#">1</a></li>`;
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;

            for (let i = currentPage - 1; i <= currentPage + 1; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `
                    <li class="page-item ${activeClass}">
                        <a class="page-link" href="#">${i}</a>
                    </li>
                `;
            }

            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="#">${totalPages}</a></li>`;
        }

        return html;
    }

    getVisiblePageCount(currentPage, totalPages) {
        let count = 0;

        if (currentPage <= 3) {
            count = Math.min(3, totalPages);
            if (totalPages > 3) {
                count += 2;
            }
        } else if (currentPage >= totalPages - 2) {
            count = 1 + 1 + Math.min(3, totalPages);
        } else {
            count = 1 + 1 + 3 + 1 + 1;
        }

        return count;
    }

    destroy() {
        this.container.off('click', '.page-link');
        if (this.options.showPerPage) {
            this.container.off('change', '#paginationPerPage');
        }
        this.container.empty();
    }
}

window.initializePagination = (container, options) => {
    return new PaginationComponent(container, options);
};

window.PaginationComponent = PaginationComponent;