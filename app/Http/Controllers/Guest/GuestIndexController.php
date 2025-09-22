<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class GuestIndexController extends Controller
{

    public function indexPage(): View
    {
        $newsList = self::getAllNewsStub();

        return view('guest.app.pages.index', compact('newsList'));
    }

    public function apiPage(): View
    {

        return view('guest.app.pages.api');
    }

    public function servicesPage(): View
    {

        return view('guest.app.pages.services');
    }

    public function rulesPage(): View
    {

        return view('guest.app.pages.rules');
    }

    public function policyPage(): View
    {

        return view('guest.app.pages.policy');
    }

    /**
     * Получение всех новостей (заглушка)
     *
     * @return array
     */
    public static function getAllNewsStub()
    {
        return [
            [
                'slug' => 'novye-uslugi-dlya-tiktok',
                'title' => 'Новые услуги для TikTok',
                'date' => '2025-01-15',
                'image' => 'assets/images/news-1.jpeg',
                'excerpt' => 'Добавили более 50 новых услуг для продвижения в TikTok с улучшенным качеством и скоростью доставки.'
            ],
            [
                'slug' => 'obnovlenie-api-v2',
                'title' => 'Обновление API v2.0',
                'date' => '2025-01-10',
                'image' => 'assets/images/news-2.jpeg',
                'excerpt' => 'Представляем новую версию API с улучшенной производительностью и расширенным функционалом.'
            ],
            [
                'slug' => 'snizhenie-tsen-instagram',
                'title' => 'Снижение цен на Instagram',
                'date' => '2025-01-05',
                'image' => 'assets/images/news-3.jpeg',
                'excerpt' => 'Цены на услуги Instagram снижены на 25% до конца месяца. Успейте воспользоваться акцией!'
            ],
            [
                'slug' => 'novogodnie-bonusy',
                'title' => 'Новогодние бонусы',
                'date' => '2025-01-01',
                'image' => 'assets/images/news-1.jpeg',
                'excerpt' => 'Специальные новогодние предложения для всех клиентов. Получите дополнительные бонусы на депозит.'
            ],
            [
                'slug' => 'podderzhka-youtube-shorts',
                'title' => 'Поддержка YouTube Shorts',
                'date' => '2024-12-25',
                'image' => 'assets/images/news-2.jpeg',
                'excerpt' => 'Теперь доступно продвижение YouTube Shorts с высоким качеством и быстрой доставкой.'
            ]
        ];
    }
}
