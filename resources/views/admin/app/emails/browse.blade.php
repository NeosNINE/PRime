@extends('admin.app.layout')
@section('title', 'Email сообщения' )

@section('content')
    <div class="container">

        <div class="page-header">
            <h1>Email сообщения</h1>
            <div class="actions">
                @if( roles()->checkAccess('emails.resend') )
                    <a
                        href="{{ route('admin.emails.resend_all') }}"
                        class="btn btn-warning btn-action btn-resend-emails-all {{ r(!emails()->getCountFails(), 'hide') }}"
                        data-success-text="Email сообщения поставлены в очередь и скоро будут отправлены"
                        data-success-refresh-page="true"
                    ><i class="fa fa-retweet fa-beat"></i> <span>Повторно отправить сообщения с ошибкой</span></a>
                @endif
            </div>
        </div>
        <div class="box no-padding">
            @include('admin.components.search_form')

            @include('admin.components.box_type_choose', [
                'items_arr' => emails()->getTypes(),
                'field' => 'type',
                'all_route' => 'admin.emails.browse'
            ])

            @if( count($emails) )

                <div class="box-table">
                    <table class="table table-striped emails-table">
                        <thead>
                            <tr class="tr-bg-primary">
                                <th>Email</th>
                                <th>Тема</th>
                                <th>Статус</th>
                                <th>Время отправки</th>
                                <th class="actions"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach( $emails as $email )
                                @include('admin.app.emails.components.table-row')
                            @endforeach

                        </tbody>
                    </table>
                </div>

                {{ admin()->paginate($emails) }}

            @else

                <div class="no-rows-found">Ничего не найдено.</div>

            @endif
        </div>

    </div>

    <load-js src="{{ mix('assets/admin/js/pages/emails.js') }}"></load-js>

@endsection
