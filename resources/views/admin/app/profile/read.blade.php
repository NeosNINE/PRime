@extends('admin.app.layout')
@section('title', 'Профиль' )

@section('content')

    <div class="container">

        @include('admin.components.link-back')
        <div class="page-header">
            <h1>Профиль</h1>
        </div>
        <div class="row">
            <div class="col-50">
                <div class="box">
                    <div class="box-body inputs-only-read">
                        <div class="input">
                            <label>
                                <span class="label">Email</span>
                                <input type="text" name="email" value="{{ $user->email }}" autocomplete="password" disabled>
                            </label>
                            @include('admin.app.users.components.email-verified-badge')
                        </div>
                        <div class="input">
                            <label>
                                <span class="label">Роль</span>
                                <div class="input-content">
                                    @if( count($user->roles) )
                                        @foreach( $user->roles as $role )
                                            <span class="d-label label-primary" @if( roles()->checkAccess('roles.edit') ) data-offcanvas-href="{{ route('admin.roles.edit', $role) }}" @endif >{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="d-label label-default">Пользователь</span>
                                    @endif
                                </div>
                            </label>
                        </div>
                        <div class="input">
                            <label>
                                <span class="label">Аватар</span>
                                <span data-file-upload="true" data-type="image" data-input-name="avatar" data-value="{{ $user->avatar }}" disabled></span>
                            </label>
                        </div>
                        <div class="input">
                            <label>
                                <span class="label">Дата регистрации</span>
                                <input type="text" name="created_at" value="{{ $user->created_at->calendar() }}" autocomplete="password" disabled>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="page-footer">
                    <div class="actions">
                        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary"><i class="fa fa-pen"></i> <span>Редактировать</span></a>
                    </div>
                </div>
            </div>

            @if( admin()->can_change_theme )
                <div class="col-50">
                    <div class="box">
                        <div class="box-header">
                            <h6>Тема</h6>
                        </div>
                        <div class="box-body">

                            <div class="check-lists" data-theme-choice>
                                @foreach( admin()->getAvailableThemes() as $theme_key => $theme )
                                    <label class="item">
                                        <input type="radio" name="theme" value="{{ $theme_key }}" @checked($theme_key == admin()->getTheme()['key'])>
                                        <span class="label">{{ $theme['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>

    <load-js src="{{ mix('assets/admin/js/pages/profile.js') }}"></load-js>


@endsection
