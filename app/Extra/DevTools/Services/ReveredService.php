<?php

namespace App\Extra\DevTools\Services;

/**
 * Данный класс нужен исключительно для разработки и поддержки проекта.
 * Функционал для самого проекта здесь не прописывается.
 */
class ReveredService
{

    /**
     * Отправить критическое сообщение разработчику
     */
    public function sendCriticalMessage( $msg ){

        //Добавляем в сообщение информацию об проекте
        $msg = config('app.name')."\n".config('app.url')."\n".$msg;

        //Отправляем в helper

    }


    /**
     * Отправить запрос в Helper
     */
    private function requestToHelper ( $data, $method ){

        //return Http::post( 'test', $data );

    }

}
