<?php

namespace App\Services\System;

use App\Models\System\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadsService
{

    /**
     * Загрузка файлов
     */
    public function upload( Request $request ): array
    {

        if( $request->input('data-validate-method') == 'avatar' ){

            $request->validate([
                'files.*' => 'file|mimes:jpg,jpeg,jpe,png|max:2048'
            ],[
                'files.*.mimes' => 'Для изображений можно использовать только следующие форматы: .jpg, .jpeg, .png',
                'files.*.max' => 'Максимальный возможный размер изображения 2 МБ.',
            ]);

        }


        $return = [];

        foreach( $request->file('files') as $file ){

            $path = $file->store('uploads','public');

            $return[] = [
                'status' => 'success',
                'file_url' => Storage::url($path),
                'file_name' => $file->getClientOriginalName()
            ];

            FileUpload::insert([
                'user_id' => auth()->id(),
                'path' => Storage::url($path),
                'original_name' => $file->getClientOriginalName(),
                'created_at' => now()
            ]);

        }


        return $return;

    }



    /**
     * Загрузка файла в WYSIWYG редакторе
     */
    public function wysiwygUpload( Request $request ): array
    {

        $img_path = $request->file('file')->store('wysiwyg-editor', 'public');

        return [
            'location' => 'storage/'.$img_path
        ];

    }



    /**
     * Проставить USED для всех файлов
     */
    public function setFilesUsed( $model, $data ): void
    {

        $model_name = get_class($model);
        $model_id = $model->id;

        FileUpload::where('model', $model_name)
                    ->where('model_id', $model_id)
                    ->update([
                        'used' => 0
                    ]);

        $paths = $this->getPathsFromData($data);

        foreach( $paths as $field_key => $path ){

            FileUpload::where('path', $path)
                ->update([
                    'model' => $model_name,
                    'model_id' => $model_id,
                    'field_key' => $field_key,
                    'used' => 1,
                    'updated_at' => now()
                ]);

        }

    }


    /**
     * Получить paths url для файлов, если он есть
     */
    public function getPathsFromData( $data ): array
    {

        $paths = [];

        foreach( $data as $field_key => $value ){

            if( is_array($value) ){

                $paths += $this->getPathsFromData($value);

            }else{

                if( str($value)->startsWith('/storage/uploads/') )
                    $paths[$field_key] = $value;

            }

        }

        return $paths;

    }

}
