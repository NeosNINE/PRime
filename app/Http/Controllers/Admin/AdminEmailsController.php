<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\System\Email;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminEmailsController extends Controller
{


    /**
        Просмотр email сообщений
    */
    public function browse( Request $request ): View
    {

        roles()->checkAccessWithAbort('emails.browse');

        return view('admin.app.emails.browse', [
            'emails' => emails()->get( $request )
        ]);

    }



    /**
        Просмотр email сообщения
    */
    public function read( Email $email ): View
    {

        roles()->checkAccessWithAbort('emails.read');

        return view('admin.app.emails.read', [
            'email' => $email
        ]);

    }



    /**
        Повторная отправка email сообщения
    */
    public function resend( Email $email ): Email
    {

        roles()->checkAccessWithAbort('emails.resend');

        return emails()->resend( $email );

    }



    /**
        Повторная отправка email сообщений (всех, у которых была ошибка при отправке)
    */
    public function resendAll(): void
    {

        roles()->checkAccessWithAbort('emails.resend');

        emails()->resendAll();

    }



    /**
        Удалить email сообщение
    */
    public function delete( Email $email ): ?bool
    {

        roles()->checkAccessWithAbort('emails.delete');

        return emails()->delete( $email);

    }


    /**
     * Восстановить email (из архива)
     * @throws \Throwable
     */
    public function restore( $email_id ): Email
    {

        roles()->checkAccessWithAbort('emails.delete');

        return emails()->restore($email_id);

    }

}
