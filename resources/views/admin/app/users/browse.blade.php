@extends('admin.app.layout')
@section('title', 'Пользователи' )

@section('content')

    <div class="container">

        <div class="page-header">
            <h1>Пользователи</h1>
            <div class="actions">
                @if( roles()->checkAccess('users.add') )
                    {!!
                        admin()->buttons([
                            [
                                'offcanvas-href' => route('admin.users.add'),
                                'style' => 'primary',
                                'icon' => 'fa fa-user-plus',
                                'text' => 'Добавить',
                                'access' => 'users.add'
                            ]
                        ])
                    !!}
                @endif
            </div>
        </div>

        <div class="box no-padding">
            @include('admin.components.search_form')

            <div class="box-table">
                <table class="table table-striped users-table @if( !count($users) ) table-no-rows-found @endif">
                    <thead>
                        <tr class="tr-bg-primary text-nowrap">
                            <th class="id">ID</th>
                            <th class="avatar"></th>
                            <th>Email</th>
                            <th>Дата регистрации</th>
                            <th>Последняя активность</th>
                            <th>Роль</th>
                            <th class="actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(  count($users)  )

                        @foreach( $users as $user )
                            @include('admin.app.users.components.table-row')
                        @endforeach

                    @else

                        <tr>
                            <td>
                                Ничего не найдено.
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>

            {{ admin()->paginate($users) }}

        </div>

    </div>

    <load-js src="{{ mix('assets/admin/js/pages/users.js') }}"></load-js>

@endsection
