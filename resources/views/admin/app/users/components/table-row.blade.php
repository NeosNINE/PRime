<tr data-id="{{ $user->id }}" @if( roles()->checkAccess('users.read') ) data-offcanvas-href="{{ route('admin.users.read', $user) }}" @endif >
    <td class="id">{{ $user->id }}</td>
    <td class="avatar"><img src="{{ $user->avatar }}" class="avatar" alt=""></td>
    <td>{{ $user->email }}</td>
    <td>{{ $user->created_at->calendar() }}</td>
    <td>@if( $user->last_active_at ) {{ $user->last_active_at->calendar() }} @else - @endif</td>
    <td>
        @if( count($user->roles) )
            @foreach( $user->roles as $role )
                <span class="d-label label-primary">{{ $role->name }}</span>
            @endforeach
        @else
            <span class="d-label label-default">Пользователь</span>
        @endif
    </td>
    <td class="actions">
        @if( !$user->isAnotherSuperAdmin() )

            @if( roles()->checkAccess('users.impersonate') && auth()->id() != $user->id )
                <a href="{{ route('admin.users.impersonate', ['user' => $user, 'key' => users()->getImpersonateKey($user)]) }}" class="btn btn-icon-default" data-impersonate-start target="_blank" title="Авторизоваться за этого пользователя"><i class="fa fa-right-to-bracket"></i></a>
            @endif
            @if( roles()->checkAccess('users.edit') )
                <a data-offcanvas-href="{{ route('admin.users.edit', $user) }}" class="btn btn-icon-success"><i class="fa fa-pen-to-square"></i></a>
            @endif
            @if( roles()->checkAccess('users.delete') )
                <button
                    class="btn btn-icon-danger"
                    data-delete-object
                    data-action="{{ route('admin.users.delete', $user) }}"
                    data-confirm-text="Вы уверены, что желаете удалить пользователя?"
                    data-success-text="Пользователь успешно удален."
                    data-id="{{ $user->id }}"
                    data-event="user.delete"
                >
                    <i class="fa fa-trash-alt"></i>
                </button>
            @endif

        @endif
    </td>
</tr>
