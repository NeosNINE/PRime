@extends('guest.app.layout')

@section('title', $news['title'] . ' - SOCNET SMM')
@section('description', 'Читайте последние новости SOCNET SMM - лучшей SMM панели для продвижения в социальных сетях.')

@section('content')
    <div class="news-page">

        <!-- Decorative Background Icons -->
        <div class="news-bg-icons">
            <i class="fas fa-newspaper news-bg-icon news-icon-1"></i>
            <i class="fas fa-rss news-bg-icon news-icon-2"></i>
            <i class="fas fa-book-open news-bg-icon news-icon-3"></i>
            <i class="fas fa-bullhorn news-bg-icon news-icon-4"></i>
            <i class="fas fa-globe news-bg-icon news-icon-5"></i>
            <i class="fas fa-pen-fancy news-bg-icon news-icon-6"></i>
            <i class="fas fa-scroll news-bg-icon news-icon-7"></i>
            <i class="fas fa-feather-alt news-bg-icon news-icon-8"></i>
            <i class="fas fa-comment-dots news-bg-icon news-icon-9"></i>
            <i class="fas fa-star news-bg-icon news-icon-10"></i>
            <i class="fas fa-heart news-bg-icon news-icon-11"></i>
            <i class="fas fa-bookmark news-bg-icon news-icon-12"></i>
        </div>

        <!-- News Header -->
        <section class="news-header-section">
            <div class="container">
                <div class="news-breadcrumb">
                    <a href="{{ route('index') }}" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        Главная
                    </a>
                    <span class="breadcrumb-separator">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                    <a href="{{ route('news.index') }}" class="breadcrumb-link">
                        Новости
                    </a>
                    <span class="breadcrumb-separator">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                    <span class="breadcrumb-current">{{ $news['title'] }}</span>
                </div>

                <div class="news-header-content">
                    <div class="news-meta">
                        <span class="news-date">
                            <i class="fas fa-calendar-alt"></i>
                            {{ \Carbon\Carbon::parse($news['date'])->format('d.m.Y') }}
                        </span>
                        <span class="news-category">
                            <i class="fas fa-tag"></i>
                            Новости
                        </span>
                    </div>

                    <h1 class="news-title">{{ $news['title'] }}</h1>
                </div>
            </div>

            <!-- Background News Header Icons -->
            <div class="news-header-bg-icons">
                <div class="news-header-bg-icon news-header-icon-1"><i class="fas fa-newspaper"></i></div>
                <div class="news-header-bg-icon news-header-icon-2"><i class="fas fa-rss"></i></div>
                <div class="news-header-bg-icon news-header-icon-3"><i class="fas fa-bullhorn"></i></div>
                <div class="news-header-bg-icon news-header-icon-4"><i class="fas fa-globe"></i></div>
                <div class="news-header-bg-icon news-header-icon-5"><i class="fas fa-pen-fancy"></i></div>
                <div class="news-header-bg-icon news-header-icon-6"><i class="fas fa-scroll"></i></div>
                <div class="news-header-bg-icon news-header-icon-7"><i class="fas fa-feather-alt"></i></div>
                <div class="news-header-bg-icon news-header-icon-8"><i class="fas fa-comment-dots"></i></div>
                <div class="news-header-bg-icon news-header-icon-9"><i class="fas fa-book-open"></i></div>
                <div class="news-header-bg-icon news-header-icon-10"><i class="fas fa-lightbulb"></i></div>
            </div>
        </section>

        <!-- News Content -->
        {{-- <section class="news-content-section">
        <div class="container">
            <div class="news-content-wrapper">
                <div class="news-image-container">
                    <img src="{{ asset($news['image']) }}" alt="{{ $news['title'] }}" class="news-image">
                </div>

                <div class="news-content">
                    @foreach ($news['content'] as $paragraph)
                        <p class="news-paragraph">{{ $paragraph }}</p>
                    @endforeach
                </div>


            </div>
        </div>
    </section> --}}

        <section class="news-content-section">
            <div class="container">
                <div class="news-content-inner">
                    <div class="news-content-main">
                        <div class="news-content-date">
                            <i class="fas fa-calendar-alt"></i>
                            06.10.2025
                        </div>
                        <div class="news-content-box">
                            <h3 class="news-content-title">
                                Обновление API v2.0
                            </h3>
                            <div class="news-content-text">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem sequi illo cumque eum optio
                                culpa facilis perferendis ducimus laborum? Earum.
                            </div>
                        </div>
                    </div>
                    <div class="news-content-image">
                        <img src="{{ asset('assets/images/news-1.jpeg') }}" alt="">
                    </div>

                </div>
                <div class="news-content-body">
                    <h3>
                        Обновление API v2.0
                    </h3>
                    <p>
                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Eligendi, labore veritatis neque
                        repudiandae beatae nam explicabo, enim, rerum praesentium mollitia dolores quasi sequi dignissimos
                        placeat consequuntur delectus eos sed autem.
                    </p>
                    <p>
                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Eligendi, labore veritatis neque
                        repudiandae beatae nam explicabo, enim, rerum praesentium mollitia dolores quasi sequi dignissimos
                        placeat consequuntur delectus eos sed autem.
                    </p>
                    <p>
                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Eligendi, labore veritatis neque
                        repudiandae beatae nam explicabo, enim, rerum praesentium mollitia dolores quasi sequi dignissimos
                        placeat consequuntur delectus eos sed autem.
                    </p>
                </div>
            </div>
        </section>

        <section class="related-news-section">
            <div class="container">
                <div class="related-news-header">
                    <h2 class="related-news-title">
                        Смотрите также
                    </h2>
                </div>
                <div class="related-news-body">
                    <div class="related-news-slider swiper">
                        <div class="swiper-wrapper">
                            @foreach ($newsList as $news)
                                <a href="{{ route('news.show', $news['slug']) }}"
                                    class="related-news-item news-list-card swiper-slide">
                                    <div class="news-list-image">
                                        <img src="{{ asset($news['image']) }}" alt="{{ $news['title'] }}">
                                    </div>
                                    <div class="news-list-content">
                                        <div class="news-list-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($news['date'])->format('d.m.Y') }}
                                        </div>
                                        <h3 class="news-list-title">{{ $news['title'] }}</h3>
                                        <p class="news-list-excerpt">{{ $news['excerpt'] }}</p>
                                        {{-- <span class="news-list-link">
                                <i class="fas fa-arrow-right"></i>
                                Читать далее
                            </span> --}}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="related-news-prev swiper-button-prev"></div>
                    <div class="related-news-next swiper-button-next"></div>
                </div>
                <div class="related-news-pagination swiper-pagination"></div>
            </div>
        </section>

        @guest
            <!-- Call to Action -->
            @include('guest.app.components.cta-guest')
        @endguest
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ mix('assets/guest/js/pages/news.js') }}"></script>
@endsection
