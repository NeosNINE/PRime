<div class="box">
    <div class="box-body">
        <div class="input">
            <label>
                <span class="label">Email <i>*</i></span>
                <input type="text" name="email" value="{{ $user->email ?? request()->input('email') }}" autocomplete="password">
            </label>
            @isset( $is_read )
                @include('admin.app.users.components.email-verified-badge')
            @endisset
        </div>

        @if( isset($is_read) )
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
        @else
            <div class="input">
                <label>
                    <span class="label">Роль</span>
                    <select name="roles[]" class="select2" multiple @disabled( isset($user) && !roles()->canEditRoles($user) ) >
                        @foreach( roles()->getAll() as $role )
                            <option value="{{ $role->id }}" @selected( in_array($role->id, isset($user) ? $user->roles->pluck('id')->toArray() : []) ) @disabled( isset($user) && !roles()->canEditRoles($user, $role) ) >{{ $role->name }}</option>
                        @endforeach
                    </select>
                </label>
                <span class="tooltip-btn" data-bs-title="Если это обычный пользователь - оставляем поле пустое"></span>
                <a href="{{ route('admin.roles.add') }}" class="btn-add-more"></a>
            </div>
        @endif

        <div class="input">
            <label>
                <span class="label">Аватар</span>
                <span data-file-upload="true" data-type="image" data-input-name="avatar" data-value="{{ $user->avatar ?? request()->input('avatar') }}" @isset( $is_read ) disabled @endisset ></span>
            </label>
        </div>

        @if( !isset($user) )

            <div class="input">
                <label>
                    <span class="label">Пароль <i>*</i></span>
                    <input type="text" name="password" value="{{ request()->input('password') ?? str()->random(8) }}" autocomplete="password">
                </label>
            </div>

        @elseif( !isset($is_read) )

            <div class="input">
                <label>
                    <span class="label">Новый пароль</span>
                    <input type="text" name="new_password" autocomplete="password">
                </label>
            </div>

        @endif

        @if( isset($is_read) && isset($user) )

            <div class="input">
                <label>
                    <span class="label">Дата регистрации</span>
                    <input type="text" name="created_at" value="{{ $user->created_at->calendar() }}" autocomplete="password" disabled>
                </label>
            </div>
            <div class="input">
                <label>
                    <span class="label">Последняя активность</span>
                    <input type="text" name="last_active_at" value="{{ r($user->last_active_at, optional($user->last_active_at)->calendar(), '-') }}" autocomplete="password" disabled>
                </label>
            </div>

        @endif

    </div>
</div>
