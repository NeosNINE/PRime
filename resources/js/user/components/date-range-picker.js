/**
 * Dynamic Date Range Picker Modal Component
 *
 * Creates a dynamic Bootstrap modal for selecting date ranges
 * with a calendar interface similar to the reference design
 */

class DateRangePicker {
    constructor(options = {}) {
        this.options = {
            // Default options
            locale: 'ru',
            format: 'DD.MM.YYYY',
            startDate: null,
            endDate: null,
            minDate: null,
            maxDate: null,
            allowSingleDate: false,
            showApplyButton: true,
            showClearButton: true,
            autoApply: false,
            ...options
        };

        this.modalId = 'dateRangePickerModal_' + Date.now();
        this.isModalOpen = false;
        this.startDate = this.options.startDate;
        this.endDate = this.options.endDate;
        this.tempStartDate = this.options.startDate || null;
        this.tempEndDate = this.options.endDate || null;
        this.leftMonth = new Date();
        this.rightMonth = new Date();
        this.isSelectingEndDate = this.tempStartDate && !this.tempEndDate;
        this.isUpdatingSelectors = false; // guard to avoid change recursion
        // По умолчанию оба календаря показывают текущий месяц

        // Month names in Russian
        this.monthNames = [
            'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
        ];

        // Day names in Russian
        this.dayNames = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

        this.callbacks = {
            onApply: null,
            onCancel: null,
            onClear: null
        };
    }

    /**
     * Set callback functions
     */
    onApply(callback) {
        this.callbacks.onApply = callback;
        return this;
    }

    onCancel(callback) {
        this.callbacks.onCancel = callback;
        return this;
    }

    onClear(callback) {
        this.callbacks.onClear = callback;
        return this;
    }

    /**
     * Open the date range picker modal
     */
    open() {
        if (this.isModalOpen) return;

        this.createModal();
        this.renderCalendars();
        this.showModal();
    }

