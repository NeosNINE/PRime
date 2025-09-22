<footer class="footer" id="footer">
    <div class="container">
        <div class="footer-content">
            <!-- About Us Section -->
            <div class="footer-section">
                <h3 class="footer-title">{{ __('О нас') }}</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('news.index') }}">{{ __('Новости') }}</a></li>
                    <li><a href="{{ route('user.referral') }}">{{ __('Реферальная программа') }}</a></li>
                    <li><a href="{{ route('api') }}">{{ __('API для реселлеров') }}</a></li>
                    <li><a href="{{ route('rules') }}">{{ __('Правила') }}</a></li>
                    <li><a href="{{ route('policy') }}">{{ __('Политика конфиденциальности') }}</a></li>
                </ul>
            </div>

            <!-- Support Section -->
            <div class="footer-section">
                <h3 class="footer-title">{{ __('Поддержка') }}</h3>
                <ul class="footer-links">
                    <li>
                        <a class="support-link telegram" href="https://t.me/socnet_support" target="_blank">
                            <i class="fab fa-telegram"></i>
                            {{ __('Поддержка в Telegram') }}
                        </a>
                    </li>
                    <li>
                        <a class="support-link whatsapp" href="https://wa.me/79051904467" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                            {{ __('Поддержка в WhatsApp') }}
                        </a>
                    </li>
                    <li>
                        <a class="support-link email" href="mailto:help@socnet.pro">
                            <i class="fas fa-envelope"></i>
                            Email: help@socnet.pro
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Logo & Social -->
            <div class="footer-section footer-brand">
                <div class="footer-logo">
                    <img src="{{ asset('assets/logo.svg') }}" alt="SOCNET SMM" class="logo-icon">
                </div>

                <!-- Social Media Icons -->
                <div class="social-links">
                    <a class="social-link instagram" href="https://instagram.com/socnet_smm" target="_blank"
                        title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a class="social-link telegram" href="https://t.me/socnet_news" target="_blank" title="Telegram">
                        <i class="fab fa-telegram"></i>
                    </a>
                    <a class="social-link youtube" href="https://www.youtube.com/@socnet_store" target="_blank"
                        title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a class="social-link twitter" href="https://x.com/SocNetSMM" target="_blank" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a class="social-link tiktok" href="https://www.tiktok.com/@socnet" target="_blank" title="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>SOCNET &copy; {{ date('Y') }}. {{ __('Все права защищены') }}</p>
        </div>
    </div>
</footer>
