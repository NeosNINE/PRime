<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AdminLogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function browse(Request $request)
    {
        roles()->checkAccessWithAbort('logs.browse');

        $type = $request->get('type'); // login|balance_increase|balance_decrease
        $query = Activity::query()->with('causer');

        if ($type) {
            $query->where('log_name', $type);
        }

        $query->orderByDesc('created_at');
        $items = $query->paginate(30)->appends($request->all());

        return view('admin.app.logs.browse', compact('items'));
    }
}


