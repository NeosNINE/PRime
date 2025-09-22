<?php

namespace App\Extra\DevTools\Services\Traits;

trait Localization
{

    /**
     * Обновить все файлы локалей в соответствии с текущими данными в базе данных
     */
    public function localizationRefresh(): string
    {

        return $this->artisanCommandRun('revered:localization refresh');

    }




    /**
     * Выгрузить локали из файлов в базу данных
     */
    public function localizationLoadToDb(): string
    {

        return $this->artisanCommandRun('revered:localization load_to_db');

    }




    /**
     * Проверить локализацию, найти потенциальные ошибки
     */
    public function localizationCheck(): string
    {

        return $this->artisanCommandRun('revered:localization check');

    }


}