    /**
     * Close the modal
     */
    close() {
        if (!this.isModalOpen) return;

        const modal = document.getElementById(this.modalId);
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        }
    }

    /**
     * Create the modal HTML structure
     */
    createModal() {
        // Remove existing modal if any
        this.removeModal();

        const modalHtml = `
            <div class="modal fade date-range-picker-modal" id="${this.modalId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-calendar-alt"></i>
                                Выберите период
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="date-range-picker-content">
                                <div class="calendars-container">
                                    <!-- Left Calendar -->
                                    <div class="calendar-wrapper left-calendar">
                                        <div class="calendar-header-container">
                                            <div class="month-year-selector">
                                                <select class="month-selector custom-select" data-calendar="left" data-action="changeMonth">
                                                    ${this.monthNames.map((month, index) =>
                                                        `<option value="${index}">${month}</option>`
                                                    ).join('')}
                                                </select>
                                                <select class="year-selector custom-select" data-calendar="left" data-action="changeYear">
                                                    ${this.generateYearOptions()}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="calendar-grid">
                                            <div class="calendar-header">
                                                ${this.dayNames.map(day => `<div class="day-header">${day}</div>`).join('')}
                                            </div>
                                            <div class="calendar-body left-calendar-body">
                                                <!-- Left calendar days will be rendered here -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Calendar -->
                                    <div class="calendar-wrapper right-calendar">
                                        <div class="calendar-header-container">
                                            <div class="month-year-selector">
                                                <select class="month-selector custom-select" data-calendar="right" data-action="changeMonth">
                                                    ${this.monthNames.map((month, index) =>
                                                        `<option value="${index}">${month}</option>`
                                                    ).join('')}
                                                </select>
                                                <select class="year-selector custom-select" data-calendar="right" data-action="changeYear">
                                                    ${this.generateYearOptions()}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="calendar-grid">
                                            <div class="calendar-header">
                                                ${this.dayNames.map(day => `<div class="day-header">${day}</div>`).join('')}
                                            </div>
                                            <div class="calendar-body right-calendar-body">
                                                <!-- Right calendar days will be rendered here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="selected-dates-info">
                                <span class="selected-range-text">Выберите даты</span>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                                    Отмена
                                </button>
                                ${this.options.showApplyButton ? `
                                    <button type="button" class="btn btn-primary" data-action="apply">
                                        <i class="fas fa-check"></i>
                                        Применить
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        // Инициализируем кастомные селекты для month/year внутри модалки
        try {
            if (window.CustomSelect && typeof window.CustomSelect.initializeAll === 'function') {
                // CustomSelect сам проинициализирует все с классом .custom-select
                setTimeout(() => window.CustomSelect.initializeAll(), 0);
            }
        } catch (e) {}
        this.bindEvents();
    }

    /**
     * Generate year options for the select
     */
    generateYearOptions() {
        const currentYear = new Date().getFullYear();
        const startYear = currentYear - 10;
        const endYear = currentYear + 5;
        let options = '';

        for (let year = startYear; year <= endYear; year++) {
            options += `<option value="${year}">${year}</option>`;
        }

        return options;
    }

    /**
     * Bind event listeners to modal elements
     */
    bindEvents() {
        const modal = document.getElementById(this.modalId);
        if (!modal) return;

        // Navigation events
        modal.addEventListener('click', (e) => {
            const action = e.target.closest('[data-action]')?.getAttribute('data-action');
            if (!action) return;

            e.preventDefault();

            switch (action) {
                case 'apply':
                    this.applySelection();
                    break;
            }
        });

        // Month/Year selector changes (native select)
        modal.addEventListener('change', (e) => {
            if (this.isUpdatingSelectors) return;
            const action = e.target.getAttribute('data-action');
            const calendar = e.target.getAttribute('data-calendar');

            if (action === 'changeMonth') {
                if (calendar === 'left') {
                    this.leftMonth.setMonth(parseInt(e.target.value));
                } else if (calendar === 'right') {
                    this.rightMonth.setMonth(parseInt(e.target.value));
                }
                this.renderCalendars();
            } else if (action === 'changeYear') {
                if (calendar === 'left') {
                    this.leftMonth.setFullYear(parseInt(e.target.value));
                } else if (calendar === 'right') {
                    this.rightMonth.setFullYear(parseInt(e.target.value));
                }
                this.renderCalendars();
            }
        });

        // Change handling for CustomSelect (listen to change on hidden original select)
        modal.querySelectorAll('.month-selector, .year-selector').forEach((sel) => {
            sel.addEventListener('change', (e) => {
                if (this.isUpdatingSelectors) return;
                const action = e.target.getAttribute('data-action');
                const calendar = e.target.getAttribute('data-calendar');
                if (action === 'changeMonth') {
                    const val = parseInt(e.target.value, 10);
                    if (calendar === 'left') this.leftMonth.setMonth(val); else this.rightMonth.setMonth(val);
                    this.renderCalendars();
                    this.syncSelectors();
                } else if (action === 'changeYear') {
                    const val = parseInt(e.target.value, 10);
                    if (calendar === 'left') this.leftMonth.setFullYear(val); else this.rightMonth.setFullYear(val);
                    this.renderCalendars();
                    this.syncSelectors();
                }
            });
        });

        // Modal events
        modal.addEventListener('shown.bs.modal', () => {
            this.isModalOpen = true;
            this.updateMonthYearSelectors();
        });

        modal.addEventListener('hidden.bs.modal', () => {
            this.isModalOpen = false;
            this.removeModal();

            if (this.callbacks.onCancel) {
                this.callbacks.onCancel();
            }
        });
    }

    /**
     * Show the modal
     */
    showModal() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    }

    /**
     * Remove modal from DOM
     */
    removeModal() {
        const existingModal = document.getElementById(this.modalId);
        if (existingModal) {
            existingModal.remove();
        }
    }

    /**
     * Update month and year selectors for both calendars
     */
    updateMonthYearSelectors() {
        const modal = document.getElementById(this.modalId);
        if (!modal) return;

        // Update left calendar selectors
        const leftMonthSelector = modal.querySelector('.left-calendar .month-selector');
        const leftYearSelector = modal.querySelector('.left-calendar .year-selector');

        this.isUpdatingSelectors = true;
        if (leftMonthSelector) {
            if (window.CustomSelect && typeof window.CustomSelect.setValue === 'function') {
                window.CustomSelect.setValue(leftMonthSelector, String(this.leftMonth.getMonth()));
            } else {
                leftMonthSelector.value = this.leftMonth.getMonth();
            }
        }
        if (leftYearSelector) {
            if (window.CustomSelect && typeof window.CustomSelect.setValue === 'function') {
                window.CustomSelect.setValue(leftYearSelector, String(this.leftMonth.getFullYear()));
            } else {
                leftYearSelector.value = this.leftMonth.getFullYear();
            }
        }

        // Update right calendar selectors
        const rightMonthSelector = modal.querySelector('.right-calendar .month-selector');
        const rightYearSelector = modal.querySelector('.right-calendar .year-selector');

        if (rightMonthSelector) {
            if (window.CustomSelect && typeof window.CustomSelect.setValue === 'function') {
                window.CustomSelect.setValue(rightMonthSelector, String(this.rightMonth.getMonth()));
            } else {
                rightMonthSelector.value = this.rightMonth.getMonth();
            }
        }
        if (rightYearSelector) {
            if (window.CustomSelect && typeof window.CustomSelect.setValue === 'function') {
                window.CustomSelect.setValue(rightYearSelector, String(this.rightMonth.getFullYear()));
            } else {
                rightYearSelector.value = this.rightMonth.getFullYear();
            }
        }
        this.isUpdatingSelectors = false;
    }

    // Sync selects values with guard
    syncSelectors() {
        this.updateMonthYearSelectors();
    }

    /**
     * Render both calendars
     */
    renderCalendars() {
        this.renderSingleCalendar(this.leftMonth, '.left-calendar-body', 'left');
        this.renderSingleCalendar(this.rightMonth, '.right-calendar-body', 'right');
    }

    /**
     * Render a single calendar
     */
    renderSingleCalendar(monthDate, containerSelector, calendarSide) {
        const modal = document.getElementById(this.modalId);
        if (!modal) return;

        const calendarBody = modal.querySelector(containerSelector);
        if (!calendarBody) return;

        const year = monthDate.getFullYear();
        const month = monthDate.getMonth();

        // Get first day of month and adjust for Monday start
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDay = (firstDay.getDay() + 6) % 7; // Adjust so Monday = 0

        let html = '';
        let date = 1;
        const daysInMonth = lastDay.getDate();

        // Calculate weeks needed - only show weeks that contain days of current month
        const weeksNeeded = Math.ceil((startDay + daysInMonth) / 7);
        const totalCells = weeksNeeded * 7;

        // Get previous and next month info for empty cells
        // Use new Date(year, month, 0) to get the last day of the previous month
        const prevMonth = new Date(year, month, 0);
        const prevMonthDays = prevMonth.getDate();
        let nextMonthDate = 1;

        for (let i = 0; i < totalCells; i++) {
            if (i % 7 === 0) {
                html += '<div class="calendar-week">';
            }

            if (i < startDay) {
                // Previous month days
                const prevDate = prevMonthDays - (startDay - 1 - i);
                const prevDateObj = new Date(year, month - 1, prevDate);
                html += `<div class="calendar-day empty" data-date="${this.formatDate(prevDateObj)}" data-calendar="${calendarSide}">${prevDateObj.getDate()}</div>`;
            } else if (date > daysInMonth) {
                // Next month days
                const nextDateObj = new Date(year, month + 1, nextMonthDate);
                html += `<div class="calendar-day empty" data-date="${this.formatDate(nextDateObj)}" data-calendar="${calendarSide}">${nextDateObj.getDate()}</div>`;
                nextMonthDate++;
            } else {
                // Current month days
                const currentDate = new Date(year, month, date);
                const classes = this.getDayClasses(currentDate, calendarSide);

                html += `<div class="calendar-day ${classes}" data-date="${this.formatDate(currentDate)}" data-calendar="${calendarSide}">${date}</div>`;
                date++;
            }

            if (i % 7 === 6) {
                html += '</div>';
            }
        }

        calendarBody.innerHTML = html;

        // Bind day click events (remove previous listeners first)
        const newCalendarBody = calendarBody.cloneNode(true);
        calendarBody.parentNode.replaceChild(newCalendarBody, calendarBody);

        newCalendarBody.addEventListener('click', (e) => {
            if (e.target.classList.contains('calendar-day')) {
                const dateString = e.target.getAttribute('data-date');
                const calendar = e.target.getAttribute('data-calendar');
                this.selectDate(dateString, calendar);
            }
        });
    }

    /**
     * Get CSS classes for a calendar day
     */
    getDayClasses(date, calendarSide) {
        const classes = [];
        const today = new Date();

        // Check if it's today
        if (this.isSameDay(date, today)) {
            classes.push('today');
        }

        // Show selected dates only in appropriate calendar
        if (calendarSide === 'left' && this.tempStartDate && this.isSameDay(date, this.tempStartDate)) {
            classes.push('selected');
        } else if (calendarSide === 'right' && this.tempEndDate && this.isSameDay(date, this.tempEndDate)) {
            classes.push('selected');
        }

        return classes.join(' ');
    }

    /**
     * Select a date
     */
    selectDate(dateString, calendarSide) {
        const selectedDate = new Date(dateString);

        if (calendarSide === 'left') {
            // Left calendar sets start date
            const prevStart = this.tempStartDate;
            this.tempStartDate = selectedDate;

            // Update classes without full rerender if month didn't change
            if (this.isSameMonth(prevStart, this.leftMonth) || this.isSameMonth(selectedDate, this.leftMonth)) {
                this.updateSelectedClasses('left', prevStart, selectedDate);
            }

            // If clicked on a date from different month, switch calendar
            const selectedMonth = selectedDate.getMonth();
            const selectedYear = selectedDate.getFullYear();
            const currentLeftMonth = this.leftMonth.getMonth();
            const currentLeftYear = this.leftMonth.getFullYear();

            if (selectedMonth !== currentLeftMonth || selectedYear !== currentLeftYear) {
                this.leftMonth = new Date(selectedYear, selectedMonth, 1);
                // Adjust right calendar to be next month
                this.rightMonth = new Date(selectedYear, selectedMonth + 1, 1);
                this.renderCalendars();
            }

            // If end date is before start date, clear it
            if (this.tempEndDate && this.tempEndDate < this.tempStartDate) {
                const prevEnd = this.tempEndDate;
                this.tempEndDate = null;
                if (this.isSameMonth(prevEnd, this.rightMonth)) {
                    this.updateSelectedClasses('right', prevEnd, null);
                }
            }
        } else if (calendarSide === 'right') {
            // Right calendar sets end date
            const prevEnd = this.tempEndDate;
            this.tempEndDate = selectedDate;

            // Update classes without full rerender if month didn't change
            if (this.isSameMonth(prevEnd, this.rightMonth) || this.isSameMonth(selectedDate, this.rightMonth)) {
                this.updateSelectedClasses('right', prevEnd, selectedDate);
            }

            // If clicked on a date from different month, switch calendar
            const selectedMonth = selectedDate.getMonth();
            const selectedYear = selectedDate.getFullYear();
            const currentRightMonth = this.rightMonth.getMonth();
            const currentRightYear = this.rightMonth.getFullYear();

            if (selectedMonth !== currentRightMonth || selectedYear !== currentRightYear) {
                this.rightMonth = new Date(selectedYear, selectedMonth, 1);
                // Adjust left calendar to be previous month
                this.leftMonth = new Date(selectedYear, selectedMonth - 1, 1);
                this.renderCalendars();
            }

            // If start date is after end date, clear it
            if (this.tempStartDate && this.tempStartDate > this.tempEndDate) {
                const prevStart = this.tempStartDate;
                this.tempStartDate = null;
                if (this.isSameMonth(prevStart, this.leftMonth)) {
                    this.updateSelectedClasses('left', prevStart, null);
                }
            }
        } else {
            // Fallback for clicks on empty cells (previous/next month)
            if (!this.tempStartDate) {
                this.tempStartDate = selectedDate;
                // Switch left calendar to show the selected month
                this.leftMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
                this.rightMonth = new Date(this.leftMonth.getFullYear(), this.leftMonth.getMonth() + 1, 1);
            } else {
                this.tempEndDate = selectedDate;

                // Ensure correct order
                if (this.tempEndDate < this.tempStartDate) {
                    const temp = this.tempStartDate;
                    this.tempStartDate = this.tempEndDate;
                    this.tempEndDate = temp;
                }

                // Switch calendars to show both selected dates
                this.adjustCalendarsToShowDates();
            }
        }

        // Only rerender if months changed due to navigation; otherwise classes already updated
        this.updateSelectedDatesInfo();
        this.updateMonthYearSelectors();

        // Auto-apply if enabled and both dates are selected
        if (this.options.autoApply && this.tempStartDate && this.tempEndDate) {
            setTimeout(() => this.applySelection(), 100);
        }
    }

    /**
     * Adjust calendars to show selected dates optimally
     */
    adjustCalendarsToShowDates() {
        if (!this.tempStartDate || !this.tempEndDate) return;

        const startYear = this.tempStartDate.getFullYear();
        const startMonth = this.tempStartDate.getMonth();
        const endYear = this.tempEndDate.getFullYear();
        const endMonth = this.tempEndDate.getMonth();

        // If dates are in the same month
        if (startYear === endYear && startMonth === endMonth) {
            this.leftMonth = new Date(startYear, startMonth, 1);
            this.rightMonth = new Date(startYear, startMonth + 1, 1);
        }
        // If dates are in consecutive months
        else if ((startYear === endYear && endMonth === startMonth + 1) ||
                 (endYear === startYear + 1 && startMonth === 11 && endMonth === 0)) {
            this.leftMonth = new Date(startYear, startMonth, 1);
            this.rightMonth = new Date(endYear, endMonth, 1);
        }
        // If dates are far apart, show start date in left, end date in right
        else {
            this.leftMonth = new Date(startYear, startMonth, 1);
            this.rightMonth = new Date(endYear, endMonth, 1);
        }
    }

    /**
     * Update the selected dates info text
     */
    updateSelectedDatesInfo() {
        const modal = document.getElementById(this.modalId);
        if (!modal) return;

        const infoElement = modal.querySelector('.selected-range-text');
        if (!infoElement) return;

        if (!this.tempStartDate) {
            infoElement.textContent = 'Выберите начальную дату';
        } else if (!this.tempEndDate && this.isSelectingEndDate) {
            infoElement.textContent = `Начальная дата: ${this.formatDisplayDate(this.tempStartDate)}. Выберите конечную дату`;
        } else if (this.tempStartDate && this.tempEndDate) {
            infoElement.textContent = `${this.formatDisplayDate(this.tempStartDate)} - ${this.formatDisplayDate(this.tempEndDate)}`;
        } else {
            infoElement.textContent = 'Выберите даты';
        }
    }

    /**
     * Apply the selection
     */
    applySelection() {
        if (!this.tempStartDate) return;

        this.startDate = this.tempStartDate;
        this.endDate = this.tempEndDate;

        if (this.callbacks.onApply) {
            this.callbacks.onApply({
                startDate: this.startDate,
                endDate: this.endDate,
                startDateString: this.formatDisplayDate(this.startDate),
                endDateString: this.endDate ? this.formatDisplayDate(this.endDate) : null
            });
        }

        this.close();
    }

    /**
     * Clear the selection
     */
    clearSelection() {
        this.tempStartDate = null;
        this.tempEndDate = null;
        this.startDate = null;
        this.endDate = null;
        this.isSelectingEndDate = false;

        if (this.callbacks.onClear) {
            this.callbacks.onClear();
        }

        this.renderCalendars();
        this.updateSelectedDatesInfo();
    }

    /**
     * Utility functions
     */
    isSameDay(date1, date2) {
        return date1.getFullYear() === date2.getFullYear() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getDate() === date2.getDate();
    }

    formatDate(date) {
        return date.getFullYear() + '-' +
               String(date.getMonth() + 1).padStart(2, '0') + '-' +
               String(date.getDate()).padStart(2, '0');
    }

    formatDisplayDate(date) {
        return String(date.getDate()).padStart(2, '0') + '.' +
               String(date.getMonth() + 1).padStart(2, '0') + '.' +
               date.getFullYear();
    }

    /**
     * Check if date belongs to the same month/year as monthDate
     */
    isSameMonth(date, monthDate) {
        return (
            date && monthDate &&
            date.getFullYear() === monthDate.getFullYear() &&
            date.getMonth() === monthDate.getMonth()
        );
    }

    /**
     * Update DOM classes for selected day without full rerender
     */
    updateSelectedClasses(calendarSide, previousDate, newDate) {
        const modal = document.getElementById(this.modalId);
        if (!modal) return;

        // Remove previous selection in this calendar
        if (previousDate) {
            const prevSelector = `.calendar-day.selected[data-calendar="${calendarSide}"][data-date="${this.formatDate(previousDate)}"]`;
            modal.querySelectorAll(prevSelector).forEach((el) => el.classList.remove('selected'));
        }

        // Add new selection in this calendar
        if (newDate) {
            const newSelector = `.calendar-day[data-calendar="${calendarSide}"][data-date="${this.formatDate(newDate)}"]`;
            const target = modal.querySelector(newSelector);
            if (target) target.classList.add('selected');
        }
    }
}

// Global factory function
window.createDateRangePicker = function(options = {}) {
    return new DateRangePicker(options);
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DateRangePicker;
}
