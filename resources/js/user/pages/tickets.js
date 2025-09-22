/**
 * Tickets Page Manager
 * Управление страницей тикетов поддержки
 */
class TicketsManager {
    constructor() {
        this.tickets = [];
        this.filteredTickets = [];
        this.currentFilters = {
            search: '',
            status: 'all',
            category: 'all'
        };
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.searchTimeout = null;
        this.currentTicket = null;
        this.chatMessages = [];
        this.attachedFiles = [];
        this.chatAttachedFiles = [];

        // Pagination component instance
        this.pagination = null;

        this.init();
    }

    init() {
        console.log('TicketsManager: Initializing...');

        this.generateSampleTickets();
        this.initializePagination();
        this.bindEvents();
        this.applyFilters();

        console.log('TicketsManager: Initialized successfully');
    }

    // Generate sample tickets data
    generateSampleTickets() {
        const categories = [
            { id: 'order', name: 'Проблема с заказом', icon: 'fas fa-shopping-cart' },
            { id: 'payment', name: 'Проблема с платежом', icon: 'fas fa-credit-card' },
            { id: 'general', name: 'Общий вопрос', icon: 'fas fa-question-circle' },
            { id: 'technical', name: 'Техническая проблема', icon: 'fas fa-cog' }
        ];

        const statuses = ['open', 'answered', 'closed'];
        const subjects = [
            'Заказ не выполняется уже 3 дня',
            'Платеж не поступил на баланс',
            'Как изменить пароль от аккаунта?',
            'Ошибка при загрузке страницы',
            'Качество выполнения заказа не соответствует',
            'Возврат средств за отмененный заказ',
            'Не работает API для автозаказов',
            'Вопрос по тарифам и ценам'
        ];

        this.tickets = [];
        for (let i = 1; i <= 25; i++) {
            const category = categories[Math.floor(Math.random() * categories.length)];
            const status = statuses[Math.floor(Math.random() * statuses.length)];
            const createdAt = new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000);
            const lastReply = new Date(createdAt.getTime() + Math.random() * 7 * 24 * 60 * 60 * 1000);

            this.tickets.push({
                id: 1000 + i,
                subject: subjects[Math.floor(Math.random() * subjects.length)],
                category: category.id,
                categoryName: category.name,
                categoryIcon: category.icon,
                status: status,
                createdAt: createdAt,
                lastReply: status === 'open' ? null : lastReply,
                messagesCount: Math.floor(Math.random() * 10) + 1,
                hasUnread: status === 'answered' && Math.random() > 0.5
            });
        }

