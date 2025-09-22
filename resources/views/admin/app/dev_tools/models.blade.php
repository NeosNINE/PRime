@extends('admin.app.dev_tools.layout')

@section('dev_tools_content_second')



        @foreach( $models as $model )
            <div class="box">
                <div class="box-header">
                    <h6>{{ $model['name'] }}</h6>
                </div>
                <div class="box-body">

                    @if( is_null($model['service']) )

                        <div class="model-info-block">
                            <p>Service not found.</p>
                        </div>

                    @else

                        @if( isset($model['fields']) )
                            <div class="model-info-block">
                                <p>Fields</p>
                                <p>@foreach( $model['fields'] as $field_name => $field ) <span class="d-label label-default ml-10 mb-10">{{ $field_name }} <small class="text-muted">{{ $field['type'] }}</small></span> @endforeach</p>
                            </div>
                        @endif

                        @if( isset($model['events_name']) )
                            <div class="model-info-block">
                                <p>Events name</p>
                                <p>@foreach( $model['events_name'] as $event_name ) <span class="d-label label-default ml-10 mb-10">{{ $event_name }}</span> @endforeach</p>
                            </div>
                        @endif

                        @if( isset($model['relations']) && count($model['relations']) )
                            <div class="model-info-block">
                                <p>Relations</p>
                                <div class="no-padding">
                                    <table class="table table-striped">
                                        @foreach( $model['relations'] as $relation )

                                            <tr>
                                                <td class="width-by-content">{{ $relation['type'] }} <i class="fa fa-arrow-right-long ml-10 mr-10"></i> {{ $relation['to'] }}</td>
                                                <td>
                                                    @if( isset($relation['service_func']) && method_exists($relation['service_func'](), 'getRequestInputNamesForRelation') )

                                                        @foreach( $model['service']->getRequestInputNamesForRelation($relation) as $input_field_name )

                                                            <span class="d-label label-default ml-5 mt-5 mb-5 small">{{ $input_field_name }}</span>

                                                        @endforeach

                                                    @endif
                                                </td>
                                            </tr>

                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        @endif


                        @if( isset($model['not_has_extends']) )
                            <p>Can not get info because service does not extended from main Service class.</p>
                        @endif


                    @endif

                </div>
            </div>
        @endforeach


@endsection
