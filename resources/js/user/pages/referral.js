// Referral Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Referral page loaded');

    // Инициализация статистики (если нужно)
    initReferralStats();
});

// Функция копирования реферальной ссылки
function copyReferralLink() {
    const linkField = document.getElementById('referralLink');
    const copyBtn = document.querySelector('.copy-btn');

    if (!linkField) {
        console.error('Referral link field not found');
        return;
    }

    const linkValue = linkField.value;

    // Анимация кнопки при клике
    copyBtn.style.transform = 'scale(0.95)';
    setTimeout(() => {
        copyBtn.style.transform = '';
    }, 150);

    // Современный API для копирования
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(linkValue)
            .then(() => {
                showCopyNotification(linkValue);
                console.log('Referral link copied to clipboard:', linkValue);
            })
            .catch(err => {
                console.error('Failed to copy link:', err);
                fallbackCopyMethod(linkField);
            });
    } else {
        // Fallback метод для старых браузеров
        fallbackCopyMethod(linkField);
    }
}

// Fallback метод копирования для старых браузеров
function fallbackCopyMethod(linkField) {
    try {
        linkField.select();
        linkField.setSelectionRange(0, 99999); // Для мобильных устройств

        const successful = document.execCommand('copy');
        if (successful) {
            showCopyNotification(linkField.value);
            console.log('Referral link copied (fallback method)');
        } else {
            throw new Error('Copy command failed');
        }
    } catch (err) {
        console.error('Fallback copy method failed:', err);

        // Показываем уведомление об ошибке через систему уведомлений
        if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
            SocnetApp.notifications.showError('Не удалось скопировать ссылку автоматически. Скопируйте её вручную');
        } else {
            alert('Не удалось скопировать ссылку автоматически. Скопируйте её вручную: ' + linkField.value);
        }
    }
}

// Показ уведомления о копировании
function showCopyNotification(linkValue) {
    // Используем систему уведомлений приложения
    if (typeof SocnetApp !== 'undefined' && SocnetApp.notifications) {
        SocnetApp.notifications.showSuccess('Реферальная ссылка скопирована в буфер обмена!');
    } else {
        // Fallback для случая, когда система уведомлений недоступна
        console.log('Referral link copied successfully:', linkValue);

        // Можно показать простое alert как fallback
        // alert('Реферальная ссылка скопирована!');
    }
}

// Инициализация статистики (заглушка для будущего AJAX)
function initReferralStats() {
    // В будущем здесь может быть AJAX запрос для получения актуальной статистики
    // updateReferralStats();
}

// Обновление статистики (заглушка для будущего AJAX)
function updateReferralStats(stats = null) {
    if (!stats) {
        // Пример обновления статистики
        stats = {
            registrations: 10,
            earnings: '125.00',
            percent: 5
        };
    }

    // Обновляем значения в DOM
    const registrationsValue = document.querySelector('.stat-card:nth-child(1) .stat-value');
    const earningsValue = document.querySelector('.stat-card:nth-child(2) .stat-value');
    const percentValue = document.querySelector('.stat-card:nth-child(3) .stat-value');

    if (registrationsValue) {
        // Анимация изменения значения
        animateCounterChange(registrationsValue, stats.registrations);
    }

    if (earningsValue) {
        animateCounterChange(earningsValue, `$${stats.earnings}`);
    }

    if (percentValue) {
        animateCounterChange(percentValue, `${stats.percent}%`);
    }

    console.log('Referral stats updated:', stats);
}

// Анимация изменения счетчика
function animateCounterChange(element, newValue) {
    if (element.textContent === newValue.toString()) {
        return; // Значение не изменилось
    }

    element.style.transform = 'scale(1.1)';
    element.style.transition = 'transform 0.2s ease';

    setTimeout(() => {
        element.textContent = newValue;
        element.style.transform = 'scale(1)';
    }, 100);
}

// Экспорт функций для глобального использования
window.copyReferralLink = copyReferralLink;
window.updateReferralStats = updateReferralStats;

// Интеграция с основным приложением (если нужно)
if (typeof SocnetApp !== 'undefined') {
    // Добавляем методы в главное приложение
    SocnetApp.referral = {
        copyLink: copyReferralLink,
        updateStats: updateReferralStats,
        init: initReferralStats
    };

    console.log('Referral module integrated with SocnetApp');
}
