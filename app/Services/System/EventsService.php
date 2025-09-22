<?php

namespace App\Services\System;

use App\Models\System\ClientEvent;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EventsService
{

    /**
     * Добавляет событие для клиента (чтобы на Frontend через JS отлавливать события)
     * @param string $event_name - название события. Пример: user.add
     * @param object|array|null $data - данные события. Пример: [ 'id' => 1, 'name' => 'Name' ]
     * @param string|null $access_key - ключ доступа, который должна иметь роль (юзер) для получения этого события
     * @param bool $unique - если TRUE, тогда за раз будет только одно уникальное событие вызвано. То есть функция $this->getNewClientEvents() будет оставлять только одно уникальное событие. Иногда это нужно на клиенте, например, если нам нужно показать уведомление о новом заказе, и не важно сколько новых заказов.
     * @param User|null $for_user - пользователь, которому предназначено событие (только он получит это событие)
     * @param User|null $created_user - пользователь, который запустил событие. Если передано null - то будет использоваться текущий (авторизованный)
     * @param bool $HTML_table_row_only_for_add_and_edit - если передан true, то html table row будет получаться только для событий add и edit
     * @return ClientEvent
     */
    public function setClientEvent(
        string $event_name,
        object|array $data = null,
        string|null $access_key = 'essence_browse',
        bool $unique = false,
        User $for_user = null,
        User $created_user = null,
        bool $HTML_table_row_only_for_add_and_edit = true
    ): ClientEvent
    {

        // Не требуем авторизацию для гостевых событий (например, восстановление пароля)
        // Если пользователь не передан и не авторизован — оставляем null, без abort()
        $created_user = users()->getUser($created_user, current_by_default: false, with_abort: false);

        if( $access_key == 'essence_browse' ){

            $essence = explode('.', $event_name);
            if( count($essence) ){

                $access_key = str($essence[0])->plural().'.browse';

            }else{

                $access_key = null;

            }

        }

        $html_table_tow = $this->getHTMLTableRowByEvent($event_name, $data, $HTML_table_row_only_for_add_and_edit);

        if( is_object($data) && method_exists($data, 'getAttributes') )
            $data = $data->getAttributes();


        if( $html_table_tow ){

            if( is_object($data) ) {

                $data->html_table_row = $html_table_tow;

            }elseif( is_array($data) ) {

                $data['html_table_row'] = $html_table_tow;

            }

        }


        return ClientEvent::create([
            'event_name' => $event_name,
            'data' => $data,
            'access_key' => $access_key,
            'unique' => $unique,
            'created_user_id' => $created_user ? $created_user->id : null,
            'for_user_id' => $for_user?->id,
            'created_at' => now()
        ]);

    }


    /**
     * Пытаемся получить HTML TR Row (если это редактирование или удаление сущности)
     */
    public function getHTMLTableRowByEvent( $event_name, $data, $only_add_and_edit = true ): bool|string
    {

        try {

            if( $only_add_and_edit && !str($event_name)->contains(['add', 'edit']) )
                return false;

            $type = explode('.', $event_name)[0] ?? false;
            $essence_id = $data['id'] ?? false;

            if( !$type || !$essence_id )
                return false;

            $type = str($type)->singular();

            return admin()->getHTMLTableRow($essence_id, $type, check_access: false); //Здесь не нужно проверять доступ, т.к. событие может быть запущено под любым юзером, но просматривать его сможет только тот, кто имеет досутп

        } catch ( \Throwable $exception ){

            report($exception);
            return false;

        }

    }


    /**
     * Получить последний ID события (чтобы подтягивать только новые события)
     */
    public function getLastClientId(): int
    {

        $last_event = ClientEvent::orderByDesc('id')->first(); //Здесь не допустимо использовать сортировку latest()

        if( $last_event )
            return $last_event->id;

        return 0;

    }


    /**
     * Получить новые события
     * @param int $last_event_id
     * @param string|array $only - event_name который нужно выбрать
     * @param string|array $except - event_name который нужно исключить
     * @return Collection
     */
    public function getNewClientEvents(
        int $last_event_id,
        string|array $only = [],
        string|array $except = []
    ): Collection
    {

        if( !is_array($only) )
            $only = [$only];

        if( !is_array($except) )
            $except = [$except];

        if( count($only) && count($except) )
            error('Необходимо указать что-то одно. Либо $only либо $except.');


        $events_unique = [];

        $events = ClientEvent::where('id', '>', $last_event_id)->where(function( $query ){

            $query->whereNull('for_user_id');

            if( Auth::check() )
                $query->orWhere('for_user_id', Auth::id());

        });


        if( count($only) )
            $events->whereIn('event_name', $only);


        if( count($except) )
            $events->whereNotIn('event_name', $except);


        $events = $events->limit(200)->get();


        foreach( $events as $key => $event ){

            if( $event->unique ){

                //Если это уникальное событие и оно уже есть в наборе, удаляем последующие
                if( in_array($event->event_name, $events_unique) ){

                    unset($events[$key]);

                }else{

                    $events_unique[] = $event->event_name;

                }

            }

            //Проверяем есть ли доступ к этому событию у пользователя
            if( $event->access_key ){

                if( !roles()->checkAccess($event->access_key) )
                    unset($events[$key]);

            }

        }

        return $events;

    }

}
