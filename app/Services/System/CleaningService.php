<?php

namespace App\Services\System;

use App\Models\System\ClientEvent;

/**
 * В данном сервисе прописываются различные функции об отчистки системы (файлов, БД) от ненужного
 */
class CleaningService
{

    /**
     * Удаляем не нужные клиентские события
     */
    public function clientEvents(): void
    {

        ClientEvent::where('created_at', '<', now()->subMinutes(5))->delete();

    }

}
