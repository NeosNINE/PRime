<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class GuestExtraController extends Controller
{

    /**
     * Обновление информации с backend для GUEST
     */
    public function refreshData( Request $request ): array
    {

        return [
            'csrf_token' => csrf_token()
        ];

    }

}