        // Sort by creation date (newest first)
        this.tickets.sort((a, b) => b.createdAt - a.createdAt);
        console.log(`Generated ${this.tickets.length} sample tickets`);
    }

    // Initialize pagination component
    initializePagination() {
        const paginationContainer = document.getElementById('ticketsPagination');
        if (paginationContainer && typeof initializePagination === 'function') {
            this.pagination = initializePagination(paginationContainer, {
                currentPage: this.currentPage,
                totalPages: Math.ceil(this.filteredTickets.length / this.itemsPerPage),
                totalItems: this.filteredTickets.length,
                itemsPerPage: this.itemsPerPage,
                showInfo: true,
                showPerPage: true,
                perPageOptions: [10, 20, 50],
                onPageChange: (page) => {
                    this.currentPage = page;
                    this.renderTickets();
                    this.scrollToTop();
                },
                onPerPageChange: (itemsPerPage) => {
                    this.itemsPerPage = itemsPerPage;
                    this.currentPage = 1;
                    this.updatePagination();
                    this.renderTickets();
                    this.scrollToTop();
                }
            });
        }
    }

    // Update pagination data
    updatePagination() {
        if (this.pagination) {
            this.pagination.setData({
                currentPage: this.currentPage,
                totalPages: Math.ceil(this.filteredTickets.length / this.itemsPerPage),
                totalItems: this.filteredTickets.length,
                itemsPerPage: this.itemsPerPage
            });
        }
    }

    // Scroll to top of tickets section
    scrollToTop() {
        const ticketsSection = document.querySelector('.tickets-page .tickets-section');
        if (ticketsSection) {
            ticketsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Generate sample messages for a ticket
    generateSampleMessages(ticketId) {
        const messages = [
            {
                id: 1,
                type: 'user',
                content: 'Здравствуйте! У меня возникла проблема с заказом #12345. Он был создан 3 дня назад, но до сих пор не выполняется. Можете помочь разобраться?',
                timestamp: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000),
                status: 'read',
                attachments: []
            },
            {
                id: 2,
                type: 'support',
                content: 'Здравствуйте! Спасибо за обращение. Мы проверим статус вашего заказа и свяжемся с провайдером. Обычно такие вопросы решаются в течение 24 часов.',
                timestamp: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000 + 2 * 60 * 60 * 1000),
                status: 'read',
                attachments: []
            },
            {
                id: 3,
                type: 'user',
                content: 'Спасибо за ответ! Буду ждать обновлений.',
                timestamp: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000 + 3 * 60 * 60 * 1000),
                status: 'read',
                attachments: []
            },
            {
                id: 4,
                type: 'support',
                content: 'Добрый день! Мы связались с провайдером. Заказ будет выполнен в ближайшие часы. Приносим извинения за задержку.',
                timestamp: new Date(Date.now() - 24 * 60 * 60 * 1000),
                status: 'sent',
                attachments: [
                    { name: 'order_details.pdf', size: '245 KB', type: 'pdf' }
                ]
            }
        ];

        return messages;
    }

    // Bind event listeners
    bindEvents() {
        // Create ticket button
        $('.tickets-page #createTicketBtn, .tickets-page #createFirstTicketBtn').on('click', () => {
            this.showCreateTicketModal();
        });

        // Search input
        $('.tickets-page #ticketSearch').on('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentFilters.search = e.target.value.toLowerCase();
                this.applyFilters();
            }, 300);
        });

        // Filter events
        $('.tickets-page #statusFilter').on('change', (e) => {
            this.currentFilters.status = e.target.value;
            this.applyFilters();
        });

        $('.tickets-page #categoryFilter').on('change', (e) => {
            this.currentFilters.category = e.target.value;
            this.applyFilters();
        });

        // Clear filters
        $('.tickets-page #clearTicketFilters').on('click', () => {
            this.clearFilters();
        });

        // Table row clicks
        $('.tickets-page').on('click', 'tbody tr', (e) => {
            if (!$(e.target).closest('.open-chat-btn').length) {
                const ticketId = $(e.currentTarget).data('ticket-id');
                this.openTicketChat(ticketId);
            }
        });

        // Open chat buttons
        $('.tickets-page').on('click', '.open-chat-btn', (e) => {
            e.stopPropagation();
            const ticketId = $(e.currentTarget).data('ticket-id');
            this.openTicketChat(ticketId);
        });

        // Back to list button
        $('.tickets-page #backToListBtn').on('click', () => {
            this.showTicketsList();
        });

        // Create ticket form
        $('.tickets-modal #createTicketForm').on('submit', (e) => {
            e.preventDefault();
            this.submitTicket();
        });



        // File input handling for create ticket
        $('.tickets-modal #ticketFiles').on('change', (e) => {
            this.handleTicketFiles(e.target.files);
        });

        // File upload placeholder click for create ticket
        $('.tickets-modal').on('click', '.file-upload-placeholder', (e) => {
            e.preventDefault();
            $('.tickets-modal #ticketFiles').trigger('click');
        });

        // Drag and drop for create ticket
        $('.tickets-modal').on('dragover', '.file-upload-area', (e) => {
            e.preventDefault();
            e.stopPropagation();
            $(e.currentTarget).find('.file-upload-placeholder').addClass('dragover');
        });

        $('.tickets-modal').on('dragleave', '.file-upload-area', (e) => {
            e.preventDefault();
            e.stopPropagation();
            $(e.currentTarget).find('.file-upload-placeholder').removeClass('dragover');
        });

        $('.tickets-modal').on('drop', '.file-upload-area', (e) => {
            e.preventDefault();
            e.stopPropagation();
            $(e.currentTarget).find('.file-upload-placeholder').removeClass('dragover');

            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                this.handleTicketFiles(files);
            }
        });

        // Chat form
        $('.tickets-page #chatMessageForm').on('submit', (e) => {
            e.preventDefault();
            this.sendChatMessage();
        });

        // Chat file input
        $('.tickets-page #chatFileInput').on('change', (e) => {
            this.handleChatFiles(e.target.files);
        });

        // Close ticket
        $('.tickets-page #closeTicketBtn').on('click', () => {
            this.showCloseTicketModal();
        });

        $('.tickets-modal #confirmCloseTicketBtn').on('click', () => {
            this.closeCurrentTicket();
        });

        // Auto-resize chat input
        $('.tickets-page #chatMessageInput').on('input', (e) => {
            this.autoResizeChatInput(e.target);
        });

        // Remove file handlers
        $('.tickets-page').on('click', '.remove-file', (e) => {
            const index = $(e.currentTarget).data('index');
            this.removeAttachedFile(index);
        });

        // Remove file handlers for modal
        $('.tickets-modal').on('click', '.remove-file', (e) => {
            const index = $(e.currentTarget).data('index');
            this.removeAttachedFile(index);
        });

        // Pagination
        $('.tickets-page').on('click', '.pagination-btn', (e) => {
            const page = $(e.currentTarget).data('page');
            if (page && !$(e.currentTarget).hasClass('active')) {
                this.currentPage = parseInt(page);
                this.renderTickets();
                this.updatePagination();
            }
        });
    }

    // Apply current filters
    applyFilters() {
        this.filteredTickets = this.tickets.filter(ticket => {
            // Search filter
            if (this.currentFilters.search) {
                const searchTerm = this.currentFilters.search;
                const matchesSearch =
                    ticket.id.toString().includes(searchTerm) ||
                    ticket.subject.toLowerCase().includes(searchTerm) ||
                    ticket.categoryName.toLowerCase().includes(searchTerm);

                if (!matchesSearch) return false;
            }

            // Status filter
            if (this.currentFilters.status !== 'all' && ticket.status !== this.currentFilters.status) {
                return false;
            }

            // Category filter
            if (this.currentFilters.category !== 'all' && ticket.category !== this.currentFilters.category) {
                return false;
            }

            return true;
        });

        // Reset to first page
        this.currentPage = 1;

        // Update UI
        this.renderTickets();
        this.updateTicketsCount();
    }

    // Render tickets table
    renderTickets() {
        const tableBody = $('.tickets-page #ticketsTableBody');
        const table = $('.tickets-page #ticketsTable');
        const loadingState = $('.tickets-page #ticketsLoadingState');
        const emptyState = $('.tickets-page #ticketsEmptyState');

        // Show loading
        loadingState.show();
        table.hide();
        emptyState.hide();

        // Simulate loading delay
        setTimeout(() => {
            // Hide loading first
            loadingState.hide();

            if (this.filteredTickets.length === 0) {
                // Show empty state when no tickets found
                emptyState.show();
                // Hide pagination when no tickets
                if (this.pagination) {
                    $('.tickets-pagination').hide();
                }
                return;
            }

            // Show pagination when there are tickets
            if (this.pagination) {
                $('.tickets-pagination').show();
            }

            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            const ticketsToShow = this.filteredTickets.slice(startIndex, endIndex);

            const ticketsHtml = ticketsToShow.map(ticket => this.createTicketRow(ticket)).join('');
            tableBody.html(ticketsHtml);
            table.show();

            // Update pagination after rendering tickets
            this.updatePagination();

        }, 300);
    }

    // Create ticket row HTML
    createTicketRow(ticket) {
        const statusClass = `status-${ticket.status}`;
        const statusText = this.getStatusText(ticket.status);
        const lastReplyText = ticket.lastReply ?
            this.formatDate(ticket.lastReply) :
            'Нет ответов';

        return `
            <tr data-ticket-id="${ticket.id}">
                <td>
                    <span class="ticket-id-cell">#${ticket.id}</span>
                </td>
                <td>
                    <div class="ticket-category-cell">
                        <i class="${ticket.categoryIcon}"></i>
                        ${ticket.categoryName}
                    </div>
                </td>
                <td>
                    <span class="ticket-subject-cell">${ticket.subject}</span>
                </td>
                <td>
                    <span class="ticket-status-badge ${statusClass}">${statusText}</span>
                </td>
                <td>
                    <span class="ticket-last-reply-cell">${lastReplyText}</span>
                </td>
                <td>
                    <span class="ticket-date-cell">${this.formatDate(ticket.createdAt)}</span>
                </td>
                <td>
                    <div class="ticket-actions-cell">
                        <div class="action-buttons">
                            <button class="open-chat-btn" data-ticket-id="${ticket.id}">
                                <i class="fas fa-comments"></i>
                                <span class="btn-text">Открыть</span>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }

    // Show create ticket modal
    showCreateTicketModal() {
        this.attachedFiles = [];
        $('.tickets-modal #createTicketForm')[0].reset();
        $('.tickets-modal #selectedTicketFiles').empty();


        const modal = new bootstrap.Modal(document.getElementById('createTicketModal'));
        modal.show();
    }



    // Handle file selection for ticket creation
    handleTicketFiles(files) {
        Array.from(files).forEach(file => {
            if (this.validateFile(file)) {
                this.attachedFiles.push(file);
            }
        });
        this.renderAttachedFiles();
    }

    // Validate file
    validateFile(file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'text/plain',
                             'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        if (file.size > maxSize) {
            this.showNotification('error', 'Файл слишком большой. Максимальный размер: 10MB');
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            this.showNotification('error', 'Неподдерживаемый тип файла');
            return false;
        }

        return true;
    }

    // Render attached files
    renderAttachedFiles() {
        const container = $('.tickets-modal #selectedTicketFiles');
        const html = this.attachedFiles.map((file, index) => `
            <div class="selected-file">
                <div class="file-info">
                    <i class="${this.getFileIcon(file.type)}"></i>
                    <div class="file-details">
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${this.formatFileSize(file.size)}</div>
                    </div>
                </div>
                <button type="button" class="remove-file" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');

        container.html(html);
    }

    // Submit ticket
    submitTicket() {
        const formData = {
            category: $('.tickets-modal #ticketCategory').val(),
            subject: $('.tickets-modal #ticketSubject').val(),
            message: $('.tickets-modal #ticketMessage').val(),
            files: this.attachedFiles
        };

        if (!formData.category || !formData.subject || !formData.message) {
            this.showNotification('error', 'Пожалуйста, заполните все обязательные поля');
            return;
        }

        // Simulate ticket creation
        const newTicket = {
            id: Math.max(...this.tickets.map(t => t.id)) + 1,
            subject: formData.subject,
            category: formData.category,
            categoryName: this.getCategoryName(formData.category),
            categoryIcon: this.getCategoryIcon(formData.category),
            status: 'open',
            createdAt: new Date(),
            lastReply: null,
            messagesCount: 1,
            hasUnread: false
        };

        this.tickets.unshift(newTicket);
        this.applyFilters();

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('createTicketModal'));
        modal.hide();

        this.showNotification('success', `Тикет #${newTicket.id} успешно создан!`);
    }

    // Open ticket chat
    openTicketChat(ticketId) {
        this.currentTicket = this.tickets.find(t => t.id === ticketId);
        if (!this.currentTicket) return;

        this.chatMessages = this.generateSampleMessages(ticketId);
        this.chatAttachedFiles = [];

        this.renderChatHeader();
        this.renderChatMessages();
        this.showChatSection();
    }

    // Show chat section
    showChatSection() {
        $('.tickets-page #ticketsListSection').hide();
        $('.tickets-page #ticketChatSection').show();

        // Scroll to bottom of messages
        setTimeout(() => {
            const messagesContainer = $('.tickets-page #chatMessages');
            messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
        }, 100);
    }

    // Show tickets list
    showTicketsList() {
        $('.tickets-page #ticketChatSection').hide();
        $('.tickets-page #ticketsListSection').show();
        this.currentTicket = null;
    }

    // Render chat header
    renderChatHeader() {
        if (!this.currentTicket) return;

        $('.tickets-page #chatTicketTitle').text(`Тикет #${this.currentTicket.id}: ${this.currentTicket.subject}`);
        $('.tickets-page #chatTicketStatus')
            .removeClass('status-open status-answered status-closed')
            .addClass(`status-${this.currentTicket.status}`)
            .text(this.getStatusText(this.currentTicket.status));
        $('.tickets-page #chatTicketCategory').text(this.currentTicket.categoryName);
        $('.tickets-page #chatTicketDate').text(this.formatDate(this.currentTicket.createdAt));
    }

    // Render chat messages
    renderChatMessages() {
        const container = $('.tickets-page #chatMessages');

        // Build with date separators: Сегодня / Вчера / dd.mm.yyyy
        let lastDateKey = null;
        const parts = [];
        this.chatMessages.forEach(message => {
            const msgDate = message.timestamp instanceof Date ? message.timestamp : new Date(message.timestamp);
            const dateKey = msgDate.toDateString();
            if (dateKey !== lastDateKey) {
                lastDateKey = dateKey;
                parts.push(`
                    <div class="chat-date-separator"><span>${this.getDateLabel(msgDate)}</span></div>
                `);
            }
            parts.push(this.createMessageHtml(message));
        });

        container.html(parts.join(''));
    }

    // Create message HTML
    createMessageHtml(message) {
        const messageClass = `message-${message.type}`;
        const avatar = message.type === 'user' ? 'ВЫ' : 'СП';
        const avatarClass = message.type === 'support' ? 'support-avatar' : '';

        const attachmentsHtml = message.attachments.length > 0 ?
            `<div class="message-attachments">
                ${message.attachments.map(att => `
                    <div class="attachment-item">
                        <i class="${this.getFileIcon(att.type)}"></i>
                        <span class="attachment-name">${att.name}</span>
                        <span class="attachment-size">${att.size}</span>
                    </div>
                `).join('')}
            </div>` : '';

        const statusIcons = message.type === 'user' ?
            `<div class="message-status">
                <i class="fas fa-check status-icon ${message.status === 'sent' ? 'status-sent' : 'status-read'}"></i>
                ${message.status === 'read' ? '<i class="fas fa-check status-icon status-read"></i>' : ''}
            </div>` : '';

        return `
            <div class="chat-message ${messageClass}">
                <div class="message-avatar ${avatarClass}">${avatar}</div>
                <div class="message-bubble">
                    <div class="message-content">
                        ${message.content}
                        ${attachmentsHtml}
                        <div class="message-meta">
                            <span class="message-time">${this.formatTime(message.timestamp)}</span>
                            ${statusIcons}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Send chat message
    sendChatMessage() {
        const messageText = $('.tickets-page #chatMessageInput').val().trim();

        if (!messageText && this.chatAttachedFiles.length === 0) {
            return;
        }

        const newMessage = {
            id: this.chatMessages.length + 1,
            type: 'user',
            content: messageText,
            timestamp: new Date(),
            status: 'sent',
            attachments: this.chatAttachedFiles.map(file => ({
                name: file.name,
                size: this.formatFileSize(file.size),
                type: file.type
            }))
        };

        this.chatMessages.push(newMessage);
        this.chatAttachedFiles = [];

        // Clear input and attachments
        $('.tickets-page #chatMessageInput').val('');
        $('.tickets-page #fileAttachmentArea').hide();
        $('.tickets-page #attachedFiles').empty();

        this.renderChatMessages();

        // Scroll to bottom
        setTimeout(() => {
            const messagesContainer = $('.tickets-page #chatMessages');
            messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
        }, 100);

        this.showNotification('success', 'Сообщение отправлено');
    }

    // Handle chat file attachments
    handleChatFiles(files) {
        Array.from(files).forEach(file => {
            if (this.validateFile(file)) {
                this.chatAttachedFiles.push(file);
            }
        });
        this.renderChatAttachedFiles();
    }

    // Render chat attached files
    renderChatAttachedFiles() {
        const container = $('.tickets-page #attachedFiles');

        if (this.chatAttachedFiles.length === 0) {
            $('.tickets-page #fileAttachmentArea').hide();
            return;
        }

        const html = this.chatAttachedFiles.map((file, index) => `
            <div class="attached-file">
                <i class="${this.getFileIcon(file.type)}"></i>
                <span>${file.name}</span>
                <button type="button" class="remove-file" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');

        container.html(html);
        $('.tickets-page #fileAttachmentArea').show();
    }

    // Remove attached file
    removeAttachedFile(index) {
        if (this.chatAttachedFiles.length > 0) {
            this.chatAttachedFiles.splice(index, 1);
            this.renderChatAttachedFiles();
        } else if (this.attachedFiles.length > 0) {
            this.attachedFiles.splice(index, 1);
            this.renderAttachedFiles();
        }
    }

    // Auto resize chat input
    autoResizeChatInput(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 128) + 'px';
    }

    // Show close ticket modal
    showCloseTicketModal() {
        const modal = new bootstrap.Modal(document.getElementById('closeTicketModal'));
        modal.show();
    }

    // Close current ticket
    closeCurrentTicket() {
        if (this.currentTicket) {
            this.currentTicket.status = 'closed';
            this.showNotification('success', `Тикет #${this.currentTicket.id} закрыт`);

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('closeTicketModal'));
            modal.hide();

            // Update chat header
            this.renderChatHeader();

            // Refresh list if needed
            this.applyFilters();
        }
    }

    // Clear all filters
    clearFilters() {
        this.currentFilters = {
            search: '',
            status: 'all',
            category: 'all'
        };

        // Reset UI
        $('.tickets-page #ticketSearch').val('');

        // Reset custom selects
        const statusSelect = document.getElementById('statusFilter');
        const categorySelect = document.getElementById('categoryFilter');

        if (window.CustomSelect) {
            if (statusSelect) {
                window.CustomSelect.setValue(statusSelect, 'all');
            }
            if (categorySelect) {
                window.CustomSelect.setValue(categorySelect, 'all');
            }
        } else {
            // Fallback for non-custom selects
            $('.tickets-page #statusFilter').val('all');
            $('.tickets-page #categoryFilter').val('all');
        }

        this.applyFilters();
        this.showNotification('success', 'Фильтры очищены');
    }

    // Update pagination (legacy method - now handled by component)
    updatePagination() {
        // This method is now handled by the pagination component
        // Kept for backward compatibility
        if (this.pagination) {
            this.pagination.setData({
                currentPage: this.currentPage,
                totalPages: Math.ceil(this.filteredTickets.length / this.itemsPerPage),
                totalItems: this.filteredTickets.length,
                itemsPerPage: this.itemsPerPage
            });
        }
    }

    // Update tickets count
    updateTicketsCount() {
        $('.tickets-page #ticketsCount').text(this.filteredTickets.length);
    }

    // Utility methods
    getStatusText(status) {
        const statuses = {
            open: 'Открыт',
            answered: 'Отвечен',
            closed: 'Закрыт'
        };
        return statuses[status] || status;
    }

    getCategoryName(categoryId) {
        const categories = {
            order: 'Проблема с заказом',
            payment: 'Проблема с платежом',
            general: 'Общий вопрос',
            technical: 'Техническая проблема'
        };
        return categories[categoryId] || categoryId;
    }

    getCategoryIcon(categoryId) {
        const icons = {
            order: 'fas fa-shopping-cart',
            payment: 'fas fa-credit-card',
            general: 'fas fa-question-circle',
            technical: 'fas fa-cog'
        };
        return icons[categoryId] || 'fas fa-ticket-alt';
    }

    getFileIcon(fileType) {
        if (fileType.startsWith('image/')) return 'fas fa-image';
        if (fileType === 'application/pdf') return 'fas fa-file-pdf';
        if (fileType.includes('word')) return 'fas fa-file-word';
        if (fileType === 'text/plain') return 'fas fa-file-alt';
        return 'fas fa-file';
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    formatDate(date) {
        return date.toLocaleDateString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    formatTime(date) {
        const d = date instanceof Date ? date : new Date(date);
        return d.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
    }

    getDateLabel(date) {
        const d = date instanceof Date ? new Date(date.getTime()) : new Date(date);
        const today = new Date();
        const normalize = (x) => { const y = new Date(x.getFullYear(), x.getMonth(), x.getDate()); y.setHours(0,0,0,0); return y; };
        const nd = normalize(d);
        const nt = normalize(today);
        const diffDays = Math.round((nt - nd) / (1000 * 60 * 60 * 24));
        if (diffDays === 0) return 'Сегодня';
        if (diffDays === 1) return 'Вчера';
        return this.formatDate(d);
    }

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

// Initialize when document is ready
$(document).ready(() => {
    window.ticketsManager = new TicketsManager();
    console.log('Tickets page loaded');
});

// Integration with main SocnetApp
if (typeof SocnetApp !== 'undefined') {
    SocnetApp.tickets = {
        manager: null,
        init() {
            this.manager = new TicketsManager();
            return this.manager;
        }
    };

    console.log('Tickets module integrated with SocnetApp');
}
