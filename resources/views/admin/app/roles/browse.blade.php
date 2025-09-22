@extends('admin.app.settings.layout')
@section('title', 'Роли' )

@section('settings_content')

    <div class="box-body no-padding" data-scroll-load>

        <div class="box-table">
            <table class="table table-striped roles-table @if( !count($roles) ) table-no-rows-found @endif">
                <thead>
                <tr class="tr-bg-primary">
                    <th>Роль</th>
                    <th>Права доступа</th>
                    <th class="actions">

                        {!!
                            admin()->buttons([
                                [
                                    'offcanvas-href' => route('admin.roles.add'),
                                    'style' => 'primary',
                                    'icon' => 'fa fa-user-plus',
                                    'text' => 'Добавить',
                                    'access' => 'roles.add',
                                    'dropdown' => [
                                        [
                                            'href' => route('admin.roles.browse', ['deleted' => true]),
                                            'text' => 'Архив'
                                        ]
                                    ]
                                ]
                            ])
                        !!}

                    </th>
                </tr>
                </thead>
                <tbody>
                @if(  count($roles)  )

                    @foreach( $roles as $role )
                        @include('admin.app.roles.components.table-row')
                    @endforeach

                @else

                    @if( request()->input('deleted') )

                        <tr>
                            <td>
                                Архив пустой.
                                <div class="actions">
                                    <a href="{{ route('admin.roles.browse') }}" class="btn btn-primary"><i class="fa fa-arrow-left-long"></i> <span>Назад</span></a>
                                </div>
                            </td>
                        </tr>

                    @else
                        <tr>
                            <td>
                                Ничего не найдено.
                                @if( roles()->checkAccess('roles.add') )
                                    <div class="actions">
                                        <a href="#" data-offcanvas-href="{{ route('admin.roles.add') }}" class="btn btn-primary"><i class="fa fa-user-plus"></i> <span>Добавить</span></a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif

                @endif
                </tbody>
            </table>
        </div>

        {{ admin()->paginate($roles) }}

    </div>


    <load-css src="{{ mix('assets/admin/css/pages/roles.css') }}"></load-css>
    <load-js src="{{ mix('assets/admin/js/pages/roles.js') }}"></load-js>

@endsection
