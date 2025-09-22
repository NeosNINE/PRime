<?php

namespace App\Services\System;

use App\Extra\Services\Service;
use App\Extra\Services\Traits\ServiceTrait;
use App\Jobs\SendEmailJob;
use App\Models\System\Email;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class EmailsService extends Service
{

    use ServiceTrait;



    /**
     * Получить тип email сообщений
     */
    public function getTypes(): array
    {
        return [];
    }



    /**
     * Получить кол-во не отправленных сообщений
     */
    public function getCountFails(): int
    {

        if( !isProduction() )
            return 0;

        return cacheService()->oneLoad('emailsCountFails', function (){

            return Email::where('status', 'error')->count();

        });
    }



    /**
     * Повторно отправить одно сообщение (отправляет прямо сейчас)
     */
    public function resend( Email $email ): Email
    {

        if( $email->status != 'error' )
            abort(403, 'Повторно может быть отправлено только сообщение с ошибкой.');

        $email->status = 'sending';
        $email->save();

        return $this->__handle($email);

    }



    /**
     * Повторно отправить все сообщения с ошибкой (ставит в очередь)
     */
    public function resendAll(): void
    {

        foreach( Email::where('status', 'error')->get() as $email ){

            $email->status = 'sending';
            $email->save();

            SendEmailJob::dispatch($email);

        }

    }




    /**
     * Отправить сообщение.
     * Отправляется на один Email. Если нужно отправить на много, нужно просто в цикле вызвать эту функцию.
     *
     * $when_should_be_send - когда нужно отправить сообщение.
     * Варианты:
     *  dispatch - отправляется через очереди (обычно используем этот метод)
     *  immediately - отправляется сразу (используем, когда нужно сразу узнать выдало ли ошибку SMTP или нет)
     *
     */
    public function send( array $data, string $when_should_be_send = 'dispatch' ): bool|Email
    {

        if( isset($data['user']) ){

            $user = users()->getUser( $data['user'] );
            if( !$user )
                return false;

            $data['user_id'] = $user->id;
            $data['email'] = $user->email;

        }

        if( !isset($data['email']) )
            return false;


        if( !isset($data['user_id']) ){

            $user_id = User::where('email', $data['email'])->value('id');
            if( $user_id )
                $data['user_id'] = $user_id;

        }


        if( isset($data['view']) ) {

            if( $data['view'] == 'mails.message' )
                $data['view_email_type'] = 'message';

            if( isset($data['view_email_type']) && $data['view_email_type'] == 'message' ){

                $markdown = Container::getInstance()->make(Markdown::class);
                $data['text'] = $markdown->render($data['view'], $data['view_data'] ?? []);


            }else{

                $data['text'] = view($data['view'], $data['view_data'] ?? []);

            }

        }



        if( isset($data['view_plain']) )
            $data['text_plain'] = view($data['view_plain'], $data['view_data'] ?? []);


        //Добавляем в базу
        $email = $this->add($data);


        if( $when_should_be_send == 'dispatch' ){

            SendEmailJob::dispatch($email);


        }elseif( $when_should_be_send == 'immediately' ){

            $email = $this->__handle( $email );

        }else{

            error('$when_should_be_send specified not correct.');

        }


        return $email;

    }


    /**
     * Отправить Email в виде сообщения
     * (в таком случае не нужно отдельный шаблон для письма создавать)
     */
    public function sendMessage(
        string $email,
        string $subject,
        string $message,
        string $btn_url = null,
        string $btn_text = null,
        string $type = null,
        array $data = [],
        string $when_should_be_send = 'dispatch'
    ): bool|Email
    {

        $view_data = $data;
        $view_data['message'] = $message;

        if( $btn_url && $btn_text )
            $view_data['btn'] = [
                'url' => $btn_url,
                'text' => $btn_text
            ];

        $data = [
            'email' => $email,
            'subject' => $subject,
            'view' => 'mails.message',
            'view_data' => $view_data
        ];

        if( $type )
            $data['type'] = $type;

        return $this->send($data, $when_should_be_send);

    }



    /**
     * Обработчик отправки сообщения на почту.
     * Для отправки сообщения использовать метод $this->>send()
     */
    public function __handle( Email $email_model ): bool|Email
    {

        if( $email_model->status != 'sending' )
            return false;

        $email_model->sent_date = Carbon::now();

        try{

            //Отправка письма должна работать только для боевого сайта
            if( !isProduction() ){

                error('Emails should be sent only in production environment.');

            }else{

                Mail::send([], [], function ($m) use( $email_model ) {

                    $m->to( $email_model->email , env('APP_NAME'))->subject( $email_model->subject );

                    if( isset($email_model->text_plain) ){

                        $m->getSwiftMessage()
                            ->setContentType('multipart/alternative; charset=utf-8')
                            ->setBody($email_model->text_plain, 'text/plain')
                            ->addPart($email_model->text, 'text/html');


                    }else{

                        $m->getSwiftMessage()
                            ->setContentType('text/html; charset=utf-8')
                            ->setBody($email_model->text, 'text/html');

                    }

                });

            }


        }catch ( \Throwable $exception ){

            Log::error('Email send error: '.$exception->getMessage());
            report($exception);

            $data = $email_model->data;
            $data['error_msg'] = $exception->getMessage();
            $email_model->data = $data;

            $email_model->status = 'error';
            $email_model->save();

            events()->setClientEvent('email.error', $email_model, HTML_table_row_only_for_add_and_edit: false);

            return  $email_model;
        }


        $email_model->status = 'success';
        $email_model->save();

        events()->setClientEvent('email.edit', $email_model);

        return $email_model;

    }

}
