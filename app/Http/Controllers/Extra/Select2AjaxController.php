<?php

namespace App\Http\Controllers\Extra;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Select2AjaxController extends Controller
{

    /**
     * Для безопасности здесь прописываем модели и поля, которые будут доступны для загрузки
     * (для админов доступны все модели и поля)
     *
     * Осторожно. Из этого скрипта данные может получить любой, даже не авторизованный посетитель
     */
    private array $access = [
        'user' => [
            'fields' => [
                'id'
            ]
        ],
        'role' => [
            'fields' => [
                'id', 'name', 'key'
            ]
        ]
    ];


    /**
     * Ajax поиск в select2
     */
    public function getSearchResults( Request $request ): JsonResponse
    {

        $model = $request->input('model');

        $search = $request->input('search');
        $per_page = 20;

        $model_path = 'App/Models/' . str($model)->ucfirst();

        if( !file_exists(base_path($model_path.'.php')) && file_exists(base_path(str_replace('/Models/', '/Models/System/', $model_path). '.php')) )
            $model_path = str_replace('/Models/', '/Models/System/', $model_path);

        $model_path = str_replace('/', '\\', $model_path);

        $schema_path = str_replace('\\Models\\', '\\Models\\Schema\\', $model_path).'.json';
        $schema_path = str_replace('\\', '/', $schema_path);

        $model = app($model_path);

        $model_name = last(
            explode(
                '\\',
                str(get_class($model))->lower()
            )
        );


        if( !auth()->user() && !in_array($model_name, array_keys($this->access)) )
            abort(403, 'У Вас нет доступа для просмотра информации.');


        $fields = $request->input('columns');
        if( !$fields )
            $fields = Schema::getColumnListing($model->getTable());

        $fields_type = [];
        if( devTools()->fileExists($schema_path) )
            $fields_type = json_decode(devTools()->getFile($schema_path), true)['fields'] ?? [];


        $results = $model::query();

        if( !auth()->user() )
            $results->select($this->access[$model_name]['fields']);


        if( $search ) {

            $results->where(function ( $query ) use ( $fields, $fields_type, $search ) {

                foreach ( $fields as $field ) {

                    if ( isset($fields_type[$field]['type']) && $fields_type[$field]['type'] == 'json' ) {

                        $query->orWhere(DB::raw('lower(`' . $field . '`)'), "LIKE", "%" . mb_strtolower($search) . "%");

                    } else {

                        $query->orWhere($field, 'LIKE', '%' . $search . '%');

                    }

                }

            });


        }


        //TODO добавить проверку на безопасность (чтобы обычные пользователи не могли легко подгружать любые данные)
        if( $request->input('with_relations') )
            $results->with(explode(',', str_replace(' ', '', $request->input('with_relations'))));

        $results = $results->paginate($per_page);

        return response()->json([
            'results' => $results
        ]);

    }
}
