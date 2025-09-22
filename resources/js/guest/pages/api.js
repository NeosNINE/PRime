// API Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize page functionality
    initSyntaxHighlighting();
    initCodeCopyButtons();
    initSmoothScrolling();

    // Basic JSON syntax highlighting
    function initSyntaxHighlighting() {
        const codeBlocks = document.querySelectorAll('.api-code-block code');

        codeBlocks.forEach(codeBlock => {
            let html = codeBlock.innerHTML;

            // Highlight JSON syntax
            html = html.replace(/"([^"]+)":/g, '<span class="token property">"$1"</span>:');
            html = html.replace(/:\s*"([^"]+)"/g, ': <span class="token string">"$1"</span>');
            html = html.replace(/:\s*(\d+)/g, ': <span class="token number">$1</span>');
            html = html.replace(/:\s*(true|false)/g, ': <span class="token boolean">$1</span>');
            html = html.replace(/:\s*(null)/g, ': <span class="token null">$1</span>');
            html = html.replace(/([{}[\],])/g, '<span class="token punctuation">$1</span>');

            codeBlock.innerHTML = html;
        });
    }

    // Add copy buttons to code blocks
    function initCodeCopyButtons() {
        const codeBlocks = document.querySelectorAll('.api-code-block');

        codeBlocks.forEach(block => {
            // Create copy button
            const copyButton = document.createElement('button');
            copyButton.className = 'code-copy-btn';
            copyButton.innerHTML = '<i class="fas fa-copy"></i>';
            copyButton.title = 'Копировать код';

            // Position button
            block.style.position = 'relative';

            // Copy functionality
            copyButton.addEventListener('click', () => {
                const code = block.querySelector('code');
                const text = code.textContent || code.innerText;

                navigator.clipboard.writeText(text).then(() => {
                    // Show success feedback
                    const originalHTML = copyButton.innerHTML;
                    copyButton.innerHTML = '<i class="fas fa-check"></i>';
                    copyButton.classList.add('success');

                    setTimeout(() => {
                        copyButton.innerHTML = originalHTML;
                        copyButton.classList.remove('success');
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy code: ', err);

                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);

                    // Show success feedback
                    const originalHTML = copyButton.innerHTML;
                    copyButton.innerHTML = '<i class="fas fa-check"></i>';
                    copyButton.classList.add('success');

                    setTimeout(() => {
                        copyButton.innerHTML = originalHTML;
                        copyButton.classList.remove('success');
                    }, 2000);
                });
            });

            block.appendChild(copyButton);
        });
    }

    // Smooth scrolling for anchor links
    function initSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');

        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Add click-to-expand functionality for long code blocks
    function initCodeExpansion() {
        const codeBlocks = document.querySelectorAll('.api-code-block pre');

        codeBlocks.forEach(pre => {
            if (pre.scrollHeight > 300) {
                pre.style.maxHeight = '300px';
                pre.style.overflow = 'hidden';

                const expandButton = document.createElement('button');
                expandButton.className = 'code-expand-btn';
                expandButton.textContent = 'Показать полностью';

                expandButton.addEventListener('click', () => {
                    if (pre.style.maxHeight === '300px') {
                        pre.style.maxHeight = 'none';
                        pre.style.overflow = 'auto';
                        expandButton.textContent = 'Свернуть';
                    } else {
                        pre.style.maxHeight = '300px';
                        pre.style.overflow = 'hidden';
                        expandButton.textContent = 'Показать полностью';
                    }
                });

                pre.parentElement.appendChild(expandButton);
            }
        });
    }

    initCodeExpansion();

    // Add loading animation for the main CTA button
    const ctaButton = document.querySelector('.api-cta-button');
    if (ctaButton) {
        ctaButton.addEventListener('click', (e) => {
            const originalText = ctaButton.textContent;
            ctaButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Переходим...';
            ctaButton.style.pointerEvents = 'none';

            // Reset after a delay (in case the navigation doesn't happen immediately)
            setTimeout(() => {
                ctaButton.textContent = originalText;
                ctaButton.style.pointerEvents = 'auto';
            }, 3000);
        });
    }

    // Add typing animation to hero title
    function initTypingAnimation() {
        const heroTitle = document.querySelector('.api-hero-title');
        if (!heroTitle) return;

        const text = heroTitle.textContent;
        heroTitle.textContent = '';
        heroTitle.style.borderRight = '2px solid';
        heroTitle.style.borderColor = 'var(--brand-primary)';

        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                heroTitle.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            } else {
                // Remove cursor after typing is complete
                setTimeout(() => {
                    heroTitle.style.borderRight = 'none';
                }, 1000);
            }
        };

        // Start typing animation after a short delay
        setTimeout(typeWriter, 500);
    }

    // Initialize typing animation only on first visit
    if (!sessionStorage.getItem('api-page-visited')) {
        initTypingAnimation();
        sessionStorage.setItem('api-page-visited', 'true');
    }

    console.log('API page JavaScript initialized successfully');
});
