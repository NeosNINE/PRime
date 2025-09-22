<tr
    data-id="{{ $role->id }}"
    @if( !in_array($role->key, roles()->getAllAccessKeys()) && roles()->checkAccess('roles.edit') ) data-offcanvas-href="{{ route('admin.roles.edit', $role) }}" @endif
    @class(['disabled' => $role->trashed() ])
>
    <td>{{ $role->name }}</td>
    <td>
        @if( $role->key == 'super_admin' )

            <span class="d-label label-success"><i class="fa fa-check-double"></i> Доступ ко всем функциям</span>
            <span class="d-label label-success"><i class="fa fa-check-double"></i> Нельзя удалить и другие админы не могут отобрать у него доступ</span>

        @elseif( $role->key == 'admin' )

            <span class="d-label label-success"><i class="fa fa-check-double"></i> Доступ ко всем функциям</span>
            <span class="d-label label-danger">Super Admin может удалить или отобрать доступ у простого админа</span>

        @elseif( $role->key == 'developer' )

            <span class="d-label label-success"><i class="fa fa-check-double"></i> Доступ ко всем функциям</span>
            <span class="d-label label-default">Имеет специальные возможности для разработки и поддержки проекта</span>

        @else

            @foreach( roles()->getGroupsAccesses() as $group_key => $group_name )

                @if( roles()->checkGroupAccess($role, $group_key) )

                    <span class="d-label label-success"><i class="fa fa-check-double"></i> {{ $group_name }}</span>

                @endif

            @endforeach

            @foreach( roles()->getGroupsAccesses() as $group_key => $group_name )

                @if( !roles()->checkGroupAccess($role, $group_key) && count(roles()->getAccessesNames($role, $group_key)) )

                    <div class="browse-role-accesses">

                        <p>{{ $group_name }}</p>
                        <ul>
                            @foreach( roles()->getAccessesNames($role, $group_key) as $access_key => $access_name )
                                <li><span class="d-label label-default"><i class="fa fa-check"></i> {{ $access_name }}</span></li>
                            @endforeach
                        </ul>

                    </div>

                @endif

            @endforeach

        @endif
    </td>
    <td class="actions">
        @if( !in_array($role->key, roles()->getAllAccessKeys()) && roles()->checkAccess('roles.delete') )

            <button class="btn btn-icon-danger" data-delete-object data-soft-deletes data-action="{{ route('admin.roles.delete', $role) }}" data-confirm-text="Вы уверены, что желаете удалить роль?" data-success-text="Роль &quot;{{ $role->name }}&quot; успешно удалена."><i class="fa fa-trash-alt"></i></button>
            <button class="btn btn-icon-default" data-restore-object @if( !$role->trashed() ) data-skip-confirm @endif data-action="{{ route('admin.roles.restore', $role) }}" data-confirm-text="Вы уверены, что желаете восстановить роль?" data-success-text="Роль &quot;{{ $role->name }}&quot; успешно восстановлена."><i class="fa fa-trash-arrow-up"></i> <span>Восстановить</span></button>

        @endif
    </td>
</tr>
