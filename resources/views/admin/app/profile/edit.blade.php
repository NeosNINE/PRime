@extends('admin.app.layout')
@section('title', 'Профиль' )

@section('content')

    <div class="container">

        @include('admin.components.link-back')

        <form
            action="#"
            method="POST"
            class="ajax-submit"
            data-action="{{ route('admin.profile.edit.save') }}"
            data-method="PUT"
            data-redirect="{{ route('admin.profile.read') }}"
            data-success-message="Данные успешно сохранены."
            data-event="user.edit"
        >
        <div class="page-header">
            <h1>Профиль</h1>
        </div>
        <div class="box w-50">
            <div class="box-body">
                <div class="box-body">
                    <div class="input">
                        <label>
                            <span class="label">Email <i>*</i></span>
                            <input type="text" name="email" value="{{ $user->email }}" autocomplete="password">
                        </label>
                    </div>
                    <div class="input">
                        <label>
                            <span class="label">Роль</span>
                            <select name="roles[]" class="select2" multiple @disabled( !roles()->checkAccess('roles.edit') ) >
                                @foreach( roles()->getAll() as $role )
                                    <option value="{{ $role->id }}" @selected( in_array($role->id, $user->roles->pluck('id')->toArray()) ) >
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        @unless( roles()->isSuperAdmin($user)  || (roles()->isUsualAdmin() && roles()->isAdmin($user)) )
                            <span class="tooltip-btn" data-bs-title="Если это обычный пользователь - оставляем поле пустое"></span>
                            <a href="{{ route('admin.roles.add') }}" class="btn-add-more"></a>
                        @endunless
                    </div>
                    <div class="input">
                        <label>
                            <span class="label">Аватар</span>
                            <span data-file-upload="true" data-type="image" data-input-name="avatar" data-value="{{ $user->avatar }}" ></span>
                        </label>
                    </div>
                    <div class="input">
                        <label>
                            <span class="label">Новый пароль</span>
                            <input type="text" name="new_password" autocomplete="password">
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-footer">
            <div class="actions">
                <button class="btn btn-primary"><i class="fa fa-check"></i> <span>Сохранить</span></button>
            </div>
        </div>
    </form>

    </div>

@endsection
