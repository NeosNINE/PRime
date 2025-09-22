<?php

namespace App\Console\Commands\Revered;

trait ReveredConsoleTrait
{

    /**
     * Вывести сообщение в outputBuffer
     */
    public function print( string $msg, string $style = 'info' ): void
    {

        if( php_sapi_name() != 'cli' )
            $msg = '['.$style.']'.$msg;

        $this->{$style}($msg);

    }

}
