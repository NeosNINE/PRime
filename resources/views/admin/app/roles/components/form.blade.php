<div class="box">
    <div class="box-body">
        <div class="input">
            <label>
                <span class="label">Название</span>
                <input type="text" name="name" autocomplete="password"  value="{{ $role->name ?? '' }}">
            </label>
        </div>
        <div class="input">
            <label>
                <span class="label">Key (название на английском)</span>
                <input type="text" name="key" autocomplete="password" value="{{ $role->key ?? '' }}">
            </label>
        </div>
        <p class="text-muted">Укажите возможности, которые будут доступны этой роли.</p>
        <hr>
        <div class="role_accesses_block">
            @foreach( roles()->getAccesses() as $essence_key => $essence_data )

                <div class="accesses-line">
                    <span class="access-name">{{ $essence_data['name'] }}</span>

                    @foreach( $essence_data['accesses'] as $access_key => $access_data )
                        @php
                            $checked = in_array($access_key, isset($role) ? $role->access ?? [] : []);
                        @endphp

                        <div class="access" @if( isset($access_data['if_specified']) && $access_data['if_specified'] ) data-if-specified="{{ $access_data['if_specified'] }}" disabled @endif >
                            <label>
                                <input
                                    type="checkbox"
                                    name="access[]"
                                    value="{{ $access_key }}"
                                    autocomplete="password"
                                    @if( isset($access_data['if_specified']) && $access_data['if_specified'] && !in_array($access_data['if_specified'], isset($role) ? $role->access ?? [] : []) ) disabled @endif
                                    @checked( $checked )
                                >
                                <span class="label">{{ $access_data['name'] }}</span>
                            </label>
                        </div>

                    @endforeach

                </div>

            @endforeach
        </div>
    </div>
</div>
