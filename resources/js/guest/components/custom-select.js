// Custom Select Component - Universal Custom Select
// ================================================

const CustomSelect = {
    selects: [],

    // Helper method to create icon element (image or icon class)
    createIconElement(icon) {
        if (!icon) return '';

        // Check if icon is a URL (starts with http://, https://, or /)
        if (icon.startsWith('http://') || icon.startsWith('https://') || icon.startsWith('/')) {
            return `<img src="${icon}" alt="icon" class="custom-select-icon-image">`;
        }

        // Check if icon is a data URL
        if (icon.startsWith('data:')) {
            return `<img src="${icon}" alt="icon" class="custom-select-icon-image">`;
        }

        // Check if icon is an asset path (contains /assets/ or .png, .jpg, .svg, etc.)
        if (icon.includes('/assets/') || icon.includes('.png') || icon.includes('.jpg') || icon.includes('.jpeg') || icon.includes('.svg') || icon.includes('.gif')) {
            return `<img src="${icon}" alt="icon" class="custom-select-icon-image">`;
        }

        // Default: treat as CSS class
        return `<i class="${icon}"></i>`;
    },

    init() {
        this.initializeAll();
        this.bindGlobalEvents();
        console.log('Custom Select initialized');
    },

    initializeAll() {
        document.querySelectorAll('.custom-select').forEach(select => {
            if (!select.hasAttribute('data-initialized')) {
                this.createCustomSelect(select);
            }
        });
    },

    createCustomSelect(originalSelect) {
        // Mark as initialized
        originalSelect.setAttribute('data-initialized', 'true');

        // Get configuration
        const placeholder = originalSelect.getAttribute('data-placeholder') || 'Выберите опцию';
        const selectedValue = originalSelect.value;
        const selectedOption = originalSelect.querySelector(`option[value="${selectedValue}"]`);

        // Create wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select-container';

        // Create display element
        const display = document.createElement('div');
        display.className = 'custom-select-display';

        // Set initial display content
        if (selectedOption) {
            const icon = selectedOption.getAttribute('data-icon');
            display.innerHTML = `
                ${this.createIconElement(icon)}
                <span class="custom-select-text">${selectedOption.textContent}</span>
                <i class="fas fa-chevron-down custom-select-arrow"></i>
            `;
        } else {
            display.innerHTML = `
                <span class="custom-select-text custom-select-placeholder">${placeholder}</span>
                <i class="fas fa-chevron-down custom-select-arrow"></i>
            `;
        }

        // Create dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'custom-select-dropdown';

        // Create options
        const optionsList = document.createElement('div');
        optionsList.className = 'custom-select-options';

        Array.from(originalSelect.options).forEach(option => {
            const optionElement = document.createElement('div');
            optionElement.className = 'custom-select-option';
            optionElement.setAttribute('data-value', option.value);

            if (option.selected) {
                optionElement.classList.add('selected');
            }

            if (option.disabled) {
                optionElement.classList.add('disabled');
                optionElement.setAttribute('data-disabled', 'true');
            }

            const icon = option.getAttribute('data-icon');
            const isDisabled = option.disabled;

            optionElement.innerHTML = `
                ${this.createIconElement(icon)}
                <span class="custom-select-option-text">${option.textContent}</span>
                ${isDisabled ? '<i class="fas fa-exclamation-triangle custom-select-option-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Данный платежный метод временно недоступен"></i>' : ''}
            `;

            optionsList.appendChild(optionElement);
        });

        dropdown.appendChild(optionsList);

        // Assemble custom select
        wrapper.appendChild(display);
        wrapper.appendChild(dropdown);

        // Insert after original select and hide original
        originalSelect.style.display = 'none';
        originalSelect.parentNode.insertBefore(wrapper, originalSelect.nextSibling);

        // Store references
        const customSelect = {
            original: originalSelect,
            wrapper: wrapper,
            display: display,
            dropdown: dropdown,
            options: optionsList,
            isOpen: false
        };

        this.selects.push(customSelect);
        this.bindSelectEvents(customSelect);

        // Initialize Bootstrap tooltips for warning icons
        this.initializeWarningTooltips(wrapper);

        return customSelect;
    },

    bindSelectEvents(customSelect) {
        const { display, dropdown, options, original } = customSelect;

        // Toggle dropdown
        display.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleDropdown(customSelect);
        });

        // Option selection
        options.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const option = e.target.closest('.custom-select-option');
            if (option && !option.classList.contains('disabled')) {
                this.selectOption(customSelect, option);
            }
        });

        // Keyboard navigation
        display.addEventListener('keydown', (e) => {
            this.handleKeyNavigation(customSelect, e);
        });

        // Make display focusable
        display.setAttribute('tabindex', '0');
    },

    bindGlobalEvents() {
        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            this.selects.forEach(customSelect => {
                if (!customSelect.wrapper.contains(e.target)) {
                    this.closeDropdown(customSelect);
                }
            });
        });

        // Close dropdowns on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.selects.forEach(customSelect => {
                    this.closeDropdown(customSelect);
                });
            }
        });
    },

    toggleDropdown(customSelect) {
        if (customSelect.isOpen) {
            this.closeDropdown(customSelect);
        } else {
            this.openDropdown(customSelect);
        }
    },

    openDropdown(customSelect) {
        // Close other dropdowns first
        this.selects.forEach(select => {
            if (select !== customSelect) {
                this.closeDropdown(select);
            }
        });

        customSelect.wrapper.classList.add('open');
        customSelect.isOpen = true;

        // Focus on selected option
        const selectedOption = customSelect.options.querySelector('.custom-select-option.selected');
        if (selectedOption) {
            this.scrollToOption(customSelect, selectedOption);
        }
    },

    closeDropdown(customSelect) {
        customSelect.wrapper.classList.remove('open');
        customSelect.isOpen = false;
    },

    selectOption(customSelect, optionElement) {
        const value = optionElement.getAttribute('data-value');
        const text = optionElement.querySelector('.custom-select-option-text').textContent;

        // Find icon element (could be i or img)
        const icon = optionElement.querySelector('i:not(.custom-select-option-warning)') ||
            optionElement.querySelector('.custom-select-icon-image');

        // Update original select
        customSelect.original.value = value;

        // Update display
        customSelect.display.innerHTML = `
            ${icon ? icon.outerHTML : ''}
            <span class="custom-select-text">${text}</span>
            <i class="fas fa-chevron-down custom-select-arrow"></i>
        `;

        // Update selected state
        customSelect.options.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        optionElement.classList.add('selected');

        // Close dropdown
        this.closeDropdown(customSelect);

        // Trigger change event on original select
        const changeEvent = new Event('change', { bubbles: true });
        customSelect.original.dispatchEvent(changeEvent);
    },

    handleKeyNavigation(customSelect, e) {
        if (!customSelect.isOpen) {
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                e.preventDefault();
                this.openDropdown(customSelect);
            }
            return;
        }

        const options = customSelect.options.querySelectorAll('.custom-select-option:not(.disabled)');
        const currentSelected = customSelect.options.querySelector('.custom-select-option.selected');
        let currentIndex = Array.from(options).indexOf(currentSelected);

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                currentIndex = Math.min(currentIndex + 1, options.length - 1);
                this.highlightOption(customSelect, options[currentIndex]);
                break;

            case 'ArrowUp':
                e.preventDefault();
                currentIndex = Math.max(currentIndex - 1, 0);
                this.highlightOption(customSelect, options[currentIndex]);
                break;

            case 'Enter':
                e.preventDefault();
                const highlighted = customSelect.options.querySelector('.custom-select-option.highlighted') || currentSelected;
                if (highlighted && !highlighted.classList.contains('disabled')) {
                    this.selectOption(customSelect, highlighted);
                }
                break;

            case 'Escape':
                e.preventDefault();
                this.closeDropdown(customSelect);
                break;
        }
    },

    highlightOption(customSelect, optionElement) {
        // Remove previous highlight
        customSelect.options.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('highlighted');
        });

        // Add highlight to new option
        optionElement.classList.add('highlighted');

        // Scroll to option if needed
        this.scrollToOption(customSelect, optionElement);
    },

    scrollToOption(customSelect, optionElement) {
        const dropdown = customSelect.dropdown;
        const optionTop = optionElement.offsetTop;
        const optionHeight = optionElement.offsetHeight;
        const dropdownScrollTop = dropdown.scrollTop;
        const dropdownHeight = dropdown.clientHeight;

        if (optionTop < dropdownScrollTop) {
            dropdown.scrollTop = optionTop;
        } else if (optionTop + optionHeight > dropdownScrollTop + dropdownHeight) {
            dropdown.scrollTop = optionTop + optionHeight - dropdownHeight;
        }
    },

    // Public method to update options programmatically
    updateOptions(selectElement, newOptions) {
        const customSelect = this.selects.find(cs => cs.original === selectElement);
        if (!customSelect) return;

        // Clear existing options
        customSelect.options.innerHTML = '';

        // Add new options
        newOptions.forEach(optionData => {
            const optionElement = document.createElement('div');
            optionElement.className = 'custom-select-option';
            optionElement.setAttribute('data-value', optionData.value);

            if (optionData.selected) {
                optionElement.classList.add('selected');
            }

            if (optionData.disabled) {
                optionElement.classList.add('disabled');
                optionElement.setAttribute('data-disabled', 'true');
            }

            const isDisabled = optionData.disabled;
            optionElement.innerHTML = `
                ${this.createIconElement(optionData.icon)}
                <span class="custom-select-option-text">${optionData.text}</span>
                ${isDisabled ? '<i class="fas fa-exclamation-triangle custom-select-option-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Данный платежный метод временно недоступен"></i>' : ''}
            `;

            customSelect.options.appendChild(optionElement);
        });

        // Initialize tooltips for new warning icons
        this.initializeWarningTooltips(customSelect.wrapper);

        // Update display if needed
        const selectedOption = customSelect.options.querySelector('.custom-select-option.selected');
        if (selectedOption) {
            // Find icon element (could be i or img)
            const icon = selectedOption.querySelector('i:not(.custom-select-option-warning)') ||
                selectedOption.querySelector('.custom-select-icon-image');
            const text = selectedOption.querySelector('.custom-select-option-text').textContent;

            customSelect.display.innerHTML = `
                ${icon ? icon.outerHTML : ''}
                <span class="custom-select-text">${text}</span>
                <i class="fas fa-chevron-down custom-select-arrow"></i>
            `;
        }
    },

    // Public method to set value programmatically
    setValue(selectElement, value) {
        const customSelect = this.selects.find(cs => cs.original === selectElement);
        if (!customSelect) return;

        const option = customSelect.options.querySelector(`[data-value="${value}"]`);
        if (option) {
            this.selectOption(customSelect, option);
        }
    },

    // Public method to destroy custom select
    destroy(selectElement) {
        const index = this.selects.findIndex(cs => cs.original === selectElement);
        if (index === -1) return;

        const customSelect = this.selects[index];
        customSelect.wrapper.remove();
        customSelect.original.style.display = '';
        customSelect.original.removeAttribute('data-initialized');

        this.selects.splice(index, 1);
    },

    // Initialize Bootstrap tooltips for warning icons
    initializeWarningTooltips(wrapper) {
        // Check if Bootstrap tooltips are available
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const warningIcons = wrapper.querySelectorAll('.custom-select-option-warning');
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
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    CustomSelect.init();
});

// Export for external access
window.CustomSelect = CustomSelect;