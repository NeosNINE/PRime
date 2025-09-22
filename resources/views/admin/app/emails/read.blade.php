@extends('admin.app.layout')
@section('title', 'Email сообщение #'.$email->id )

@section('content')

    <div class="container">

        <div class="page-header">
        </div>
        <div class="box no-padding">
            <div class="box-header">
                <h1>Email сообщение #{{ $email->id }}</h1>
                <div class="actions"></div>
            </div>
            <div class="box-body inputs-only-read">
                <div class="input">
                    <label>
                        <span class="label">Тема</span>
                        <input type="text" name="subject" value="{{ $email->subject }}" autocomplete="password" disabled>
                    </label>
                </div>
                <div class="input" data-always-active="true">
                    <label>
                        <span class="label">Сообщение</span>
                        <iframe class="iframe-default" srcdoc="{{ $email->text }}"></iframe>
                    </label>
                </div>
                <div class="input">
                    <label>
                        <span class="label">Email</span>
                        <div class="input-content">
                            @if( $email->user )

                                <a href="" data-offcanvas-href="{{ route('admin.users.read', $email->user) }}">{{ $email->email }}</a>

                            @else

                                {{ $email->email }}

                            @endif
                        </div>
                    </label>
                </div>
                <div class="input">
                    <label>
                        <span class="label">Статус</span>
                        <div class="input-content">
                            @include('admin.app.emails.components.status')
                        </div>
                    </label>
                </div>
                <div class="input">
                    <label>
                        <span class="label">Время отправки</span>
                        <input type="text" name="sent_date" value="{{ r($email->sent_date, $email->sent_date?->calendar(), '-') }}" autocomplete="password" disabled>
                    </label>
                </div>
            </div>
        </div>

    </div>

@endsection
