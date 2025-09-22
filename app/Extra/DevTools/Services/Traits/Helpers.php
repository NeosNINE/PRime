<?php

namespace App\Extra\DevTools\Services\Traits;

use Illuminate\Support\Facades\Artisan;

trait Helpers
{

    /**
     * Обновить helpers.php
     */
    public function refreshHelpers(): string
    {

        return $this->artisanCommandRun('revered:helperRefresh');

    }


    /**
     * Сгенерировать ide-helpers
     */
    public function ideHelpers(): void
    {

        $files = [
            '.phpstorm.meta.php',
            '_ide_helper.php',
            '_ide_helper_models.php'
        ];

        Artisan::call('ide-helper:generate');
        Artisan::call('ide-helper:models -n');
        Artisan::call('ide-helper:meta');

        //Так как команды запускались в контроллере, файлы создались в папке /public, переносим их в корень
        foreach( $files as $file ){

            copy( public_path($file), base_path($file) );
            unlink( public_path($file));

        }

    }

}
