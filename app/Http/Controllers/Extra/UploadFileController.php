<?php

namespace App\Http\Controllers\Extra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadFileController extends Controller
{

    /**
     * Загрузка файлов
     */
    public function upload( Request $request ): array
    {

        return fileUploads()->upload( $request );

    }



    /**
     * Загрузка файла в WYSIWYG редакторе
     */
    public function wysiwygUpload( Request $request ): array
    {

        return fileUploads()->wysiwygUpload( $request );

    }
}
